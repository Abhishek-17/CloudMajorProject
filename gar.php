<html><head><script src="jquery.js"></script><script src="sqooper.js"></script> <link rel="stylesheet" type="text/css" href="sqooper.css">
</head>
<body>
<div id="logoDiv"><img id = "LogoID" src="images/logo.jpg" style="float: left;" height="100"/></div>
<h2 align='center'> Sqooper</h2>
<div id="container" align="left">
<?

$path="/";
if(isset($_GET['path'])){
	$path=$_GET['path'];
}

$output= shell_exec("hadoop fs -ls ".$path." | tr -s ' ' | cut -d ' ' -f8");
$token = strtok($output, "\n");

//##################################################################################### form #######################
echo "<form action='action.php' method='post' enctype='multipart/form-data'>";//<input type='hidden' name='op' value='export'>";
echo "<fieldset><legend>Select Directory</legend>";
while ($token != false)
{
	echo '<input type="radio" name="directory" value='.$token.'><a href="gar.php?path='.$token.'">'.$token.'</a><br>';
	//echo "$token<br>";
	$token = strtok("\n");
} 
echo '<input type="radio" name="directory" value="default">Create Default Directory with Name as TableName (Works only for Import)<br>';
echo "</fieldset><br/>";
echo "<fieldset><legend>Select Table:</legend>";
$con=mysqli_connect("localhost","root","","hadoop");
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
 $query="show tables";
 $result = mysqli_query($con,$query);
 while($row=mysqli_fetch_array($result)){
 		echo '<input type="radio" name="table" value='.$row[0].'>'.$row[0].'<br>';
 }
 echo "</fieldset>";
 echo "<br/>";
 // START1
 echo '<div class="expandable-panel" id="cp-1" align="left">
        <div class="expandable-panel-heading">
            <h2>Common Advanced Options<span class="icon-close-open"></span></h2>
        </div>
        <div class="expandable-panel-content">';
//echo "<fieldset><legend>General Options:</legend>";
	echo "<br/>";
	echo "<fieldset><legend>Connection details for Database:</legend>";
		echo 'Ip Address: <input type="text" name="machine" value="localhost" size=15 >';
		echo 'Database name: <input type="text" name="database_name" value="hadoop" size=15 >';
		echo 'Username: <input type="text" name="db_username" value="root" size=15 >';
		echo 'Password: <input type="password" name="password" size=15  >';
		echo 'Table <input type="text" name="mytable" value="login"size=15 >';
	echo "</fieldset>";
	echo "<br/>";
 	echo '<input type="checkbox" name="validate" value="--validate">Validate the Transfer..<br>';
 	echo '<input type="checkbox" name="parallel1" value="Yes">Choose No of parallel task.. ';
 	echo ' <select name="parallel">';
 	for($i=5 ;$i<21;$i++){
 		echo '<option value="'.$i.'">'.$i.'</option>';
 	}
	echo'</select><br/><br/>';
	echo '<input type="checkbox" name="useconfig" value="yes">Provide Consolidated Configuration Settings:<br>';
	echo "<fieldset><legend>Choose Alternative:</legend>";

	echo'Using Text Field: <input type="radio" name="config_option" value="text">';
	echo '<textarea rows="3" cols="60" name="configtext"></textarea><br/>';
	echo 'Using Config File: <input type="radio" name="config_option" value="file">';
	echo '<input type="file" name="file" id="file"><br>';
	echo "</fieldset><br/>";
//echo "</fieldset>";
echo "<br/>";
echo "</div></div>";
	// END1
//START2
echo '<input type="radio" name="op" value="export">Export from HDFS to RDBMS<br/>';
echo  '<div class="expandable-panel" id="cp-2"><div class="expandable-panel-heading"><h2>Export Advanced Options<span class="icon-close-open"></span></h2></div><div class="expandable-panel-content">';

 //echo "<fieldset><legend>Export:</legend>";
 echo "<br><fieldset><legend>Decryption Settings:</legend>";
 	echo '<input type="checkbox" name="decrypt" value="yes">Use Decryption<br/>';
 	echo 'Decryption Key (16 characters i.e. 128 bits AES key):<input type="password" name="deckey" > <br>';
 	echo 'Specify Columns to be decrypted:<input type="text" name="decrypt_columns"  value="username,password"><br/>';
 echo '</fieldset><br>';
 echo 'Specify Source directory (Optional): <input type="text" name="sourcedir"> <br/>';
 echo '<input type="checkbox" name="update" value="yes">Update using reference column: '.'<input type="text" name="refcol" value="id"> <br/>';;
// echo "</fieldset>";

echo "<br/>";
//END2
echo "</div></div>";
 
//START3
echo '<input type="radio" name="op" value="import">Import from RDBMS to HDFS<br/>';
echo  '<div class="expandable-panel" id="cp-2"><div class="expandable-panel-heading"><h2>Import Advanced Options<span class="icon-close-open"></span></h2></div><div class="expandable-panel-content">';
echo "<fieldset><legend>Import:</legend>";
  echo '<input type="checkbox" name="delete" value="--delete-target-dir">Delete target dir<br>';
 echo '<input type="radio" name="filetype" value="--as-avrodatafile">As a Avrodata file<br>';
 echo '<input type="radio" name="filetype" value="--as-sequencefile">As a Sequence file<br>';
 echo '<input type="checkbox" name="importall" value="importall">Import All tables<br>';

 echo "<br><fieldset><legend>Encryption:</legend>";
 echo '<input type="checkbox" name="encrypt" value="yes">Encrypt<br>';
 echo ' key:<input type="password" name="enckey" ><br>';
 echo 'Specify Particular columns to be encrypted<input type="text" name="encrypt_columns" value="username,password"><br/>';
 echo "</fieldset><br>";
 echo '<input type="text" name="where"> Specify where condition like '."id='2'<br/>";
 echo '<input type="text" name="targetdir">Specify target dir <br/>';
 echo '<input type="text" name="columns" value="username,password">Specify Particular columns <br/><br/>';

 echo "<fieldset><legend>Delimiters:</legend>";
 	echo '<input type="text" name="field_delim">Field Delimiter <br/>';
 	echo '<input type="text" name="line_delim">Line Delimiter <br/>';
 	echo '<input type="radio" name="enclosed" value="--optionally-enclosed-by">Optionally enclosed by quotes<br>';
 	echo '<input type="radio" name="enclosed" value="--enclosed-by">Enclosed by quotes<br>';
 	echo '<input type="text" name="enclosingchar">: Enclosing char<br/>';
  echo "</fieldset>";
  echo "</fieldset>";

//END3 
 echo "</div></div>";
echo '<br/><input type="submit" value="Transfer Data"/></form>';

//##################################################################################### form #######################

?>
</div></body></html>
