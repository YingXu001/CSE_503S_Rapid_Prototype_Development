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
    <title>login</title>
    <link rel="stylesheet" type="text/css" href="style/login.css">
</head>
<body>
    <h1>Login System</h1><br><br>
    <div class = "login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Endter your password" required><br>
            <input type="hidden" name="login_token" value="<?php echo htmlentities($_SESSION['token']);?>">
            <input type="submit" value="Log In">
        </form>
        <br>
        <form action="register.php" method="POST">
            <p>Not yet registered? <input type="submit" value="Register"></p>
        </form>

        <form action="main.php" method="POST">
            <p>Wanna view as a guest? <input type="submit" value="Guest Entry"></p>
        </form>
    </div>

    <?php
        if(!isset($_POST['username']) || !isset($_POST['password'])){
        }
        else{
            require 'database.php';

            $username = $_POST['username'];
            $password = $_POST['password'];
            $login_token = isset($_POST['login_token']) ? $_POST['login_token'] : null;
            // test for validity of the CSRF token on the server side
            if(!hash_equals($_SESSION['token'], $login_token)) {
                echo "<p>Invalid form submission.</p>";
            }
            else{
                // sql query for check if user is vaild
                $stmt = $mysqli->prepare("select id, username, password from users where username=?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->bind_result($db_id, $db_user, $db_pw);
                $stmt->fetch();
                $stmt->close();
                if(!password_verify($password, $db_pw)){
                    echo "<p>Incorrect username or password!</p>";
                    exit;
                }
                else{
                    $_SESSION['username'] = $username;
                    $_SESSION['userid'] = $db_id;
                    header("Location: main.php");
                    exit;
                }
            }
        }
    ?>

</body>
</html>
