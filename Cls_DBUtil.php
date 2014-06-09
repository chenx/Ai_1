<?php
//
// A class for manipulating a database table.
//
// @Author: Tom Chen
// @Created on: 4/29/2013
// @Last modified: 4/29/2013
//

require_once("db.php");

class Cls_DBUtil {

    function __construct() {

    }

    function __destruct() {
        // do nothing.
    }

    public function getDBTablesAsArray() {
        $query = "SHOW TABLES";
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = array();
        if (mysql_num_rows($result) > 0) {
            $i = 0;
            while ($info = mysql_fetch_array($result)) {
                array_push($s, $info[0]);
            }
        }
        return $s;
    }

    public function getDBTableColumnsAsArray($tbl_name) {
        $query = "show columns from $tbl_name";
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = array();
        if (mysql_num_rows($result) > 0) {
            $i = 0;
            while ($info = mysql_fetch_array($result)) {
                array_push($s, $info['Field']);
            }
        }
        return $s;
    }

    public function getDBTableAsXml($query) {
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = "";
        if (mysql_num_rows($result) == 0) {
            $s = "";
        } else {
            $i = 0;
            while ($info = mysql_fetch_array($result)) {
                ++ $i;
                $s .= "<row>" . $this->writeRowAsXml($info) . "</row>";
            }
        }
        $s = "<?xml version=\"1.0\"?><root>$s</root>";

        return $s;
    }

    private function writeRowAsXml($a) {
        $s = "";
        $i = 0;
        foreach ($a as $key => $value) {
            ++ $i;
            $value = db_htmlEncode($value);
            if ($i % 2 == 0) $s .= "<$key>$value</$key>";
        }

        return $s;
    }


    public function getDBTableAsInsertSQL($query, $tbl) {
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = "";
        if (mysql_num_rows($result) == 0) {
            $s = "(no data yet)";
        } else {
            $info = mysql_fetch_array($result);

            $cols = $this->getVals($info, "key");
            $vals = $this->getVals($info, "value");
            $s .= "INSERT INTO $tbl ($cols) VALUES ($vals)\n";

            while ($info = mysql_fetch_array($result)) {
                $vals = $this->getVals($info, "value");
                $s .= "INSERT INTO $tbl ($cols) VALUES ($vals)\n";
            }

        }
        return $s;
    }

    private function getVals($info, $v_type) {
        $i = 0;
        $vals = "";
        foreach ($info as $key => $value) {
            ++ $i;
            if ($i % 2 == 0) {
                $comma = ($i == 2) ? '' : ', ';
                if ($v_type == 'key') $vals .= $comma . $key;
                else $vals .= $comma . "'$value'";
            }
        }
        return $vals;
    }


    public function getDBTableAsHtmlTable($query, $title = '', $doManage = 0) {
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = "";
        if (mysql_num_rows($result) == 0) {
            $s = "<h3>$title</h3>(no data yet)";
        } else {
            // Write header and first row.
            $info = mysql_fetch_array($result);
            $s .= $this->writeHdr($info, $doManage);
            $s .= $this->writeRow($info, $doManage);

            while ($info = mysql_fetch_array($result)) {
                //print_r($info); break;
                $s .= $this->writeRow($info, $doManage);
            }

            $addNew = ($doManage) ? " [ <a href='admin_tbl_add.php?tbl=$this->tbl_name'>Add New</a> ]<br/>" : "";

            $style = "border: 1px solid;";
            $s = "<h3>$title</h3>$addNew<table border='1' style='$style'>$s</table>";
        }
        return $s;
    }

    private function writeHdr($a, $doManage) {
        $i = 0;
        $s = "";

        if ($doManage) $s .= "<td><b>&nbsp;Action&nbsp;</b></td>";

        foreach ($a as $key => $value) {
            ++ $i;
            if ($i % 2 == 0) $s .= "<td><b>&nbsp;$key&nbsp;</b></td>";
        }
        return "<tr>$s</tr>";
    }

    private function writeRow($a, $doManage) {
        $ct = count($a);
        $s = "";

        if ($doManage) {
            $pk = $a[$this->tbl_pk];
            $s .= "<td><a href='admin_tbl_edit.php?tbl=$this->tbl_name&pk=$pk'>Edit</a> ";
            $s .= "<a href='#', onclick='javascript:tbl_del(\"$this->tbl_name\", $pk);'>Delete</a></td>";
        }
        for ($i = 0; $i < $ct; $i += 2) {
            //echo $i.",";
            $idx = $i / 2;
            $s .= "<td>&nbsp;$a[$idx]&nbsp;</td>";
        }
        $s = "<tr>$s</tr>";
        return $s;
    }

    private function doExit($msg) {
        die($msg . ". Please contact your system administrator.");
        exit();
    }

}
