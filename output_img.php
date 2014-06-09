<?php

$image_url = $_REQUEST['url'];
$imginfo = getimagesize($image_url);
header("Content-type: $imginfo[mime]");
readfile($image_url);

?>
