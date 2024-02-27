<?php
    require 'database.php';
    if(isset($_POST['delete_comment'])) {
        session_start();
        if(isset($_POST['comment_id']) && isset($_POST['story_id'])){
            $id = $_POST['comment_id'];
            $story_id = $_POST['story_id'];

            if (!isset($_SESSION['username'])) {
                header("Location: login.php");
                exit;
            }else{
                $user_id = $_POST['user_id'];
                // users can only update their own comments
                if($_SESSION['userid']==$user_id){
                    // delete comment from database
                    $stmt = $mysqli->prepare("delete from comments where id = ?");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();
            
                    header("Location: detailedPage.php?id=". $story_id);
                }
            }
        }
    }
?>