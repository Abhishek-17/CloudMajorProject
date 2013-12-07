<?php

$con=mysqli_connect("localhost","root","","hadoop");
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sqlcheck = "select * from login where email='" . $_POST['email'] ."'";
//echo $sqlcheck;
$result=mysqli_query($con,$sqlcheck);
$row = mysqli_fetch_array($result);
//echo $row;
if($row=="")
{
    $sql="insert into login(username,password,email) values('$_POST[username]','$_POST[password]','$_POST[email]')" ;
    mysqli_query($con,$sql);
    header( 'Location: login.php?message="you have been successfully registered!"' ) ;
    mysqli_close($con);
}
else
{
    header( 'Location: registration.php?message="Email already registered."' ) ;
    mysqli_close($con);
}

?>