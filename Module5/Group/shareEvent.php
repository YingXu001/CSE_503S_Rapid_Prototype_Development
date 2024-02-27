<?php 
    header('Content-Type: application/json; charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true);
    $sharename = $data['name'];
    $username = $data['username'];

    header("Content-Type: application/json");
	ini_set("session.cookie_httponly", 1);
	session_start();
    if(!preg_match('/^[\w_\.\-]+$/', $sharename)){
        $str = array
        (
            'result'=>'fail'
        );

        $jsonencode = json_encode($str);
        echo $jsonencode;
        exit;
    }
    if($_SESSION['username']==$username){
        require 'database.php';

        // sql query for user_id
        $sql = "SELECT id FROM users WHERE username=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $sharename);
            $stmt->execute();
            $stmt->bind_result($shareid);
            $stmt->fetch();  
            $stmt->close();
        }

        // sql query for all events of certain usernames
        if($shareid){
            $sql = "INSERT INTO events(user_id,username,title,content,time,todo_status,category) 
            SELECT ?,?,title,content,time,todo_status,category
            FROM events 
            WHERE username=?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param('iss', $shareid,$sharename,$username);
                $stmt->execute();
                $stmt->close();
            }

            $str = array
                (
                    'result'=>'success'
                );

            $jsonencode = json_encode($str);
            echo $jsonencode;
        }else{
            $str = array
            (
                'result'=>'invaild'
            );
    
            $jsonencode = json_encode($str);
            echo $jsonencode;
        }

    }
?>
