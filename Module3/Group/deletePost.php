<?php
    require 'database.php';
    if(isset($_GET['storyid'])) {
        session_start();
        $user_ID = $_SESSION['userid'];
        $storyid = $_GET['storyid'];
    
        // users can only delete their own posts
        if($user_ID==$_GET['userid']){
            // delete post from database
            $stmt = $mysqli->prepare("delete from stories where id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $storyid);
            $stmt->execute();
            $stmt->close();
    
            header("Location: main.php");
        }
    }
?>