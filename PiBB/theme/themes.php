<?php
//
// Define theme here. relevant css files are in ../css/$theme/
// Available themes: plain, blue.
//
require_once("../func/mobile.php");

// Public variables that every theme can use.
$cur_time = getCurrentTime();
$is_mobile = isMobile();

// Define a theme.
$theme = "blue";

$PAGE_DESCRIPTION = "CareerAssist Forum";
$PAGE_KEYWORDS = "CareerAssist, Forum";
$PAGE_TITLE = "CareerAssist Forum";

$homepage_news = "<font color='red'>News: <a href='../imail/' style='color:blue;'>Mailbox</a> is online</font>";
//$homepage_news = ""; // later will get news from database.
if ($homepage_news != "") $homepage_news = "<span class='homepage_note'>$homepage_news</span>";

// desktop div can show only on desktop, i.e., non-mobile platforms.
$forum_top_row = <<<EOF
<tr><td style='text-align:left; padding-left: 5px;'>
<div class="desktop"><a href="./"><img src="../image/blue_1000_150.gif" border="0"></a><br/><br/></div>
<span class="homepage_time">$cur_time</span>$homepage_news
<span style='display:block;'><a href="http://careerassist.org/">CareerAssist</a></span></td></tr>
EOF;

//$theme = "plain";


//
// Functions.
//

if (! isset($page_title) || $page_title == "" ) { $page_title = $PAGE_TITLE; }

function getThemeCss() {
    global $theme, $custom_header;
    $site_css = isMobile() ? "site_mobile.css" : "site.css";
    $style = <<<EOF
<link type="text/css" rel="stylesheet" href="../css/menu.css" />
<link type="text/css" rel="stylesheet" href="../css/$site_css" />
EOF;

    if (isset($custom_header)) { $style .= $custom_header; } 
    $style = str_replace("/css/", "/css/$theme/", $style);
    return $style;
}

function getCurrentTime() {
    global $T_currentTime;
    //return date("F j, Y, g:i a");
    return "$T_currentTime: " . date("D M j G:i:s T Y"); 
}

?>
