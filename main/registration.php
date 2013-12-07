<html lang="en">
<head>
<title>IIIT-H : Context Aware Workflow Management System</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="layout.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="js/cufon-yui.js" type="text/javascript"></script>
<script src="js/cufon-replace.js" type="text/javascript"></script>
<script src="js/Myriad_Pro_400.font.js" type="text/javascript"></script>
<script src="js/Myriad_Pro_600.font.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body id="page1">
<!-- header -->
<?php include('header.php')?>
<!-- content -->
        <div id="content">
            <div class="container">
                <div class="wrapper">
                    <div class="aside">
                        <div class="box">
                            <h3>Login Form</h3>
                            <form action="login_validation.php" id="login-form" method="post" name="login">
                                <fieldset>
                                    <div class="field"><label for="text">Username&nbsp;:&nbsp;</label><input type="text" class="text" name="username"/></div>
                                    <div class="field"><label for="text">Password&nbsp;&nbsp;:&nbsp;</label><input type="password" class="password" name="password"/></div>
                                    <div class="wrapper">
                                        <input type="submit" value="Log In" class="submit fleft" />
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                        <div class="mainContent">
				<div class="article">
                                    <?php
                                    if(isset($_GET['message']))
                                    {
                                        echo '<h5>'.$_GET['message'].'</h5>';
                                    }
                                    ?>
                                    <center><h1><font face="Arial" color="#FF2222">Register</font></h1></center>
                                    <form action="registration_validation.php" id="login-form1" method="post">
                                    <center><table><tbody>
                                        <tr>
                                        <div class="field"><td><label for="text">Email&nbsp;:&nbsp;</label></td><td><input type="text" class="text" name="email"/></td></div>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                        <div class="field"><td><label for="text">Username&nbsp;:&nbsp;</label></td><td><input type="text" class="text" name="username"/></td></div>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                        <div class="field"><td><label for="text">Password&nbsp;&nbsp;:&nbsp;</label></td><td><input type="password" class="password" name="password"/></td></div>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                        <div class="wrapper">
                                        <td></td>
                                        <td><input type="submit" value="Register" class="submit fleft" /></td>
                                        </div>
                                        </tr>
                                    </tbody></table></center>
                                    </form>
                                 </div>
                        </div>
                </div>
        </div>
</div>
<!-- footer -->
<?php include'footer.php'; ?>
<script type="text/javascript"> Cufon.now(); </script>
</body>
</html>