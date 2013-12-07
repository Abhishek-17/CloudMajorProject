<?php
if(isset($_GET['message']))
{
    echo $_GET['message'];
}
?>
<html>
<body>
<h1><font face="Arial" size="+1" color="#FF2222">LOGIN</font></h1>
<form action="gar.php" method="post">
<input type="submit" value="BACK"/>
</form>
<form action="login_validation.php" method="post">
    EMAIL : <input type="text" name="email" /><br><br>
    PASSWORD : <input type="password" name="password" /><br><br>
    <input type="submit" value="LOGIN" />
</form> 
</body>
</html>