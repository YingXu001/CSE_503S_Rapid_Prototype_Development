<?php
    session_start();
    
    // csrf token check
    if(!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }

    require 'database.php';

    if(isset($_POST['submit'])) {
        if(isset($_POST['user_id']) && isset($_POST['story_id']) && isset($_POST['comment_content'])) {
            // FIEO
            if (strlen($_POST['comment_content']) > 100) {
                echo "<script>alert('Content must be no more than 100 characters.');</script>";
                exit;
            }
            $userid = $_POST['user_id'];
            $story_id = $_POST['story_id'];
            $content = $_POST['comment_content'];
            $comment_token = isset($_POST['token']) ? $_POST['comment_token'] : null;
            // test for validity of the CSRF token on the server side
            if(!hash_equals($_SESSION['token'], $comment_token)) {
                echo "<p>Invalid form submission.</p>";
            }
            // insert into database
            $stmt = $mysqli->prepare("insert into comments (user_ID, story_ID, content) values (?, ?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('iis', $userid, $story_id, $content);
            $stmt->execute();
            $stmt->close();

            header("Location: detailedPage.php?id=".$story_id);
        }
    }
?>