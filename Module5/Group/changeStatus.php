<?php
    header('Content-Type: application/json; charset=utf-8');
    ini_set("session.cookie_httponly", 1);
	session_start();
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $id = $data['eventId'];

    if($_SESSION['username']==$username){
        require 'database.php';
    
        // sql query for all events of certain usernames
        $sql = "UPDATE events SET todo_status = NOT todo_status WHERE event_id=?";
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