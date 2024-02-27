<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
    <link rel="stylesheet" type="text/css" href="style/detailedPage.css">
</head>
<body>
    <h1>Update your comment below</h1>
    <?php
        require 'database.php';
        session_start();
        $user_id = $_POST['user_id'];

        if(isset($_POST['comment_id']) && isset($_POST['story_id'])){
            $id = $_POST['comment_id'];
            $story_id = $_POST['story_id'];
        }
        // guest can not make comments
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit;
        }else{
            // users can only edit their own comment
            if($_SESSION['userid']==$user_id){
                echo '<form action="submitUpdateComment.php" method="POST">
                        <textarea id="comment_content" name="comment_content"></textarea><br>
                        <input type="hidden" name="comment_id" value="' . htmlentities($id) . '">
                        <input type="hidden" name="story_id" value="' . htmlentities($story_id) . '">
                        <input type="hidden" name="user_id" value="' . htmlentities($user_id) . '">
                        <input type="submit" name="submit" value="Submit">
                    </form>';
            }
        }
    ?>
</body>
</html>