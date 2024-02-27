<?php
	session_start();
	session_unset();
	session_destroy();
	//redirect to the homepage
	header('Location: login.php');
?>