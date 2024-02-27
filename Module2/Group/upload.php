<?php
$username = $_POST['username'];
$folder_size = $_POST['size'];
// check if successfully uploaded
if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
    echo "Note: PHP will restrict you to a maximum 2 MB upload file size.";
}else{
    // $path = '/home/RohanSong/hide/' .$username;
    $path = '/home/Fiona/hide/' .$username;
    if(!file_exists($path)){
        echo '<script>alert("User not found, back and try again!")</script>';
    }else{
        // check if exceed size limit
        if ($_FILES['file']['size']>=2000000){
            echo "PHP will restrict you to a maximum 2 MB upload file size, please try another file.";
            exit;
        }
        if (round($_FILES['file']['size']/1024,2)+$folder_size>=20480){
            echo "Exceed folder limit(20Mb), please manage your folder and try again.";
            exit;
        }
        // check if this file already exist
        // if (file_exists("/home/RohanSong/hide/$username/" . $_FILES["file"]["name"])){
        if (file_exists("/home/Fiona/hide/$username/" . $_FILES["file"]["name"])){
            echo $_FILES["file"]["name"] . " has already existed. ";
        }else{
            // filename check
            if(!preg_match('/^[\w_\.\-]+$/', $_FILES["file"]["name"]) ){
                echo "Invalid filename";
                exit;
            }
            // replace filr from temp folder to user's folder
            // if(move_uploaded_file($_FILES["file"]["tmp_name"], "/home/RohanSong/hide/$username/" . $_FILES["file"]["name"])){
            if(move_uploaded_file($_FILES["file"]["tmp_name"], "/home/Fiona/hide/$username/" . $_FILES["file"]["name"])){
                echo "Successfully saved!";
            }else{
                echo "Upload failed!";
            }
        }
    }
}
?>