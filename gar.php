<?

echo "hello<br/>";
$path="/";
if(isset($_GET['path'])){
	$path=$_GET['path'];
}

$output= shell_exec("hadoop fs -ls ".$path." | tr -s ' ' | cut -d ' ' -f8");
$token = strtok($output, "\n");

//##################################################################################### form #######################
echo "<form action='action.php' method='GET'>";//<input type='hidden' name='op' value='export'>";
echo "<fieldset><legend>Select Directory</legend>";
while ($token != false)
{
	echo '<input type="radio" name="directory" value='.$token.'><a href="gar.php?path='.$token.'">'.$token.'</a><br>';
	//echo "$token<br>";
	$token = strtok("\n");
} 
echo '<input type="radio" name="directory" value="default">Create Default Directory with Name as TableName (Only works for Import)<br>';
echo "</fieldset><br/><br/>";
echo "<fieldset><legend>Select Table:</legend>";
$con=mysqli_connect("localhost","root","","login");
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
 echo '<input type="radio" name="op" value="export">Export from HDFS to RDBMS<br/>';
 echo '<input type="radio" name="op" value="import">Import from RDBMS to HDFS<br/>';
echo '<br/><input type="submit" value="Export to Mysql"/></form>';
//##################################################################################### form #######################

?>
