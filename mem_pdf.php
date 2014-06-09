<?php
//
// @By: X.C.
// @Created On: 4/21/2013
//

require_once("auth.php");
require_once("db.php");
require_once("func/util_fs.php");
require_once("lib/fpdf/fpdf.php"); // http://fpdf.org/

$page_title = "Member Home - PDF";
?>

<?php include("header.php"); ?>

<p><a href="home.php">Member Home</a> &gt; PDF</p>

<p>Articles under /post folder. Read to display on page, or created as a PDF file.</p>

<div id='divL' style="float: left; width: 250px; height: 100%; border: 0; background-color: #eeeeee; padding: 5px;">
<b>Article List:</b><br/>

<?php 
writeFileList(); 
?>

</div>

<div id='divR' style="float: left; margin-left: 15px; border: 0; padding: 5px;"></div>

<script type='text/javascript'>
function open_file(f) {
    $.post("get_post.php?f=" + f, function(data) { $("#divR").html(data); });
}
</script>

<?php include("footer.php"); ?>

<?php

function writeFileList() {
    $fs = getSubFilesAsArray("posts");
    $ct = count($fs);

    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $s .= "<li><a href=\"#\" onclick=\"javascript: open_file('$fs[$i]');\">$fs[$i]</a> [<a href='pdf.php?f=$fs[$i]'>PDF</a>]</li>";
    }

    if ($s == "") {
        $s = "(No file yet)";
    } else {
        $s = "<ol>$s</ol>";
    }
    print $s;
}

?>
