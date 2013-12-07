<a href="upload_file.php">Go to upload_file.php</a><br>
<?php
require 'config.php';
if(isset($_POST['idlist'])){
    $ids=explode(";",$_POST['idlist']);
    $nodes=explode(";",$_POST['nodelist']);
    $l=count($ids);
    $i=0;
    while($i<$l){
      //  echo $ids[$i]." ".$nodes[$i].'<br>';
        
        if(isset($_POST[$ids[$i]])){
            $query="select activated,doer from transition"." WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
            if (!$result=mysqli_query($con, $query)) {
            die('Error ' . mysqli_error($con));
         }
            $row=  mysqli_fetch_array($result);
            if($row['activated']=="2"){
              //  echo $row['doer']."<br>";
               // echo $_POST[$ids[$i]];
               // exit(0);
                 if($row['doer']!=$_POST[$ids[$i]])fallforward($_POST['wid'],$nodes[$i]);
            
            }
            $update="UPDATE transition SET doer=".$_POST[$ids[$i]]."
                WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
            mysqli_query($con,$update);
            $update="UPDATE transition SET time_limit=".$_POST['time_limit']."
                WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
            mysqli_query($con,$update);
            if(isset($_POST['reset'])){
                $update="UPDATE transition SET activated=1
              WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
            mysqli_query($con,$update);}
            $update="UPDATE transition SET start_time='".time()."' 
              WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
          //  echo $update;
           // exit(0);
            if (!$result=mysqli_query($con, $update)) {
            die('Error ' . mysqli_error($con));
         }
            
            echo $update.'<br/>';
            logg("added doer wid=".$_POST['wid']." nodeid=".$_POST['nodeid']);
        }
        $i+=1;
    }
    header( 'Location: activity_list.php?wid='.$_POST['wid'] ) ;
}
else echo "error: idlist is empty in adddoer.php<br/>";
?>