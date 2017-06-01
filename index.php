<?php
require('gview_svg.inc');

$upload_dir="uploads/";
$fh = opendir($upload_dir);

if(is_array($_FILES) && sizeof($_FILES) > 0)
{
        $target_path = $upload_dir . basename( $_FILES['uploadedfile']['name']);
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path) && chmod($target_path, 0775)) {
                if(gcode_to_svg($target_path))
                        header("Location: /");
                echo "The file ".  basename( $_FILES['uploadedfile']['name']) . " has been uploaded";
        } else{
                echo "There was an error uploading the file, please try again!";
        }
}
?>
<center>
<?php
while(($file = readdir($fh)))
{
        if(filetype($upload_dir . "/" . $file) == "dir" && $file != "." && $file != "..")
        {
                ?>
                        <a href=view.php?file=<?php echo $file; ?>><?php echo $file; ?></a><br>
                <?php
        }
}
?>
<br>
<br>
<form enctype="multipart/form-data" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
Choose a file to upload: <input name="uploadedfile" type="file" /> <input type="submit" value="Upload File" />
</form>
</center>
