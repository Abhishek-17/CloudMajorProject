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
			<?php include 'sidebar.php'; ?>
                        <div class="mainContent">
				<div class="article">
<?php //echo $_SESSION['email'];
require 'config.php';
if(isset($_GET['message']))
{
    echo '<h2>'.$_GET['message'].'</h2><br>';
}
echo '
<a href="issues.php" >Manage Issues</a>&nbsp;&nbsp;&nbsp;<br/>
<form action="upload_file_validation.php" method="post"
enctype="multipart/form-data">
<label for="file">Upload XML File:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>
<form action="upload_file_validation.php" method="post"
enctype="multipart/form-data">
<label for="file">Upload Function as XML File:</label>
<input type="file" name="file" id="file"><br>
<input type="hidden" name="func"value=1>
<input type="submit" name="submit" value="Submit">
</form>
<a href="create_instance.php">Create instances of workflow</a>
    <h3>Currently available Workflows</h3>
    ';
    $result=selectfromdb("workflow",array('id','name'),"manager_id=".$user_id);
    echo '<table border="1"><tr><td>id</td><td>workflow name</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td>';
        
        echo '<td><a href="showtask_1.php?wid='.$row['id'].'">'.$row['name'].'</a><br/></td>';
        echo '</tr>';
    }
     echo '</table>';
    

     echo '<h3>Currently available Functions as Workflow</h3>';
    $result=selectfromdb("function_workflow",array('id','name'),"manager_id=".$user_id);
    echo '<table border="1"><tr><td>id</td><td>Function name</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td>';
        
        echo '<td><a href="function_list.php?fwid='.$row['id'].'">'.$row['name'].'</a><br/></td>';
        echo '</tr>';
    }
     echo '</table>';
    

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
                               