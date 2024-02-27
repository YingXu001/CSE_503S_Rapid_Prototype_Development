<?php

    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
    
    // HTTP-Only Cookies
    ini_set("session.cookie_httponly", 1);
    session_start();
    if(isset($_SESSION['username'])){
        echo json_encode(array(
            "status" => true,
            "username" => $_SESSION['username']
        ));
        exit;
    }else{
        echo json_encode(array(
            "status" => false,
            "username" => ""
        ));
        exit;
    }
?>
