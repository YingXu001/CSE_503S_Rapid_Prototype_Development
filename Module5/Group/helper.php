<?php
    function addUser($username, $passwordHash) {
        require "database.php";
    
        // Check if the username is already taken
        $stmt = $mysqli->prepare('select * from users where username = ?');
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            return false;
        }
        $stmt->close();

        // Insert the new user into the database
        $stmt = $mysqli->prepare('insert into users (username, password) values (?, ?)');
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('ss', $username, $passwordHash);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    
    
    function loginValidate($username, $password): bool {
        require "database.php";
        $stmt = $mysqli->prepare("select * from users where username=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        // Bind the parameter
        $stmt->bind_param('s', $username);
        $stmt->execute();
        // Bind the results
        $stmt->bind_result($db_id, $db_name, $db_pwd);
        $stmt->fetch();
        $stmt->close();

        return password_verify($password, $db_pwd);
    }
?>