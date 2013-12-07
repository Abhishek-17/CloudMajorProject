<h2 align='center'> Sqooper</h2>
<?
require ("encrypt.php");

$machine="localhost";
$database_name="hadoop";
$db_username="root";
$password="";
$TABLE="login";

function codegen($str="",$table=""){
	/*codegen:
			generate import-export code without
			performing actual import/export, modify the 
			generated java file, compile it to jar, 
			give it to sqoop.
		*/
	global $TABLE,$machine,$password,$db_username,$database_name;
	$codegen="";
	if($str==""){
		unlink($TABLE.".java");
		unlink($TABLE.".jar");
		unlink($TABLE.".class");
		$codegen= "sqoop codegen --connect jdbc:mysql://".$machine."/".$database_name." --username ".$db_username." --password '".$password."' --table ".$TABLE;
		
		if($_POST['op']=="import"){
			if($_POST['field_delim']!="")$codegen.=" --fields-terminated-by "."'" .$_POST['field_delim']."'";
			if($_POST['line_delim']!="")$codegen.=" --lines-terminated-by "."'" .$_POST['line_delim']."'";
			if(isset($_POST['enclosed'])){
					if($_POST['enclosingchar']=='"')$_POST['enclosingchar']='\"';
					$codegen.=" ".$_POST['enclosed']." '".$_POST['enclosingchar']."'";
			}

		}
	}
	else {
		unlink($table.".java");
		unlink($table.".jar");
		unlink($table.".class");
		$codegen=$str;

	}
	$codegen.= " 2> err ; echo $?";
	echo "codegen command=".$codegen."<br/>";
	$output= shell_exec($codegen);

	if($output[strlen($output)-2]!="0"){echo "<br>??????????????Codegen error<br>";echo $output;exit(0);}
}
function compile($table=""){
	global $TABLE;
	if($table=="")$table=$TABLE;
	$compile="/usr/lib/jvm/jdk1.7.0_45/bin/javac -classpath /usr/lib/sqoop/sqoop-1.4.3-cdh4.4.0.jar:/usr/lib/hadoop/hadoop-common.jar:/usr/lib/hadoop-0.20-mapreduce/hadoop-core.jar:CipherUtils.jar:commons-codec-1.5.jar  ".$table.".java ; /usr/lib/jvm/jdk1.7.0_45/bin/jar -cf ".$table.".jar ".$table.".class";
	$compile.= " ; echo $?";
	echo "compile command=".$compile."<br/>";
	$output= shell_exec($compile);
	if($output[strlen($output)-2]!="0"){echo "<br>??????????????Jar compilation error<br>";echo $output;exit(0);}
}

function getfile_name(){//uploaad  file
	if (($_FILES["file"]["type"] == "text/plain") && ($_FILES["file"]["size"] < 20000))
 	 {
  		if ($_FILES["file"]["error"] > 0)
	  	{
	  	 	 echo "Error: " . $_FILES["file"]["error"] . "<br>";
	   	 	exit(0);
	    }
	  	else
	   	{
		  /*  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
	    	echo "string"; "Type: " . $_FILES["file"]["type"] . "<br>";
	    	echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
	    	echo "Stored in: " . $_FILES["file"]["tmp_name"];*/
	    	return $_FILES["file"]["tmp_name"];
	    }
	 }
	
	else
  	{
  		echo "Invalid file";
  		exit(0);
  	}

}

function use_file(){//reads config file...
	$filename=".param";
	$op="";
	if($_POST['config_option']=="file"){
		file_put_contents($filename,file_get_contents(getfile_name())); //put contents in .param file
	}
	else{
		file_put_contents($filename,$_POST['configtext']);	
	}

	$arr=explode("\n",file_get_contents($filename));
	foreach ($arr as $key) {
		if(trim($key)[0]!="#"){$op=trim($key);break;}//get operation name from config file: import or  export, # ignore comments
	}

	$fl=0;
	$str="";
	$array="";
	$key="";
	//check whether to encrypt/decrypt or not and if yes, remove those lines from sqoop param file (we support it no sqoop :P)
	$i=0;
	while($i<count($arr)) {
		$line2=$arr[$i];
		$line=trim($line2);
		if($fl==1&&$line[0]!="#"){
			$array=explode(",",$line); //comma seperated values e,g.: username,id
			$fl=2;
			
		}
		else if($line=="--decrypt-columns"){
			if($op=="export"&&$fl==0)$fl=1;
		}
		else if($line=="--encrypt-columns"){
			if($op=="import"&&$fl==0)$fl=1;
		}
		else if($line=="--key"){
			$key=trim($arr[++$i]);
		}
		else $str.=$line2."\n";
		$i++;
	}
	echo "str=".$str."<br/>";
	echo "key=" .$key.".<br/> array::";
	print_r($array);
	

	file_put_contents($filename,$str);
	$exp="";
	if($fl==2){//encryption or decryption is to b done
		$s="sqoop codegen";
		$table="";
		$i=0;
		while($i<count($arr)){//do comment handling
			$line=trim($arr[$i]);
			echo $line." :<br/>".$s."<br>";
			if(strlen($line)==0){$i+=1;continue;}
			if($line=="--connect")$s.=" --connect ".trim($arr[++$i]);
			else if($line=="--table"){$s.=" --table ".trim($arr[++$i]);$table=trim($arr[$i]);}
			else if($line=="--username")$s.=" --username ".trim($arr[++$i]);
			else if($line=="--password")$s.=" --password ".trim($arr[++$i]);
			else if($line=="--fields-terminated-by")$s.=" --fields-terminated-by ".trim($arr[++$i]);
			else if($line=="--lines-terminated-by")$s.=" --lines-terminated-by ".trim($arr[++$i]);
			else if($line=="--enclosed-by")$s.=" --enclosed-by ".trim($arr[++$i]);
			else if($line=="--optionally-enclosed-by")$s.=" --optionally-enclosed-by ".trim($arr[++$i]);
			$i+=1;
		}
		echo "code gen str=".$s."<br>";
		codegen($s,$table);
		
		if($op=="import"){
			change_import($table.".java",$array,$key);
		}
		else change_export($table.".java",$array,$key);
		compile($table);
		$exp="  --jar-file ".$table.".jar --class-name ".$table;
	}
	
	$output=shell_exec("sqoop --options-file ".$filename." ".$exp." ;echo $? ");

	if($output[strlen($output)-2]=="0")
	{
		echo "Successfully ".$op."ed!!<br/>";
	}
	else {
		echo $op." failed!<br/>";
	}
}


###########################################################################################################################
//main

if(isset($_POST['useconfig'])){
	use_file();
	exit(0);
}


$op=$_POST['op'];
$comm="sqoop"." ".$op;

if($_POST['machine']!=""&&$_POST['database_name']!=""&&$_POST['mytable']!=""){
	$machine=$_POST['machine'];
	$database_name=$_POST['database_name'];
	$db_username=$_POST['db_username'];
	$password=$_POST['password'];
	$TABLE=$_POST['mytable'];
}

else if(isset($_POST['table']))$TABLE=$_POST['table'];
//echo $TABLE; exit(0);
if($op=="export"){

	if(isset($_POST['directory']))$sourcedir=$_POST['directory']; //radio based input
	if($_POST['sourcedir']!="")$sourcedir=$_POST['sourcedir']; //text based input : priority

	$exp="";
	if(isset($_POST['decrypt'])){
		codegen();

		$array=explode(",",$_POST['decrypt_columns']);
		print_r($array);
		change_export($TABLE.".java",$array,$_POST['deckey']);

		compile();
		//echo $output;
		$exp="  --jar-file ".$TABLE.".jar --class-name ".$TABLE;
	
	}

	$comm.=" --connect jdbc:mysql://".$machine."/".$database_name." --username ".$db_username." --password '".$password."' --table ".$TABLE." ".$exp;

	if(isset($_POST['update'])){//update while exporting
			if($_POST['refcol']==""){echo "Specify the reference column<br/>";exit(0);}
			$comm.=" --update-key ".$_POST['refcol'];
	}

	$comm.=" --export-dir ".$sourcedir;
	if(isset($_POST['validate']))$comm.=" --validate";
	if(isset($_POST['parallel1']))$comm.=" -m ".$_POST['parallel'];

	$comm.=" ; echo $?";
	echo "Export command=".$comm."<br>";
	$output= shell_exec($comm);
	#echo $output;
	$val=$output[strlen($output)-2];

	if($val=="0") {//;//shell_exec("echo $?");
		echo "<h2> Successfully Exported from HDFS to RDBMS:)</h2>";
	}
	else echo "<h2> Export from HDFS to RDBMS Failed :( </h2>";
}
else{
	//echo exec('whoami')." - - - -";return 0;

	
	$imp="";
	if(isset($_POST['encrypt'])){

		codegen();

		$array=explode(",",$_POST['encrypt_columns']);
		print_r($array);
		change_import($TABLE.".java",$array,$_POST['enckey']);
		compile();

		//echo $output;
		
		
		/*if($_POST['field_delim']!="")$imp.=" --fields-terminated-by "."'" .$_POST['field_delim']."'";
		if($_POST['line_delim']!="")$imp.=" --lines-terminated-by "."'" .$_POST['line_delim']."'";
		if(isset($_POST['enclosed'])){
				if($_POST['enclosingchar']=='"')$_POST['enclosingchar']='\"';
				$imp.=" ".$_POST['enclosed']." '".$_POST['enclosingchar']."'";
		}*/

		$imp="  --jar-file ".$TABLE.".jar --class-name ".$TABLE;
		/*$imp.= " 2> err; echo $?";
		echo "import command=".$imp."<br/>";
		$output= shell_exec($imp);
		if($output[strlen($output)-2]!="0"){echo "??????????????Import error";echo $output;exit(0);}
		echo"<br/>------------------------------enenne";
		exit(0);*/
	}
	$comm.=" --connect jdbc:mysql://".$machine."/".$database_name." --username ".$db_username." --password '".$password."' --table ".$TABLE." ".$imp;
	
	if($_POST['targetdir']!="")$comm.=" --target-dir ".$_POST['targetdir'];
	else if(!isset($_POST['directory'])){

			$f=0;

	}
	else if($_POST['directory'] != "default")
	{
	
		$comm.=" --target-dir ".$_POST['directory'];
	}

	//if(isset($_POST['delete']))$comm.= " --delete-tarPOST-dir";
	if(isset($_POST['filetype']))$comm.= " ".$_POST['filetype'];
	//if(isset($_POST['importall']))
	if(isset($_POST['append']))$comm.= " --append";
	if($_POST['where']!=""){
			$comm.= ' --where "'.$_POST['where'].'"';
	}
	else if(isset($_POST['validate']))$comm.=" --validate";

	if(!isset($_POST['encrypt'])){

		if($_POST['columns']!="")$comm.= " --columns ".'"'.$_POST['columns'].'"';
		if($_POST['field_delim']!="")$comm.=" --fields-terminated-by "."'" .$_POST['field_delim']."'";
		if($_POST['line_delim']!="")$comm.=" --lines-terminated-by "."'" .$_POST['line_delim']."'";
		if(isset($_POST['enclosed'])){
				if($_POST['enclosingchar']=='"')$_POST['enclosingchar']='\"';
				$comm.=" ".$_POST['enclosed']." '".$_POST['enclosingchar']."'";
		}
	}

	if(isset($_POST['compressed']))$comm.=" -z";
	if(isset($_POST['parallel1']))$comm.=" -m ".$_POST['parallel'];

	$comm.= " ; echo $?";
	echo "command=".$comm."--<br/>"; 
	//return 0;
	//$output= shell_exec($comm." 2> err");
	//#########################################################################################################
	$output= shell_exec($comm);
	$val=$output[strlen($output)-2];
	//echo "---".$output[strlen($output)-1]."---"."---".$output[strlen($output)-2]."---"."---".$output[strlen($output)-3]."---"."<br/>";
	if($val=="0") {//;//shell_exec("echo $?");
		echo "<h2> Successfully Imported from RDBMS to HDFS:)</h2>";
	}
	else echo "<h2> Import from RDBMS to HDFS Failed :( </h2>";
	echo $output;
}


?>