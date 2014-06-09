<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("db.php");
//require_once("db_table.php"); // deprecated, replaced by Cls_DBTable.php.
require_once("Cls_DBTable.php");

$page_title = "Site Admin - Manage Users - Add New";

$tbl = isset($_REQUEST['tbl']) ? $_REQUEST['tbl'] : "";
?>

<?php include("header.php"); ?>

<h3>Add New</h3>

<form method="post">
<?php

if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>The New Entry has been successfully added.</font></p>";
    print "<a href='admin_tbl_add.php?tbl=User'>Add another</a>";
    exit();
}

db_open();

$cols_pwd = array('passwd');
$cols_default = array('gid');
$cls_tbl = new Cls_DBTable($tbl, "ID", $cols_pwd, $cols_default);

$msg = "";
if (isset($_REQUEST['btnSubmit'])) {
    $msg = $cls_tbl->insertNew(1);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        //print "$msg";
        print $cls_tbl->writeNewForm(1, 'admin_users.php', 0, 0);
        //print "<p><a href='admin_users.php'>Cancel Add New</a></p>";
    } else {
        header("Location: admin_tbl_add.php?tbl=$tbl&ok=Y");
        exit();
    }
} else {
    print $cls_tbl->writeNewForm(1, 'admin_users.php', 0, 0);
    //print "<p><a href='admin_users.php'>Cancel Add New</a></p>";
}

db_close();
?>
</form>

<?php include("footer.php"); ?>

