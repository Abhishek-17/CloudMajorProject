<?php

session_start();
if (isset($_SESSION['email'])) {
    echo '
	<div class="aside">
            <div class="box">
		<h3> Welcome </h3>
                    Hi, &nbsp;&nbsp;' . $_SESSION['username'] . '
                    <br>
                    <br/>
                    Email : ' .$_SESSION['email'].'
                    <br/>
                    <br/>
                    <form action="logout.php" id="login-form">
                        <input type="submit" value="Logout" class="submit fright" />
                    </form>
            </div>
        </div>';
    $con = mysqli_connect("localhost", "root", "", "workflows");

    if (mysqli_connect_errno($con)) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $sql = "select id,status from login where email='" . $_SESSION['email'] . "'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    $user_id = $row['id'];
    $user_status = $row['status'];
    if($user_status=="root")
        $stat=0;
    else if($user_status=="manager")
        $stat=1;
    else
        $stat=2;
}

else
{

echo '<div class="aside">
	<div class="box">
		<h3>Login Form</h3>';
if(isset($_GET["err"]))
{
	echo "<div style='color:#000000;'><b>" . $_GET["err"]."</b></div><br><br>";		
}

echo '<form action="login_validation.php" id="login-form" method="post" name="login">
      <fieldset>
	<div class="field"><label for="text">Email &nbsp;:<br></label><input type="text" class="text" name="email"/></div>
	<div class="field"><label for="text">Password&nbsp;&nbsp;:<br></label><input type="password" class="password" name="password"/></div>
        <div class="wrapper">
        	<input type="submit" value="Log In" class="submit fleft" /></form>
        	<form action="registration.php" name="submit">
        		<div class="fright"><input type="submit" value="Register" class="submit fright" /></div>
        </div>
      </fieldset>
      </form>
    </div>
</div>';
}

?>
