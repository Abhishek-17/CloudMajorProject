<a href="upload_file.php">Go to upload_file.php</a><br>
<?php //echo $_SESSION['email'];
require 'config.php'; 
?>
<html>
<body>

    <h3>Currently available Activities</h3>
 <?php 
    $result=selectfromdb("workflow",array("cost"),"id=".$_GET['wid']);
    echo "<h3>Cost till now:".mysqli_fetch_array($result)['cost']."</h3><br>";
    $result=selectfromdb("transition",array('id','type'),"workflow_id=".$_GET['wid']);
    echo '<a href="modify.php?wid='.$_GET['wid'].'">Modify Workflow</a><br>';
    echo '<a href="delete.php?wid='.$_GET['wid'].'">Delete Workflow</a><br>';
    echo '<a href="status.php?wid='.$_GET['wid'].'">Workflow Status</a><br>';
    echo '<table border="1"><tr><td>id</td><td>activity id</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td>';
        
        echo '<td><a href="showtask_1.php?wid='.$_GET['wid'].'&tid='.$row['id'].'"> Task '.$row['id']." type=".$row['type'].'</a><br/></td>';
        echo '</tr>';
    }
     echo '</table>';
    

 ?>
</body>
</html>