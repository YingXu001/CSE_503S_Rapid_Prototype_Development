<?php 
    header('Content-Type: application/json; charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'];
    $content = $data['content'];
    $year = $data['year'];
    $month = $data['month'];
    $day = $data['day'];
    $username = $data['username'];
    $time = $data['time'];
    $id = $data['eventId'];
    $category = $data['category'];
    $token = $data['token'];

    header("Content-Type: application/json");
	ini_set("session.cookie_httponly", 1);
	session_start();
    // CSRF check
    if(!hash_equals($_SESSION['token'], $token)){
        die("Request forgery detected");
    }
    if($_SESSION['username']==$username){
        $dateFormatted = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $dateFormatted = $dateFormatted . ' ' . $time . ':00';
    
        require 'database.php';
        // sql query for all the story details shown on the main page
        $sql = "UPDATE events SET username=?, time=?, title=?, content=?, category=? WHERE event_id=?";
  
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('sssssi', $username,$dateFormatted,$title,$content,$category,$id);
            $stmt->execute();
            $stmt->close();
        }
    
        $str = array
            (
                'result'=>"success",
            );
    
        $jsonencode = json_encode($str);
        echo $jsonencode;
    }
?>
