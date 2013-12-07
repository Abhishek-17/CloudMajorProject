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

if(isset($_POST['org']))
{
    $sql='delete from organization where org_name="'.$_POST['org'].'"';
    $result=mysqli_query($con,$sql);
    header('Location: initialize.php?message="Organization successfully deleted."');
}
else
{
    echo '
    <form action="delete_organization.php" method="post" id="login-form1">
    <center><table><tbody>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>
    <select name="org">
    ';
    $sql="select org_name from organization";
    $result=mysqli_query($con,$sql);
    while($row=  mysqli_fetch_array($result))
    {
        echo '
            <option value="'.$row['org_name'].'">'.$row['org_name'].'</option>
            ';
    }
    echo '
    </select>
    </td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" value="Submit" class="submit fright">
    </td></tr></tbody></table></center>
    </form>
    ';
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