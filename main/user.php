<?php

$con=mysqli_connect("localhost","root","","workflows");
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
if(isset($_GET['message']))
{
    echo $_GET['message'];
}

session_start();
$sql="select id from login where email='".$_SESSION['email']."'";
$result=  mysqli_query($con, $sql);
$row=  mysqli_fetch_array($result);

$sql="select workflow_id,id from transition where doer=".$row['id']." and activated=1";
$result=  mysqli_query($con, $sql);
if(mysqli_num_rows($result))
{
    while($row=mysqli_fetch_array($result))
    {
        echo "WFid : ".$row['workflow_id']." and Taskid : ".$row['id'];

        echo '
            <html>
            <body>

            <form action="activity.php" method="GET">
            <input type="hidden" name="wid" value="'.$row['workflow_id'].'" /> 
            <input type="hidden" name="tid" value="'.$row['id'].'" /> 
            <input type="submit" value="Perform Activity">
            </form>

            </body>
            </html>
        ';
    }
    
}
else
{
    echo "No task assigned till now.";
}

?>
