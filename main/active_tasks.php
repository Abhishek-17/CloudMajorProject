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

if (isset($_GET['message'])) {
    echo "<h3>" . $_GET['message'] . "</h3><br>";
}
if (isset($_GET['wid']) && isset($_GET['nodeid'])) {
    //for security
//$con=mysqli_connect("localhost","root","","workflows");
    $sql = "select id from login where email='" . $_SESSION['email'] . "'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    $sql = "select id,activated from transition where doer='" . $row['id'] . "' and activated>0 and activated<3 and id='" . $_GET['nodeid'] . "' and workflow_id='" . $_GET['wid'] . "'";
    //echo $sql."<br>";
    $result2 = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result2);
    if ($row == "") {
        echo "You are not authorized to do this task. Contact Your Manager";
        exit(0);
    }



    echo "<h2>Submit Data </h1>";
    echo '<form action="submit_data.php" method="post">';
    echo '<input type="hidden" name="wid" value="' . $_GET['wid'] . '"> ';
    echo '<input type="hidden" name="nodeid" value="' . $_GET['nodeid'] . '"';
    
    if ($row['activated'] == "2")
        extractform($_GET['wid'], $_GET['nodeid'], 1, 1);
    else
        extractform($_GET['wid'], $_GET['nodeid'], 0, 0);
    $result = selectfromdb("transition", array("functions"), "workflow_id='" . $_GET['wid'] . "' and id='" . $_GET['nodeid'] . "'");
    $list = unserialize(mysqli_fetch_array($result)['functions']);
    $list=$list['before'];//functions to be executed at the beginning
    foreach ($list as $pair) {//pair has parameter +functionlist + branch + fallback
        $parameters = array();
        $data = $pair["data"];
        $functionlist = $pair["functions"];
        $branch = $pair["branch"];
        $fallback = $pair["fallback"];
        foreach ($data as $param) {
            //echo $param[0] . " -------------------<br>";
            if ($param[0] === "data")
                array_push($parameters, $param[1]);
            else if ($param[0] === "node") {
                //print_r($param);echo "<br>";
                $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $param[2] . "' and transition_id='" . $param[1] . "' and workflow_id='" . $_GET['wid'] . "'");
                $row2 = mysqli_fetch_array($result2);
                //echo ":".$row2['chosen_val'].":<br>";
                
                array_push($parameters, $row2['chosen_val']);
            }
            //-------------------------------------
            else if($param[0]==="table"){
                $table=$param[1];
                $field=$param[2];
                $conditions=$param[3];
                $condition="";
                $ct=count($conditions);
                $i=0;
                foreach($conditions as $cond){
                    $i+=1;
                    $condition.=$cond[0];
                    $v=explode(",",$cond[1]);
                    if(count($v)==1){//data
                       $condition.="'" . $cond[1] . "' ";
                    if($i!=$ct){$condition.="and ";}
                    }
                    else{//from node
                         $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $v[1] . "' and transition_id='" . $v[0] . "' and workflow_id='" . $_GET['wid'] . "'");
                         $row2 = mysqli_fetch_array($result2);
                         $condition.="'".$row['chosen_val']."' ";
                      
                        if($i!=$ct){$condition.="and ";}
                    }
                }
                 array_push($parameters, mysqli_fetch_array(selectfromdb($table,array($field), $condition))[$field]);
            }
        }
      //  echo "here--------------::<br>";exit(0);   
         if(count($functionlist)==0){
            $a="";
            foreach($parameter as $p)$a.=$p;
            $parameter=$a;
            
        }
        else
        foreach ($functionlist as $function) {
            $parameters = callfunc($function, $parameters);
            //echo "here--------------:" . $function . ":<br>";exit(0);
            //----------------------------------
            //if there is an error and a fallback condition has been added
            //print_r($fallback);
            ///echo "-------------------------------------------------: ".$fallback.":<br>";
            // exit(0);
        }
       
        foreach ($fallback as $br) {
          //  e
           // echo "here--------------:" . $parameters . ":<br>   ";
            $validation = $br['validation'];
            $outnodes = $br['outnodes'];
            $fl = "1";
            foreach ($validation as $condition) {

                $property = $condition[0];
                $val = $condition[1];
                $operator = $condition[2];
                $fl = checkcondition($parameters, $property, $val, $operator);
                //originalvalue  , property,value tobe compared with, operator
                if ($fl != "1")
                    break; //conditions dont hold
            }
            if ($fl == "1") {
                fallback($_GET['wid'], $_GET['nodeid'], array(), $outnodes); //visited array,outnode array where we havre to move
               // exit(0);
                if ($user_id != 0) {
                    if ($user_status == "manager") {
                        header('Location: upload_file.php?message="Oophs! There was some Error!"');
                    }
                    else
                        header('Location: active_tasks.php');
                }
                else
                    header('Location: login.php');
                exit(0);
            }

            //----------------------------------
        }
    }
    echo ' <br/><input type="submit" value="submit" /></form>';
} else {
    echo "<h2>Active Tasks </h1>";
    $con = mysqli_connect("localhost", "root", "", "workflows");
    if (mysqli_connect_errno($con)) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }


    //session_start();
    $sql = "select id from login where email='" . $_SESSION['email'] . "'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);

    $sql = "select workflow_id,id,time_limit,start_time,activated from transition where doer=" . $row['id'] . " and activated>0 and activated<3";
    //echo $row['id']." here<br>";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_array($result)) {

            $timeleft = $row['time_limit'] * 60 - (time() - $row['start_time']);
            //echo $row['start_time']."<br>";
            if ($timeleft > 0) {
                //$timeleft=0;
                $a = $timeleft;
                $unit = "minutes";
                $timeleft = round($timeleft / 60); //in minutes
                if ($timeleft < 2) {
                    $timeleft = $a;
                    $unit = "seconds";
                }
                echo "WFid : " . $row['workflow_id'] . " and Taskid : " . $row['id'] . "<br>";
                  $status="Pending";
                  if($row['activated']=='2')$status="Done!";
                echo '
            <html>
            <body>

            <form action="active_tasks.php" method="GET" style="float:left;clear:right;">
            <input type="hidden" name="wid" value="' . $row['workflow_id'] . '" /> 
            <input type="hidden" name="nodeid" value="' . $row['id'] . '" /> 
            <input type="submit" value="Perform Activity" >
            </form>
            <div style="margin-left:10%">Status :' . $status .'</div>
            <div style="margin-left:10%">Timeleft :' . $timeleft . " " . $unit . '</div><br>
            ';
                echo'
            </body>
            </html>
            ';
            } else {
               // echo"dfcsd";
               // exit(0);
                if ($row['activated'] == "2")
                    $sql = "update transition set activated=4 where " . "id=" . $row['id'] . " and workflow_id=" . $row['workflow_id'];
                else
                    $sql = "update transition set activated=3,doer=0 where " . "id=" . $row['id'] . " and workflow_id=" . $row['workflow_id'];
                mysqli_query($con, $sql);
            }
        }
    } else {
        echo "No task assigned till now.";
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