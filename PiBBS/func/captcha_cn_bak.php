<?php
//
// http://wyden.com/web/php/basics-1/how-to-implement-captcha-with-php
//

session_start();

header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

require_once("../func/util.php");
require_once("../func/dict_cn.php");
include_once("../conf/conf.php");

$w = 180; //150. 180 - best for mobile.
$h = 30; //20;
$font_size = 14; // 10;

$im = ImageCreate($w, $h);
$white = ImageColorAllocate($im, 0xFF, 0xFF, 0xFF);
$black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
$blue =  ImageColorAllocate($im, 0x00, 0x00, 0xCC);
$green = ImageColorAllocate($im, 0x00, 0xCC, 0x00);
//ImageFilledRectangle($im, 50, 25, 120, 30, $black);

drawText($im, $w, $h, $black);
drawLine($im, $w, $h, $blue);
drawLine($im, $w, $h, $blue);
drawLine($im, $w, $h, $green);

header('Content-Type: image/png');
ImagePNG($im);
ImageDestroy($im);
die();

//
// imagettftext() function:
// http://php.net/manual/en/function.imagettftext.php
// 
function drawText($im, $w, $h, $color) {
    global $font_size, $_USE_CAPTCHA;

    $len = (isset($_USE_CAPTCHA)) ? $_USE_CAPTCHA : "2";
    $text = getRandStr_CN( $len );
    $_SESSION['captcha'] = $text;
    $x = rand(0, $w * 0.60) - $h/2; //10;
    $y = rand(0, $h * 0.25) + $h/2; //10;

    $font = "font_cn_song_ti.ttf";
    imagettftext($im, $font_size, 0, $x, $y, $color, $font, $text);
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
    global $dict_cn;
    $characters = str_replace("\n", "", $dict_cn);

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
