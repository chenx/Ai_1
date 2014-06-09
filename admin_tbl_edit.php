<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("db.php");
//require_once("db_table.php"); // deprecated, replaced by Cls_DBTable.php.
require_once("Cls_DBTable.php");

$page_title = "Site Admin - Manage Users - Edit";

$tbl = isset($_REQUEST['tbl']) ? $_REQUEST['tbl'] : "";
$id  = isset($_REQUEST['pk'])  ? $_REQUEST['pk']  : "";
?>

<?php include("header.php"); ?>

<h3>Edit Entry</h3>

<form method="post">
<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>The Entry has been successfully updated.</font></p>";
    print "<a href='admin_tbl_edit.php?tbl=$tbl&pk=$id'>Edit again</a>";
    exit();
}

db_open();

$cols_pwd = array('passwd');
$cols_default = array('gid');
$cls_tbl = new Cls_DBTable($tbl, "ID", $cols_pwd, $cols_default);

$msg = "";
if (isset($_REQUEST['btnSubmit'])) {
    $msg = $cls_tbl->update($id, 1);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        print "$msg";
    }
    else {
        header("Location: admin_tbl_edit.php?tbl=$tbl&pk=$id&ok=Y");
    }
}

print $cls_tbl->writeEditForm($id, 1, "admin_users.php");
//print "<p><a href='admin_users.php'>Cancel Edit</a></p>";

db_close();
?>
</form>

<?php include("footer.php"); ?>
