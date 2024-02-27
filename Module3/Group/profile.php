<?php
    require 'database.php';
    $user_id = $_GET['id'];

    // fetch user profile from datbase
    $user_query = "SELECT username FROM users WHERE id = ?";
    $user_stmt = $mysqli->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_stmt->bind_result($username);
    $user_stmt->fetch();
    $user_stmt->close();

    // fetch all the stories the user posted
    $stories_query = "SELECT id, title, content, time FROM stories WHERE user_ID = ?";
    $stories_stmt = $mysqli->prepare($stories_query);
    $stories_stmt->bind_param("i", $user_id);
    $stories_stmt->execute();
    $stories_stmt->bind_result($story_id, $title, $content, $time);
    $stories = array();
    while ($stories_stmt->fetch()) {
        $stories[] = array("id"=>$story_id, "title"=>$title, "content"=>$content, "time"=>$time);
    }
    $stories_stmt->close();

    // fetch all the comments the user made
    $comments_query = "SELECT c.id, c.content, c.time, s.title FROM comments AS c JOIN stories AS s ON c.story_ID = s.id WHERE c.user_ID = ?";
    $comments_stmt = $mysqli->prepare($comments_query);
    $comments_stmt->bind_param("i", $user_id);
    $comments_stmt->execute();
    $comments_stmt->bind_result($comment_id, $comment_content, $comment_time, $story_title);
    $comments = array();
    while ($comments_stmt->fetch()) {
        $comments[] = array("id"=>$comment_id, "content"=>$comment_content, "time"=>$comment_time, "title"=>$story_title);
    }
    $comments_stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <link rel="stylesheet" type="text/css" href="style/profile.css">
</head>
<body>
    <h1><?php echo htmlentities($username); ?></h1>
    <p>ID: <?php echo htmlentities($user_id); ?></p>
    <h2>Stories</h2>
    <?php foreach ($stories as $story): ?>
        <div class="story">
            <h3><?php echo htmlentities($story['title']); ?></h3>
            <p><?php echo htmlentities($story['content']); ?></p>
            <p>Time: <?php echo htmlentities($story['time']); ?></p>
        </div>
    <?php endforeach; ?>

    <h2>Comments</h2>
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p><?php echo htmlentities($comment['content']); ?></p>
            <p>Time: <?php echo htmlentities($comment['time']); ?></p>
            <p>Related Story: <?php echo htmlentities($comment['title']); ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
