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
    <html>
        <?php
            // guests can not post
            if (!isset($_SESSION['username'])) {
                header("Location: login.php");
                exit;
            }
        ?>
        <head>
            <title>Upload Your Story</title>
        </head>
        <body>
            <h1>Upload Your Story</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required><br><br>
                <label for="title">URL:</label>
                <input type="text" name="url" id="url" required><br><br>
                <label for="content">Content:</label>
                <textarea name="content" id="content" rows="10" cols="50" required></textarea><br><br>
                <input type="hidden" name="post_token" value="<?php echo $_SESSION['token'];?>">
                <input type="submit" name="submit" value="Submit">
            </form>
        </body>

        <?php
            require 'database.php';
            $user_ID = $_SESSION['userid'];
            if(isset($_POST['submit'])) {
                $post_token = isset($_POST['post_token']) ? $_POST['post_token'] : null;
                // test for validity of the CSRF token on the server side
                if(!hash_equals($_SESSION['token'], $post_token)) {
                    echo "<p>Invalid form submission.</p>";
                }
                if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['url'])) {
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

                    // sql query for inserting posts
                    $stmt = $mysqli->prepare("insert into stories (user_ID, title, content, link) values (?, ?, ?, ?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('isss', $user_ID, $title, $content, $url);
                    if(!$stmt->execute()) {
                        echo "error";
                    }
                    $stmt->close();

                    header("Location: main.php");
                }
            }
        ?>

    </html>




</body>
</html>