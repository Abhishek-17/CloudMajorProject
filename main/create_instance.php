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

require_once 'config.php';
if (isset($_GET['message'])) {
    echo "<h3>" . $_GET['message'] . '</h3>';
}
if (!isset($_POST['wid'])) {

    $result = selectfromdb("workflow", array('id', 'name'), "manager_id=" . $user_id);
    echo '<form method="post" action="create_instance.php">start_date:<input type="text" name="s_date" value=""><br>end_date:<input type="text" name="e_date" value=""><br/>
        No of instances(max 10):<input type="number" name="count" min="1" max="10"></br>
        <br><table border="1"><tr><td>id</td><td>workflow name</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>' . $row['id'] . '</td>';

        echo '<td><input type="checkbox" name="wid[]" value="' . $row['id'] . '"> ' . $row['name'] . '</a><br/></td>';
        echo '</tr>';
    }
    echo '</table><br><input type="submit" value="create_instance"> <form>';
} else if (isset($_POST['wid'])) {
    if ($_POST['count'] == "")
        $_POST['count'] = 1;
    $sdate = $_POST['s_date'];
    $edate = $_POST['e_date'];
    if ($sdate == "")
        $sdate = date("Y/m/d");
    if ($edate == "")
        $edate = date("Y/m/d");
    $widarray = $_POST['wid'];
    $gar = array();
    $gar = serialize($gar);

    foreach ($_POST['wid'] as $wid) {
        $ct = 0;
        
        while ($ct < $_POST['count']) {
            $result = selectfromdb("workflow", array("*"), "manager_id=" . $user_id . " and id=" . $wid);
            $row = mysqli_fetch_array($result);
            if ($row == "")
                continue;
            $row['start_date'] = $sdate;
            $row['end_date'] = $edate;
            $a = array();
            $i = 0;
            foreach ($row as $v) {
                if ($i % 2 && $i != 1)
                    array_push($a, $v);
                $i+=1;
            }
            insertintodb("workflow", $a, 0);
            $mwid = getmaxidfromdb("workflow");
            $result = selectfromdb("transition", array("*"), "workflow_id=" . $wid);
            while ($row = mysqli_fetch_array($result)) {
                $row['workflow_id'] = $mwid;
                $row['done_innodes'] = $gar;
                $row['path'] = $gar;
                $row['activated'] = ($row['type'] == "start") ? 1 : 0;
                $a = array();
                $i = 0;
                foreach ($row as $v) {
                    if ($i % 2)
                        array_push($a, $v);
                    $i+=1;
                }
                insertintodb("transition", $a, 1);
            }
            $result = selectfromdb("inputs", array("*"), "workflow_id=" . $wid);
            while ($row = mysqli_fetch_array($result)) {
                $row['workflow_id'] = $mwid;
                $row['chosen_val'] = "";

                $a = array();
                $i = 0;
                foreach ($row as $v) {
                    if ($i % 2 && $i != 1)
                        array_push($a, $v);
                    $i+=1;
                }
                insertintodb("inputs", $a, 0);
            }
            logg("created instance of wid:" . $wid . "with new wid=" . $mwid);
            $ct+=1;
        }
    }


    header('Location: create_instance.php?message=Instance created!!');
}
else if (isset($_POST['wid[]'])) {
    exit(0);
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
