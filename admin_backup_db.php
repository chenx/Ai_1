<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("conf/conf.php");
require_once("func/util_fs.php");
require_once("func/util.php");

$page_title = "Site Admin - Backup Database";
?>

<?php include("header.php"); ?>

<script type="text/javascript">
function deleteFile(file) {
    var r = confirm("Are you sure to delete file " + file + "?");
    if (r) {
        document.getElementById('btnDelete').value = file;
        document.forms[0].submit();
    }
}
function viewFile(file) {
    document.getElementById('btnView').value = file;
    document.forms[0].submit();
}
</script>

<h3>Backup Database</h3>

<p>
Back up database using the mysqldump command into a sql file.
</p>

<form method="post">
<p>
<input type='submit' name='btnSubmit' value='Backup Database' />
<input type='button' value='Refresh Page' onclick="javascript: window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>';" />
</p>
<input type='hidden' id='btnDelete' name='btnDelete' value='' />
<input type='hidden' id='btnView' name='btnView' value='' />
<?php

$dir_bak = "bak_db";

if (isset($_REQUEST['btnSubmit']) && $_REQUEST['btnSubmit'] != "") {
    backup_db($dir_bak);
}
else if (isset($_REQUEST['btnDelete']) && $_REQUEST['btnDelete'] != "") {
    delete_backup($dir_bak . "/" . $_REQUEST['btnDelete']);
}

$dir = getSubFilesAsArray($dir_bak);
if (! is_array($dir)) {
    echo $dir;
} else {
    //print_r($dir);
    show_backup_list($dir);
}

if (isset($_REQUEST['btnView']) && $_REQUEST['btnView'] != "") {
    view_backup($_REQUEST['btnView']);
}

?>
</form>

<?php include("footer.php"); ?>

<?php

function view_backup($filename) {
    global $dir_bak;
    $s = file_get_contents( "$dir_bak/$filename" );
    echo "<br/>$filename<br/>";
    echo "<TEXTAREA style='width: 100%; height: 200px;'>$s</TEXTAREA>";
}

//
// $ASC: 1 - in ascending order (of time), 0 - in descending order (default).
//
function show_backup_list($dir, $ASC = 0) {
    $ct = count($dir);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        if (! endsWith($dir[$i], ".sql")) continue; // only download sql file.
        $t = "<a href='#' onclick='javascript: deleteFile(\"$dir[$i]\");'>Delete</a>&nbsp;"
           . "<a href='#' onclick=\"javascript: window.location.href='download_backup_db.php?f=$dir[$i]&t=text/plain';\">Download</a>&nbsp;"
           . "<a href='#' onclick='javascript: viewFile(\"$dir[$i]\");'>View</a>&nbsp;"
           . " $dir[$i]<br/>";

        if ($ASC == 0) $s = $s . $t;
        else $s = $t . $s;
    }
    print $s;
}

function delete_backup($file) {
    try {
        if (! file_exists($file)) {
            $s = "File $file does not exist.";
            $color = "green";
        }
        unlink($file);
        $s = "File $file has been successfully deleted.";
        $color = 'green';
    } catch (Exception $e) {
        $s = $e->getMessage();
        $color = 'red';
    }
    echo "<p><font color='$color'>$s</font></p>";
}

function backup_db($dir_bak) {
    global $db, $db_usr, $db_pwd;
    $backup_name = $db . "_" . date('y-m-d-h-i-s') . ".sql";
    $shell_cmd = "mysqldump -u $db_usr -p$db_pwd $db > $dir_bak/$backup_name";
    system($shell_cmd);
}

?>
