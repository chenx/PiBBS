<?php
session_start();
require_once("../bbs/bbs_func.php");

//print "gook"; exit();

if (! is_loggedIn()) { bbs_exit("-1"); }

$forum_id = U_REQUEST_INT('f');
$col   = U_REQUEST_INT('c'); // 1 - private, 2 - hidden, 3 - readonly.
$val     = U_REQUEST_INT('v');  // 0 - false, 1 - true.

if ( $forum_id == 0 ||
     ($col < 1 || $col > 3) ||
     ($val != 1 && $val != 0) ) { bbs_exit(-2); }


if (IsAdmin() || IsBoardManager($forum_id)) {
    toggle_forum_status($forum_id, $col, $val);
}


function toggle_forum_status($forum_id, $col, $val) {
    $field = getField($col);
    //print "toggle forum $forum_id $field status to $val";
    try {
        $sql = "UPDATE BBS_BoardList SET $field = " . db_encode($val) 
               . " WHERE ID = " . db_encode($forum_id);
        executeNonQuery($sql);
        bbs_exit(1);
    } catch (Exception $e) {
        bbs_exit(-3);
    }
}

function getField($col) {
    if ($col == '1') return "private";
    else if ($col == '2') return "hidden";
    else if ($col == '3') return "readonly";
    else return "";
}

function bbs_exit($error_no) {
    print $error_no;
    exit();
}

?>
