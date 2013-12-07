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
                                    
                                    if(isset($_GET['message']))
                                        {
                                            echo '<h5>'.$_GET['message'].'</h5>';
                                        }
                                    ?>
                                    <center><h1><font face="Arial" color="#FF2222">APPLICATION</font></h1></center><br><br>
                                    <center><a href="initialize_organization.php" >ADD ORGANIZATION</a></center><br>
                                    <center><a href="delete_organization.php" >DELETE ORGANIZATION</a></center><br>
                                    <center><a href="edit_organization.php" >EDIT ORGANIZATION</a></center><br>
                                    <center><a href="initialize_contract.php" >ADD CONTRACT</a></center><br>
                                    <center><a href="delete_contract.php" >DELETE CONTRACT</a></center><br>
                                    <center><a href="edit_contract.php" >EDIT CONTRACT</a></center><br>
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