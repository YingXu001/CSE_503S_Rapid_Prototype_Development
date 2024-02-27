<?php 
    header('Content-Type: application/json; charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'];
    $getUsername = $data['username'];

	ini_set("session.cookie_httponly", 1);
	session_start();
    // CSRF check
    // if(!hash_equals($_SESSION['token'], $token)){
    //     die("Request forgery detected");
    // }
    if($_SESSION['username']==$getUsername){
        $dateTimestamp = strtotime($date);
        $dateFormatted = date('Y-m-d', $dateTimestamp);
    
        require 'database.php';
    
        // sql query for all the story details shown on the main page
        $sql = "SELECT * 
                FROM events as e
                where DATE(time)=? and username=? and todo_status=FALSE";
    
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('ss', $dateFormatted,$getUsername);
            $stmt->execute();
            $stmt->bind_result($eventId, $userID, $username, $title, $content, $datetime, $status, $category);
            // for id
            $count = 0;
            $todoHtmlResult = "";
            while ($stmt->fetch()) {
                $count += 1;
                // link to detailed page
                $todoHtmlResult .= "<tr><td><input type='radio' class='radio' name='event-select' value='".htmlentities($eventId)."'></td><td>".htmlentities($count)."</td><th>".htmlentities($title)."</th></tr>"
                ."<tr><td></td><td>".htmlentities($datetime) . "</td></tr>"
                ."<tr><td></td><td>".htmlentities($content) . "</td></tr>"
                ."<tr><td>".htmlentities($category) . "</td></tr>"
                ."<tr><td></td><td><button class='edit-button' data-id='".htmlentities($eventId)."'>Edit</button><button class='delete-button' data-id='".htmlentities($eventId)."'>Delete</button></td></tr>";    
            }
            $stmt->close();
        }

        $sql = "SELECT * 
        FROM events as e
        where DATE(time)=? and username=? and todo_status=TRUE";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('ss', $dateFormatted,$getUsername);
            $stmt->execute();
            $stmt->bind_result($eventId, $userID, $username, $title, $content, $datetime, $status, $category);
            // for id
            $count = 0;
            $doneHtmlResult = "";
            while ($stmt->fetch()) {
                $count += 1;
                // link to detailed page
                $doneHtmlResult .= "<tr><td><input type='radio' class='radio' name='event-select' value='".htmlentities($eventId)."'></td><td>".htmlentities($count)."</td><th>".htmlentities($title)."</th></tr>"
                ."<tr><td></td><td>".htmlentities($datetime) . "</td></tr>"
                ."<tr><td></td><td>".htmlentities($content) . "</td></tr>"
                ."<tr><td>".htmlentities($category) . "</td></tr>"
                ."<tr><td></td><td><button class='edit-button' data-id='".htmlentities($eventId)."'>Edit</button><button class='delete-button' data-id='".htmlentities($eventId)."'>Delete</button></td></tr>";    
            }
            $stmt->close();
        }
    
        $str = array
            (
                'todoHtmlCode'=>$todoHtmlResult,
                'doneHtmlCode'=>$doneHtmlResult
            );
    
        $jsonencode = json_encode($str);
        echo $jsonencode;
    }

?>
