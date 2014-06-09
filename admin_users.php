<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("db.php");
//require_once("db_table.php"); // deprecated, replaced by Cls_DBTable.php.
require_once("Cls_DBTable.php");

$page_title = "Site Admin - Manage Users";
?>

<html>
<head>
<title>Ai</title>
<script type="text/javascript">

function tbl_del(tbl, id) {
    var r = confirm("Are you sure to delete entry " + id + "?");
    if (r) {
        document.getElementById('deleteTbl').value = tbl;
        document.getElementById('deleteID').value = id;
        document.forms[0].submit();
    }
}

</script>
</head>
<body>
<?php include("header.php"); ?>

<form method="POST">
<input type='hidden' id='deleteTbl' name='deleteTbl' value''/>
<input type='hidden' id='deleteID' name='deleteID' value=''/>
</form>

<?php
db_open();

$cls_tbl = new Cls_DBTable("User", "ID");

if (isset($_REQUEST['deleteID']) && $_REQUEST['deleteID']  != "") {
    $msg = $cls_tbl->deleteEntry($_REQUEST['deleteID']);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        print $msg;
    }
    else {
        //print "<script type='text/javascript'>alert('Deleted has finished successfully.');</script>";
    }
}

$cls_tbl->manage_table("User", "User");

//print_r ( $cls_tbl->getDBTableColumnsAsArray("User") );

db_close();
?>

<?php include("footer.php"); ?>

