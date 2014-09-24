<?php 
session_start(); 
include_once("../func/setting.php");
require_once("../func/email.php");
require_once("../func/util.php");
require_once("../bbs/bbs_func.php");
require_once("terms_mail.php");

$page_title = "Mail";
$root_path = "..";

$is_mobile = isMobile();
$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";
$custom_header = <<<EOF
<link type="text/css" rel="stylesheet" href="../css/$bbs_css" />
<script type="text/javascript" src="../js/jquery.watermark.min.js"></script>
EOF;

include_once("../theme/header.php"); 

$msg = "";
$title = "";
$body = "";

// Send group mail.
$forum_id = U_REQUEST_INT("f");
if ($forum_id != "0") {
    $my_uid = $_SESSION['ID'];
    if (! IsAdmin() && ! userInPrivateForum($forum_id, $my_uid)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    $mail_to = getPrivateForumMailTo($forum_id, $my_uid);
    header("Location: ?to=$mail_to"); 
    exit;
}

$mail_to = U_REQUEST("to");
$IsPostBack = (U_REQUEST("IsPostBack") != "") ? 1 : 0;
?>

<table width="100%" border="0"><tr><td class="emailForm"> 
<p class="desktop"><br></p>

<?php 
if (! is_loggedIn()) {
    print "<p><b>$T_greeting</b></p>";
    writeLoginLink("../email/?to=$mail_to");
}
else {
    $mail_from = $_SESSION['username'];
    if ($IsPostBack) {
        sendEmail();
    }
    writeContactForm(); // If sendEmail() worked, will redirect before this.
}
?>

</td></tr></table>

<script type="text/javascript">
function submitForm() {
    var to = $.trim($("#to").val());
    var title = $.trim($("#title").val());
    var body = $.trim($("#body").val());

    $("#e_to").html('');
    $("#e_title").html('');
    $("#e_body").html('');

    if (to == '') {
        $("#e_to").html('<?php print $T_no_empty; ?>');
        $("#to").focus();
    } else if (title == '') {
        $("#e_title").html('<?php print $T_no_empty; ?>');
        $("#title").focus();
    } else if (body == '') {
        $("#e_body").html('<?php print $T_no_empty; ?>');
        $("#body").focus();
    } else {
        $("#btnSubmit").attr("disabled", "disabled");
        document.forms[0].submit();
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
           $T_submit, $T_email_sent, $T_email_error, $T_back, $T_home;
    global $mail_to, $mail_from, $msg, $_USE_USER_EMAIL, $title, $body;
    print "<img src=\"../image/contactus.jpg\" style=\"float:left; width: 347px;\" class=\"desktop\">";

    if (isset($_REQUEST['ok'])) {
        if ($msg == "") {
            $msg = "<font color='green'>$T_email_sent<br><br><a href='../bbs'>$T_back$T_home</a></font>";
        } else {
            $msg = "<font color='red'>$T_email_error<br><br>$msg</font>";
        }
        //print "<br/><br/><table class=\"emailForm\"><tr><td>$msg</td></tr></table>";
        print "<br/><br/>$msg";
    } else { 
        $to = db_htmlEncode($mail_to);  //getUserLink($mail_to);
        $from = getUserLink($mail_from);
        $_title = db_htmlEncode($title);
        $_body = db_htmlEncode($body);

        $s = <<<EOF
<center><p><b>$T_greeting</b></p></center>

<form method="post">
<table class="emailForm">
<tr><td>$T_to_name: <font color="red">*</font></td>
<td><input type="text" id="to" name="to" class="emailTitle" value="$to"><br/>
<span id="e_to" style="color: red;"></span>
</td></tr>
<tr><td>$T_from_name: </td><td>$from</td></tr>
<tr><td>$T_title: <font color="red">*</font></td><td><input type="text" id="title" name="title" class="emailTitle" value="$_title"><br>
        <span id="e_title" style="color: red;"></span></tr></td>
<tr><td>$T_body: <font color="red">*</font></td>
    <td><textarea id="body" name="body" class="emailBody">$_body</textarea><br>
        <span id="e_body" style="color: red;"></span></td></tr>
<tr><td><br></td>
    <td><input type="button" id="btnSubmit" value="$T_submit" onclick="javascript:submitForm();" style="width:120px;"></td></tr>
</table>

<input type="hidden" id="IsPostBack" name="IsPostBack" value="Y"/>
</form>
EOF;

        print $s;
    }
}


function getUserLink($user) {
    return "<a href=\"../users/user.php?u=$user\" class=\"bbs_user\">$user</a>";
}


function sendEmail($mail_to, $mail_from) {
    global $_SITE_NAME, $_HOST_EMAIL, $_USE_USER_EMAIL;
    global $mail_to, $mail_from, $title, $body;
    $msg = "";

    try {
        $title = trim(U_POST("title"));
        $body = trim(U_POST("body"));
        send_emails($mail_to, $title, $body, $mail_from);
        header("Location: ?ok");
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
        print "<font color=\"red\">$msg</font>";
    }
}


//
// For privacy reason:
// In general, don't use user email, and email individually.
// If send group email, then people's id and email will have to be both shown.
// --> a possible solution: 
//     if send group email, then use people's real name instead of id.
// --> another solution:
//     if send group email, don't show people's id, only email address.
// ok, now take the second solution.
//
function send_emails($mail_to_list, $title, $body, $mail_from) {
    global $_SITE_NAME, $_USE_USER_EMAIL, $_EMAIL_INDIVIDUALLY;
$_EMAIL_INDIVIDUALLY = 0;

    // If send group email, then people's id and email will have to be both shown.
    // So sender should be public too.
    //if (! $_EMAIL_INDIVIDUALLY) { $_USE_USER_EMAIL = 1; }

    // 1) Get $from and $signature.
    if ($_USE_USER_EMAIL) {
        $email_from = executeScalar("select email from User where login = " . db_encode($mail_from));
        if ($email_from == "") { throw new Exception("Unknown sender \"$mail_from\""); }
        $from = "$mail_from <$email_from>";
        $signature = "\n\n--\nSent from $_SITE_NAME";
    } else {
        $from = $_HOST_EMAIL;
        $signature = "";
    }

    $reply_link = "http://$_SITE_NAME/email/?to=$mail_from";
    //print $reply_link;

    // 2) get receiver list.
    $recvers = get_recver_email_list($mail_to_list);
    if (! is_array($recvers)) { throw new Exception($recvers); } // error message.

    // 3) send emails.
    if ($_EMAIL_INDIVIDUALLY) { // send to each receipient individually.
        foreach ($recvers as $key => $val) {
            //print "$key -> $val<br/>";
            $mail_to = $key;
            $email_to = $val;
            if (! $_USE_USER_EMAIL) {
                $membership = getPrivateBoardMembership($mail_from, $mail_to);
                $_body = <<<EOF
From: $mail_from at $_SITE_NAME $membership
Reply: $reply_link

$body
EOF;
            }
            send_email("$mail_to <$email_to>", $title, $_body, $from, $signature);
        }
    } else { // send group email.
        $mail_tos = "";
        foreach ($recvers as $key => $val) {
            //print "$key -> $val<br/>";
            $mail_to = ""; //$key;
            $email_to = $val;
            if ($mail_tos != "") { $mail_tos .= ","; }
            $mail_tos .= "$mail_to <$email_to>";
        }

        if (! $_USE_USER_EMAIL) {
            $body = <<<EOF
From: $mail_from at $_SITE_NAME 
Reply: $reply_link

$body
EOF;
        }

        send_email("$mail_tos", $title, $body, $from, $signature);
        //send_email($from, $title, $body, 
            //$from . "\r\nBcc: $mail_tos\r\nX-Mailer: PHP/" . phpversion(), $signature);
    }
}


//
// @params:
//  - $mail_to: a list of users, delimited by "," or ";"
// @return: 
//  if no error: an associative array containing the (username => email)
//      pairs of the receivers. Note duplicates will be ignored in this step.
//  else: a string containing description of the error.
//
function get_recver_email_list($mail_to) {
    $mail_to = str_replace(";", ",", $mail_to);
    $recvers = split(",", $mail_to);
    $ct = count($recvers);
    if ($ct == 0) { return "There is no valid user"; }

    $h = array();
    $msg = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $email = getUserEmailFromLogin($recvers[$i]);
        //print ":$email  " . $recvers[$i] . "<br/>";
        if ($email != "") {
            $h[$recvers[$i]] = $email;
        }
        else {
            if ($msg != "") $msg .= ", ";
            $msg .= $recvers[$i];
        }
    }

    if ($msg != "") return "Invalid user: $msg";
    else return $h;
}


function getUserEmailFromLogin($login) {
    $sql = "SELECT email FROM User WHERE login = " . db_encode($login);
    return executeScalar($sql);
}

?>

