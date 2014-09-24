<?php
include_once("$root_path/func/setting.php");

$_LANG = "cn";
if ($_LANG == "cn") {
    $T_welcome = "欢迎访问&Pi;";
    $T_login_to_use_oj = "使用OJ前请先登";
    $T_profile = "个人资料";
    $T_admin = "网站管理";
    $T_home = "主页";
    $T_about = "关于";
    $T_help = "帮助";
    $T_logout = "退出";
} else {
    $T_welcome = "Welcome to &Pi;";
    $T_login_to_use_oj = "Please login to use OJ";
    $T_profile = "Profile";
    $T_admin = "Site Admin";
    $T_home = "Home";
    $T_about = "About";
    $T_help = "Help";
    $T_logout = "Logout";
}
?>

<table border="0" width="100%" id="hdrBar">
<tr style="height:20px;">
<td style="width: 1px; white-space:nowrap;">
&nbsp;<?php print $T_welcome; ?><?php if ( isset($_SESSION["username"]) ) { ?>，<?php echo $_SESSION["username"]; ?><?php } ?>
</td>

<?php if (! isset($_SESSION["username"]) && preg_match("/oj\/index.php/i", $_SERVER['PHP_SELF']) ) { ?>
<td>
<marquee behavior="scroll" direction="left"><font color="black"><?php print $T_login_to_use_oj; ?></font></marquee>
</td>
<?php } ?>

<td align="right" style="width: 1px; white-space:nowrap;">

<?php
if ( isset($_SESSION["username"]) ) {
    print "<a href=\"$root_path/profile\" class=\"menu\">$T_profile</a> | ";
}

if ( isset($_SESSION["role"]) && $_SESSION["role"] == "admin" ) {
    print "<a href=\"$root_path/admin\" class=\"menu\">$T_admin</a> | ";
} 

$strMenu = <<<EOF
<a href="$root_path/" class="menu">$T_home</a>  
 | <a href="$root_path/oj" class="menu">OJ</a> 
 | <a href="$root_path/about" class="menu">$T_about</a> 
 | <a href="$root_path/help" class="menu">$T_help</a> 
EOF;

print $strMenu;

if ( isset($_SESSION["username"]) ) {
  print " | <a href=\"$root_path/logout\" class=\"menu\">$T_logout</a>"; 
}
?>

&nbsp;
</td>
</tr>
</table>

