<?php

//
// http://wyden.com/web/php/basics-1/how-to-implement-captcha-with-php
//

session_start();

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Expires: 0");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

require_once("../func/util.php");

$w = 150;
$h = 20;

$im = ImageCreate($w, $h);
$white = ImageColorAllocate($im, 0xFF, 0xFF, 0xFF);
$black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
$blue =  ImageColorAllocate($im, 0x00, 0x00, 0xCC);
//ImageFilledRectangle($im, 50, 25, 120, 30, $black);

drawText($im, $w, $h, $black);
drawLine($im, $w, $h, $blue);
drawLine($im, $w, $h, $blue);

header('Content-Type: image/png');
ImagePNG($im);
ImageDestroy($im);
die();

//
// imagettftext() function:
// http://php.net/manual/en/function.imagettftext.php
// 
function drawText($im, $w, $h, $color) {
    $text = getRandStr_CN(6);
    $_SESSION['captcha'] = $text;
    $x = rand(0, $w * 0.60);
    $y = rand(0, $h * 0.25) + 10;

    //$font = imageloadfont("/Library/Fonts/华文宋体.ttf");
    //ImageString($im, $font, $x, $y, $text, $color); // this does not work.

    //$font = "/Library/Fonts/华文宋体.ttf";
    $font = "font_cn_song_ti.ttf";
    imagettftext($im, 10, 0, $x, $y, $color, $font, $text);
}

function drawLine($im, $w, $h, $color) {
    $x1 = rand(0, $w);
    $y1 = rand(0, $h);
    ImageLine($im, 0, $y1, $w, $y1, $color);
    ImageLine($im, $x1, 0, $x1, $h, $color);
}


// 
// Note that each chinese character uses 3 positions (of ascii code).
// 
function getRandStr_CN($len) {
    $characters = "华文宋体一二三四五六七八九十"; 

    //$size = strlen($characters) - 1;
    $size = strlen($characters)/3 - 1;

    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        //$randomString .= $characters[rand(0, $size)];
        $randomString .= substr($characters, rand(0, $size) * 3, 3);
    }
    return $randomString;   
}


?>
