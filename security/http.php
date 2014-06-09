<?php
require_once("../include/auth.php");
require_once("../include/auth_admin.php");

$page_title = "Site Admin";
?>

<?php include("../include/header.php"); ?>

<p>Spoofed HTTP Requests. p31.</p>

<form method="POST" action="">

<input type="hidden" name="IsPostBack" value="Y">
URL: <input type="text" name="_url" value="/xc/Ai/security/hi.txt">
Site: <input type="text" name="_site" value="cssauh.com">
<input type="submit" name="btnSubmit" value="Submit"><br/>

<?php

$http_response = '';

if ( isset($_POST['IsPostBack']) ) {
    $url  = trim( $_POST['_url']  ); 
    $site = trim( $_POST['_site'] );
} else {
    $url = "/xc/Ai/security/hi.txt";
    $site = "cssauh.com";
}

$fp = fsockopen("$site", 80);
fputs($fp, "GET $url HTTP/1.1\r\n");
fputs($fp, "Host: $site\r\n\r\n");

if ($fp) {
    echo "fsockopen(" . "$site" . ") ok.";
    while (! feof($fp)) 
    {
        echo ".";
        //$http_response .= fgets($fp, 128);
        $http_response .= fread($fp, 1024);
    }
} else {
    echo "fsockopen(" . "$site" . ") failed.";
}

fclose($fp);

echo "<br/>Below are contents read from spoofed HTTP Request:<HR/>";
echo nl2br(htmlentities($http_response, ENT_QUOTES, 'UTF-8'));

?>

</form>


<?php include("../include/footer.php"); ?>
