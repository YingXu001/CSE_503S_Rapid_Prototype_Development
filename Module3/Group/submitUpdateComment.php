<?php
    require 'database.php';
    if(isset($_POST['submit'])) {
        session_start();
        if(isset($_POST['comment_id']) && isset($_POST['story_id'])){
            // FIEO
            if (strlen($_POST['comment_content']) > 100) {
                echo "<script>alert('Content must be no more than 100 characters.');</script>";
                exit;
            }
            $id = $_POST['comment_id'];
            $story_id = $_POST['story_id'];

            if (!isset($_SESSION['username'])) {
                header("Location: login.php");
                exit;
            }else{
                $user_id = $_POST['user_id'];
                // users can only edit their own comments
                if($_SESSION['userid']==$user_id){
                    $content = $_POST['comment_content'];
                    // sql query for updating the comment
                    $stmt = $mysqli->prepare("update comments set content=? where id=?");
                    if (!$stmt) {
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('si', $content, $id);
                    $stmt->execute();
                    $stmt->close();
                
                    header("Location: detailedPage.php?id=".$story_id);
                    exit;
                }
            }
        }

    }
?>
