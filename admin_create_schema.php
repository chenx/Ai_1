<?php
require_once("auth.php");
require_once("auth_admin.php");
require_once("Cls_DBUtil.php");

$page_title = "Site Admin - Create Schema";
?>

<?php include("header.php"); ?>

<h3>Create Schema For Tables</h3>

<p>
This creates schema of a database table in the format of insertion sql statements. When select "(all)", all the tables (except Schema_TblCol) are used.
The user can update "Title" and "HtmlType" of fields. 
The output can be used to create a table in the database (In this framework, a table <u>Schema_TblCol</u> has been created for this purpose by default).
This is useful when creating view/edit form for database tables (you can use "show columns for [table]", but field Title and html_type need to be specified manually).
</p>

<form method="post">
<?php
db_open();

writeSchemaForDBTable();

db_close();
?>
</form>

<?php include("footer.php"); ?>

<?php
function writeSchemaForDBTable() {
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
            if ($tbl == "Schema_TblCol") continue;
            $query = "show columns from $tbls[$i]";
            $s .= "-- Table: $tbls[$i] -- \n" . getDBSchemaAsInsertSQL($query, $tbl) . "\n";
            $t .= $u->getDBTableAsHtmlTable($query, "Table <u>$tbls[$i]</u> Schema:");
         }
        $s2 = <<<EOF
DROP TABLE IF EXISTS Schema_TblCol;
CREATE TABLE Schema_TblCol (
    ID int    NOT NULL PRIMARY KEY AUTO_INCREMENT,
    TableName varchar(100) NOT NULL,
    Title     varchar(100),
    Field     varchar(100) NOT NULL,
    `Type`    varchar(100) NOT NULL,
    `Null`    varchar(100),
    `Key`     varchar(100),
    `Default` varchar(100),
    Extra     varchar(100)
);
ALTER TABLE Schema_TblCol ADD UNIQUE KEY (TableName, Field);
EOF;
        $s = "$s2\n\n" . $s;

    } else {
        $query = "show columns from $tbl";
        $s = getDBSchemaAsInsertSQL($query, $tbl);
        $t = $u->getDBTableAsHtmlTable($query, "Table <u>$tbl</u> Schema:");
    }

    print "<TEXTAREA style='width:100%; height: 100px;'>$s</TEXTAREA>";
    print "<br/><font color='green'>Copy and paste the sql code above in a mysql shell to insert to <u>Schema_TblCol</u> table.</font>";
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
        $s .= "INSERT INTO $tbl_schema ($cols) VALUES ($vals);\n";

        while ($info = mysql_fetch_array($result)) {
            $vals = getVals($info, "value", $tbl);
            $s .= "INSERT INTO $tbl_schema ($cols) VALUES ($vals);\n";
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
            if ($key == 'Default') {
                $value = db_encode($value);
            } else {
                $value = "'$value'"; 
            }

            if ($i == 2) {
                if ($v_type == 'key') $vals .= "`TableName`, `Title`, `HtmlType`, `$key`";
                else $vals .= "'$tbl', $value, 'text', $value";
            } else {
                if ($v_type == 'key') $vals .= ", `$key`";
                else $vals .= ", $value";
            }
        }
    }
    return $vals;
}

?>
