<a href="upload_file.php">Go to upload_file.php</a><br>
<?php
require_once 'config.php';
if(isset($_GET['wid']) && isset($_GET['tid'])){
    $wid=$_GET['wid'];
    $tid=$_GET['tid'];
    if(isset($_GET['func'])){
    
    echo '<a href="modify.php?wid='.$_GET['wid'].'&nodeid='.$_GET['tid'].'&func=1">Modify Task</a><br>';
    extractform($wid,$tid,0,0,1);
    exit(0);
    }
    else extractform($wid,$tid,0,0,0);
    $sql="select type from transition where workflow_id=".$wid." and id=".$tid;
    $result=  mysqli_query($con, $sql);
    $row= mysqli_fetch_array($result);
    echo '<a href="modify.php?wid='.$_GET['wid'].'&nodeid='.$_GET['tid'].'">Modify Task</a><br>';
    echo '<form action="adddoer.php" method="post"><input type="hidden" name="wid" value='.$wid.'>';
    
    $nodelist='';
    $idlist='';
    
        
       // extractform($wid,$tid);
        //echo "here";exit(0);
        echo "<br>Nodetype:".$row['type']."<br>";
        if(isset($_GET['reset'])){
        echo '<input type="hidden" name="reset" value=1>';
        }
         echo '<br>Set time_limit value(in minutes):<input type="text" name="time_limit" value=10>';
        //generate list of users/doers
        
        echo "<br>Add the doer:";
        echo '<select name="n'.$tid.'"><optgroup label="Default"><option value="0">none</option</optgroup><br><br>';
        $idlist.="n".$tid.";";
        $nodelist.=$tid.";";
        $result1=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
        while($row1 = mysqli_fetch_array($result1))
        {
             echo '<optgroup label="'.$row1['org_name'].'">';
             $result2=selectfromdb("login",array("id","username","email"),"org_id=".$row1['id']);
             while($row2 = mysqli_fetch_array($result2)){
                 echo '<option value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
             }
             echo '</optgroup>';
        }
        echo '
        </select></br>';
    //}
    echo '<input type="hidden" name="idlist" value="'.$idlist.'">';
    echo '<input type="hidden" name="nodelist" value="'.$nodelist.'">';
    echo '
        
    <input type="submit" value="submit" />
    </form>';

}
?>