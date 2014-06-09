<?php

//
// http://www.webmasterworld.com/forum88/4590.htm
// http://www.php.net/manual/en/function.mysql-connect.php
//

require_once('conf/conf.php');

session_start(); 

if ( isset($_REQUEST["btnSubmit"]) ) {
    $username = trim( $_POST['username'] ); 
    $userpass = md5( trim( $_POST['userpass'] ) ); 

    $link = mysql_connect($host, $db_usr, $db_pwd);
    if (!$link) {
        doExit('Could not connect: ' . mysql_error());
    }
    //echo "connected successfully";
    mysql_select_db($db, $link);

    $sql = "select ID, gid from User where login='$username' and passwd='$userpass'"; 
    $result = mysql_query($sql, $link); 
    if (! $result) {
        doExit('Invalid query: ' . mysql_error());
    }

    if (mysql_num_rows($result)!= 1) { 
        $error = "Login failed"; 
    } else { 
        $_SESSION['username'] = "$username"; 
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR']; 
        //other data needed to navigate the site or to authenticate the user.
        $info = mysql_fetch_array($result);
        $_SESSION['role'] = getRole( $info['gid'] ); 
        $_SESSION['ID'] = $info['ID'];
        $error = "";
    }

    mysql_close($link);

    if ($error == "") {
        //header('Location: home.php');
        header('Location: ./');
        exit();
    }
}
else {
    $username = "";
    $error = "";
}

function doExit($msg) {
    die($msg . ". Please contact your system administrator.");
    exit();
}

function getRole($gid) {
    $role = "";
    switch ($gid) {
        case 0:
            $role = "admin";
            break;
        case 1:
            $role = "user";
            break;
        default:
            $role = "";
            break;
    }
    return $role;
}

?>

<?php
$page_title = "Home - Welcome";
?>

<?php include("header.php"); ?>

<center>
<p>Welcome to <font size='16px'>Ai</font>.</p>

<?php if (! isset($_SESSION['username'])) { ?>

<h3>Login</h3>
<form name="frmLogin" method="POST">

<table>
<tr>
<td>Account: </td>
<td><input type="text" name="username" value="<?php echo $username; ?>" /></td>
</tr>

<tr>
<td>Password: </td>
<td><input type="password" name="userpass" value="" /></td>
</tr>

<tr>
<td align="right" colspan="2">
<input type="submit" name="btnSubmit" value="Submit" />
</td>
</tr>
</table>

<?php
if ($error != "") {
    print "<p><font color='red'>$error</font></p>";
}
?>

<a href='register.php'>Register</a> | <a href='getpwd.php'>Forgot Password</a>

</form>

<?php } ?>

</center>

<?php include("footer.php"); ?>

