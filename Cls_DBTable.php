<?php
//
// A class for manipulating a database table.
//
// @Author: Tom Chen
// @Created on: 4/26/2013
// @Last modified: 4/26/2013
//

require_once("db.php");

class Cls_DBTable {
  
    private $tbl_name;
    private $tbl_pk;
    private $cols_pwd;
    private $cols_default; // an array, for columns use default values.

    function __construct($tbl_name, $tbl_pk, $cols_pwd = array(), $cols_default = array()) {
        $this->tbl_name = $tbl_name;
        $this->tbl_pk   = $tbl_pk;
        $this->cols_pwd = $cols_pwd;
        $this->cols_default = $cols_default;
        //print_r($this->cols_pwd);
        //print_r($this->cols_default);
    }

    function __destruct() {
        // do nothing.
    }

    //
    // Output the form to edit an entry.
    // $xml - from getDBTableAsXml("SELECT * FROM $tbl WHERE ID = '$id'").
    // $change_default - 1 if allow change default value, 0 otherwise.
    //
    // If want to change value of default cols, set $change_default = 1 both here
    // and in function call to update().
    //
    public function writeEditForm($id, $change_default = 0, $back_url = '', $verifyFirst = 0, $pwd_needed = '1') {
        $query = "SELECT * FROM $this->tbl_name WHERE $this->tbl_pk = '$id'"; //echo $query;
        $xml = $this->getDBTableAsXml($query);
        $record = new SimpleXMLElement($xml);
        $cols = $record->row[0];
        //echo "[[[" . $cols->first_name . "...]]]<br/>";
        $schema = $this->getDBTableSchema();

        $IsPostBack = (U_REQUEST('IsPostBack') != '');
        $s = "";
        foreach ($schema->xpath('//row') as $field) {
            // Pkey is autoincrement. Don't add to the new form.
            $name = trim( $field->Field );
            if ($name == $this->tbl_pk) continue;

            $allow_null = ($field->Null != 'NO');
            $star = $allow_null ? "" : " <font color='red'>*</font>";

            $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star&nbsp;</td>";
            $v = U_REQUEST("txt_$name") ? U_REQUEST("txt_$name") : '';
            $v = db_htmlEncode($v);

            $readonly = ($name == $this->tbl_pk ||
                        ($change_default == 0 && in_array($name, $this->cols_default))) ?
                        " readonly " : "";
            $value = db_htmlEncode( $cols->$name );
            $v = isset( $_REQUEST["txt_$name"] ) ? trim( $_REQUEST["txt_$name"] ) : $value;

            $note = '';
            if ( in_array($name, $this->cols_pwd) ) {
                $type = 'password';
                //$v = isset( $_REQUEST["txt_$name"] ) ? $_REQUEST["txt_$name"] : '';
                $note = ' (leave blank if do not change)';
                if ($IsPostBack) {
                    $errMsg = $this->verify_pwd($name, $pwd_needed);
                    if ($errMsg != "") {
                        $errMsg = writeP2(" $errMsg", 0);
                        $note = $errMsg;
                    }
                }
                $v = ""; // don't keep value of password fields.
            } else {
                $type = 'text';
                if (! $allow_null && $IsPostBack && $v == "") { $note = writeP2(" Cannot be empty", 0); }
            }

            $s .= "<td><input type=\"$type\" $readonly name=\"txt_$name\" width=\"50\" value=\"$v\"/>$note</td></tr>\n";

            if ( in_array($name, $this->cols_pwd) ) {
                $s .= "<tr><td>&nbsp;$name$star (repeat)&nbsp;</td>";
                $s .= "<td><input type=\"$type\" $readonly name=\"txt_2_$name\" width=\"50\" value=\"\"/>$note</td></tr>";
            }
        }

        $s = "<table>\n$s</table>\n<br/>\n";
        if ($verifyFirst) {
            $s .= "<input type=\"submit\" name=\"btnVerify\" value=\"Verify\" />";
        } else {
            $s .= "<input type=\"submit\" name=\"btnSubmit\" value=\"Submit\" />";
        }
        //$s .= "<input type=\"reset\" value=\"Reset\" />";
        if ($back_url != "") {
            $s .= "<input type=\"button\" value=\"Cancel Edit\" onclick=\"javascript: window.location.href='$back_url'\" />";
        }
        $s .= "<input type='hidden' name='IsPostBack' value='IsPostBack'>";

        return $s;
    }

    //
    // Show view instead of Edit form. Adapted from writeEditForm().
    //
    public function writeViewForm($id, $change_default = 0) {
        $query = "SELECT * FROM $this->tbl_name WHERE $this->tbl_pk = '$id'"; 
        //echo $query;
        $xml = $this->getDBTableAsXml($query);
        $record = new SimpleXMLElement($xml);
        $cols = $record->row[0];
        $schema = $this->getDBTableSchema();

        $s = "";
        foreach ($schema->xpath('//row') as $field) {
            // Pkey is autoincrement. Don't add to the new form.
            $name = $field->Field;
            if ($name == $this->tbl_pk) continue;

            $allow_null = ($field->Null != 'NO');
            //$star = $allow_null ? "" : " <font color='red'>*</font>";
            $star = ""; // don't use star in view form.

            $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star&nbsp;</td>";

            $v = db_htmlEncode( $cols->$name );

            if ( in_array($name, $this->cols_pwd) ) {
                $v = "********"; // isset( $_REQUEST["txt_$name"] ) ? $_REQUEST["txt_$name"] : '';
            }

            $s .= "<td>&nbsp; $v &nbsp;</td>";
        }

        $s = "<table border='1'>$s</table><br/><a href='?mode=edit'>Edit Content</a>";
        return $s;
    }


    private function getDBTableSchema() {
        //print $this->getDBTableAsHtmlTable("show columns from $this->tbl_name", "$this->tbl_name Columns", 0);
        $query = "show columns from $this->tbl_name";
        $xml = $this->getDBTableAsXml($query);
        return new SimpleXMLElement($xml);
    }

    // Output the form to add new entry.
    public function writeNewForm($change_default = 0, $back_url = '', $useCaptcha = 0, $useVerify = 1) {
        $schema = $this->getDBTableSchema();
        //print_r($record);
        $IsPostBack = (U_REQUEST('IsPostBack') != '');
        $s = "";
        foreach ($schema->xpath('//row') as $field) {
            // Pkey is autoincrement. Don't add to the new form.
            $name = $field->Field;
            $default = $field->Default;
            $unique  = ($field->Key == 'UNI');
            $allow_null = ($field->Null != 'NO');
            $star = $allow_null ? "" : " <font color='red'>*</font>";

            if ($name== $this->tbl_pk ||
                ($change_default == 0 && in_array($name, $this->cols_default))) continue;
            $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star&nbsp;</td>";
            $v = U_REQUEST("txt_$name") ? U_REQUEST("txt_$name") : $default;
            $v = db_htmlEncode($v);

            $errMsg = "";
            if ( in_array($name, $this->cols_pwd) ) {
                $type = "password";
                if ($IsPostBack) { 
                    $errMsg = $this->verify_pwd($name);
                    if ($errMsg != "") $errMsg = writeP2(" $errMsg", 0);
                }
                $v = ""; // don't keep posted back value for password field.
            } else {
                $type = "text";
                if (! $allow_null && $IsPostBack && $v == "") { $errMsg = writeP2(" Cannot be empty", 0); }
            }

            $s .= "<td><input type=\"$type\" name=\"txt_$name\" width=\"50\" value=\"$v\">$errMsg</td></tr>\n";

            if ( in_array($name, $this->cols_pwd) ) {
                $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star (repeat)&nbsp;</td>";
                $s .= "<td><input type=\"$type\" name=\"txt_2_$name\" width=\"50\" value=\"$v\"></td></tr>\n";
            }
        }

        if ($useCaptcha) {
            $errMsg = "";
            if (! $allow_null && $IsPostBack && $v == "") { $errMsg = writeP2(" Should match image code", 0); }
            $t = file_get_contents("func/captcha2.inc");
            $t = str_replace("#errMsg", $errMsg, $t);
            $s .= $t;
        }

        $s = "<table>$s</table><br/>";
        $btnName = $useVerify ? "btnVerify" : "btnSubmit";
        $s .= "<input type=\"submit\" name=\"$btnName\" value=\"Submit\">";
        $s .= "<input type=\"reset\" value=\"Reset\">";
        //$s .= "<input type='submit' name='btnClear' value='Clear Form'>";
        if ($back_url != "") {
            $s .= "<input type=\"button\" value=\"Cancel Add New\" onclick=\"javascript: window.location.href='$back_url'\" />";
        }
        $s .= "<input type='hidden' name='IsPostBack' value='IsPostBack'>";
        return $s;
    }

    public function writeVerifyForm($change_default = 0, $back_url = '') {
        $schema = $this->getDBTableSchema();
        //print_r($record);
        $s = "";
        foreach ($schema->xpath('//row') as $field) {
            // Pkey is autoincrement. Don't add to the new form.
            $name = $field->Field;
            $default = $field->Default;
            $unique  = ($field->Key == 'UNI');
            $allow_null = ($field->Null != 'NO');
            $star = $allow_null ? "" : " <font color='red'>*</font>";

            if ($name== $this->tbl_pk ||
                ($change_default == 0 && in_array($name, $this->cols_default))) continue;
            $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star&nbsp;</td>";
            $v = isset( $_REQUEST["txt_$name"] ) ? trim( $_REQUEST["txt_$name"] ) : $default;
            $v = db_htmlEncode($v);
            $h = "<input type=\"hidden\" name=\"txt_$name\" value=\"$v\" />";

            if ( in_array($name, $this->cols_pwd) ) {
                $s .= "<td>&nbsp;********&nbsp;$h</td></tr>\n";

                $v2 = isset( $_REQUEST["txt_2_$name"] ) ? trim( $_REQUEST["txt_2_$name"] ) : "";
                $v2 = db_htmlEncode($v);
                $h = "<input type=\"hidden\" name=\"txt_2_$name\" value=\"$v2\" />";
                $s .= "<tr><td>&nbsp;" . ucfirst($name) . "$star (repeat)&nbsp;</td>";
                $s .= "<td>&nbsp;********&nbsp;$h</td></tr>\n";
            }
            else {
                $s .= "<td>&nbsp;$v&nbsp;$h</td></tr>\n";
            }
        }
        $s = "<table>$s</table><br/>";
        $s .= "<input type=\"submit\" name=\"btnSubmit\" value=\"Submit\">";
        if ($back_url != "") {
            $s .= "<input type=\"submit\" value=\"Edit More\" />";
        }
        return $s;
    }

    //
    // if $pwd_needed is 0, don't verify it if both fields are empty.
    //
    public function verifyForm($change_default = 0, $useCaptcha = '0', $pwd_needed = '1') {
        $schema = $this->getDBTableSchema();
        $s = "";
        foreach ($schema->xpath('//row') as $field) {
            // Pkey is autoincrement. Don't add to the new form.
            $name = $field->Field;
            $default = $field->Default;
            $unique  = ($field->Key == 'UNI');
            $allow_null = ($field->Null != 'NO');

            if ($name== $this->tbl_pk ||
                ($change_default == 0 && in_array($name, $this->cols_default))) continue;

            $v = isset( $_REQUEST["txt_$name"] ) ? trim( $_REQUEST["txt_$name"] ) : $default;

            if ( in_array($name, $this->cols_pwd) ) {
                $v2 = isset( $_REQUEST["txt_2_$name"] ) ? trim( $_REQUEST["txt_2_$name"] ) : "";

                if ($v == '' && $v2 == '') { // if new password is empty, do not insert.
                    if ($pwd_needed) {
                        $s .= "Password cannot be empty.\n";
                    }
                }
                else if ($v != $v2) {
                    $s .= "The two entries for $name should match.\n";
                }
                else {
                    $e = $this->validate_password_rule($v);
                    if ($e != "") { $s .= "$e\n"; }
                }
            }
            else {
                if (! $allow_null && $v == '') {
                    $s .= "Field $name cannot be empty\n";
                }
            }
        }

        if ($useCaptcha) {
            //echo $_SESSION['captcha'] . " ?= " . $_POST['txtCaptcha'] . "<br/>";
            if ($_SESSION['captcha'] != $_POST['txtCaptcha']) {
                $s .= "The image code entered is not correct.\n";
            }
        }

        if ($s != '') {
            $s = str_replace("\n", "<br/>", $s);
            $s = "<font color='red'>Error:<br/>$s</font>";
        }

        return $s;
    }

    // $name: name of the column field.
    private function verify_pwd($name, $pwd_needed = '1') {
        $s = "";
        $v  = isset( $_REQUEST["txt_$name"] ) ? trim( $_REQUEST["txt_$name"] ) : "";
        $v2 = isset( $_REQUEST["txt_2_$name"] ) ? trim( $_REQUEST["txt_2_$name"] ) : "";
        if ($v == '' && $v2 == '') { // if new password is empty, do not insert.
            if ($pwd_needed) {
                $s = "Cannot be empty\n";
            }
        }
        else if ($v != $v2) {
            $s = "Two password entries should match\n";
        }
        else {
            $e = $this->validate_password_rule($v);
            if ($e != "") { $s = "$e\n"; }
        }
        return $s;
    }

    // $v1, $v2: value of the 2 password entries.
    private function verify_pwd_2($v, $v2) {
        $s = "";
        //$v2 = isset( $_REQUEST["txt_2_$name"] ) ? trim( $_REQUEST["txt_2_$name"] ) : "";
        if ($v == '' && $v2 == '') { // if new password is empty, do not insert.
            $s = "Password cannot be empty.\n";
        }
        else if ($v != $v2) {
            $s = "The two entries for $name should match.\n";
        }
        else {
            $e = $this->validate_password_rule($v);
            if ($e != "") { $s = "$e\n"; }
        }
        return $s;
    }

    public function writeNewForm2() {
        $cols = $this->getDBTableColumnsAsArray($this->tbl_name);
        $ct = count($cols);
        $s = "";
        for ($i = 0; $i < $ct; ++ $i) {
            // Pkey is autoincrement. Don't add to the new form.
            if ($cols[$i] == $this->tbl_pk ||
                in_array($cols[$i], $this->cols_default)) continue;
            $s .= "<tr><td>&nbsp;$cols[$i]&nbsp;</td>";
            $v = trim( $_REQUEST["txt_$cols[$i]"] );

            $type = in_array($cols[$i], $this->cols_pwd) ? 'password' : 'text';
            $s .= "<td><input type='$type' name='txt_$cols[$i]' width='50' value='$v'></td>";
        }
        $s = "<table>$s</table><br/>";
        $s .= "<input type='submit' name='btnSubmit' value='Submit'>";
        $s .= "<input type='reset' value='Reset'>";
        //$s .= "<input type='submit' name='btnClear' value='Clear Form'>";
        return $s;
    }


    private function validate_password_rule($v) {
        $s = "";
        if (strlen($v) < 8) {
             $s .= "Should contain at least 8 letters";
        }
        return $s;
    }

    //
    // $id - pkey of the entry to update.
    // $change_default - 1 if allow change default value, 0 otherwise.
    // 
    // If want to change value of default cols, set $change_default = 1 both here
    // and in function call to writeEditForm().
    //
    public function update($id, $change_default = 0) {
        $cols = $this->getDBTableColumnsAsArray($this->tbl_name);
        $ct = count($cols);
        $s = "";
        $v = "";
        for ($i = 0; $i < $ct; ++ $i) {
            // Pkey is autoincrement. Don't add to the new form.
            if ($cols[$i] == $this->tbl_pk ||
                ($change_default == 0 && in_array($cols[$i], $this->cols_default))) continue;
            $_s = $cols[$i];
            $_v = trim( $_REQUEST["txt_$cols[$i]"] );

            if ( in_array($cols[$i], $this->cols_pwd) ) {
                $_v2 = trim( $_REQUEST["txt_2_$cols[$i]"] );
                if ($_v == '' && $_v2 == '') continue; // if new password is empty, do not update.
                else if ($_v != $_v2) {
                    $msg = "<font color='red'>Error: The two entries for $cols[$i] should match.</font>";
                    return $msg; // break out of the loop and return.
                }
                else {
                    $e = $this->validate_password_rule($_v);
                    if ($e != "") {
                        $msg = "<font color='red'>Error: $e</font>";
                        return $msg;
                    }
                    else {
                        $_v = MD5($_v);
                    }
                }
            }

            $_v = db_encode( $_v );
            if ($s == "") {
                $s .= "$_s = $_v";
            } else {
                $s .= ", $_s = $_v";
            }
        }

        $query = "UPDATE $this->tbl_name SET $s WHERE $this->tbl_pk = $id";
        //echo "$query<br/>"; return "..Error.";

        try {
            $result = mysql_query($query);
            if (! $result) {
                $msg = "<font color='red'>Error: " . mysql_error() . "</font>";
            } else {
                $msg = "<font color='green'>New entry is successfully added</font>";
            }
        } catch (Exception $e) {
            $msg = "<font color='red'>Error: " . $e->getMessage() . "</font>";
        }

        return $msg;
    }

    public function insertNew($change_default = 0) {
        $cols = $this->getDBTableColumnsAsArray($this->tbl_name);
        $ct = count($cols);
        $s = "";
        $v = "";
        for ($i = 0; $i < $ct; ++ $i) {
            // Pkey is autoincrement. Don't add to the new form.
            if ($cols[$i] == $this->tbl_pk ||
                ($change_default == 0 && in_array($cols[$i], $this->cols_default))) continue;
            $_s = $cols[$i];
            $_v = trim( $_REQUEST["txt_$cols[$i]"] );
            //if ( in_array($cols[$i], $this->cols_pwd) ) $_v = MD5($_v);

            if ( in_array($cols[$i], $this->cols_pwd) ) {
                $_v2 = trim( $_REQUEST["txt_2_$cols[$i]"] );
                if ($_v == '' && $_v2 == '') { // if new password is empty, do not insert.
                    $msg = "<font color='red'>Error: Password cannot be empty.</font>";
                    return $msg; // break out of the loop and return.
                }
                else if ($_v != $_v2) {
                    $msg = "<font color='red'>Error: The two entries for $cols[$i] should match.</font>";
                    return $msg; // break out of the loop and return.
                }
                else {
                    $e = $this->validate_password_rule($_v);
                    if ($e != "") {
                        $msg = "<font color='red'>Error: $e</font>";
                        return $msg;
                    }
                    else {
                        $_v = MD5($_v);
                    }
                }
            }

            $_v = db_encode( $_v );
            if ($s == "") {
                $s .= $_s;
                $v .= $_v;
            }
            else {
                $s .= ", $_s";
                $v .= ", $_v";
            }
        }

        $query = "INSERT INTO $this->tbl_name ($s) VALUES ($v)";
        //echo "$query<br/>";

        try {
            $result = mysql_query($query);
            if (! $result) {
                $msg = "<font color='red'>Error: " . mysql_error() . "</font>";
            } else {
                $msg = "<font color='green'>New entry is successfully added</font>";
            }
        } catch (Exception $e) {
            $msg = "<font color='red'>Error: " . $e->getMessage() . "</font>";
        }

        return $msg;
    }

    public function deleteEntry($ID) {
        $query = "DELETE FROM $this->tbl_name WHERE $this->tbl_pk = '$ID'";
        try {
            $result = mysql_query($query);
            if (! $result) {
                $msg = "<font color='red'>Error: " . mysql_error() . "</font>";
            } else {
                $msg = "<font color='green'>Entry is successfully deleted</font>";
            }
        } catch (Exception $e) {
            $msg = "<font color='red'>Error: " . $e->getMessage() . "</font>";
        }
        return $msg;
    }

    public function manage_table($tbl_name, $title) {
        $query = "SELECT * FROM $tbl_name ORDER BY $this->tbl_pk ASC";
        //$query = "select ID, first_name, last_name, email, login, gid from $tbl_name";
        print $this->getDBTableAsHtmlTable($query, $title, 1);
    }

    //
    // Return table columns as an array
    //
    public function getTableColumns($tbl_name) {
        //print str_replace("<", "&lt;", getDBTableAsXml("show columns from $tbl_name"));
        //print_r( getDBTableColumns($tbl_name) );
        print $this->getDBTableAsHtmlTable("show columns from $tbl_name", "$tbl_name Columns", 0);
    }

//
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

//
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

//
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

    public function getDBTableAsHtmlTable($query, $title, $doManage) {
        global $link;
        $result = mysql_query($query, $link);
        if (! $result) {
            $this->doExit('Invalid query: ' . mysql_error());
        }

        $s = "";
        if (mysql_num_rows($result) == 0) {
            $s = "(no user yet)";
        } else {
            // Write header and first row.
            $info = mysql_fetch_array($result);
            $s .= $this->writeHdr($info, $doManage);
            $s .= $this->writeRow($info, $doManage);

            while ($info = mysql_fetch_array($result)) {
                //print_r($info); break;
                $s .= $this->writeRow($info, $doManage);
            }

            $addNew = ($doManage) ? " [ <a href='admin_tbl_add.php?tbl=$this->tbl_name'>Add New</a> ]" : "";

            $style = "border: 1px solid;";
            $s = "<h3>$title</h3>$addNew<br/><table border='1' style='$style'>$s</table>";
        }
        return $s;
    }

    private function writeHdr($a, $doManage) {
        $ct = count($a);
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

} // end of class.

?>
