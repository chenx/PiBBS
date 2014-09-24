<?php
session_start();
include_once("../func/setting.php");
require_once("../func/util.php");
require_once("../func/db.php");
require_once("imail_func.php");

//
// parameters:
//  - user's id: $_SESSION['ID']
//  - mail's id in IMail:   $_REQUEST_INT("id")
//  - state:     $_REQUEST_INT("state")
//  - action:    $_REQUEST("action")
//

//
// Must be a logged in user.
//
if (! isset($_SESSION['ID']) || $_SESSION['ID'] == '') {
    do_exit(0);
}
$uid = $_SESSION['ID'];

$mail_id = U_REQUEST_INT("id");
if ($mail_id == "0") {
    do_exit(-1);
}

//
// 
// Send: 1 - draft, 2 - sent, Recv: 6 - new, 7 - read
// 11 - 17, corresponding deleted states.
// 21 - 27, corresponding permanently deleted states.
//
$valid_states = array(
'1', '2', '6', '7',
'11', '12', '16', '17',
'21', '22', '26', '27'
);

$state = U_REQUEST_INT("state");
if (! in_array($state, $valid_states)) {
    do_exit(-2);
} 

//
// action: d - delete, u - undelete.
//
$action = U_REQUEST("action");
if ($action != "d" && $action != "u") {
    do_exit('-3' . $action);
}

$uid = db_encode($uid);
$mail_id = db_encode($mail_id);
$sql = "";

if ($action  == 'd') { // delete.
    if ($state == '1' || $state == '2') {
        $sql = <<<EOF
UPDATE IMail SET send_state = send_state + 10
WHERE ID = $mail_id AND fk_sender_id = $uid
EOF;
    }
    else if ($state == '6' || $state == '7') {
        $sql = <<<EOF
UPDATE IMailRecv SET recv_state = recv_state + 10
WHERE fk_mail_id = $mail_id AND fk_recver_id = $uid
EOF;
    }
    else if ($state == '16' || $state == '17') { // recv
        $sql = <<<EOF
UPDATE IMailRecv SET recv_state = recv_state + 10 
WHERE fk_mail_id = $mail_id AND fk_recver_id = $uid
EOF;
        // alternative is to delete this entry from IMailRecv.
    }
    else if ($state == '11' || $state == '12') { // send
        $sql = <<<EOF
UPDATE IMail SET send_state = send_state + 10 
WHERE ID = $mail_id AND fk_sender_id = $uid
EOF;
        // alternative is to delete this entry from IMailSend.
    }
} else if ($action  == 'u') { // un-delete
    if ($state == '16' || $state == '17') {
        $sql = <<<EOF
UPDATE IMailRecv SET recv_state = recv_state - 10 
WHERE fk_mail_id = $mail_id AND fk_recver_id = $uid
EOF;
    }
    else if ($state == '11' || $state == '12') {
        $sql = <<<EOF
UPDATE IMail SET send_state = send_state - 10 
WHERE ID = $mail_id AND fk_sender_id = $uid
EOF;
    
    }
}

if ($sql == "") {
    do_exit(-4);
}


try {
    executeNonQuery($sql);
} catch (Exception $e) {
    if ($_DEBUG) { do_exit('-5: ' . $e->getMessage()); }
    else { do_exit(-5); }
}

do_exit(1);


function do_exit($v) {
    print $v;
    exit();
}
?>
