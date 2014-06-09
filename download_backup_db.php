<?php
// 
// http://www.richnetapps.com/the-right-way-to-handle-file-downloads-in-php/
// http://webdesign.about.com/od/php/ht/force_download.htm
//

require_once("auth.php");

$filename = $_REQUEST['f'];
$content_type = $_REQUEST['t'];
//
// content_type can be, e.g.:
//   application/pdf
//   text/plain
//

// sanitize path, only use name part, to avoid attack using relative path.
$path_parts = pathinfo($filename);
$filename  = $path_parts['basename'];
$path = "bak_db/$filename";

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-type: $content_type");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
readfile($path);
?>
