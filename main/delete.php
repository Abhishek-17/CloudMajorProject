<?php
require 'config.php';
if(isset($_GET['wid'])){
    $wid=$_GET['wid'];
    $f="";
    if(isset($_GET['func']))$f="function_";
$delete="delete from ".$f."workflow where id=".$wid;
    if (!mysqli_query($con, $delete)) {
            die('Error :' . mysqli_error($con));
        }
         $delete="delete from ".$f."transition where workflow_id=".$wid;
    if (!mysqli_query($con, $delete)) {
            die('Error :' . mysqli_error($con));
        }
         $delete="delete from ".$f."inputs where workflow_id=".$wid;
    if (!mysqli_query($con, $delete)) {
            die('Error :' . mysqli_error($con));
        }
    
                if($f="")logg("deleted workflow wid=".$_GET['wid']);
                else logg("deleted function fwid=".$_GET['wid']);
     
}
header('Location:upload_file.php');
?>
