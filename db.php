<?php
//
// @Author: Tom Chen
// @Created on: 4/26/2013
// @Last modified: 4/29/2013
//

require_once("conf/conf.php");

function db_open() {
    global $host, $db_usr, $db_pwd, $db, $link;
    $link = mysql_connect($host, $db_usr, $db_pwd);
    if (!$link) {
        doExit('Could not connect: ' . mysql_error());
    }
    mysql_select_db($db, $link);
}

function db_close() {
    global $link;
    mysql_close($link);
}

// assume db_open() was called.
function getScalar($query, $col) {
    global $link;
    $result = mysql_query($query, $link);
    if (! $result) {
        doExit('Invalid query: ' . mysql_error());
    }

    if (mysql_num_rows($result) != 1) {
        //doExit("getScalar failed: returned rows is not 1");
        return "";
    } else {
        $info = mysql_fetch_array($result);
        return $info[$col];
    }
}

function doExit($msg) {
    die($msg . ". Please contact your system administrator.");
    exit();
}

//
// To display html/xml tag symbols from database in browsers.
//
function db_htmlEncode($s) {
    // Converts the smallest set of entities possible to generate valid html.
    // htmlentities(input, [quote_style, [charset]]); // Programming PHP, p83.
    return htmlspecialchars($s); 

    // Converts all chars with HTML entity equivalents into those equivalents.
    // htmlentities(input, [quote_style, [charset]]);
    //return htmlentities($s, ENT_QUOTES);

    //same as below. 
    //$s = str_replace("&", "&amp;", $s);
    //$s = str_replace("<", "&lt;", $s);
    //$s = str_replace(">", "&gt;", $s);
    //$s = str_replace("\"", "&quot;", $s);
    //return $s;
}

//
// To insert/update value to database.
//
function db_encode($s) {
    $s = trim($s);
    if ($s == "") {
        $s = "NULL";
    }
    else {
        //$s = addslashes($s); // This does not work.
        $s = str_replace("\\", "\\\\", $s);
        $s = str_replace("'", "''", $s);
        $s = "'$s'";
    }
    return $s;
}

?>
