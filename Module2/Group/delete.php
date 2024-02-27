<?php 
// get username from session  
session_start();
$username = $_SESSION['user'];    
 
$path = '/home/Fiona/hide/' .$username;
// $path = '/home/RohanSong/hide/' .$username;
// if file exists, delete it
if (isset($_POST['file'])) {
    $file = $_POST['file'];
    if (is_file($path . '/' . $file)) {
        unlink($path . '/' . $file);
        echo 'Document ' . $file . ' have already been deleted';
    } else {
        echo 'Document ' . $file . ' did not exist';
    }
}
?>