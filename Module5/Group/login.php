<?php
    // session_start();

    // if(!isset($_SESSION['token'])) {
    //     $_SESSION['token'] = bin2hex(random_bytes(32));
    // }

    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
    
    require 'helper.php';
    
    // HTTP-Only Cookies
    ini_set("session.cookie_httponly", 1);

    // Because you are posting the data via fetch(), php has to retrieve it elsewhere.
    $json_str = file_get_contents('php://input');
    // This will store the data into an associative array
    $json_obj = json_decode($json_str, true);

    //Variables can be accessed as such:
    $username = $json_obj['username'];
    $password = $json_obj['password'];
    //This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

    // Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
    // check if username is invalid
    if(!preg_match('/^[\w_\.\-]+$/', $username)){
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid Username or Password",
            "username" => $username,
            "password" => $password));
        exit;
    }

    if(loginValidate($username, $password)){
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 
    
        echo json_encode(array(
            "success" => true,
            "username" => $username,
            "password" => $password,
            "token" => $_SESSION['token']
        ));
        exit;
    }else{
        echo json_encode(array(
            "success" => false,
            "message" => "Incorrect Username or Password",
            "username" => $username,
            "password" => $password
        ));
        exit;
    }
?>
