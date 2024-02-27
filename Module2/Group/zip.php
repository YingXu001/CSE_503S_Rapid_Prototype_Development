<?php
session_start();
$username = $_SESSION['user'];

// $userpath = "/home/RohanSong/hide/";
$userpath = "/home/Fiona/hide/";
$path = $userpath . $username;

// Get real path for our folder
$rootPath = realpath($path);

// Initialize archive object
$zip = new ZipArchive();
// get current time as the zip file name
$time = date("Ymdhis");
$zip->open($rootPath.'/'.$time.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
// for all files in current folder
foreach ($files as $name => $file)
{
    // Skip directories
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();
echo "Successfully zipped all files.";
?>