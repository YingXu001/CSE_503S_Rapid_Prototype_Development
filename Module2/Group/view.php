<?php
session_start();
// filename check
$filename = $_GET['file'];
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}
// username check
$username = $_SESSION['user'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}

// $full_path = sprintf("/home/RohanSong/hide/%s/%s", $username, $filename);
$full_path = sprintf("/home/Fiona/hide/%s/%s", $username, $filename);

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($full_path);
// push file from server to brower
header("Content-Type: ".$mime);
header('content-disposition: inline; filename="'.$filename.'";');
readfile($full_path);

?>