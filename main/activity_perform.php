<?php
require_once 'config.php';
$con=mysqli_connect("localhost","root","","workflows");

if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
if(isset($_POST['wid'])&&isset($_POST['tid']))
{
    $wfid=$_POST['wid'];
    $taskid=$_POST['tid'];
}
else
{
    header('Location: user.php?message="Some error occured!!!"');
}

echo "<h3>Workflow number: $wfid and Task number: $taskid</h3><br/>" ;
/*$query="select type,name,val from inputs where workflow_id=$wfid and transition_id=$taskid";
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result))
{
    $temp=$row['name'];
    echo $_POST["$temp"];
}*/

$query="select name,validation from inputs where workflow_id=$wfid and transition_id=$taskid";
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result))
{
    $x=$_POST[$row['name']];
    //echo "Value to be checked : ".$x.'<br>';
    $ans=1;
    $arr = unserialize($row['validation']);
    foreach ($arr as $key => $val)
    {   
        if(count($val)!=0)
        {
            foreach($val as $key2 => $val2)
            {
                //echo 'Property to be checked : '.$key2.'<br>';
                //echo 'Value to be checked against: '.$val2[0].'<br>';
                //echo 'Operator to be used : '.$val2[1].'<br>';
                $ret=checkcondition($x, $key2, $val2[0], $val2[1]);
                echo $ret;
                if($ret!="1")
                {
                    $url="Location: activity.php?wid=$wfid&tid=$taskid&message=$ret";
                    header($url);
                    exit;
                }
            }
        }
    }
    //echo '<br><br>';
    echo 'Input yet to be stored';
    
    $update="UPDATE inputs SET chosen_val='".$_POST[$row['name']]."' WHERE transition_id='".$_POST['nodeid']."' AND workflow_id=". $_POST['wid']." AND name='".$row['name']."'";
       // echo $update.'<br>';
         mysqli_query($con,$update);
    
    /*
     * Write your storing inputs code here
     */
}

?>
