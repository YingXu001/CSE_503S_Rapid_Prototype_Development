<?php
    session_start();

    if(!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
    <link rel="stylesheet" type="text/css" href="style/detailedPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
</head>
<body>
    <table>
        <tbody>
            <tr>
                <td>
                    <a href="main.php">
                        <img src="static/logo.png" alt="" class='logo'>
                    </a> 
                </td>
                <td>
                    <span>
                        <a href="main.php">Main Page</a>
                        |
                        <?php
                            // session_start();
                            if(isset($_SESSION['username'])) {
                                // User is logged in
                                echo '<a href="profile.php?id='.htmlentities($_SESSION['userid']).'">'.htmlentities($_SESSION['username']).'</a>
                                |
                                <a href="logout.php">Logout</a>';
                            } else {
                                // User is not logged in
                                echo '<a href="login.php">Login</a>
                                |
                                <a href="register.php">Register</a>';
                            }                            
                        ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
        <?php
            require "database.php";
            $id = $_GET['id'];
            // sql query
            $stmt = $mysqli->prepare(
                    "SELECT u.username, s.title, s.time, s.content, s.link, s.clicks
                    FROM stories AS s
                    JOIN users AS u ON s.user_ID = u.id
                    WHERE s.id = ?");

            if (!$stmt) {
                echo "Error: " . htmlentities($mysqli->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($username, $title, $time, $content, $link, $click);
            $stmt->fetch();
            $stmt->close();

            // check click times and update from database
            $click += 1;
            $stmt = $mysqli->prepare('update stories set clicks=? where id=?');
            $stmt->bind_param('ii', $click, $id);
            $stmt->execute();
            $stmt->close();
        ?>
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <!-- comment info -->
        <div class="post-info">
            <p>Written by <?php echo htmlspecialchars($username); ?>&nbsp;</p>
            <p>on <?php echo htmlspecialchars($time); ?>&nbsp;CDT</p>
        </div>
        <div class="container">
            <p class="content"><?php echo htmlspecialchars($content); ?></p>
            <div class="linkdiv">
                From: <a class="link" href="<?php echo htmlspecialchars($link); ?>"><?php echo htmlspecialchars($link); ?></a>
            </div>
        </div>

    
        <h2>Comments</h2>

        <?php
            require 'database.php';
            $id = $_GET['id'];
            // sql query for show all the details
            $stmt = $mysqli->prepare("select c.id, u.id,u.username,c.content,c.time
                                        from comments as c
                                        join users as u on c.user_ID = u.id
                                        where c.story_ID = ?");

            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($commentid, $userid, $username, $content, $time);
            while ($stmt->fetch()) {
                echo '<div class="comment">';
                echo '<p>Comment by ' . htmlspecialchars($username) . ' on ' . htmlspecialchars($time);
                // update and delete comment actions
                if($_SESSION['userid']==$userid) {
                    echo '<form action="updateComment.php" method="POST">';
                    echo '<input type="hidden" name="comment_id" value="' . htmlentities($commentid) . '">';
                    echo '<input type="hidden" name="story_id" value="' . htmlentities($id) . '">';
                    echo '<input type="hidden" name="user_id" value="' . htmlentities($userid) . '">';
                    echo '<button type="submit" name="edit_comment" style="border: none; background: none;">';
                    echo '<i class="fas fa-edit"></i>';
                    echo '</button>';
                    echo '</form>';
                    echo '<form action="deleteComment.php" method="POST">';
                    echo '<input type="hidden" name="comment_id" value="' . htmlentities($commentid). '">';
                    echo '<input type="hidden" name="story_id" value="' . htmlentities($id) . '">';
                    echo '<input type="hidden" name="user_id" value="' . htmlentities($userid) . '">';
                    echo '<input type="hidden" name="comment_token" value="' . htmlentities($_SESSION['token']) . '">';

                    echo '<button type="submit" name="delete_comment" style="border: none; background: none;">';
                    echo '<i class="fas fa-trash-alt"></i>';
                    echo '</button>';
                    echo '</form>';
                }else{
                    echo '</p>';
                }
                echo '<p>' . htmlspecialchars($content) . '</p>';
                echo '</div>';                        
            }
            $stmt->close();

            if(isset($_SESSION['username'])) {
                // User is logged in
                $userid = $_SESSION['userid'];
                echo '<form action="addComment.php" method="POST">
                        <label for="comment_content">'.htmlentities($_SESSION['username']).', add a comment:</label><br>
                        <textarea id="comment_content" name="comment_content"></textarea><br>
                        <input type="hidden" name="story_id" value="'.htmlentities($id).'">
                        <input type="hidden" name="user_id" value="'.htmlentities($userid).'">
                        <input type="submit" name="submit" value="Submit">
                    </form>';
            } else {
                // User is not logged in, guests can not make a comment
                echo '<a href="login.php">Please log in before making comments!</a>';
            } 
        ?>

</body>
</html>