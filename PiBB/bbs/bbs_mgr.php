<?php
session_start();
require_once("bbs_func.php");

//print "gook"; exit();

if (! is_loggedIn()) { bbs_exit("-1"); }

$forum_id = U_REQUEST_INT('f');
$action   = U_REQUEST_INT('a'); // 1 - mark, 2 - digest, 3 - top, 4 - readonly, 5 - hide
$type     = U_REQUEST_INT('t'); // 1 - post, 2 - thread.
$id       = U_REQUEST_INT('i'); // post's id (when t = 1), or thread_id (when t = 2).
$thread_id = U_REQUEST_INT('h');
$user_id  = U_REQUEST_INT('u'); // user_id
$mode     = U_REQUEST_INT('m'); // 1 - on, 0 -off.
$forum_tbl = "";
$forum_title = "";

if ( ($forum_id == 0 || $id == 0) ||
     ($action < 1 || $action > 5) ||
     ($type != 1 && $type != 2) ||
     ($mode != 0 && $mode != 1) ||
     ($id == 0 || $thread_id == 0)) { bbs_exit(-2); }

getBoard_DB();

if ($forum_tbl == "") { bbs_exit(-3); }

if (IsAdmin()) {
    mgr_toggle($forum_id, $forum_tbl, $action, $type, $id, $thread_id, $user_id, $mode);
}
else if (IsBoardManager($forum_id)) {
    mgr_toggle($forum_id, $forum_tbl, $action, $type, $id, $thread_id, $user_id, $mode);
}

//
// Functions
//

function bbs_exit($error_no) {
    print $error_no;
    exit();
}

function mgr_toggle($forum_id, $forum_tbl, $action, $type, $id, $thread_id, $user_id, $mode) {
    $field = getField($action);
    if ($type == 1) { // for post
        $query = "UPDATE $forum_tbl SET $field = " . db_encode($mode) . " WHERE ID = " . db_encode($id);
    } else if ($type == 2) {
        $query = "UPDATE $forum_tbl SET $field = " . db_encode($mode) . " WHERE thread_id = " . db_encode($id);
    } else {
        bbs_exit(-4); // wrong type. shouldn't happen.
    }
    //print $query; exit();

    if ($action == 5) {
        $post_count = getThreadPostCount($forum_tbl, $thread_id, '0');
    }

    db_open();
    if ("" == executeNonQuery($query)) {
        print "1";
    } else {
        print "0";
    }
    bbs_updateUserScore($forum_tbl, $action, $id, $mode);

    if ($action == 1) { // mark
        $change = ($mode == 1) ? 1 : -1;
        updateCount_mark($user_id, $change);
    }
    else if ($action == 2) { // digest
        $change = ($mode == 1) ? 1 : -1;
        updateCount_digest($user_id, $change);
    }
    else if ($action == 5) { // hide
        // if turn on hide, dec post_count, dec Thread_count if post_count is now 0 for this thread.
        // if turn off hide, inc post_count, inc Thread_count if post_count is now 1 for this thread.
        // hide won't affect post_count_admin and thread_count_admin.

        $post_count2 = getThreadPostCount($forum_tbl, $thread_id, '0');

        $thread_count_change = 0;
        if ($mode == 1) { // hide
            if ($post_count > 0 && $post_count2 == 0) $thread_count_change = -1;
        } else if ($mode == 0) { //unhide
            if ($post_count == 0 && $post_count2 > 0) $thread_count_change = 1;
        }

        updateCount_hide($forum_id, $post_count2 - $post_count, $thread_count_change);
    }

    db_close();
}

function updateCount_mark($user_id, $change) {
    $query = "UPDATE User SET bbs_mark_count = bbs_mark_count + ($change) WHERE ID = " . db_encode($user_id);
    executeNonQuery($query);
}

function updateCount_digest($user_id, $change) {
    $query = "UPDATE User SET bbs_digest_count = bbs_digest_count + ($change) WHERE ID = " . db_encode($user_id);
    executeNonQuery($query);
}

function updateCount_hide($forum_id, $post_count_change, $thread_count_change) {
    $query = "UPDATE BBS_BoardList SET post_count = post_count + ($post_count_change), thread_count = thread_count + ($thread_count_change) WHERE ID = " . db_encode($forum_id);
    executeNonQuery($query);
}


function getField($action) {
    $s = "";
    if ($action == 1) $s = "marked";
    else if ($action == 2) $s = "digested";
    else if ($action == 3) $s = "top";
    else if ($action == 4) $s = "readonly";
    else if ($action == 5) $s = "hidden";

    return $s;
}

function bbs_updateUserScore($forum_tbl, $action, $id, $mode) {
    if ($action != "1" && $action != "2") return;

    $user_id = getPostUserID($forum_tbl, $id);

    if ($action == "1") { // mark
        if ($mode == "1") bbs_addUserScore($user_id, 10);
        else if ($mode == "0") bbs_addUserScore($user_id, -10);
    } else if ($action == "2") { // digest
        if ($mode == "1") bbs_addUserScore($user_id, 10);
        else if ($mode == "0") bbs_addUserScore($user_id, -10);
    }
}

function getPostUserID($forum_tbl, $id) {
    global $link;
    $query = "SELECT user_id FROM $forum_tbl WHERE ID = " . db_encode($id);
    return executeScalar($query, "user_id");
}
?>
