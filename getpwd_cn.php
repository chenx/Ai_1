<?php

//
// Send email: http://php.net/manual/en/function.mail.php
//

require_once('db.php');
require_once('func/util.php');

session_start(); 

$page_title = "Retrieve Password";

$msg = "";
$login = U_POST('txtLogin');

if ($login != "") {
    if ($_SESSION['captcha'] != $_POST['txtCaptcha']) {
        $msg = writeP("The image code entered is not correct.", 0); 
    } else {
        db_open();
        $email = getScalar("SELECT email FROM User WHERE login = " . db_encode( $login ), "email");
        if ($email == "") {
            $msg = writeP("This login name is not found", 0);
        } else {
            $newPwd = updatePwd($login);
            $subj = "Your new password";
            $txt = "This is your new password: $newPwd";
            $headers = "From: webmaster@ai.com";

            try {
                mail($email, $subj, $txt, $headers);
          
                $loc = $_SERVER['PHP_SELF'] . "?ok";
                header("Location: $loc");
            } catch (Exception $e) {
                $msg = "Error: " . $e->getMessage();
            }
        }
        db_close();
    }
}

function updatePwd($login) {
    $s = "********";

    return $s;
}
?>

<?php include("header.php"); ?>

<center>

<h3>Retrieve Password</h3>

<?php 
if (isset($_REQUEST['ok'])) {
    print writeP('An email has been set to your email address.', 1);
} else {
?>

<form method="post">

<table border='0'>
<tr><td>Enter login name:<font color='red'>*</font></td><td colspan='2'><input type='text' id='txtLogin' name='txtLogin' value=''/></td></tr>

<tr>
<td>Enter code in image:<font color='red'>*</font></td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/></td>
<td>
<img id='imgCaptcha' src='func/captcha_cn.php' border='1' style='vertical-align: middle;' title='Captcha image' width='150' height='20'>
<img id='btnChange' src='images/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td colspan='2'>
<input type='button' name='btnSubmit' value='Send Me New Password' onclick='javascript: validate();'>
</td>
</tr>
</table>

</form>

<?php
}

if ($msg != "") {
    print $msg;
}
?>

<a href='./'>Home</a>

</center>

<script type='text/javascript'>
function validate() {
    //    if (document.getElementById('txtLogin').value.trim() == '' ||
    //        document.getElementById('txtCaptcha').value.trim() == '') {
    if ($.trim( $("#txtLogin").val() ) == '' || $.trim( $("#txtCaptcha").val() ) == "") {
        alert("Please enter both login name and image code.");
    } else {
        document.forms[0].submit();
    }
}

//
// Use this to avoid refresh the entire page.
//
function changeCaptcha(o) {
    // for firefox, it won't reload if the image src is the same. 
    // So trick it to think the url is differet by using a random variable.
    //$("#imgC").attr('src', "func/captcha_cn.php?" + Math.random());
    document.getElementById('imgCaptcha').src = "func/captcha_cn.php?" + Math.random();
}

</script>

<?php include("footer.php"); ?>

