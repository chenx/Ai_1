<?php

//
// http://www.webmasterworld.com/forum88/4590.htm
// http://www.php.net/manual/en/function.mysql-connect.php
//

session_start(); 
require_once("auth.php");
require_once("Cls_DBTable.php");
?>

<?php
$page_title = "Profile";
$page_name = $_SERVER['PHP_SELF'];
?>

<?php include("header.php"); ?>


<h3>My Profile</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>Your profile has been successfully received.</font></p>";
    print "<a href='$page_name'>Go to My Profile</a>";
} else {
    db_open();

    $cols_pwd = array('passwd');
    $cols_default = array('gid', 'login');
    $cls_tbl = new Cls_DBTable("User", "ID", $cols_pwd, $cols_default);

    $id = $_SESSION['ID'];
    $mode = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : '';

    $msg = "";
    if ($mode == "edit") {
        if (isset($_REQUEST['btnVerify'])) {
            $msg = $cls_tbl->verifyForm(0, 0, 0); 
            if ($msg != "" && strpos($msg, "Error") > 0) {
                //print "$msg";
                print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
            } else {
                print $cls_tbl->writeVerifyForm(1, $page_name);
            }
        }
        else if (isset($_REQUEST['btnSubmit'])) {
            //$msg = $cls_tbl->insertNew(0);
            $msg = $cls_tbl->update($id, 0);
            if ($msg != "" && strpos($msg, "Error") > 0) {
                //print "$msg";
                print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
            } else {
                header("Location: $page_name?ok");
                exit();
            }
        } 
        else {
            print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
        }
    } else {
        print $cls_tbl->writeViewForm($id, 0);
    }

    db_close();
}
?>

</form>


<?php include("footer.php"); ?>

