<?php

//
// http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
//

//
// Hanlde request variables that are not set.
// If not set, return default_val; else return trimmed value.
//
function U_REQUEST($v, $default_val = '') {
    if (! isset($_REQUEST[$v])) return $default_val;
    return trim( $_REQUEST[$v] );
}

function U_POST($v, $default_val = '') {
    if (! isset($_POST[$v])) return $default_val;
    return trim( $_POST[$v] );
}

function U_GET($v, $default_val = '') {
    if (! isset($_GET[$v])) return $default_val;
    return trim( $_GET[$v] );
}

//
// Write a select dropdown list.
// @parameters:
//   - $options: array(val1, disp1,  val2, disp2,  ...).
//   - $submitFormId: submit form on change. If empty, don't submit.
//
function writeSelect($id, $name, $options, $title = '', $default_val = '', $submitFormId = '') {
    $selected_val = ((isset($_REQUEST[$name])) ? trim( $_REQUEST[$name] ) : $default_val);
    $s = "";
    for ($i = 0, $ct = count($options); $i < $ct; $i += 2) {
        $selected = ($selected_val == $options[$i]) ? " selected" : "";
        $s .= "<OPTION value='" . $options[$i]. "'$selected>" . $options[$i+1] . "</OPTION>";
    }
    $title = (($title == "") ? "" : " title='$title'");
    $submit = ( ($submitFormId == "") ? "" : " onChange='javascript: document.forms[$submitFormId].submit();'" );
    $s = "<SELECT id='$id' name='$name'$title$submit>$s</SELECT>";
    return $s;
}

//
// $val - input array of values.
// $title - an array of same length as $a, for display name. If empty, use $a.
//
function convertArrayToSelect($val, $id, $name, $title = '') {
     $s = "<SELECT id=\"$id\" name=\"$name\">";
     $s .= "<OPTION value=''>-- SELECT --</OPTION>";
     $ct = count($val);
     $use_title = (is_array($title) && count($title) == $ct);

     $isPostBack = isset($_REQUEST[$name]);

     for ($i = 0; $i < $ct; ++ $i) {
         $v = $val[$i];
         $t = $use_title ? $title[$i] : $val[$i];

         $selected = ($isPostBack && $_REQUEST[$name] == $v) ? " selected" : "";
         $s .= "<OPTION value=\"$v\"$selected>$t</OPTION>";
     }
     $s .= "</SELECT>";
     return $s;
}

function startsWith( $haystack, $needle ){
  return $needle === ''.substr( $haystack, 0, strlen( $needle )); // substr's false => empty string
}

function endsWith( $haystack, $needle ){
  $len = strlen( $needle );
  return $needle === ''.substr( $haystack, -$len, $len ); // ! len=0
}

function writeP( $msg, $good ) {
    $color = $good ? 'green' : 'red';
    return "<p><font color='$color'>$msg</font></p>";
}

function writeP2( $msg, $good ) {
    $color = $good ? 'green' : 'red';
    return "<font color='$color'>$msg</font>";
}

function U_star() {
    return "<font color='red'>*</font>";
}

function writeSpan($msg, $maxlen) {
    if (strLen($msg) > $maxlen) {
        $m = substr($msg, 1, $maxlen) . "...";
    } else {
        $m = $msg;
    }
    $s = "<span title='$msg'>$m</span>";
    return $s;
}

//
// Create a random string of length $len
// http://stackoverflow.com/questions/4356289/php-random-string-generator
//
// @parameter:
//    - $type: 1 (0-9-a-z-A-Z), 2 (a-zA-Z), 3 (A-Z), 4 (0-9)
//
function getRandStr($len, $type) {
    switch($type) {
        case 4:
            $characters = '0123456789';
            break;
        case 3:
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 2:
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 1:
        default:
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
    }
    $size = strlen($characters) - 1;
    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        $randomString .= $characters[rand(0, $size)];
    }
    return $randomString;   
}

?>
