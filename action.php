<?
$op=$_GET['op'];
$comm="sqoop";
if($op=="export"){
	if(!isset($_GET['table'])||!(isset($_GET['directory']))){
		echo "Error: Invalid parameters";
		exit(0);
	}
	$comm.=" export --connect jdbc:mysql://localhost/login --username root --table ".$_GET['table'];
	
	$comm.=" --export-dir ".$_GET['directory']." ; echo $?";

	$output= shell_exec($comm);
	#echo $output;
	$val=$output[strlen($output)-2];
	//echo "---".$output[strlen($output)-1]."---"."---".$output[strlen($output)-2]."---"."---".$output[strlen($output)-3]."---"."<br/>";
	if($val=="0") {//;//shell_exec("echo $?");
		echo "<h2> Successfully Exported from HDFS to RDBMS:)</h2>";
	}
	else echo "<h2> Export from HDFS to RDBMS Failed :( </h2>";
}
else{
	//echo exec('whoami')." - - - -";return 0;
	if(!isset($_GET['table'])||!(isset($_GET['directory']))){
		echo "Error: Invalid parameters";
		exit(0);
	}
	$comm.=" import --connect jdbc:mysql://localhost/login --username root  --table ".$_GET['table'];
	if($_GET['directory'] != "default")
	{
	
		$comm.=" --target-dir ".$_GET['directory'];
	}
	$comm.= " --delete-target-dir ; echo $?";
	//$comm.= " ; echo $?";
	echo "command=".$comm."--<br/>";
	$output= shell_exec($comm." 2> err");
	echo $output;
	$val=$output[strlen($output)-2];
	//echo "---".$output[strlen($output)-1]."---"."---".$output[strlen($output)-2]."---"."---".$output[strlen($output)-3]."---"."<br/>";
	if($val=="0") {//;//shell_exec("echo $?");
		echo "<h2> Successfully Imported from RDBMS to HDFS:)</h2>";
	}
	else echo "<h2> Import from RDBMS to HDFS Failed :( </h2>";
}


?>