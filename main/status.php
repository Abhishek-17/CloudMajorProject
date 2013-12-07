<?php
require 'config.php';
if(isset($_GET['wid'])){
    $result=  selectfromdb("transition",array("id"),"type='start'");
    $start=mysqli_fetch_array($result)['id'];
    if($start!=""){
        echo "<h2>workflow id=".$_GET['wid']."</h2>";
        getstatus($_GET['wid'],$start);
    }
}
else{echo "no workflow selected";}
?>
