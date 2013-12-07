<?php

$con=mysqli_connect("localhost","root","","hadoop");
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$sqlcheck = "select * from login where email='" . $_POST['email'] . "'" . " and password='" . $_POST['password'] . "'";
echo $sqlcheck;
$result = mysqli_query($con,$sqlcheck);
$row = mysqli_fetch_array($result);
if($row=="")
{
    header( 'Location: login.php?message="LOGIN FAILED : Incorrect Email/Password Combination"' ) ;
    mysql_close($con);
}
else
{
    session_start();
    $_SESSION['email']=$_POST['email'];
    $_SESSION['username']=$row['username'];
    //$_SESSION['password']=$_POST['password'];
    header( 'Location: index.php' ) ;
    mysql_close($con);
}
?>
