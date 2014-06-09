<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UTF-8</title>
</head>
<body>
Unicode table: <a href="http://www.tamasoft.co.jp/en/general-info/unicode.html">
http://www.tamasoft.co.jp/en/general-info/unicode.html</a><br/>

Chinese - Unicode converter: <a href="http://weber.ucsd.edu/~dkjordan/resources/unicodemaker.html">http://weber.ucsd.edu/~dkjordan/resources/unicodemaker.html</a></br>

&#8364;
&#209;
&#9999;

&#20013;&#22269;&#35805;

<form method="POST" action="">
<input type="text" name="txtName" value="<?php if (isset($_POST['txtName'])) echo $_POST['txtName'] ?>"/>
<input type="submit" name="btnSubmit"/>
</form>

<?php
if ( isset($_POST['btnSubmit']) ) {
    //showCN($_POST['txtName'], "/Library/Fonts/华文宋体.ttf");
    showCN($_POST['txtName'], "../func/华文宋体.ttf");
    echo "[" . $_POST['txtName'] . "]";
}
else {
    //showCN("hello world", "/Library/Fonts/Apple Chancery.ttf");
    //showFonts("This is way cool", "/Library/Fonts/");
    showCN("Hello world!", "../func/华文宋体.ttf");
}

// http://forums.phpfreaks.com/topic/20624-php-image-available-fonts/
function showFonts($text, $dir) {
	$fontInt = 1;
        //$dir = "/Library/Fonts/";
	foreach (glob($dir . "*.ttf") as $filename) {
		$im = @imagecreate(450, 70) or die("Cannot Initialize new GD image stream");
		$background_color = imagecolorallocate($im, 255, 255, 255);
		$text_color = imagecolorallocate($im, 0, 0, 0);
		$font = $filename;
		imagettftext($im, 30, 0, 10, 40, $text_color, $font, $text);
		$imgFile = "fonts/font" . $fontInt . ".png";
		imagepng($im, $imgFile);
		imagedestroy($im);
		echo "<p><img src=$imgFile><br />$filename</p>\n\n";
		$fontInt++;
	}
}

function showCN($text, $font) {
    $filename = "font_cn.png";
                $im = @imagecreate(450, 70) or die("Cannot Initialize new GD image stream");
                $background_color = imagecolorallocate($im, 255, 255, 255);
                $text_color = imagecolorallocate($im, 0, 0, 0);
                imagettftext($im, 30, 0, 10, 40, $text_color, $font, $text);
                $imgFile = "fonts/font_cn.png";
                imagepng($im, $imgFile);
                imagedestroy($im);
                echo "<p><img src=$imgFile><br />$filename</p>\n\n";
}
?>

</body>
</html>
