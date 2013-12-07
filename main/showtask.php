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
                                        <?php
                                        require 'config.php';
                                        if(isset($_GET['wid'])){
                                            $wid=$_GET['wid'];
                                            echo '<center><h1>Workflow Number : '.$_GET['wid'].'</h1></center>';
                                            $result=  selectfromdb("transition", array('id','type','doer'), "workflow_id=".$_GET['wid']);
                                            echo '<form action="adddoer.php" id="login-form1" method="post"><input type="hidden" name="wid" value='.$wid.'>';
                                            echo '<center><table><tbody>';
                                            echo '<tr><td align="center">Task name&nbsp;&nbsp;</td><td></td><td align="center">Doer</td>';
                                            echo '</tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                            $nodelist='';
                                            $idlist='';
                                            while($node=  mysqli_fetch_array($result)){
                                              if($node['type']!="end")
                                             {
                                                echo '<tr><td align="center"> TASK '.$node['id'].' &nbsp;&nbsp;</td><td>';
                                                //extractform($_GET['wid'],$node['id']);
                                                //generate list of users/doers
                                                echo "</td><td>";
                                                echo '<select name="n'.$node['id'].'"><optgroup label="Default"><option value="0">none</option</optgroup><br><br>';
                                                $idlist.="n".$node['id'].";";
                                                $nodelist.=$node['id'].";";
                                                $result1=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
                                                while($row1 = mysqli_fetch_array($result1))
                                                {
                                                     echo '<optgroup label="'.$row1['org_name'].'">';
                                                     $result2=selectfromdb("login",array("id","username","email"),"org_id=".$row1['id']);
                                                     while($row2 = mysqli_fetch_array($result2)){
                                                         if($node['doer']==$row2['id'])
                                                         {
                                                            echo '<option selected="selected" value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
                                                         }
                                                         else
                                                         {
                                                             echo '<option value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
                                                         }
                                                         
                                                     }
                                                     echo '</optgroup>';
                                                }
                                               echo '</select></td>';
                                               echo '</tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                             }
                                            }
                                            echo '<input type="hidden" name="idlist" value="'.$idlist.'">';
                                            echo '<input type="hidden" name="nodelist" value="'.$nodelist.'">';
                                            echo '
                                             <tr><td></td><td>   
                                             <div class="wrapper"><input type="submit" value="Submit" class="submit fleft" /></div>
                                             </td></tr>
                                            </form></tbody></table></center>';

                                        }
                                        else
                                        {
                                            echo '<center><h1>Some error occured</h1></center>';
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