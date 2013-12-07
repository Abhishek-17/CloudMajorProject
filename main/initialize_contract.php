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
                        
                        <?php include 'config.php'; ?>
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
                                        echo $_GET['message'];
                                    }
                                    if(isset($_POST['con_name']) && isset($_POST['con_manager_id']) && isset($_POST['con_deadline']))
                                    {
                                        $count=0;
                                        $sql="select org_name from organization";
                                        $result=mysqli_query($con,$sql);
                                        if(mysqli_num_fields($result))
                                        {
                                            while($row=  mysqli_fetch_array($result))
                                            {
                                                if(isset($_POST[$row['org_name']]))
                                                {
                                                    $count=$count+1;
                                                }
                                            }
                                        }
                                        if($count<2)
                                        {   
                                            echo 'count='.$count;
                                            //header('Location: initialize_contract.php?message="Atleast 2 organizations are needed to specify the contract."');
                                        }
                                        else if($_POST['con_name']!="" && $_POST['con_manager_id']!="" && $_POST['con_deadline']!="")
                                        {
                                            $org_id=array();
                                            $sql="select id,org_name from organization";
                                            $result=mysqli_query($con,$sql);
                                            if(mysqli_num_fields($result))
                                            {
                                                while($row=  mysqli_fetch_array($result))
                                                {
                                                    if(isset($_POST[$row['org_name']]))
                                                    {
                                                        array_push($org_id, $row['id']);
                                                    }
                                                }
                                            }
                                            $org=serialize($org_id);
                                            echo $org;
                                            //$sql="insert into contract (con_name,con_manager_id,con_deadline,org_id) values('$_POST[con_name]' ,$_POST[con_manager_id] ,$_POST[con_deadline] ,$org )";
                                            echo "Here1";
                                            insertintodb("contract", array($_POST['con_name'],$_POST['con_manager_id'],$_POST['con_deadline'],$org), 0);
                                            //echo $sql;
                                            $sql="update login set status='manager' where id=".$_POST['con_manager_id'];
                                            //echo $sql;
                                            $result=mysqli_query($con,$sql);
                                            header('Location: initialize.php?message="Contract added successfully."');
                                        }
                                        else
                                        {
                                            header('Location: initialize_contract.php?message="Some field was not filled"');
                                            //echo 'Some field was not filled '
                                            /*<br>
                                            <html>
                                            <body>
                                            <h1><font face="Arial" size="+1" color="#FF2222">ADD CONTRACT</font></h1>
                                            <form action="initialize_contract.php" method="post">
                                                <!--CONTRACT NAME : <input type="text" name="con_name"  /><br><br>
                                                CONTACT MANAGER : <input type="text" name="con_manager_id" /><br><br>
                                                CONTRACT DEADLINE: <input type="text" name="con_deadline" /><br><br>
                                                ORGANIZATION 1 ID:<input type="text" name="org1_id" /><br><br>
                                                ORGANIZATION 2 ID:<input type="text" name="org2_id" /><br><br>-->
                                                <input type="submit" value="BACK" />
                                             </form> 
                                             </body>
                                             </html>';*/
                                        }
                                    }
                                    else
                                    {

                                    echo '
                                        <h1><center><font face="Arial" color="#FF2222">ADD CONTRACT</font></center></h1>
                                        <form action="initialize_contract.php" id="login-form1" method="post">
                                            <center><table><tbody>
                                            <tr>
                                            <td>CONTRACT NAME  </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="con_name" /></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td>CONTACT MANAGER </td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<select name="con_manager_id"><br><br>';
                                            $sql="select id,username,email from login where status!='root'";
                                            $result=  mysqli_query($con, $sql);
                                            while($row2 = mysqli_fetch_array($result))
                                            {
                                                     echo '<option value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
                                            }
                                            echo '
                                            </select></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td>CONTRACT DEADLINE </td><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="con_deadline" /></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td>ORGANIZATIONS PARTICIPATING</td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;';
                                            $sql="select id,org_name from organization";
                                            //$result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
                                            $result=  mysqli_query($con, $sql);
                                            while($row = mysqli_fetch_array($result))
                                            {
                                               echo '<input type="checkbox" name="'.$row['org_name'].'">'.$row['org_name'].'</option>';
                                            }
                                            echo '
                                            </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td></td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<div class="wrapper"><input type="submit" value="Create" class="submit fleft" /></div></td>
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
                                    