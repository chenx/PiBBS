<?php 
session_start(); 
include_once("../func/setting.php");
require_once("../func/email.php");
require_once("../func/util.php");
//$_LANG = "en"; // for testing purpose.
require_once("../func/db.php");
require_once("../bbs/bbs_func.php");
require_once("terms_mail.php");
require_once("imail_func.php");

$page_title = "IMail";
$root_path = "..";

$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";
$custom_header = <<<EOF
<link type="text/css" rel="stylesheet" href="../css/$bbs_css" />
EOF;

include_once("../theme/header.php"); 

$mbox_type = U_REQUEST('t');
if ($mbox_type == "") { $mbox_type = "inbox"; }

//$msg = "";
//$title = "";
//$body = "";
//$replied = U_REQUEST_INT('rep');
$mid = U_REQUEST_INT('id');
?>

<table width="100%" border="0"><tr><td align="center">
<!--p class="desktop"><br></p-->

<?php 
if (! is_loggedIn()) {
    print "<p><b>$T_greeting</b></p>";
    writeLoginLink("../imail/");
}
else if ($mid == '' || $mid == '0') {
    print "<p><b>Unknown mail</b></p>";
    header("Location: ./");
}
else {
    writeEmailBoxTypes($mbox_type); 
    writeMBoxType($mbox_type);
    db_open();
    getIMail($mid, $_SESSION['ID']);
    db_close();
}
?>

</td></tr></table>


<script type="text/javascript">
function delete_mail(id, state, action) {
    var msg;
    if (action == 'd') { 
        if (state <= 10) { msg = ('Are you sure to delete this mail?'); }
        else if (state > 10) { msg = ('Are you sure to permanently delete this mail?'); }
    }
    else if (action == 'u') { msg = ('Are you sure to un-delete this mail?'); }

    if (msg == '') { alert('Unknown action: ' + action + ', state: ' + state); return; }

    var c = confirm(msg);
    if (! c) { return; }

    $.post("delete_mail.php", { id: id, state: state, action: action }, function(data, status) {
        if (status == "success") {
            if ( data == 1 ) {
                var url = '';
                if (state == '1') { url = './?t=draft'; }
                else if (state == '2') { url = './?t=sent'; }
                else if (state == '6' || state == '7') { url = './?t=inbox'; }
                else if (state == '11' || state == '12' ||
                         state == '16' || state == '17') { url = './?t=trash'; }

                if (url != '') { 
                    window.location = url;
                } else { // should not happen.
                    alert('Unknown delete state. Please contact system administrator.' + data);
                }
            } else {
                alert('Delete mail failed. Please contact system administrator.' + data);
            }
            return 1;
        } else {
            return 1; // ok
        }
    }, 5);
}
</script>

<?php include_once("../theme/footer.php");  ?>


<?php 
function writeMBoxType($mbox_type) {
    if ($mbox_type == "inbox") { $s = "Inbox"; }
    else if ($mbox_type == "draft") { $s = "Draft"; }
    else if ($mbox_type == "sent") { $s = "Sent Mail"; }
    else if ($mbox_type == "trash") { $s = "Trash"; }
    else { $s = "Unknown"; }

    $s = "<div class='iMailListTitle'>$s</div>";
    print $s;
}


function getSql_IMailRecv($mid, $uid) {
    $sql = <<<EOF
SELECT S.ID AS sID, M.title, M.body, M.salt, S.recv_time AS send_time, M.sender,
       M.recv_list as recver, M.fk_sender_id, S.fk_recver_id, recv_state AS `state`, replied
FROM IMail M, IMailRecv S
WHERE M.ID = $mid AND M.ID = S.fk_mail_id AND S.fk_recver_id = $uid
EOF;
    return $sql;
}


function getSql_IMailSend($mid, $uid) {
    $sql = <<<EOF
SELECT ID AS sID, title, body, salt, send_time, sender,
       recv_list AS recver, fk_sender_id, '' AS `fk_recver_id`, send_state AS `state`, '0' AS `replied`
FROM IMail 
WHERE ID = $mid AND fk_sender_id = $uid
EOF;
    return $sql;
}


// 
// Return which side a state belongs to: IMailSend (draft, sent) or IMailRecv (new, read).
//
function getSrc($state) {
    $send_states = array('1', '2', '11', '12');
    $recv_states = array('6', '7', '16', '17');

    if (in_array($state, $send_states)) { return "send"; }
    else if (in_array($state, $recv_states)) { return "recv"; } 
    else { return ""; }
}


function getIMail($mail_id, $user_id) {
    global $mbox_type;
    $uid = db_encode($user_id);
    $mid = db_encode($mail_id);

    $state = U_REQUEST("s"); // useful only for trash mode.
    $src = getSrc($state);
    //print "state=$state, src= $src";

    if ($mbox_type == "inbox") {
        $src = "recv";
        $sql = getSql_IMailRecv($mid, $uid);
    }
    else if ($mbox_type == "sent") {
        $src = "send";
        $sql = getSql_IMailSend($mid, $uid);
    }
    else if ($mbox_type == "trash") {
        if ($src == "recv") { $sql = getSql_IMailRecv($mid, $uid); }
        else if ($src == "send") { $sql = getSql_IMailSend($mid, $uid); }
        else { return; }
    }
    else {
        return;
    }

    //print $sql;
    $t = executeAssociateDataTable_2($sql);
    $ct = count($t);
    if ($ct != 2) {
        print "Unknown mail";
        return;
    }

    $state_id = $t[1]['sID']; // ID in IMailSend or IMailRecv.
    $title = db_htmlencode( $t[1]['title'] );
    $body = db_htmlencode( $t[1]['body'] );
    $salt = $t[1]['salt'];
    $from = $t[1]['sender'];
    $to = $t[1]['recver'];
    $send_time = $t[1]['send_time'];
    $sender_uid = $t[1]['fk_sender_id'];
    $recver_uid = $t[1]['fk_recver_id']; // for getting membership notation.
    $state = $t[1]['state'];
    $replied = $t[1]['replied'];

    //$body .= "<br/><br/>uid = $uid, sender: $sender_uid, recver: $recver_uid, send_state: $send_state, recv_state: $recv_state<br/>";

    $tool_bar = "";
    if ($mbox_type == "inbox") { 
        if ($state == "6") { // new, unread mail.
            // now set this as read.
            $my_uid = db_encode( $_SESSION['ID'] );
            $read_time = db_encode( date('Y-m-d H:i:s', time()) );
            $read_ip = db_encode( $_SESSION['ip'] );
            $sql = <<<EOF
UPDATE IMailRecv 
SET recv_state = '7', read_time = $read_time, read_ip = $read_ip 
WHERE fk_recver_id = $my_uid AND fk_mail_id = $mid
EOF;
            //print $sql;
            executeScalar($sql);
        }

        $tool_bar = <<<EOF
[ <a href="compose.php?m=reply&src=recv&id=$mail_id&from=$from">Reply</a> ]
[ <a href="compose.php?m=forward&src=recv&id=$mail_id&from=$from">Forward</a> ]
[ <a href="#" onclick="javascript:delete_mail($mid,'7','d');">Delete</a> ]
EOF;
    }
    else if ($mbox_type == "sent") { // state is 2.
        $tool_bar = <<<EOF
[ <a href="compose.php?m=forward&src=send&id=$mail_id">Forward</a> ]
[ <a href="#" onclick="javascript:delete_mail($mid,'2','d');">Delete</a> ]
EOF;
    }
    else if ($mbox_type == "draft") {
        $tool_bar = <<<EOF
[ <a href="compose.php?m=edit&src=send&id=$mail_id">Edit Draft</a> ]
[ <a href="#" onclick="javascript:delete_mail($mid,'1','d');">Delete Draft</a> ]
EOF;
    }
    else if ($mbox_type == "trash") { // $src can be "recv" or "send".
        $tool_bar = <<<EOF
[ <a href="compose.php?m=reply&src=$src&id=$mail_id&from=$from">Reply</a> ]
[ <a href="compose.php?m=forward&src=$src&id=$mail_id&from=$from">Forward</a> ]
[ <a href="#" onclick="javascript:delete_mail($mid,'$state','u');">Un-delete</a> ]
[ <a href="#" onclick="javascript:delete_mail($mid,'$state','d');">Permanently Delete</a> ]
EOF;
    }

    $rep = $replied ? "<img src='../image/replied.gif' border='0' style='vertical-align:middle;' title='Replied'>" : "";
    $attachment = get_attachment($from, $mail_id, $salt); // in attachment.php
    $to_users = getUserListAsLinks($to);
    $membership = ($src == "recv") ? getPrivateBoardMembershipById($sender_uid, $recver_uid) : "";

    $back_link = "./?t=$mbox_type";
    $nav_page = U_REQUEST("p");
    if ($nav_page != "") { $back_link .= "&p=$nav_page"; }

    $body = wrapViewBody_imail($body);

    $s = <<<EOF
<table class="iMail">
<tr><td colspan="2" class="iMailToolbar">[ <a href="$back_link">Back</a> ] $tool_bar</td></tr>
<tr class="iMailTitle"><td>Title:</td><td>$rep$title</td></tr>
<tr class="iMailTitle"><td>From:</td><td><a href="../users/user.php?u=$from" class="bbs_user">$from</a> $membership</td></tr>
<tr class="iMailTitle"><td>To:</td><td>$to_users</td></tr>
<tr class="iMailTitle"><td>Sent time:</td><td>$send_time</td></tr>
<tr><td colspan="2">
$body
$attachment
</td></tr>
</table>
EOF;

    print $s;
}


function wrapViewBody_imail($s) {
    $s = "<pre class='bbs'>$s</pre>";
    $s = homecoxHtmlEncode( $s, "div_media_img_imail" );
    return $s;
}

?>

