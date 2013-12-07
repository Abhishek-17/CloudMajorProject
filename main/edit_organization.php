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
if(isset($_GET['org_id']))
{
    if(isset($_POST['org_name']) && isset($_POST['org_address']) && isset($_POST['org_payment']))
    {
        echo "Reached here 1";
        if($_POST['org_name']!="" && $_POST['org_address']!="" && $_POST['org_payment']!="")
        {
            $sql="update organization set org_name='".$_POST['org_name']."', org_address='".$_POST['org_address']."', org_payment=".$_POST['org_payment'].' where id='.$_GET['org_id'];
            $result=mysqli_query($con,$sql);

            $sql="select org_id,username,email from login where email!='root'";
            $result=mysqli_query($con,$sql);
            while($row = mysqli_fetch_array($result))
            {
                if(isset($_POST[$row['email']]))
                {
                    $sql="update login set org_id=".$_GET['org_id']." where email='".$row['email']."'";
                    $result2=mysqli_query($con,$sql);
                }
                else if($row['org_id']==$_GET['org_id'])
                {
                    $sql="update login set org_id=0 where email='".$row['email']."'";
                    $result2=mysqli_query($con,$sql);
                }
            }

            header('Location: edit_organization.php?message="Organization successfully edited."');
        }
        else
        {
            header('Location: edit_organization.php?message="Some field was not filled"&org_id='.$_GET['org_id']);
        }
    }
    else
    {
        echo "Reached here 2";
        $sql="select org_id,username,email from login where org_id=0 or org_id=".$_GET['org_id']." and email!='root'";
        $result=mysqli_query($con,$sql);
        $sql2="select org_name,org_address,org_payment from organization where id=".$_GET['org_id']."";
        $result2=mysqli_query($con,$sql2);
        $row2=  mysqli_fetch_array($result2);

        echo '
        <h1><center><font face="Arial" color="#FF2222">ADD ORGANIZATION</font></center></h1>
        <form action="edit_organization.php?org_id='.$_GET['org_id'].'" id="login-form1" method="post">
            <center><table><tbody>
            <tr>
            <td>ORGANIZATION NAME  </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_name" value="'.$row2['org_name'].'" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            <td>ORGANIZATION ADDRESS  </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_address" value="'.$row2['org_address'].'" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            <td>PAYMENT DETAILS </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="org_payment" value='.$row2['org_payment'].' /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            <td>USERS TO BE ADDED </td>
            <td>
        ';
        while($row = mysqli_fetch_array($result))
        {
            if($row['org_id']==$_GET['org_id'])
            {
                $x='&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$row['email'].'" checked> Name: '.$row['username'].'  Email: '.$row['email'].'<br>';
                echo $x;
            }
            else
            {
                $x='&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$row['email'].'"> Name: '.$row['username'].'  Email: '.$row['email'].'<br>';
                echo $x;
            }
        }

        echo    '
            </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <tr>    
        <td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;<div class="wrapper"><input type="submit" value="Update" class="submit fleft" /></div></td>
        </tr>
        </tbody></table></center>
        </form>';
    }
}
else
{
        $sql="select id,org_name from organization";
        $result=mysqli_query($con,$sql);
        if(mysqli_num_rows($result))
        {
            while($row=  mysqli_fetch_array($result))
            {
                echo '<center><a href="edit_organization.php?org_id='.$row['id'].'" >EDIT&nbsp;&nbsp;&nbsp;"'.$row['org_name'].'"</a></center><br>';
            }
        }
        else
        {
            echo '<center><h3>No organization exists</h3></center>';
        }
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
                                 