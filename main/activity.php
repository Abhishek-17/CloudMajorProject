<?php
require_once 'config.php';
$con=mysqli_connect("localhost","root","","workflows");

if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if(isset($_GET['wid'])&&isset($_GET['tid']))
{
    $wfid=$_GET['wid'];
    $taskid=$_GET['tid'];
}
else
{
    header('Location: user.php?message="Some error occured!!!"<br>');
}
if(isset($_GET['message']))
{
    echo $_GET['message'];
}



//echo $_GET['wid'];
//extractform($_GET['wid'],$_GET['tid']);

echo "<h3>Workflow number: $wfid and Task number: $taskid</h3><br/>" ;
$query="select type,name,val from inputs where workflow_id=$wfid and transition_id=$taskid";
$result = mysqli_query($con,$query);
//$divid='w'.$wfid.'t'.$taskid.'div';
//echo "<div id='$divid' style='display:none'>";
$res='<form action="activity_perform.php" method="post"><br>';
while($row = mysqli_fetch_array($result))
{
    $res.=$row['name']." : <br>";
    $arr = unserialize($row['val']);
    foreach ($arr as $key => $val)
    {
        $res.='<input type="' . $row['type'] . '" name="' . $row['name'] . '" value="' . $key . '" >' . $val . '<br>';
    }
    /*$res.="<br><br>";
    echo "<br/>";
    echo "HTML code is <br/>";
    echo '<pre>' . htmlspecialchars ( $res ) . '</pre>';*/
}
$res.='<input type="hidden" name="wid" value="'.$_GET['wid'].'" /> 
       <input type="hidden" name="tid" value="'.$_GET['tid'].'" /> 
       <input type="submit" value="Submit"><br>';
$res.='</form>';
echo '<pre>' . htmlspecialchars ( $res ) . '</pre>';
echo $res;

/*echo "</div>";
$p="'";
$p.=$divid."'";
echo '<button onclick="toggleshow('.$p.');return false;">Deatils.. </button>';
echo '
    <html>
    <body>

    <form action="activity_perform.php" method="GET">
    <input type="hidden" name="wid" value="'.$_GET['wid'].'" /> 
    <input type="hidden" name="tid" value="'.$_GET['tid'].'" /> 
    <input type="submit" value="Submit">
    </form>

    </body>
    </html>
    ';*/

?>
