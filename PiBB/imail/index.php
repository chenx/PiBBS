<?php 
session_start(); 
include_once("../func/setting.php");
require_once("../func/email.php");
require_once("../func/util.php");
//$_LANG = "en"; // for testing purpose.
require_once("../func/db.php");
require_once("../bbs/bbs_func.php");
require_once("../bbs/bbs_terms_$_LANG.php");
require_once("terms_mail.php");
require_once("imail_func.php");

$page_title = "IMail";
$root_path = "..";

//$bbs_css = ! $is_mobile ? "bbs_mobile.css" : "bbs.css";
$custom_header = <<<EOF
<!--link type="text/css" rel="stylesheet" href="../css/$bbs_css" /-->
<script type="text/javascript" src="bbs_$_LANG.js"></script>
<script type="text/javascript" src="../func/ClsPage.js"></script>
EOF;

include_once("../theme/header.php"); 

$mbox_type = U_REQUEST('t');
if ($mbox_type == "") { $mbox_type = "inbox"; }

$msg = "";
$title = "";
$body = "";
$mail_to = U_REQUEST("to");
?>

<table width="100%" border="0"><tr><td align="center">

<?php 
if (! is_loggedIn()) {
    print "<p><b>$T_greeting</b></p>";
    writeLoginLink("../imail/");
}
else {
    writeEmailBoxTypes($mbox_type);
    getEmailList($_SESSION['ID']);
}
?>

</td></tr></table>


<?php include_once("../theme/footer.php");  ?>


<?php 
function getIMailCount($mbox_type) {
    $user_id = db_encode( $_SESSION['ID'] );
    $sql = "SELECT COUNT(*) FROM ";
    if ($mbox_type == "inbox") { 
        $sql .= "IMailRecv WHERE fk_recver_id = $user_id AND recv_state IN (6,7)";  
    }
    else if ($mbox_type == "draft") {  
        $sql .= "IMail WHERE fk_sender_id = $user_id AND send_state = 1"; 
    }
    else if ($mbox_type == "sent") {  
        $sql .= "IMail WHERE fk_sender_id = $user_id AND send_state = 2"; 
    }
    else if ($mbox_type == "trash") { 
        $sql1 = "$sql IMailRecv WHERE fk_recver_id = $user_id AND recv_state IN (16, 17)";
        $sql2 = "$sql IMail WHERE fk_sender_id = $user_id AND send_state IN (11, 12)"; 
        return executeScalar($sql1) + executeScalar($sql2);
    }
    else { return 0; }

    return executeScalar($sql);
}


function getEmailList($user_id) {
    global $mbox_type;

    db_open();
    if ($mbox_type == "draft") {
        print "<div class='iMailListTitle'>Drafts</div>";
        getEmailList_draft($user_id);
    }
    else if ($mbox_type == "sent") {
        print "<div class='iMailListTitle'>Sent Mail</div>";
        getEmailList_sent($user_id);
    }
    else if ($mbox_type == "inbox") {
        print "<div class='iMailListTitle'>Inbox</div>";
        getEmailList_inbox($user_id);
    } 
    else if ($mbox_type == "trash") {
        print "<div class='iMailListTitle'>Trash</div>";
        getEmailList_trash($user_id);
    }
    db_close();
}


function getEmailList_draft($user_id) {
    //print "<h3>Drafts</h3>";
    $uid = db_encode($user_id);
    $sql = <<<EOF
SELECT ID AS mailID, title AS Title, attachment, send_time AS `Time`, SUBSTR(recv_list, 1, 20) AS `Who`, 
       '1' AS state, 'send' AS type, '0' AS `replied`
FROM IMail M
WHERE fk_sender_id = $uid AND send_state = 1
ORDER BY send_time DESC
EOF;
    getIMailList($sql, "draft");
}


function getEmailList_sent($user_id) {
    //print "<h3>Sent Mail</h3>";
    $uid = db_encode($user_id);
    $sql = <<<EOF
SELECT ID AS mailID, title AS Title, attachment, send_time AS `Time`, SUBSTR(recv_list, 1, 20) AS `Who`, 
       '2' AS state, 'send' AS type, '0' AS `replied`
FROM IMail M
WHERE fk_sender_id = $uid AND send_state = 2
ORDER BY send_time DESC
EOF;
    getIMailList($sql, "sent");
}


function getEmailList_inbox($user_id) {
    //print "<h3>Inbox</h3>";
    $uid = db_encode($user_id);
    $sql = <<<EOF
SELECT M.ID AS mailID, M.title AS Title, M.attachment, S.recv_time AS `Time`, M.sender AS `Who`, S.recv_state AS state, 
       'recv' AS type, replied, M.fk_sender_id, S.fk_recver_id
FROM IMail M, IMailRecv S
WHERE M.ID = S.fk_mail_id AND S.fk_recver_id = $uid AND S.recv_state IN (6, 7)
ORDER BY S.recv_time DESC
EOF;
    getIMailList($sql, "inbox");
}


function getEmailList_trash($user_id) {
    //print "<h3>Trash</h3>";
    $uid = db_encode($user_id);
    $sql = <<<EOF
SELECT M.ID AS mailID, M.title AS Title, M.attachment, S.recv_time AS `Time`, M.sender AS `Who`, 
       S.recv_state AS state, 'recv' AS type, replied
FROM IMail M, IMailRecv S
WHERE M.ID = S.fk_mail_id AND S.fk_recver_id = $uid AND S.recv_state IN (16, 17)

UNION

SELECT ID AS mailID, title AS Title, attachment, send_time AS `Time`, SUBSTR(recv_list, 1, 20) AS `Who`, 
       send_state AS state, 'send' AS type, '0' AS `replied`
FROM IMail
WHERE fk_sender_id = $uid AND send_state IN (11, 12)

ORDER BY Time DESC
EOF;
    getIMailList($sql, "trash");
}


function getWhoTitle($src) {
    $t = "";
    if ($sr == "draft" || $src == "sent") $t = "To";
    else if ($src == "inbox") $t = "From";
    else if ($src == "trash") $t = "Who";
    return $t;
}


function getIMailList($sql, $src) {
//print $sql . "<br/>";
//return;
    global $mbox_type, $_LANG, $T_page, $T_threads;

    $PAGE_SIZE = 50;
    $cls_page = new ClsPage(getIMailCount($mbox_type), U_REQUEST_INT('p'), $PAGE_SIZE, 10, "p", $_LANG);
    //print "<div style='text-align:right;'>" . $cls_page->writeNavBar($T_page, $T_threads) . "</div>";

    $unread_mails = ""; //getUnReadEmails($_SESSION['ID']); // don't display it here now.
    $nav = "" . $cls_page->writeNavBar($T_page, $T_threads);
    $nav_bar = <<<EOF
<div class='nav'><div class='nav_left'>$unread_mails</div><div class='nav_right'>$nav</div></div>
EOF;
    print $nav_bar;

    $PAGE_START = $cls_page->getStart() - 1;
    $sql .= " LIMIT $PAGE_START, $PAGE_SIZE";

    $t = executeAssociateDataTable_2($sql);
    $ct = count($t);
    $s = "";

    if ($ct == 0) {
        print "<br/><div style='width:100%; text-align: center;'>(empty)</div>";
        return;
    }

    $title = $t[0]['Title'];
    $time = $t[0]['Time'];
    $who = getWhoTitle($src); // $t[0]['Who'];

    $s = <<<EOF
<tr>
<td><br/></td><td>$who</td><td>$title</td><td>$time</td>
</tr>
EOF;

    // navigative page number.
    $nav_page = U_REQUEST("p");
    $p_nav_page = ($nav_page != "") ? "&p=$nav_page" : "";

    for ($i = 1; $i < $ct; ++ $i) {
        $id = $t[$i]['mailID'];
        $title = $t[$i]['Title'];
        $time  = $t[$i]['Time'];
        $who   = $t[$i]['Who'];
        $state = $t[$i]['state'];
        $type = $t[$i]['type'];
        $replied = $t[$i]['replied'];
        $has_attachment = $t[$i]['attachment'] == "1" ? "<span title='Mail has attachment'><img src='../image/clip.png' style='height:16px; vertical-align:middle;' border='0'> </span>" : "";

        $membership = "";
        if ($src == "inbox") {
            $sender_uid = $t[$i]['fk_sender_id'];
            $recver_uid = $t[$i]['fk_recver_id'];
            $membership = getPrivateBoardMembershipById($sender_uid, $recver_uid);
            //print "$sender_uid, $recver_uid, $membership.";
            $membership2 = "<br/><font color='#999' style='font-size:8px;'>" . str_truncate($membership, 20) . "</font>";
        }

        if ($src == "trash" && $type == "send") { $who = "To: $who"; }
        $who = str_truncate($who, 20);

        $title = db_htmlencode( str_truncate($title, 99) );
        if ($state == "6") { $title = "<b>$title</b>"; $who = "<b>$who</b>"; }

        $src_tbl = "";
        if ($src == "trash") { $src_tbl = "&s=$state"; }

        $s_replied = ""; 
        if ($replied) { //$s_replied = " (replied)"; $title .= $s_replied; 
            $s_replied = "<img src='../image/replied.gif' border='0' style='vertical-align: middle;' title='Replied'>";
        }

        $no = $i + $PAGE_SIZE * $nav_page;

        $s .= <<<EOF
<tr>
<td>$no</td><td title="$membership">$who</td>
<td>$s_replied$has_attachment<a href='imail.php?t=$mbox_type&id=$id$src_tbl$p_nav_page'>$title</a>
<td>$time</td></td>
</tr>
EOF;
    }
    $s = "<table class=\"iMailList\">$s</table>";
    print $s;

}

?>

