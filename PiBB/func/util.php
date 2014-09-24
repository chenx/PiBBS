<?php

//
// http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
//

//
// Hanlde request variables that are not set.
// If not set, return default_val; else return trimmed value.
//
function U_REQUEST($v, $default_val = '') {
    if (! isset($_REQUEST[$v])) return $default_val;
    return trim( $_REQUEST[$v] );
}

function U_POST($v, $default_val = '') {
    if (! isset($_POST[$v])) return $default_val;
    return trim( $_POST[$v] );
}

function U_GET($v, $default_val = '') {
    if (! isset($_GET[$v])) return $default_val;
    return trim( $_GET[$v] );
}


//
// Get the given value as an integers. 
// If it is not an integer, return 0.
//
// This is for the security of getting url parameter: 
// when getting a param of int type, don't want to get url injection attack.
//
function U_REQUEST_INT($v) {
    $v = U_REQUEST($v);
    if (! myIsInt($v)) $v = 0;
    return $v;
}

//
// Determine if $x is an integer
// From: http://www.php.net/manual/en/function.is-int.php
//
function myIsInt ($x) {
    return (is_numeric($x) ? intval($x) == $x : false);
}


//
// Retrun a MB string, if its length is greater than size, cut to fit size.
//
function getMBStrMaxSize($s, $size=50, $encoding='utf-8') {
    //print "len($s): " . mb_strlen($s, $encoding) . "<br>";
    $len = mb_strlen($s, $encoding); 
    if ($len <= $size) return $s;
    return mb_substr($s, 0, $size); // . "...";
}

function getStrMaxSize($s, $size=50) {
    //print "len($s): " . mb_strlen($s, 'utf-8') . "<br>";
    $len = strlen($s);
    if ($len <= $size) return $s;
    return substr($s, 0, $size) . "...";
}

//
// Write a select dropdown list.
// @parameters:
//   - $options: array(val1, disp1,  val2, disp2,  ...).
//   - $submitFormId: submit form on change. If empty, don't submit.
//
function writeSelect($id, $name, $options, $title = '', $default_val = '', $submitFormId = '') {
    $selected_val = ((isset($_REQUEST[$name])) ? trim( $_REQUEST[$name] ) : $default_val);
    $s = "";
    for ($i = 0, $ct = count($options); $i < $ct; $i += 2) {
        $selected = ($selected_val == $options[$i]) ? " selected" : "";
        $s .= "<OPTION value='" . $options[$i]. "'$selected>" . $options[$i+1] . "</OPTION>";
    }
    $title = (($title == "") ? "" : " title='$title'");
    $submit = ( ($submitFormId == "") ? "" : " onChange='javascript: document.forms[$submitFormId].submit();'" );
    $s = "<SELECT id='$id' name='$name'$title$submit>$s</SELECT>";
    return $s;
}

//
// $val - input array of values.
// $title - an array of same length as $a, for display name. If empty, use $a.
//
function convertArrayToSelect($val, $id, $name, $title = '') {
     $s = "<SELECT id=\"$id\" name=\"$name\">";
     $s .= "<OPTION value=''>-- SELECT --</OPTION>";
     $ct = count($val);
     $use_title = (is_array($title) && count($title) == $ct);

     $isPostBack = isset($_REQUEST[$name]);

     for ($i = 0; $i < $ct; ++ $i) {
         $v = $val[$i];
         $t = $use_title ? $title[$i] : $val[$i];

         $selected = ($isPostBack && $_REQUEST[$name] == $v) ? " selected" : "";
         $s .= "<OPTION value=\"$v\"$selected>$t</OPTION>";
     }
     $s .= "</SELECT>";
     return $s;
}

function startsWith( $haystack, $needle ){
  return $needle === ''.substr( $haystack, 0, strlen( $needle )); // substr's false => empty string
}

function endsWith( $haystack, $needle ){
  $len = strlen( $needle );
  return $needle === ''.substr( $haystack, -$len, $len ); // ! len=0
}

function writeP( $msg, $good ) {
    $color = $good ? 'green' : 'red';
    return "<p><font color='$color'>$msg</font></p>";
}

function writeP2( $msg, $good ) {
    $color = $good ? 'green' : 'red';
    return "<font color='$color'>$msg</font>";
}

function U_star() {
    return "<font color='red'>*</font>";
}

function writeSpan($msg, $maxlen) {
    if (strLen($msg) > $maxlen) {
        $m = substr($msg, 1, $maxlen) . "...";
    } else {
        $m = $msg;
    }
    $s = "<span title='$msg'>$m</span>";
    return $s;
}

//
// Create a random string of length $len
// http://stackoverflow.com/questions/4356289/php-random-string-generator
//
// @parameter:
//    - $type: 1 (0-9-a-z-A-Z), 2 (a-zA-Z), 3 (A-Z), 4 (0-9)
//
function getRandStr($len, $type=1) {
    switch($type) {
        case 4:
            $characters = '0123456789';
            break;
        case 3:
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 2:
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 1:
        default:
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
    }
    $size = strlen($characters) - 1;
    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        $randomString .= $characters[rand(0, $size)];
    }
    return $randomString;   
}


function get_salt($len=10, $type=1) {
    return getRandStr($len, $type);
}


function str_truncate($s, $maxlen) {
    $len = strlen($s);
    if ($len > $maxlen) {
        $s = substr($s, 0, $maxlen - 3) . "...";
    }
    return $s;
}


function getFileSize($bytes, $style=1) {
    $s = "";
    if ($bytes < 1024) { $s = "$bytes bytes"; }
    else if ($bytes < 1048576) { $s = ( round($bytes / 1024.0, 1) ) . " KB"; }
    else { $s = ( round($bytes / 1048576.0, 2) ) . " MB"; }

    if ($style == 1) { $s = " <span style='color:#999;'>($s)</span>"; }

    return $s;
}


?>
