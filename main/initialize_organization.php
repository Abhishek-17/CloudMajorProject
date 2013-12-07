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
			<?php include 'sidebar.php';
                        if($stat!=0)
                        {
                            header('Location: index.php?err="You need to login."');
                        }?>
                        
                        <div class="mainContent">
				<div class="article">

<?php
$con=mysqli_connect("localhost","root","","workflows");
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
if(isset($_GET['message']))
{
    echo '<h5>'.$_GET['message'].'</h5>';
}
if(isset($_POST['org_name']) && isset($_POST['org_address']) && isset($_POST['org_payment']))
{
    if($_POST['org_name']!="" && $_POST['org_address']!="" && $_POST['org_payment']!="")
    {
        $sql="select id from organization where org_name='".$_POST['org_name']."'";
        $result=mysqli_query($con,$sql);
        if(mysqli_num_rows($result))
        {
            header('Location: initialize_organization.php?message="Organization already exists."');
        }
        else
        {
            $sql="insert into organization(org_name,org_address,org_payment) values('$_POST[org_name]' ,'$_POST[org_address]' ,$_POST[org_payment] )";
            //echo $sql;
            $result=mysqli_query($con,$sql);

            $sql="select id from organization where org_name='".$_POST['org_name']."' and org_address='".$_POST['org_address']."'";
            //echo $sql;
            $result=mysqli_query($con,$sql);
            $row = mysqli_fetch_array($result);
            $id=$row['id'];

            $sql="select username,email from login where email!='root' and org_id=0";
            $result=mysqli_query($con,$sql);
            while($row = mysqli_fetch_array($result))
            {
                //echo $_POST[$row['email']];
                if(isset($_POST[$row['email']]))
                {
                    $sql="update login set org_id=".$id." where email='".$row['email']."'";
                    $result2=mysqli_query($con,$sql);
                }
            }

            header('Location: initialize.php');
        }
    }
    else
    {
        header('Location: initialize_organization.php?message="Some field was not filled"');
        /*echo 'Some field was not filled <br>
        <html>
        <body>
        <h1><font face="Arial" size="+1" color="#FF2222">ADD ORGANIZATION</font></h1>
        <form action="initialize_organization.php" method="post">
            ORGANIZATION NAME : <input type="text" name="org_name" value="'.$_POST['org_name'].'" /><br><br>
            ORGANIZATION ADDRESS : <input type="text" name="org_address" value="'.$_POST['org_address'].'" /><br><br>
            PAYMENT DETAILS: <input type="text" name="org_payment" value="'.$_POST['org_payment'].'" /><br><br>
            <input type="submit" value="CREATE" />
        </form> 
        </body>
        </html>';*/
    }
}
else
{

    
    $sql="select username,email from login where email!='root' and org_id=0";
    $result=mysqli_query($con,$sql);
    
    echo '
    <h1><center><font face="Arial" color="#FF2222">ADD ORGANIZATION</font></center></h1>
    <form action="initialize_organization.php" id="login-form1" method="post">
        <center><table><tbody>
        <tr>
        <td>ORGANIZATION NAME  </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_name" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
        <td>ORGANIZATION ADDRESS  </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_address" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
        <td>PAYMENT DETAILS </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_payment" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
        <td>USERS TO BE ADDED </td>
        <td>
    ';
    if(mysqli_num_rows($result))
    {
        while($row = mysqli_fetch_array($result))
        {
            $x='&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$row['email'].'" > Name: '.$row['username'].'  Email: '.$row['email'].'<br>';
            //echo '<pre>'.htmlspecialchars($x).'</pre>';
            echo $x;
        }
    }
    else
    {
        echo 'No users available.';
    }
    
    echo    '
        </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    <tr>    
    <td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;<div class="wrapper"><input type="submit" value="Create" class="submit fleft" /></div></td>
    </tr>
    </tbody></table></center>
    </form>';
}
?>

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
                                 