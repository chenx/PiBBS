<?php
//
// Can setup a cron job and call this at:
// http://.../admin/email_notify.php?code=ABNDfjasdfou29823470bMdfljF&btnNotify=y
//

session_start();

//require_once("../func/auth.php");
//require_once("../func/auth_admin.php");
require_once("../conf/conf.php"); // for $_IMAIL_NOTIFY_ACCESS_CODE
require_once("../func/db.php");
require_once("../func/email.php");

$page_title = "Admin - IMail notification";

if (! isset($_REQUEST['code']) || $_REQUEST['code'] != $_IMAIL_NOTIFY_ACCESS_CODE) {
    //print "You have no permission to enter this page";
    header("Location: ./");
    exit();
} 
$code = $_REQUEST['code'];

//$div_main_id = "main_full_width";
include("../theme/header.php"); 
?>
<table width="100%"><tr><td>

<P><a href="../admin/">Admin</a> &gt; IMail Notification <?php echo $tbl_uc1; ?></P>

<form method="POST">
<p><input type="submit" name="btnNotify" value="Send IMail Notification Emails"/></p>
<input type="hidden" name="cody" value="<?php print $code; ?>"/>
</form>

<?php
imail_notify();

print "</td></tr></table>";
include("../theme/footer.php");

//
// Functions.
//

function imail_notify() {
    global $_IMAIL_NOTIFY_INTERVAL_DAYS, $_IMAIL_NOTIFY_DIGEST_LEN;

    $interval = 
        ($_IMAIL_NOTIFY_INTERVAL_DAYS > 0) ?
        " AND notify_time > DATE_SUB(NOW(), INTERVAL $_IMAIL_NOTIFY_INTERVAL_DAYS DAY)" 
        : "";
    $len = $_IMAIL_NOTIFY_DIGEST_LEN; // length of digest.

    $sql = <<<EOF
SELECT
    S.ID, S.recver, S.recv_state, S.recv_time, U.email, M.sender, M.title,
    CASE
        WHEN LENGTH(M.body) > $len THEN CONCAT(substr(M.body, 1, $len), ' ...')
        ELSE M.body
    END as body
FROM IMailRecv S, IMail M, User U
WHERE S.fk_mail_id = M.ID AND S.fk_recver_id = U.ID AND recv_state = '6'
AND S.ID NOT IN
(
    SELECT fk_imailrecv_id FROM IMailRecvNotify WHERE fk_imailrecv_id = S.ID $interval
)
EOF;
    //print $sql;

    try {
        db_open();
        get_imails_to_notify($sql);
        send_notify_emails($sql);
        db_close();
    } catch (Exception $e) {
        print "Error: " . $e->getMessage();
    }
}

function get_imails_to_notify($sql) {
    global $link;
    $s = executeDataTable_ToHtmlTable($sql, " border=1", 1, 1);
    if ($s == "") { $s = "(empty)"; }
    print $s . "<br/>";
}

function send_notify_emails($sql) {
    if (! isset($_REQUEST['btnNotify']) || $_REQUEST['btnNotify'] == "") return;

    global $link, $_IMAIL_NOTIFY;

    $t = executeAssociateDataTable($sql);
    $ct = count($t);
    $prev_recver = "";
    $prev_email = "";
    $msg = "";
    $msg_ct = 0;
    for ($i = 0; $i < $ct; ++ $i) {
        $sender = $t[$i]['sender'];
        $recver = $t[$i]['recver'];
        $recv_time = $t[$i]['recv_time'];
        $title = $t[$i]['title'];
        $body = format_digest( $t[$i]['body'] );
        $email = $t[$i]['email'];

        $id = db_encode( $t[$i]['ID'] );
        $time = db_encode( date('Y-m-d H:i:s', time()) );

        $query = <<<EOF
INSERT INTO IMailRecvNotify (fk_imailrecv_id, notify_time) VALUES ($id, $time)
EOF;
        if ($_IMAIL_NOTIFY) { executeNonQuery($query); }

        if ($recver != $prev_recver) {
            if ($prev_recver == "") {
                $prev_recver = $recver;
                $prev_email = $email;
            } else {
                send_msg($prev_email, $prev_recver, $msg, $msg_ct);
                $msg = "";
                $msg_ct = 0;
                $prev_recver = $recver;
                $prev_email = $email;
            }
        }

        $msg .= "Title: $title (From: $sender. Received on: $recv_time)\n";
        $msg .= "Message: $body\n\n";
        ++ $msg_ct;
    }

    if ($msg_ct > 0) {
        send_msg($email, $recver, $msg, $msg_ct);
    }
}


function send_msg($email, $recver, $msg, $msg_ct) {
    global $_IMAIL_NOTIFY, $_SITE_NAME;

    $s = ($msg_ct > 1) ? "s" : "";
    $msg = <<<EOF
Dear $recver,

You have received internal mail$s:

$msg
You can check your I-Mails at: $_SITE_NAME/imail
EOF;

    if ($_IMAIL_NOTIFY) {
        print "<div style='color: green;'>sending email to $recver ($email) ... ";
        send_email($email, "I-Mail Notification", $msg);
        print "ok</div>";
    }
    else {
        $msg = str_replace("\n", "<br/>", $msg);
        print "<hr/>To: $email<br/>" . $msg;
    }
}


function format_digest($s) {
    return preg_replace("/[ |\t|\r|\n]+/", " ", $s);
}

?>


