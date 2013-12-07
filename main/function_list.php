<a href="upload_file.php">Go to upload_file.php</a><br>
<?php //echo $_SESSION['email'];
require 'config.php'; 
?>
<html>
<body>

    <h3>Currently available Functions</h3>
 <?php 
   
    $result=selectfromdb("function_transition",array('id','type'),"workflow_id=".$_GET['fwid']);
   // echo '<a href="modify.php?wid='.$_GET['fwid'].'&func=1">Modify Function</a><br>';
    echo '<a href="delete.php?wid='.$_GET['fwid'].'&func=1">Delete Function</a><br>';
    echo '<table border="1"><tr><td>id</td><td>activity id</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td>';
        
        echo '<td><a href="showtask_1.php?wid='.$_GET['fwid'].'&tid='.$row['id'].'&func=1"> Task '.$row['id'].'</a><br/></td>';
        echo '</tr>';
    }
     echo '</table>';
    

 ?>
</body>
</html>
