<?php

//
// States of imails.
//
$_IMAIL_draft = 1; // in IMailSend
$_IMAIL_sent  = 2; // in IMailSend
$_IMAIL_new   = 6; // in IMailRecv
$_IMAIL_read  = 7; // in IMailRecv

$_IMAIL_del_draft = 11;
$_IMAIL_del_sent  = 12;
$_IMAIL_del_new   = 16;
$_IMAIL_del_read  = 17;

$_IMAIL_perm_del_draft = 21;
$_IMAIL_perm_del_sent  = 22;
$_IMAIL_perm_del_new   = 26;
$_IMAIL_perm_del_read  = 27;


function writeEmailBoxTypes($mbox_type) {
    global $T_imail;

    $s1 = $mbox_type == "compose" ? "imailbox_type_selected" : "imailbox_type";
    $s2 = $mbox_type == "inbox" ? "imailbox_type_selected" : "imailbox_type";
    $s3 = $mbox_type == "draft" ? "imailbox_type_selected" : "imailbox_type";
    $s4 = $mbox_type == "sent" ? "imailbox_type_selected" : "imailbox_type";
    $s5 = $mbox_type == "trash" ? "imailbox_type_selected" : "imailbox_type";

    $s = <<<EOF
<a href='./' style='text-decoration:none; font-weight:bold; color:white;' title="$T_imail">
<div class="imailbox_head">@$T_imail</div>
<div class="desktop" style="display:inline;"><img src="../image/stamp_128.png" border="0" class="imail_icon"></div>
</a>
<div title="Compose" class="$s1" style="ibackground-color:#ff6666;"><a href="compose.php" style="color:#ff0000;">Compose</a></div>
<div title="Inbox" class="$s2"><a href="./?t=inbox">Inbox</a></div>
<!--div title="Draft" class="$s3"><a href="./?t=draft">Draft</a></div-->
<div title="Sent Mail" class="$s4"><a href="./?t=sent">Sent Mail</a></div>
<div title="Trash" class="$s5"><a href="./?t=trash">Trash</a></div>
<br/><br/>
EOF;
    print $s;
}

//
// Given a string like: "user1; user2; ...",
// break it into a list of links.
//
function getUserListAsLinks($s) {
    $s = str_replace(";", ",", $s);
    $users = split(",", $s);
    $ct = count($users);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        if ($i > 0) { $s .= ", "; }
        $u = trim( $users[$i] );
        $s .= "<a href='../users/user.php?u=$u' class='bbs_user'>$u</a>";
    }
    return $s;
}


//
// return true if the given user is in this forum.
//
function userInPrivateForum($forum_id, $user_id) {
    $sql = "SELECT count(*) FROM BBS_PrivateMembership WHERE forum_id = "
           . db_encode($forum_id) . " AND user_id = " . db_encode($user_id);
    $ct = executeScalar($sql);
    return $ct > 0;
}


//
// Given a forum id, get all members in this forum as mail_to targets (exclude self).
//
function getPrivateForumMailTo($forum_id, $my_uid, $exclude_self=1) {
    if ($forum_id == "") return;
    $sql = "SELECT user_id FROM BBS_PrivateMembership WHERE forum_id = " . db_encode($forum_id);
    $t = executeDataTable($sql);
    $ct = count($t);
    if ($ct < 2) { return; }

    $s = "";
    for ($i = 1; $i < $ct; ++ $i) {
        if ($exclude_self && $my_uid == $t[$i][0]) { continue; } // ignore self
        if ($s != "") $s .= ", ";
        $sql = "SELECT login FROM User WHERE ID = " . db_encode( $t[$i][0] );
        $s .= executeScalar($sql);
    }
    return $s;
}


/*
//
// If both from and to persons belong to the same private boards, say so.
//
function getPrivateBoardMembership($from, $to) {
    $from_id = getUserIdFromLogin($from);
    $to_id = getUserIdFromLogin($to);
    return getPrivateBoardMembershipById($from_id, $to_id);
}


function getPrivateBoardMembershipById($from_id, $to_id) {
    if ($from_id == '' || $to_id == '') return "";
    if ($from_id == $to_id) return ""; // send to self, no need to know membership.

    $sql = <<<EOF
select distinct A.forum_id from
(select forum_id FROM BBS_PrivateMembership WHERE user_id = '$from_id') A
join
(select forum_id FROM BBS_PrivateMembership WHERE user_id = '$to_id') B
ON A.forum_id = B.forum_id
EOF;
    //print $sql;

    $t = executeDataTable($sql);
    $ct = count($t);
    $s = ""; //"$sql. $ct";
    for ($i = 1; $i < $ct; ++ $i) {
        if ($s != "") { $s .= ", "; }
        $s .= getForumNameFromId( $t[$i][0] );
    }
    if ($s != "") { $s = "($s)"; }
    return $s;
}


function getForumNameFromId($forum_id) {
    $sql = "SELECT title_en FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    return executeScalar($sql);
}

function getUserIdFromLogin($login) {
    $sql = "SELECT ID FROM User WHERE login = " . db_encode($login);
    return executeScalar($sql);
}
*/

function getUnReadEmails($user_id) {
    if (! isset($user_id) || $user_id == '') { return ""; }

    $sql = "SELECT count(*) FROM IMailRecv WHERE recv_state = '6' AND fk_recver_id = " . db_encode($user_id);
    $ct = executeScalar($sql);
    if ($ct == 0) { return ""; }
    else { 
        global $_LANG;
        if ($_LANG == "cn") {
            $s = "您有 <font color='red'>$ct</font> 封未读 <a href='../imail/' class='imail'>站内邮件</a>";
        }
        else {
            $s = ($ct > 1 ? "s" : "");
            $s = "You have <font color='red'>$ct</font> unread <a href='../imail/' class='imail'>new mail$s</a>";
        }
        return $s;
    }
}

?>
