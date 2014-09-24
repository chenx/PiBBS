<?php
include_once("$root_path/func/setting.php");
include_once("terms_menu_new.php");
require_once("$root_path/func/mobile.php");
require_once("$root_path/func/db.php");
require_once("$root_path/imail/imail_func.php");
?>

<table id="hdrBar">
<tr>
<td class="hdrBarLogo">&nbsp;<?php print $T_logo; ?></td>

<?php getUnreadIMail_menu(); ?>

<td style="align: right;">

<?php
$board_manage_link = "";
if (isABoardManager()) {
    $board_manage_link = <<<EOF
    <li class="topline"><a href="$root_path/boardmanage/" class="menulink">$T_manage_board</a></li>
EOF;
}


$admin_menu = "";
$admin_menu_sub = "";
if ( isset($_SESSION["role"]) && $_SESSION["role"] == "admin" ) {
    $admin_menu_sub = <<<EOF
<li><a href="$root_path/admin" class="menulink">$T_admin</a></li>
EOF;

    $admin_funcs = getAdminFuncs();
    $admin_menu = <<<EOF
<li class="desktop"><a href="$root_path/admin" class="menulink">$T_admin</a>$admin_funcs</li>
EOF;
}

$imail_menu = "";
if ($_USE_IMAIL) {
    $imail_menu = <<<EOF
    <li class="topline"><a href="$root_path/imail/" class="menulink">$T_imail</a></li>
EOF;
}    

if ( isLoggedIn() ) {
    $user_menu = <<<EOF
<li><a href="#" class="menulink">$_SESSION[username]</a>
    <ul>
    <li class="topline"><a href="$root_path/profile" class="menulink">$T_profile</a></li>
    <li class="topline"><a href="$root_path/profile/avatar.php" class="menulink">$T_avatar</a></li>
$imail_menu
$board_manage_link
$admin_menu_sub
    <li><a href="$root_path/logout" class="menulink">$T_logout</a></li>
    </ul>
</li>
EOF;
} else {
    $user_menu = <<<EOF
<li><a href="$root_path/login" class="menulink">$T_login</a>
    <ul>
    <li class="topline"><a href="$root_path/register" class="menulink">$T_register</a></li>
    <li><a href="$root_path/getpwd" class="menulink">$T_getpasswd</a></li>
    </ul>
</li>
EOF;
}

/*
$admin_menu = "";
if ( isset($_SESSION["role"]) && $_SESSION["role"] == "admin" ) {
    //$admin_funcs = getAdminFuncs();
    $admin_menu = <<<EOF
<li><a href="$root_path/admin" class="menulink">$T_admin</a>$admin_funcs</li>
EOF;
}
*/

/*
$home_menu = <<<EOF
<li><a href="$root_path/" class='menulink'>$T_home</a></li>
EOF;
*/
$home_menu = "";
if ( isLoggedIn() ) {
    $home_menu = <<<EOF
<ul>
  <li class="topline"><a href="$root_path/users" class="menulink">$T_user_list</a></li>
</ul>
EOF;
}

$contact_us = "";
if ($_USE_CONTACT_US) {
    $contact_us = <<<EOF
<li><a href="$root_path/contact" class="menulink">$T_contact_us</a></li>
EOF;
}

$s = <<<EOF
<div id='menuDIV' style="display: inline;">
<ul class='menu' id='menu' >
$user_menu
<!--
<li><a href="$root_path/about" class="menulink">$T_about</a>
<ul>
  <li class="topline"><a href="$root_path/help" class="menulink">$T_help</a></li>
  $contact_us
</ul>
-->
</li>
<li><a href="$root_path/bbs" class="menulink">$T_home</a>
$home_menu
</li>
$admin_menu
<li><a href="../" class="menulink">$T_homepage</a></li>
</ul>
</div>
<script type='text/javascript'> var menu=new menu.dd('menu');   menu.init('menu','menuhover');</script>
EOF;

    print $s;
?>

</td>
</tr>
</table>

<?php

function isLoggedIn() {
    return isset($_SESSION["username"]) && $_SESSION["username"] != "";
}

function isABoardManager() {
    return isset($_SESSION['bbs_role']) && $_SESSION['bbs_role'] != "";
}

function getAdminFuncs() {
    global $root_path;
    $s = <<<EOF
<ul class='menu2'>
  <li class="topline"><a href="$root_path/admin/admin_list.php?tbl=User" class="menulink2">User Table</a></li>
  <li><a href="$root_path/admin/admin_list.php?tbl=UserGroup" class="menulink">UserGroup Table</a></li>
  <li><a href="$root_path/admin/admin_list.php?tbl=code_register" class="menulink">Reg. Code Table</a></li>
  <li><a href="$root_path/admin/admin_list.php?tbl=BBS_BoardList" class="menulink">BBS Boards Table</a></li>
  <li><a href="$root_path/admin/admin_list.php?tbl=v_log_site" class="menulink2">Site Log View</a></li>
  <li><a href="$root_path/admin/backup_db.php" class="menulink2">Backup DB</a></li>
  <li><a href="$root_path/admin/gen_reg_code.php" class="menulink">Generate Reg. Code</a></li>
  <li><a href="$root_path/admin/report_reg_code.php" class="menulink">Reg. Code Report</a></li>
  <li><a href="$root_path/admin/report_site_activity.php" class="menulink">Site Activity Report</a></li>
</ul>
EOF;
    return $s;
}


function getUnreadIMail_menu() {
    if (isMobile()) { return; } // mobile is not wide enough to display here.

    global $_USE_IMAIL, $root_path;
    if ($_USE_IMAIL && isset($_SESSION['ID'])) {
        $unread_imail = getUnReadEmails($_SESSION['ID']);
        if ($unread_imail != "") {
            print "<td><div class='menu_unread_imail_note'>$unread_imail</div></td>";
        }
    }
}

?>
