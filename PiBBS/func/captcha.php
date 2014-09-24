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
include_once("../conf/conf.php");

$w = 180; // 150; 180 - best for mobile.
$h = 30;  //20;
$font_size = 14; // 10;

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

function drawText($im, $w, $h, $color) {
    global $font_size, $_USE_CAPTCHA;

    $len = (isset($_USE_CAPTCHA)) ? $_USE_CAPTCHA : "6";
    $text = getRandStr(6, 3); // getRandStr(len, type) is defined in util.php
    $_SESSION['captcha'] = $text;
    //$x = rand(0, $w - 50);
    //$y = rand(0, $h - 15);
    $x = rand(0, $w * 0.60);
    $y = rand(0, $h * 0.25);
    ImageString($im, $font_size, $x, $y, $text, $color);
}

function drawLine($im, $w, $h, $color) {
    $x1 = rand(0, $w);
    $y1 = rand(0, $h);
    ImageLine($im, 0, $y1, $w, $y1, $color);
    ImageLine($im, $x1, 0, $x1, $h, $color);
}
?>
