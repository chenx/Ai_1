<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("Cls_DBUtil.php");

$page_title = "Site Admin - Create Schema";
?>

<?php include("header.php"); ?>

<h3>Dump Contents Of Tables</h3>

<p>
This dumps content of a database table in the format of insertion sql statements. 
When select "(all)", all the tables are used, and script to regenerate tables from scratch are also included.
The output can be used to recover tables in the database.
</p>

<form method="post">
<?php
db_open();

dumpDBTable();

db_close();
?>
</form>

<?php include("footer.php"); ?>

<?php
//
// Adapted from admin_create_schema.php.
//
function dumpDBTable() {
    $u = new Cls_DBUtil();

    $tbls = $u->getDBTablesAsArray();
    //print_r ($tbls);

    array_unshift($tbls, '(all)');

    $selName = "dbTbl";
    print "Select a Table: ";
    print convertArrayToSelect($tbls, $selName, $selName);
    print "<input type='submit' value='Submit' name='btnSubmit' />";

    //$s = $u->getDBTableAsXml($query);
    //print (str_replace("<", "&lt;", $s));

    // Exit if no table is selected.
    if ( ! isset($_REQUEST[$selName]) || $_REQUEST[$selName] == "" ) return;

    $tbl = $_REQUEST[$selName];

    if ($tbl == '(all)') {
        $s = "";
        $t = "";
        $ct = count($tbls);
        for ($i = 1; $i < $ct; ++ $i) {
            $tbl = $tbls[$i];
            //if ($tbl == "Schema_TblCol") continue;
            $query = "select * from $tbl";
            $s .= "-- Table: $tbls[$i] -- \n" . getDBSchemaAsInsertSQL($query, $tbl) . "\n";
            $t .= $u->getDBTableAsHtmlTable($query, "Table <u>$tbls[$i]</u> Contents:");
         }

         $s2 = file_get_contents("conf/makedb.sql"); // create database tables from scratch.
         $s = "$s2\n" . $s;

    } else {
        //$query = "show columns from $tbl";
        $query = "select * from $tbl";
        $s = getDBSchemaAsInsertSQL($query, $tbl);
        $t = $u->getDBTableAsHtmlTable($query, "Table <u>$tbl</u> Schema:");
    }

    print "<TEXTAREA style='width:100%; height: 100px;'>$s</TEXTAREA>";
    //print "<br/><font color='green'>Copy and paste the sql code above in a mysql shell to insert to <u>Schema_TblCol</u> table.</font>";
    print $t;
}

function getDBSchemaAsInsertSQL($query, $tbl) {
    global $link;
    $result = mysql_query($query, $link);
    if (! $result) {
        $this->doExit('Invalid query: ' . mysql_error());
    }

    $tbl_schema = "Schema_TblCol";
    $s = "";
    if (mysql_num_rows($result) == 0) {
        $s = "(no data yet)";
    } else {
        $info = mysql_fetch_array($result);

        $cols = getVals($info, "key", $tbl);
        $vals = getVals($info, "value", $tbl);
        $s .= "INSERT INTO $tbl ($cols) VALUES ($vals);\n";

        while ($info = mysql_fetch_array($result)) {
            $vals = getVals($info, "value", $tbl);
            $s .= "INSERT INTO $tbl ($cols) VALUES ($vals);\n";
        }

    }
    return $s;
}

function getVals($info, $v_type, $tbl) {
    $i = 0;
    $vals = "";
    foreach ($info as $key => $value) {
        ++ $i;
        if ($i % 2 == 0) {
            $value = db_encode($value);

            if ($i == 2) {
                if ($v_type == 'key') $vals .= "`$key`";
                else $vals .= "$value";
            } else {
                if ($v_type == 'key') $vals .= ", `$key`";
                else $vals .= ", $value";
            }
        }
    }
    return $vals;
}

?>
