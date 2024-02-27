<?php 
    header('Content-Type: application/json; charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['eventId'];
    $username = $data['username'];

    header("Content-Type: application/json");
	ini_set("session.cookie_httponly", 1);
	session_start();
    if($_SESSION['username']==$username){
        require 'database.php';
    
        // sql query for all the story details shown on the main page
        $sql = "DELETE FROM events WHERE event_id = ?";
  
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    
        $str = array
            (
                'result'=>"success"
            );
    
        $jsonencode = json_encode($str);
        echo $jsonencode;
    }
?>
