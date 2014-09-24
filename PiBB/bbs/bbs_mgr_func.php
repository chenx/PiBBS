<?php

// for post.php, view.php

function m_writeCol($v, $style="") {
    return "<td$style>$v</td>";
}

function writeManageHeader() {
    global $forum_id;
    $s = "";
    if ( IsAdmin() ) {
        $s .= m_writeCol("Mark") . m_writeCol("Digest") . /*m_writeCol("Top") .*/
              m_writeCol("Readonly") .  m_writeCol("Hide") . m_writeCol("Delete");
    } else if ( IsBoardManager($forum_id) ) {
        $s .= m_writeCol("Mark") . m_writeCol("Digest") . /*m_writeCol("Top") .*/
              m_writeCol("Readonly") .  m_writeCol("Hide") . m_writeCol("Delete");
    }
    return $s;
}

function getManageFunc($info, $rank) {
    global $forum_id, $thread_id, $post_count;
    $id = $info['ID'];
    $user_id = $info['user_id'];

    $m_check = ($info['marked'] == '1') ? 'checked' : '';
    $d_check = ($info['digested'] == '1') ? 'checked' : '';
    $t_check = ($info['top'] == '1') ? 'checked' : '';
    $r_check = ($info['readonly'] == '1') ? 'checked' : '';
    $h_check = ($info['hidden'] == '1') ? 'checked' : '';

    $mark = m_writeCol("<input type='checkbox' $m_check onchange=\"javascript: mgr_toggle($forum_id, 1, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m1_$id'");
    $digest = m_writeCol("<input type='checkbox' $d_check onchange=\"javascript: mgr_toggle($forum_id, 2, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m2_$id'");
    $top = ""; //m_writeCol("<input type='checkbox' $t_check onchange=\"javascript: mgr_toggle($forum_id, 3, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m3_$id'");
    $readonly = m_writeCol("<input type='checkbox' $r_check onchange=\"javascript: mgr_toggle($forum_id, 4, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m4_$id'");

    if ($id == $thread_id && $post_count > 1) {
        $hide = m_writeCol("<input type='checkbox' $h_check disabled title='Cannot hide since there are replies.'\">", " id='m5_$id'");
        $delete = m_writeCol("<span style='color:#999999' title='Cannot delete since there are replies.'>Delete</span>");
    } else {
        $hide = m_writeCol("<input type='checkbox' $h_check onchange=\"javascript: mgr_toggle($forum_id, 5, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m5_$id'");
        $delete = m_writeCol("<a href='#' onclick=\"javascript: deletePost($id, $rank);return false;\">Delete</a>");
    }

    $s = "";
    if ( IsAdmin() ) {
        $s = "$mark $digest $top $readonly $hide $delete";
    }
    else if ( IsBoardManager($forum_id) ) {
        $s = "$mark $digest $top $readonly $hide $delete";
    }

    return $s;
}


// For forum.php. 

function m_writeColH($v) {
    $style = " class='bbs_post_manage_head' ";
    return m_writeCol($v, $style);
}

function list_writeManageHeader() {
    global $forum_id;
    $s = "";
    if ( IsAdmin() ) {
        $s .= /*m_writeColH("Mark") . m_writeColH("Digest") .*/ m_writeColH("Top") .
              m_writeColH("Readonly") .  m_writeColH("Hide") . m_writeColH("Delete");
    } else if ( IsBoardManager($forum_id) ) {
        $s .= /*m_writeColH("Mark") . m_writeColH("Digest") .*/ m_writeColH("Top") .
              m_writeColH("Readonly") .  m_writeColH("Hide") . m_writeColH("Delete");
    }
    return $s;
}

function list_getManageFunc($info, $rank) {
    global $forum_id;
    $id = $info['ID'];
    $thread_id = $info['thread_id']; // print "id=$id, thread_id=$thread_id<br>";
    $user_id = $info['user_id'];

    $m_check = ($info['marked'] == '1') ? 'checked' : '';
    $d_check = ($info['digested'] == '1') ? 'checked' : '';
    $t_check = ($info['top'] == '1') ? 'checked' : '';
    $r_check = ($info['readonly'] == '1') ? 'checked' : '';
    $h_check = ($info['hidden'] == '1') ? 'checked' : '';

    $mark = ""; //m_writeCol("<input type='checkbox' $m_check onchange=\"javascript: mgr_toggle($forum_id, 1, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m1_$id'");
    $digest = ""; //m_writeCol("<input type='checkbox' $d_check onchange=\"javascript: mgr_toggle($forum_id, 2, 1, $id, $thread_id, $user_id, this.checked);\">", " id='m2_$id'");
    $top = m_writeCol("<input type='checkbox' $t_check onchange=\"javascript: mgr_toggle($forum_id, 3, 2, $thread_id, $thread_id, $user_id, this.checked);\">", " id='m3_$id'");
    $readonly = m_writeCol("<input type='checkbox' $r_check onchange=\"javascript: mgr_toggle($forum_id, 4, 2, $thread_id, $thread_id, $user_id, this.checked);\">", " id='m4_$id'");
    $hide = m_writeCol("<input type='checkbox' $h_check onchange=\"javascript: mgr_toggle($forum_id, 5, 2, $thread_id, $thread_id, $user_id, this.checked);\">", " id='m5_$id'");
    $delete = m_writeCol("<a href='#' onclick=\"javascript: deleteThread($thread_id, $rank);\">Delete</a>");

    $s = "";
    if ( IsAdmin() ) {
        $s = "$mark $digest $top $readonly $hide $delete";
    }
    else if ( IsBoardManager($forum_id) ) {
        $s = "$mark $digest $top $readonly $hide $delete";
    }

    return $s;
}

?>
