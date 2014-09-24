<?php 
session_start(); 
include_once("../func/setting.php");
require_once("../func/email.php");
require_once("../func/util.php");
require_once("../bbs/bbs_func.php");
require_once("../bbs/bbs_terms_$_LANG.php");
require_once("terms_mail.php");
require_once("attachment_func.php");
require_once("imail_func.php");

$page_title = "IMail";
$root_path = "..";

// watermark: https://code.google.com/p/jquery-watermark/
$is_mobile = isMobile();
$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";
$custom_header = <<<EOF
<!--link type="text/css" rel="stylesheet" href="../css/$bbs_css" /-->
<script type="text/javascript" src="../js/jquery.watermark.min.js"></script>
<script type="text/javascript">
function iframe_upload_autoResize(h){
    $('#iframe_upload').height(h + 20);
}
</script>
EOF;

include_once("../theme/header.php"); 

$msg = "";
$title = "";
$body = "";
$id = ""; // used by "reply" mode.

// Send group mail.
$forum_id = U_REQUEST_INT("f");
if ($forum_id != "0") {
    $my_uid = $_SESSION['ID'];
    if (! IsAdmin() && ! userInPrivateForum($forum_id, $my_uid)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    $mail_to = getPrivateForumMailTo($forum_id, $my_uid);
    header("Location: ?to=$mail_to"); // This allows edit mail to list. Otherwise always use entire group.
    exit;
}

$IsPostBack = (U_REQUEST("IsPostBack") != "") ? 1 : 0;
$mode = "compose"; // can be: compose, reply, forward.

if ($IsPostBack) {
    $mail_to = U_REQUEST("to");
    $title = trim(U_POST("title"));
    $mode = U_POST("mode");
} else if (U_REQUEST("m") != "") {
    $mode = U_REQUEST("m");
    if ($mode == "reply") {
        get_reply_params();
    } 
    else if ($mode == "forward") {
        get_forward_params();
    }
} else {
    $mail_to = U_REQUEST("to");
}
?>

<table width="100%" border="0"><tr><td align="center"> 

<?php 
if (! is_loggedIn()) {
    print "<p><b>$T_greeting</b></p>";
    writeLoginLink("../email/?to=$mail_to");
}
else {
    writeEmailBoxTypes("compose"); 
    $mail_from = $_SESSION['username'];
    if ($IsPostBack) {
        db_open();
        sendEmail($mail_to, $mail_from);
        db_close();
    }
    writeContactForm();
}
?>

</td></tr></table>


<script type="text/javascript">
function submitForm() {
    var mailto = $.trim($("#to").val());
    var title = $.trim($("#title").val());
    var body = $.trim($("#body").val());

    $("#e_mailto").html('');
    $("#e_title").html('');
    $("#e_body").html('');

    if (mailto == '') {
        $("#e_mailto").html('<?php print $T_no_empty; ?>');
        $("#to").focus();
    } else if (title == '') {
        $("#e_title").html('<?php print $T_no_empty; ?>');
        $("#title").focus();
    } else if (body == '') {
        $("#e_body").html('<?php print $T_no_empty; ?>');
        $("#body").focus();
    } else {
        //if ( confirm('Are you sure to submit?') ) 
        {
            $("#btnSubmit").attr("disabled", "disabled");
            document.forms[0].submit();
        }
    }
}

//
// Toggle help message for post.
// Called in bbs_terms_cn/en.php.
//
function toggle_help() {
    var m = document.getElementById("bbs_help_mode");
    var h = document.getElementById('bbs_help');
    if (h == null || m == null) return;
    if (m.innerHTML == '+') {
        h.style.display='block';
        m.innerHTML = '-';
    }
    else {
        h.style.display='none';
        m.innerHTML = '+';
    }
}

$(function () {
    $("#to").watermark("separate users by comma or semicolon");
});
</script>

<?php include_once("../theme/footer.php");  ?>


<?php 
function writeContactForm() {
    global $T_greeting, $T_title, $T_body, $T_from_name, $T_from_mail, $T_to_name, $T_to_mail,
           $T_submit, $T_email_sent, $T_email_error, $T_back, $T_home, $T_forum_help, $T_back;
    global $mail_to, $mail_from, $msg, $title, $body;
    //print "<img src=\"../image/contactus.jpg\" style=\"float:left; width: 347px;\" class=\"desktop\">";

    if (isset($_REQUEST['ok'])) {
        if ($msg == "") {
            $msg = "<font color='green'>$T_email_sent<br><!--br><a href='../imail'>$T_back IMail</a--></font>";
        } else {
            $msg = "<font color='red'>$T_email_error<br><br>$msg</font>";
        }
        print "<br/><br/><br/><br/>$msg";
    } else { 
        $to = db_htmlEncode($mail_to);
        $from = getUserListAsLinks($mail_from);
        $_title = db_htmlEncode($title);
        $_body = db_htmlEncode($body);

        $attachment = get_form_attachment_row("?mode=1&user=$mail_from");
        global $mode, $id;

        $s = <<<EOF
<center><p><b>$T_greeting</b></p></center>

<form method="post">
<table class="imailForm">
<tr><td>$T_to_name:
<font color="red">*</font>
<!--img src="../image/question.gif" border="0" style="vertical-align: middle;" title="Separate user names by ',' or ';'"-->
</td><td>
<input type="text" id="to" name="to" value="$to" class="imailTitle"/>
<br/><span id="e_mailto" style="color: red;"></span>
</td></tr>
<tr><td>$T_from_name: </td><td>$from</td></tr>
<tr><td>$T_title: <font color="red">*</font></td><td><input type="text" id="title" name="title" class="imailTitle" value="$_title" maxlength="256"><br>
        <span id="e_title" style="color: red;"></span></tr></td>
<tr><td>$T_body: <font color="red">*</font></td>
    <td><textarea id="body" name="body" class="imailBody">$_body</textarea><br>
        <span id="e_body" style="color: red;"></span></td></tr>

$attachment

<tr>
    <td align="center" colspan="2">
    <input type="button" id="btnSubmit" value="$T_submit" onclick="javascript:submitForm();" style="width:120px;">
    </td></tr>

<tr><td align="center" colspan="2">
<input type="hidden" id="IsPostBack" name="IsPostBack" value="Y"/>
<input type="hidden" id="id" name="id" value="$id"/>
<input type="hidden" id="mode" name="mode" value="$mode"/>
</td></tr>


<tr><td colspan="2">
$T_forum_help
</td></tr>

</table>

</form>

<p>[ <a href="javascript: history.go(-1);">$T_back</a> ]</p>

EOF;

        print $s;
    }
}


function sendEmail($mail_to, $mail_from) {
    global $mail_to, $mail_from, $title, $body;
    $msg = "";

    try {
        $body = trim(U_POST("body"));
        send_imail($mail_to, $title, $body, $mail_from, $signature);
        header("Location: ?ok");
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
        print "<font color=\"red\">$msg</font>";
    }
}


//
// @params:
//  - $mail_to: a list of users, delimited by "," or ";"
// @return:
//  if no error: an associative array containing the (username => user_id)
//      pairs of the receivers. Note duplicates will be ignored in this step.
//  else: a string containing description of the error.
//
function get_recver_list($mail_to) {
    $mail_to = str_replace(";", ",", $mail_to);
    $recvers = split(",", $mail_to);
    $ct = count($recvers);
    if ($ct == 0) { return "There is no valid user"; }
    
    $h = array();
    $msg = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $id = getUserIdFromLogin($recvers[$i]);
        //print ":$id  " . $recvers[$i] . "<br/>";
        if ($id != "") {
            $h[$recvers[$i]] = $id;
        }
        else {
            if ($msg != "") $msg .= ", ";
            $msg .= $recvers[$i];
        }
    }

    if ($msg != "") return "Invalid user: $msg";
    else return $h;
}


function send_imail($mail_to, $title, $body, $mail_from, $signature) {
    global $_DEBUG;

    $MAIL_TO_MAX_LEN = 10000;
    if (strlen($mail_to) > $MAIL_TO_MAX_LEN) { // This is the length of IMail.recv_list: varchar(10000) in database.
        throw new Exception("Receiver list max length ($MAIL_TO_MAX_LEN) reached. Please contact system administrator.");
        return;
    }

    // 1) get receiver list (name list and id list).
    $recvers = get_recver_list($mail_to);
    if (! is_array($recvers)) { throw new Exception($recvers); } // error message.

    $recv_list = "";
    $recv_id_list = "";
    $ct = 0;
    foreach ($recvers as $key => $val) {
        if ($ct > 0) {
            $recv_list .= ",";
            $recv_id_list .= ",";
        }
        $recv_list .= $key;
        $recv_id_list .= $val;
        ++ $ct;
        //print "$key -> $val<br/>";
    }
    //throw new Exception("ll");

    // 2) insert to table IMail.
    $title = db_encode($title);
    $body = db_encode($body);
    $salt0 = get_salt() ;
    $salt = db_encode( $salt0 );

    $fk_sender_id = db_encode( getUserIdFromLogin($mail_from) );
    $send_state = db_encode( "2" );
    $send_time = db_encode( date('Y-m-d H:i:s', time()) );
    $send_ip = db_encode( $_SESSION['ip'] );
    $fk_recver_id = db_encode( getUserIdFromLogin($mail_to) );
    //$recv_state = db_encode( "6" );

    $sender = db_encode($mail_from);
    $recver = db_encode($mail_to);

    $recv_list = db_encode($recv_list);
    $recv_id_list = db_encode($recv_id_list);

    $sql = <<<EOF
INSERT INTO IMail (
    title, body, salt,
    sender, fk_sender_id, send_state, send_time, send_ip,
    recv_list, recv_id_list
) VALUES (
    $title, $body, $salt,
    $sender, $fk_sender_id, $send_state, $send_time, $send_ip,
    $recv_list, $recv_id_list
)
EOF;
    $result = mysql_query($sql);
    if (! $result) {
        $msg = ($_DEBUG ? (mysql_error() . " $sql") : "Database error");
        throw new Exception($msg);
    }
    //executeNonQuery($sql);

    $sql = "SELECT LAST_INSERT_ID() FROM IMail";
    $mail_id = executeScalar($sql);
    //print "mail_id = $mail_id<br/>";
    //print $salt; exit;
    $fk_mail_id = db_encode($mail_id);


    // 3) Insert to table IMailRecv.
    foreach ($recvers as $key => $val) {
        sendIMailToRecver($fk_mail_id, $key, $val);
    }

    // 4) If is Reply, set "reply" to true.
    global $mode;
    if ($mode == "reply") {
        $id = db_encode( U_REQUEST_INT("id") );
        $my_uid = db_encode($_SESSION['ID']);
        $sql = <<<EOF
UPDATE IMailRecv SET replied = '1' WHERE fk_mail_id = $id AND fk_recver_id = $my_uid;
EOF;
        executeNonQuery($sql);
    }

    // 5) Move attachment.
    try {
        $has_attachment = insert_attachment($mail_from, $mail_id, $salt0); // in attachment.php
        if ($has_attachment) {
            $sql = "UPDATE IMail SET attachment = '1' WHERE ID = $fk_mail_id";
            executeNonQuery($sql);
        }
    } catch (Exception $e) {
        $msg .= $e->getMessage();
    }
}


function sendIMailToRecver($mail_id, $recver, $recver_id) {
    $time = db_encode( date('Y-m-d H:i:s', time()) );
    $recver = db_encode($recver);

    $sql = <<<EOF
INSERT INTO IMailRecv (
    fk_mail_id, recv_time, fk_recver_id, recver, recv_state
) VALUES (
    $mail_id, $time, $recver_id, $recver, '6'
)
EOF;
    //print $sql;
    executeNonQuery($sql);
}


function get_reply_params() {
    global $title, $body, $mail_to, $id;
    $src = U_REQUEST("src");
    $mail_id = U_REQUEST_INT("id");
    $id = $mail_id; // to pass this parameter on submit, to update IMailRecv 'replied' field.
    if ($src == "" || $mail_id == "0") return;

    // Get receiver list and time.
    $my_uid = db_encode( $_SESSION['ID'] );
    if ($src == "send") {
        $sql = <<<EOF
SELECT title, body, send_time AS `time`, recv_list
FROM IMail
WHERE ID = $mail_id AND fk_sender_id = $my_uid
EOF;
    } else if ($src == "recv") {
        $sql = <<<EOF
SELECT M.title, M.body, S.recv_time AS `time`, M.recv_list
FROM IMailRecv S, IMail M
WHERE M.ID = S.fk_mail_id AND fk_mail_id = $mail_id AND fk_recver_id = $my_uid
EOF;
    }
    $t = executeAssociateDataTable_2($sql);
    if (count($t) < 2) return;

    $mail_to = U_REQUEST("from");

    $time = $t[1]['time'];
    $recv_list = $t[1]['recv_list'];
    $title = $t[1]['title'];
    if (! startsWith($title, "Re:")) { $title = "Re: $title"; }
    $body = prepare_body( $t[1]['body'] );
    $body = <<<EOF



On $time, $mail_to wrote:
$body
EOF;

}


function prepare_body($s) {
    $a = explode("\n", $s);

    $t = "";
    //$len = min(5, count($a));
    $len = count($a);
    for ($i = 0; $i < $len; ++ $i) {
        $t .= ": $a[$i]\n";
    }

    return $t;
}


function get_forward_params() {
    global $title, $body;
    $src = U_REQUEST("src");
    $mail_id = db_encode( U_REQUEST_INT("id") );
    if ($src == "" || $mail_id == "0") return;

    // Get receiver list and time.
    $my_uid = db_encode( $_SESSION['ID'] );
    if ($src == "send") {
        $sql = <<<EOF
SELECT title, body, send_time AS `time`, recv_list 
FROM IMail 
WHERE ID = $mail_id AND fk_sender_id = $my_uid
EOF;
    } else if ($src == "recv") {
        $sql = <<<EOF
SELECT M.title, M.body, S.recv_time AS `time`, M.recv_list 
FROM IMailRecv S, IMail M
WHERE M.ID = S.fk_mail_id AND fk_mail_id = $mail_id AND fk_recver_id = $my_uid
EOF;
    }
    $t = executeAssociateDataTable_2($sql);

    // Get title and body.
    //$sql = "SELECT title, body FROM IMail WHERE ID = $mail_id"; 
    //$t = executeAssociateDataTable_2($sql);
    if (count($t) < 2) return;

    $from = U_REQUEST("from");

    $time = $t[1]['time'];
    $recv_list = $t[1]['recv_list'];
    $title = $t[1]['title'];
    $title_orig = $title;
    if (! startsWith($title, "Fwd:")) { $title = "Fwd: $title"; }
    $body = $t[1]['body'];
    $body = <<<EOF



---------- Forwarded message ----------
From: $from
Date: $time
Title: $title_orig
To: $recv_list

$body
EOF;
}

?>

