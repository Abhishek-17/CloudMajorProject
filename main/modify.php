
<?php

if (isset($_GET['wid'])) {
    $f = "";
    if (isset($_GET['func']))
        $f = "function_";
    if (isset($_GET['nodeid'])) {
        echo '<form action="upload_file_validation.php?nodeid=" method="post"
enctype="multipart/form-data">
<label for="file">Upload XML File:</label>
<input type="file" name="file" id="file"><br>';
        if ($f != "")
            echo '<input type="hidden" name="func" value="">';
        echo '
<input type="hidden" name="wid" value="' . $_GET['wid'] . '">
   <input type="hidden" name="nodeid" value="' . $_GET['nodeid'] . '">
<input type="submit" name="submit" value="Submit">
</form>';
    }
    else {
        echo'<form action="upload_file_validation.php" method="post"
enctype="multipart/form-data">
<label for="file">Upload XML File:</label>
<input type="file" name="file" id="file"><br>';
        if ($f != "")
            echo '<input type="hidden" name="func" value="">';
        echo '
<input type="hidden" name="wid" value="' . $_GET['wid'] . '">
<input type="submit" name="submit" value="Submit">
</form>';
    }
}
?>
