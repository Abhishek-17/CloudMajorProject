<?php
require 'config.php';
$result=selectfromdb("transition,workflow",array("workflow_id","transition.id"),"activated=3 and manager_id=".$user_id." and transition.workflow_id=workflow.id");
$row=  mysqli_fetch_array($result);
if($row==""){
    echo "<h2> No issues </h1>";
}
else{
    echo '<h2> Delayed Tasks </h2>';
    echo '<a href="showtask_1.php?wid='.$row['workflow_id'].'&tid='.$row['id'].'&reset=1">'.'wid='.$row['workflow_id'].' tid='.$row['id'].'</a><br>';
    while($row=mysqli_fetch_array($result)){
        echo '<a href="showtask_1.php?wid='.$row['workflow_id'].'&tid='.$row['id'].'&reset=1">'.'wid='.$row['workflow_id'].' tid='.$row['id'].'</a><br>';
    }
}

?>
