<?php

//
// http://www.webmasterworld.com/forum88/4590.htm
// http://www.php.net/manual/en/function.mysql-connect.php
//

session_start(); 
require_once("Cls_DBTable.php");
?>

<?php
$page_title = "Register";
?>

<?php include("header.php"); ?>

<center>

<h3>Register</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>Your registration has been successfully received.</font></p>";
} else {
    db_open();

    $cols_pwd = array('passwd');
    $cols_default = array('gid');
    $cls_tbl = new Cls_DBTable("User", "ID", $cols_pwd, $cols_default);

    $msg = "";
    if (isset($_REQUEST['btnVerify'])) {
        $msg = $cls_tbl->verifyForm(0, 1); // $cls_tbl->insertNew(1);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            //print "$msg";
            print $cls_tbl->writeNewForm(0, '', 1);
        } else {
            print $cls_tbl->writeVerifyForm(0, "register.php");
        }
    }
    else if (isset($_REQUEST['btnSubmit'])) {
        $msg = $cls_tbl->insertNew(0);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            print "$msg";
            print $cls_tbl->writeNewForm(0, '', 1);
        } else {
            header('Location: register.php?ok');
            exit();
        }
    } else {
        print $cls_tbl->writeNewForm(0, '', 1);
    }

    db_close();
}
?>

</form>

<p><a href='./'>Home</a></p>

</center>

<?php include("footer.php"); ?>

