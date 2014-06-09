<?php
//
// @By: X.C.
// @Created On: 6/21/2013
//

require_once("auth.php");
require_once("db.php");
require_once("func/util_fs.php");
require_once("lib/fpdf/fpdf.php"); // http://fpdf.org/

$page_title = "Member Home";
?>

<?php include("header.php"); ?>

<p><a href="home.php">Member Home</a> &gt; Paypal</p>

<p>1. Direct payment (single transaction), using paypal or credit card. 
Very simple, but user is redirected to paypal's payment page, and disrupts process flow.</p>

Price: $1.99

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top_p">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<p>2. <a href="https://developer.paypal.com/webapps/developer/docs/classic/express-checkout/ht_ec-basicDigGoodsPayment-curl-etc/">Express checkout</a></p> Need to setup a business account first.



<?php include("footer.php"); ?>
