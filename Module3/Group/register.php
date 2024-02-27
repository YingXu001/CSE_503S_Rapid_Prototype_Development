<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" type="text/css" href="style/login.css">
</head>
<body>
    <h1>Register System</h1><br><br>
    <div class = "login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="register" value="1">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Endter your password" required><br><br>
            <input type="submit" value="Register">
        </form>
    </div>
    <br><br><br><br>
    <form class = "back" action="login.php">
        <input type="submit" value="Back to Log In Page!">
    </form>
    <?php
        session_start();

        require 'database.php';

        // Check if the form has been submitted and the redirect has not already happened
        if (isset($_POST['register'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Check if the username is already taken
            $stmt = $mysqli->prepare('select * from users where username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row) {
                echo "<p>This username is already taken. Please choose a different one.</p>";
                header('Location: register.php');
                exit;
            }
            $stmt->close();

            // Check if the username is valid
            if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
                echo "<p>Username can only contain letters and numbers.</p>";
                header('Location: register.php');
                exit;
            }

            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $mysqli->prepare('insert into users (username, password) values (?, ?)');
            $stmt->bind_param('ss', $username, $passwordHash);
            $stmt->execute();
            $stmt->close();

            // Redirect the user to the login page
            echo "<p>You have successfully registered. Please log in.</p>";
            header('Location: login.php');
            exit;
        }
    ?>
</body>
</html>