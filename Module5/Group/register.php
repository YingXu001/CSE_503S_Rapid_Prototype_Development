<?php
header("Content-Type: application/json");
require 'helper.php';
ini_set("session.cookie_httponly", 1);
session_start();

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = $json_obj['username'];
$password = $json_obj['password'];

// Check if the username is valid
if(!preg_match('/^[\w_\.\-]+$/', $username)){
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid Username",
        "username" => $username,
        "password" => $password
    ));
    exit;
}

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

if(addUser($username, $passwordHash)){
    echo json_encode(array(
        "success" => true,
        "username" => $username,
        "password" => $password
    ));
    exit;
}else 
{
    echo json_encode(array(
        "success" => false,
        "message" => "Duplicate Username",
        "username" => $username,
        "password" => $password
    ));
    exit;
}
?>






