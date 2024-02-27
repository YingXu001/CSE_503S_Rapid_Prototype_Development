<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" type="text/css" href="style/homepage.css">
</head>
<body>
    <h1>Welcome to Your Homepage!~</h1>
    <?php
        // get username from session  
        session_start();
        $username = $_SESSION['user'];
        // if not logged in, exit and redirect to the login page
        if(!$username){
            echo "please log in first!";
            echo "<br>";
            echo "<a href='login.php'>Click to jump to login page!</a>";
            exit;
        }

        $userpath = "/home/Fiona/hide/";
        // $userpath = "/home/RohanSong/hide/";
        $path = $userpath . $username;
        // make user's dir if it doesn't not exist
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
            echo "Folder created successfully";
        }

        // scan the user's folder and post all the files to the table, including size, type.
        // delete files by clicking delete button.
        $files = scandir($path);
        echo '<table class="tb">
        <tr>
            <td class="td">File Name</td>
            <td class="td">Sizes</td>
            <td class="td">Type</td>
            <td class="td">Action</td>
        </tr>';
        $folder_size = 0;
        foreach ($files as $key=>$value){
            // check if it is a file
            if (is_file($path . '/' . $value)){
                $bytes = filesize($path . '/' . $value);
                $size = round($bytes/1024,2);
                $type = pathinfo($path . '/' . $value, PATHINFO_EXTENSION);
                $folder_size += $size;
                echo '<tr class="td">
                <td class="td"><a href="view.php?file=' . $value . '">' . $value . '</a></td>
                <td class="td">'.$size.'kbs</td>
                <td class="td">'.$type.'</td>
                <td class="td"><form class=delete action="delete.php" method="post"><input type="hidden" name="file" value="' . $value . '"><input type="submit" value="Delete!"></form></td>
                </tr>';
            }
        }
        echo '</table>';
        // check folder size
        echo round($folder_size/1024,2)."Mb/20Mb";
        // zip all files in the folder by clicking "zip all"
        echo '<form action="zip.php" method="post">
                <input type="submit" name="button" class="btn" value="Zip all" />
            </form>';
        // log out by clicking "Log out"
        echo '<form action="logout.php" method="post">
            <input type="submit" name="button" class="logoutbtn" value="Log out" />
        </form>';

        echo '<form action="upload.php" method="post" enctype="multipart/form-data">
                <p>Choose the user you are uploading to?</p>
                <input type="text" name="username" class="usrname" placeholder="Please input your username" required><br><br>
                <label for="file">Click to select files: </label>
                <input type="hidden" value='.$folder_size.' name="size">
                <input type="file" name="file" id="file"><br>
                <input type="submit" name="submit" value="Upload">
            </form>';
    ?>

</body>
</html>