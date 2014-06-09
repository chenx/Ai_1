<?php
require_once("auth.php");
require_once("db.php");
require_once("Cls_DBTable.php");

$page_title = "My Profile";
$page_name = $_SERVER['PHP_SELF'];
?>

<?php include("header.php"); ?>

<h3>My Profile</h3>

<form method="post">
<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>Your profile has been successfully updated.</font></p>";
    print "<a href='$page_name'>Go to My Profile</a>";
    exit();
}

db_open();

$cols_pwd = array('passwd');
$cols_default = array('gid', 'login');
$cls_tbl = new Cls_DBTable("User", "ID", $cols_pwd, $cols_default);

$id = $_SESSION['ID'];
$mode = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : '';

if ($mode == 'edit') {
    if (isset($_REQUEST['btnSubmit'])) {
        $msg = $cls_tbl->update($id, 0);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            print "$msg";
        }
        else {
            header("Location: $page_name?ok=Y");
        }
    }
    //else 
    {
        print $cls_tbl->writeEditForm($id, 0, $page_name);
        //print "<p><a href='$page_name'>Cancel Edit</a></p>";
    }
} else {
    print $cls_tbl->writeViewForm($id, 0);
}

db_close();
?>
</form>

<?php include("footer.php"); ?>
