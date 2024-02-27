<?php
// destrou session and unset variables
session_start();
unset($_SESSION["user"]);
session_destroy();
header("Location: login.php");
?>