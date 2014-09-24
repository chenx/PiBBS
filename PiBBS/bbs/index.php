<?php
session_start();

require_once("../func/db.php");
if ($_BBS_AUTH_USER_ONLY) {
    require_once("../func/auth.php");
}
if ($_BBS_ADMIN_ONLY) {
    require_once("../func/auth_admin.php");
}
require_once("../func/Cls_DBTable_Custom.php");
require_once("bbs_terms_$_LANG.php");
require_once("bbs_func.php");

$root_path = "..";
$page_title = ""; 

$is_mobile = isMobile();
$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";

$custom_header = <<<EOF
<link type="text/css" rel="stylesheet" href="../css/$bbs_css" />
<link type="text/css" rel="stylesheet" href="../css/digest.css" />
<!--[if IE]><script type="text/javascript" src="bbs_ie.js"></script><![endif]-->
EOF;
?>

<?php include("../theme/header.php"); ?>

<center>

<table class="bbs_box_index">
<?php print $forum_top_row; ?>
<tr>
<td class="bbs_board_list_left"><br></td>

<td align="center" class="bbs_board_list_mid"> 
<!--<h3><?php echo $T_forumList; ?></h3>-->
<?php showForums(); ?> </td>

<td class="bbs_board_list_right"><br></td>
</tr>
</table>

<p><br/></p>
<?php
if ($_USE_BBS) { 
    include_once("bbs_digest_func.php");
    get_bbs_digests(); 
}
?>

<?php
include("../theme/footer.php");
?>
