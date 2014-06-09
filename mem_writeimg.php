<?php
//
// @By: X.C.
// @Created On: 6/21/2013
//

require_once("auth.php");
require_once("db.php");
require_once("func/util_fs.php");
require_once("lib/fpdf/fpdf.php"); // http://fpdf.org/

$page_title = "Member Home - Write Image";

$url = "";
$url_color = "";
$url_default = "Enter Image Url";

$IsPostBack = isset( $_POST['IsPostBack'] ) ? 1 : 0;

if (isset( $_POST['btnSubmit'] )) {
    $url = ( $_POST['txtUrl'] );

    if ($url == $url_default) $url = "";
}
else {
    if (! $IsPostBack || isset( $_POST['btnClear'] )) {
        $url = $url_default;
        $url_color = "color: #999999;";
    }
}
?>

<?php include("header.php"); ?>

<p><a href="home.php">Member Home</a> &gt; Write image from URL directly (no cache on server)</p>

<p>This page also demonstrates: 1) write image from url (no cache on server), 2) the use of default shadowed text in textbox.</p>

<form method="post">
Image Url: <input type="text" name="txtUrl" id="txtUrl" value="<?php echo $url; ?>" size="80" onclick="javascript: f();" style="<?php echo $url_color;?>" />
<input type="submit" name="btnSubmit" value="Submit"/>
<input type="button" name="btnClear" value="Clear" onclick="javascript: do_clear();" />
<input type='hidden' name="IsPostBack" value="Y" />
</form>

<div id="divImg">
<?php
if ($IsPostBack && $url != "") 
{
    echo $url . "<br>";
    echo "<img src='output_img.php?url=$url'>";
}
?>
</div>

<script type="text/javascript">
function f() {
    var o = document.getElementById('txtUrl');
    if (o && o.value == '<?php echo $url_default; ?>') {
        o.value = '';
        o.style.color = '#000000';
    }
}

function do_clear() {
    document.getElementById("divImg").innerHTML = '';
    var o = document.getElementById("txtUrl");
    o.value = "<?php echo $url_default; ?>";
    o.style.color = '#999999';
}
</script>

<?php include("footer.php"); ?>
