<?php
    require 'database.php';
    session_start();
    $user_ID = $_SESSION['userid'];
    $storyid = $_POST['storyid'];

    // users can only edit their own posts
    if($user_ID==$_POST['userid']){
        if(isset($_POST['submit'])) {
            // FIEO
            if (strlen($_POST['title']) > 255) {
                echo "<script>alert('Title must be no more than 255 characters.');</script>";
                exit;
            }
            if (strlen($_POST['url']) > 255) {
                echo "<script>alert('URL must be no more than 255 characters.');</script>";
                exit;
            }
            if (strlen($_POST['content']) > 65535) {
                echo "<script>alert('Content must be no more than 65535 characters.');</script>";
                exit;
            }
            if (!filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                echo "<script>alert('URL is not valid.');</script>";
                exit;
            }
            $title = $_POST['title'];
            $url = $_POST['url'];
            $content = $_POST['content'];

            // sql query for update the post
            $stmt = $mysqli->prepare("update stories set title=?,content=?,link=? where id=?");
            if (!$stmt) {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('sssi', $title, $content,$url, $storyid);
            if(!$stmt->execute()) {
                echo "error";
            }
            $stmt->close();
        
            header("Location: detailedPage.php?id=".$storyid);
            exit;
        }
    }
?>