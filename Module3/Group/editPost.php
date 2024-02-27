<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post News</title>
    <link rel="stylesheet" type="text/css" href="style/post.css">
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
                            session_start();
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

    <h1>Edit Your Story</h1>
    <form action="submitEditPost.php" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br><br>
        <label for="title">URL:</label>
        <input type="text" name="url" id="url" required><br><br>
        <input type="hidden" name="storyid" value="<?php echo htmlentities($_GET['storyid']); ?>" >
        <input type="hidden" name="userid" value="<?php echo htmlentities($_GET['userid']); ?>" >
        <label for="content">Content:</label>
        <textarea name="content" id="content" rows="10" cols="50" required></textarea><br><br>
        <input type="submit" name="submit" value="Submit">
    </form>

</body>
</html>
