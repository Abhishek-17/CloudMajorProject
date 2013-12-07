<?php

//$file="";
if ($_FILES["file"]["error"] > 0) {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {
    $allowedExts = array("xml", "XML");
    $temp = explode(".", $_FILES["file"]["name"]);
    $extension = end($temp);

    if ($_FILES["file"]["type"] == "text/xml" && ($_FILES["file"]["size"] < 20000) && in_array($extension, $allowedExts)) {
        if ($_FILES["file"]["error"] > 0) {
            echo "Error: " . $_FILES["file"]["error"] . "<br>";
        } else {
            /* echo "Upload: " . $_FILES["file"]["name"] . "<br>";
              echo "Type: " . $_FILES["file"]["type"] . "<br>";
              echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
              echo "Stored in: " . $_FILES["file"]["tmp_name"]; */
            $file = $_FILES["file"]["tmp_name"];
        }
    } else {
        echo "Invalid file<br/>Expected xml file.";
    }
}
if (!isset($_POST['func'])) {
   
    if (isset($_POST['nodeid']) && isset($_POST['wid']))
        require 'parsenode.php';
    else if (isset($_POST['wid']))
        require 'parseworkflow.php';
    else
        require 'parse.php';
}
else {
    
    if (isset($_POST['nodeid']) && isset($_POST['wid']))
        require 'functionparsenode.php';
    else if (isset($_POST['wid']))
        require 'functionparseworkflow.php';
    else
        require 'functionparse.php';
}
// 
?>
