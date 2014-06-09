<?php

$f = $_REQUEST['f'];
if ($f == "") {
   print "(no file specified)";
   return;
}

$path = "posts/$f";

$s = htmlspecialchars( file_get_contents($path) );
$s = str_replace("\n", "<br/>", $s);
$s = str_replace(" ", "&nbsp;", $s);

print $s;

?>
