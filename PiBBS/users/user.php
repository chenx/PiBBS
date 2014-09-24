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
require_once("../bbs/bbs_terms_$_LANG.php");
include_once("../func/avatar.php");
require_once("../func/mobile.php");
include_once("../users/terms_users.php");

$page_title = "User";

$is_mobile = isMobile();
$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";
$custom_header = <<<EOF
<link type="text/css" rel="stylesheet" href="../css/$bbs_css" />
<!--[if IE]><script type="text/javascript" src="bbs_ie.js"></script><![endif]-->
EOF;
?>

<?php include("../theme/header.php"); ?>

<center>

<p class="desktop"><br></p>

<h3><?php echo $T_userinfo; ?></h3>

<?php
showUserInfo( U_REQUEST('u') );
?>

<p><a href="javascript: window.history.back();"><?php print $T_back; ?></a></p>

<?php
include("../theme/footer.php");


function showUserInfo($user_name) {
    global $T_username, $T_note, $T_bbs_score, $T_bbs_new_posts, $T_bbs_reply_posts,
           $T_bbs_mark_posts, $T_bbs_digest_posts, $T_send_email, $T_send_imail;

    db_open();
    $query = "SELECT login, email, note, gid, bbs_score, bbs_new_count, bbs_reply_count, " .
             "bbs_mark_count, bbs_digest_count, money FROM User WHERE login = " . db_encode($user_name);
    //echo $query;
    $ret = executeAssociateDataTable($query);
    db_close();

    //print "<br>"; print_r($ret);

    $s = "";
    foreach ($ret as $key => $val) {
        $s =   "<tr><td>$T_username:</td><td>$val[login]</td></tr>"
             /*. "<tr><td>User Type:</td><td>" . getUserType($val["gid"]) . "</td></tr>"*/
             . "<tr><td>$T_note:</td><td>$val[note]</td></tr>"
             . "<tr><td>$T_bbs_score:</td><td>$val[bbs_score]</td></tr>"
             . "<tr><td>$T_bbs_new_posts:</td><td>$val[bbs_new_count]</td></tr>"
             . "<tr><td>$T_bbs_reply_posts:</td><td>$val[bbs_reply_count]</td></tr>"
             . "<tr><td>$T_bbs_mark_posts:</td><td>$val[bbs_mark_count]</td></tr>"
             . "<tr><td>$T_bbs_digest_posts:</td><td>$val[bbs_digest_count]</td></tr>"
             /*. "<tr><td>Money:</td><td>$val[money]</td></tr>"*/
             ;
    }

    // get icon for user.
    $icon = get_avatar($val['email']); 

    $s = "<br><img src='$icon' width='80'><br><br><table id=\"bbs_user\" class=\"bbs_user\">$s</table>";

    $mail_link = "<p><br/><a href=\"../email/?to=$user_name\"><img src='../image/email.gif' title='$T_send_email' border='0'></a> <a href=\"../imail/compose.php?to=$user_name\"><img src='../image/imail.png' title='$T_send_imail' border='0'></a></p>";
    $s .= $mail_link;

    print $s;
}

function getUserType($gid) {
    $type = "";
    if ($gid == "0") $type = "Admin";
    else if ($gid == "1") $type = "User";
    //else if ($gid == "2") $type = "Board Manager";
    
    return $type;
}

?>
