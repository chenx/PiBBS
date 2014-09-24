<?php

require_once("../func/db.php");
if ($_BBS_AUTH_USER_ONLY) {
    require_once("../func/auth.php");
}
if ($_BBS_ADMIN_ONLY) {
    require_once("../func/auth_admin.php");
}
require_once("../func/mobile.php");
require_once("../func/Cls_DBTable_Custom.php");
require_once("bbs_terms_$_LANG.php");
require_once("../func/ClsPage.php");
require_once("bbs_func_showForums.php");
require_once("../func/avatar.php");
require_once("attachment_func.php"); // for post attachment.
require_once("../func/util.php");

//echo "bbs_role: " . $_SESSION['bbs_role'] . "<br>";

function get_username() {
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    return "";
}

function get_url_params() {
    global $forum_id, $mode, $page;
    $params = "f=$forum_id";   
    if ($mode != "") $params .= "&m=$mode";
    if ($page != "") $params .= "&p=$page";
    return $params;
}

function getForumThreadCount($forum_id, $mode = 0) {
    global $link;

    if ($mode == 0) {
        $query = "SELECT thread_count FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    } else if ($mode == 1) {
        $query = "SELECT thread_count_admin FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    } else {
        return 0;
    }
    return executeScalar($query); 
}

function getThreadPostCount($forum_tbl, $thread_id, $mode) {
    if ($mode == 0) { // user mode.
        $query = "SELECT count(ID) AS ct FROM $forum_tbl WHERE thread_id = $thread_id AND hidden = 0";
    } else if ($mode == 1) { // manage mode.
        $query = "SELECT count(ID) AS ct FROM $forum_tbl WHERE thread_id = $thread_id";
    } else {
        return 0;
    }
    $ct = executeScalar($query, "ct");
    return executeScalar($query, "ct");
}

function updateCount_delete($forum_id, $post_count_change, $thread_count_change, 
                            $post_count_admin_change, $thread_count_admin_change) {
    $query = <<<EOF
UPDATE BBS_BoardList SET 
    post_count = post_count + ($post_count_change), 
    thread_count = thread_count + ($thread_count_change),
    post_count_admin = post_count_admin + ($post_count_admin_change), 
    thread_count_admin = thread_count_admin + ($thread_count_admin_change) WHERE ID = '$forum_id';
EOF;
    executeNonQuery($query);
}


//
// Permission control.
//

//
// Forum level permission on: new/edit/delete/reply.
//
function forum_can_modify() {
    global $forum_id, $forum_private, $forum_readonly;
    //print "private: $forum_private. user: $user_id. is member: " . is_private_board_member($forum_id);
    if ($forum_readonly) { // only admin can modify.
        return IsAdmin();
    } else if ($forum_private) { // admin or member can modify.
        return IsAdmin() || is_private_board_member($forum_id);
    }

    // not readonly or private, everyone can modify.
    return 1;
}


// Can post new.
function can_new() {
    // 1) Forum level can_new
    global $forum_private, $forum_readonly;
    if ( ($forum_private || $forum_readonly) && ! forum_can_modify()) { return 0; }
    // else, handle to POST level permission control.

    // 2) Post level can_new.
    return 1;
}


// Can modify: edit/delete
function can_modify($user_id, $readonly) {
    // 1) Forum level can_modify.
    global $forum_private, $forum_readonly;
    if ( ($forum_private || $forum_readonly) && ! forum_can_modify()) { return 0; }
    // else, handle to POST level permission control.

    // 2) POST level can_modify.
    // These can modify: board master, admin, or post author when post is not readonly.
/*
    if (isset($_SESSION['ID']) && $user_id == $_SESSION['ID']) return 1;
    else if (can_manage()) return 1;
    else return 0;
*/  
    global $mode;
    if ($mode == '1' && can_manage()) return 1;
    else if ($readonly) return 0;
    else return IsSelf($user_id);
}


// $mode: 1 - manage, 0 - normal.
function can_reply($user_id, $readonly, $mode) {
    // 1) Forum level can_reply
    global $forum_private, $forum_readonly;
    if ( ($forum_private || $forum_readonly) && ! forum_can_modify()) { return 0; }
    // else, handle to POST level permission control.

    // 2) POST level can_reply.
    global $mode;
    if ($mode == '1' && can_manage()) return 1;
    else if ($readonly) return 0;
    else return 1;
}


function can_manage() {
    global $forum_id;
    return IsAdmin() || IsBoardManager($forum_id);
}

function get_manage_link() {
    if (can_manage() == 0) return "";

    global $mode, $forum_id, $T_turn_on_manage_mode, $T_turn_off_manage_mode;

    $manage = "";
    $url_params = "";
    $page_file = basename($_SERVER['PHP_SELF']);
    if ($page_file == "forum.php" || $page_file == "digest.php" || $page_file == "mark.php") {
        $url_params = "f=$forum_id";
        if ( isset($_REQUEST['p']) ) $url_params .= "&p=" . U_REQUEST_INT('p');
    } else if ($page_file == "view.php") {
        global $thread_id;
        $url_params = "f=$forum_id&t=$thread_id&pg=" . U_REQUEST_INT('pg');
    } else if ($page_file == "post.php") {
        global $thread_id, $id;
        $url_params = "f=$forum_id&t=$thread_id&i=$id&pg=" . U_REQUEST_INT('pg');
    }

    if ($url_params != "") {
        if ($mode == "") {
            $manage = "<a href='$page_file?$url_params&m=1' class='bbs_manage'>$T_turn_on_manage_mode</a>";
        } else {
            $manage = "<a href='$page_file?$url_params' class='bbs_manage'>$T_turn_off_manage_mode</a>";
        }
    }

    //$manage = "<td align='right'>$manage</td>";
    if ($manage != "") $manage = " - $manage";

    return $manage;
}

//
// Role of user.
//

function is_loggedIn() {
    return isset($_SESSION['username']) && $_SESSION['username'] != "";
}

function IsAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == "admin";
}

function IsBoardManager($forum_id) {
    if (! is_loggedIn()) return 0;
    if (! isset($_SESSION['bbs_role']) || $_SESSION['bbs_role'] == '') return 0;
    $pos = strpos($_SESSION['bbs_role'], "|$forum_id,");
    if ($pos === false) return 0; // false and position '0' are different.
    return 1;
}

// Return true if $user_id belongs to the current logged in user.
function IsSelf($user_id) {
    return isset($_SESSION['ID']) && $user_id == $_SESSION['ID'];
}

// User score management.

function bbs_addUserScore($user_id, $v) {
    if ($user_id == "") return;
    if (! myIsInt($v)) return;
    $query = "UPDATE User set bbs_score = bbs_score + (" . $v . ") WHERE ID = " . db_encode($user_id);
    executeNonQuery($query);
}

function bbs_addUserNewCount($user_id, $v) {
    if ($user_id == "") return;
    if (! myIsInt($v)) return;
    $query = "UPDATE User set bbs_new_count = bbs_new_count + (" . $v . ") WHERE ID = " . db_encode($user_id);
    executeNonQuery($query);
}

function bbs_addUserReplyCount($user_id, $v) {
    if ($user_id == "") return;
    if (! myIsInt($v)) return;
    $query = "UPDATE User set bbs_reply_count = bbs_reply_count + (" . $v . ") WHERE ID = " . db_encode($user_id);
    executeNonQuery($query);
}


function getTitleBar($page_name) {
    global $forum_title, $forum_id, $url_params;
    global $T_board, $T_digest_area, $T_mark_area;

    //if ($page_name == "forum.php" || $page_name == "digest.php" || $page_name == "mark.php") {
        // in this case, ignore 'p', since you want to go to the first page.
        $_url_params = "f=$forum_id";
        if ( isset($_REQUEST['m']) ) $_url_params .= "&m=" . U_REQUEST_INT('m');
    //}
    //else {
    //    $_url_params = $url_params;
    //}

    $b1 = $T_board;
    $b1L = "<a href=\"forum.php?$_url_params\">$b1</a>";
    $b2 = $T_digest_area;
    $b2L = "<a href=\"digest.php?$_url_params\">$b2</a>";
    $b3 = $T_mark_area;
    $b3L = "<a href=\"mark.php?$_url_params\">$b3</a>";

    if ($page_name == "forum.php") {
        $b = "$b1 | $b2L | $b3L";
    } else if ($page_name == "digest.php") {
        $b = "$b1L | $b2 | $b3L";
    } else if ($page_name == "mark.php") {
        $b = "$b1L | $b2L | $b3";
    } else {        
        $b = "$b1L | $b2L | $b3L";
    }

    global $forum_readonly, $forum_hidden, $forum_private, $forum_disabled;
    $attrib = getBoardAttrib($forum_readonly, $forum_hidden, $forum_private, $forum_disabled, $forum_id);

    $send_group_imail = send_group_imail($forum_private);
    $send_group_email = send_group_email($forum_private);
    if ($send_group_email != "") { $send_group_email = " | $send_group_email"; }

    global $is_mobile;
    if ($is_mobile) {
        $s = "$attrib<br/>$b";
    } else {
        $s = <<<EOF
<table width="100%" border="0"><tr>
<td width="30%">$send_group_imail $send_group_email</td>
<td width="40%" align="center"><h3>$forum_title$attrib</h3></td>
<td width="30%" align="right">$b</td>
</tr></table>
EOF;
    }

    return $s;
}


function send_group_imail($forum_private) {
    global $_USE_IMAIL;
    if (! $_USE_IMAIL || ! $forum_private || ! is_loggedIn()) return "";

    global $forum_id;
    $user_id = $_SESSION['ID'];
    
    if (! has_group_imail_permission($forum_id, $user_id)) { return ""; }

    global $T_send_group_imail, $T_send_group_imail_desc;
    return "<a href='../imail/compose.php?f=$forum_id' title='$T_send_group_imail_desc'>$T_send_group_imail</a>";
}


// Now Admin and private board members can send I-Mail.
function has_group_imail_permission($forum_id, $user_id) {
    if (IsAdmin()) return 1;

    $sql = "SELECT count(*) FROM BBS_PrivateMembership WHERE forum_id = "
           . db_encode($forum_id) . " AND user_id = " . db_encode($user_id);
    $ct = executeScalar($sql);
    return $ct > 0;
}


function send_group_email($forum_private) {
    global $_USE_EMAIL;
    if (! $_USE_EMAIL || ! $forum_private || ! is_loggedIn()) return "";

    global $forum_id;
    $user_id = $_SESSION['ID'];

    if (! has_group_email_permission($forum_id, $user_id)) { return ""; }

    global $T_send_group_email, $T_send_group_email_desc;
    return "<a href='../email/?f=$forum_id' title='$T_send_group_email_desc (Manager Only)'>$T_send_group_email</a>";
}


// Now only Admin and group Manager can send E-Mail.
function has_group_email_permission($forum_id, $user_id) {
    if (IsAdmin()) return 1;

    $sql = "SELECT count(*) FROM BBS_BoardManager WHERE forum_id = "
           . db_encode($forum_id) . " AND user_id = " . db_encode($user_id);
    $ct = executeScalar($sql);
    return $ct > 0;
}


function write_breadcrumb() {
    global $forum_managers, $forum_title, $forum_id, $forum_thread_ct, $forum_post_ct, $url_params;
    global $forum_hidden, $forum_private;
    global $T_forumList, $T_home, $T_threads, $T_posts, $T_search_title, $T_search_placeholder, $T_manager;
    global $is_mobile;

    $manage = get_manage_link();
    
    $forum = $forum_title;
    $page_name = basename($_SERVER['PHP_SELF']);
    if ($page_name != "forum.php") {
        $forum = "<a href='forum.php?$url_params'>$forum</a>";
    }

    $k = db_htmlEncode( U_REQUEST('k') ); // search value.
    $searchBar = <<<EOF
<input type="search" results id="searchTxt" value="$k" onkeyup="javascript: OnEnterSearch(event, '$url_params');" 
       placeholder="$T_search_placeholder" title="$T_search_title" class="searchBox">
EOF;
//<img src="../image/search.png" border="0" style="height:20px; vertical-align:top;" 
//     onclick="javascript: DoSearch('$url_params');" title="$T_search_title">
//EOF;

    //$home = "<a href=\"../\">$T_home</a> - "; // <font style='font-size:14px;font-weight:bold;'>

    $title_bar = getTitleBar($page_name);

    $managers = "$T_manager: " . writeManagers($forum_managers);

    if ($is_mobile) {
        $mail = "";
        $send_group_email = send_group_email($forum_private);
        if ($send_group_email != "") { $mail = "$send_group_email"; }

        $send_group_imail = send_group_imail($forum_private);
        if ($send_group_imail != "") { 
            $mail = ($mail == "") ? "$send_group_imail" : "$send_group_imail | $mail"; 
        }
        if ($mail != "") { $mail = "$mail<br/>"; }

        $s = <<<EOF
<table class="bbs_list_breadcrumb">
<tr><td>$searchBar</td></tr>
<tr>
<td><a href='./'>$T_forumList</a> - $forum</a> $title_bar<br/>
$T_threads: $forum_thread_ct | $T_posts: $forum_post_ct <br/>
$managers $manage <br/>
$mail<br/>
</table>
EOF;
    } else {
        $s = <<<EOF
<table class="bbs_list_breadcrumb"><tr>
<td><a href='./'>$T_forumList</a> - $forum</a> 
- $T_threads: $forum_thread_ct | $T_posts: $forum_post_ct | $managers $manage</td>
<td align="right">$searchBar</td>
</tr>
<tr><td colspan="2">$title_bar</td>
</tr></table>
EOF;
    }

    print $s;
}

function writeLoginLink($back_url) {
    global $T_please,$T_first, $T_login, $_LANG;

    $s = urlencode($back_url);

    if ($_LANG == "en") {
        $s = <<<EOF
    <p>Please <a href="../login/index.php?s=$s">log in</a> first.</p>
EOF;
    } else {
        $s = <<<EOF
    <p>$T_please$T_first<a href="../login/index.php?s=$s">$T_login</a></p>
EOF;
    }

    print $s;
}

function writeManagers($forum_managers) {
    global $T_none;
    $a = explode("|", $forum_managers);
    $s = "";

    $len = count($a);
    for ($i = 0; $i < $len; ++ $i) {
        $m = explode(",", $a[$i]);
        if (count($m) < 2) continue;
        $s .= " <a href='user.php?u=$m[1]' class='bbs_user'>$m[1]</a>";
    }
    if ($s == "") $s = "<font color='#999999'>($T_none)</font>";

    //$s = "$T_manager: $s";
    return $s;
}

function getBoard_DB() {
    global $forum_id, $forum_tbl, $forum_title, $forum_thread_ct, $forum_post_ct, $forum_managers, $mode; 
    global $forum_readonly, $forum_hidden, $forum_private, $forum_disabled, $_LANG;
    $forum_id = U_REQUEST_INT("f");
    if ($forum_id == "") return;
    $title = $_LANG == "en" ? "title_en" : "title";

    db_open();
    if ($mode == '1') {
        $query = "SELECT name, $title AS title, managers, thread_count_admin as tc, post_count_admin as pc, readonly, hidden, private, disabled FROM BBS_BoardList where ID = " . db_encode($forum_id);
    } else {
        $query = "SELECT name, $title AS title, managers, thread_count as tc, post_count as pc, readonly, hidden, private, disabled FROM BBS_BoardList where ID = " . db_encode($forum_id);
    }
    $ret = executeAssociateDataTable($query);
    foreach ($ret as $key => $val) {
        $forum_tbl = $val['name'];
        $forum_title = $val['title'];
        $forum_thread_ct = $val['tc'];
        $forum_post_ct = $val['pc'];
        $forum_managers = $val['managers'];
        $forum_readonly = $val['readonly'];
        $forum_hidden = $val['hidden'];
        $forum_private = $val['private'];
        $forum_disabled = $val['disabled'];
        //print "$query. $forum_tbl $forum_title <br>";
        break;
    }
    db_close();
}


//
// Used in view.php, post.php
// wrap each keyword with link, to search page based on keyword.
//
function wrapKeywords($s) {
    //return $s;
    global $url_params, $thread_id;
    $a = explode(",", $s);
    //print_r($a);
    $t = "";
    for ($i = 0, $len = count($a); $i < $len; ++ $i) {
        $v = urlencode( trim($a[$i]) );
        if ($v != "") {
            $t .= "<a href='keyword.php?$url_params&t=$thread_id&k=$v'>$a[$i]</a> ";
        }
    }
    return $t;
}

// Prepare before store into database:
// 1) remove all spaces.
// 2) use "," at the beginning and end, for accurate match purpose in keywords.php.
function prepareKeywords($s) {
    $s = trim($s);
    if ($s == "") return "";
    $a = explode(",", $s);
    $t = ",";
    for ($i = 0, $len = count($a); $i < $len; ++ $i) {
        $v = trim($a[$i]);
        if ($v != "") {
            $t .= "$v,";
        }
    }
    return $t;
}

// new.php, reply.php, view.php
// Add keywords to database table BBS_Keywords, increment count if exists.
function upsertKeywords($s) {
    $a = explode(",", $s);
    for ($i = 0, $len = count($a); $i < $len; ++ $i) {
        $v = trim($a[$i]);
        if ($v != "") { upsertKeyword($v); }
    }
}

function upsertKeyword($v) {
    if ($v == "") return;

    global $forum_id;
    if ($forum_id == "") return;

    $query = "SELECT ID FROM BBS_Keywords WHERE forum_id = " . db_encode($forum_id) . " AND word = " . db_encode($v);
    $id = executeScalar($query);
    if ($id == "") {
        $query = "INSERT INTO BBS_Keywords (word, forum_id, forum_ct) VALUES (" .
                 db_encode($v) . ", " . db_encode($forum_id) . ", 1)";
    } else {
        $query = "UPDATE BBS_Keywords SET forum_ct = forum_ct + 1 WHERE ID = " . db_encode($id);
    }
    executeNonQuery($query);
}


// View form. In view.php, keyword.php, post.php, search.php

//
// Need a second varaible $div_media_img, because this container's width needs to be 
// specified as a absolute value, e.g., 1000px, so img.media_img's max-width can work.
//
function homecoxHtmlEncode($s, $div_media_img="div_media_img") {
    $s = preg_replace("/@\[a (.*?)\]/", "<a $1>", $s); // '?' for non-greedy match (match first ']').
    $s = str_replace("@[/a]", "</a>", $s);

    // Note: if not use div imgContainer, then if input is <img width="1500" class="media_img">
    // then in Firefox, max-width:100% of media_img won't work, the image size will still be 1500 and too wide.
    // Use a imgContainer div, then the max size is always restricted to 100%, or 1000px in this case.
    $s = preg_replace("/@\[img (.*?)\]/", "<div class='$div_media_img'><img $1 class=\"media_img\"></div>", $s);

    // See: http://fettblog.eu/blog/2013/06/16/preserving-aspect-ratio-for-embedded-iframes/
    $s = preg_replace("/@\[iframe (.*?)\]/", "<div class=\"aspect-ratio\"><iframe $1></iframe></div>", $s);

    $s = str_replace("@[code]", "<div class=\"code\">", $s);
    $s = str_replace("@[/code]", "</div>", $s);

    $s = preg_replace("/@\[codearea rows=(\d+)\]/", 
             "<textarea rows='$1' READONLY class='code'>", $s);
    $s = str_replace("@[/codearea]", "</textarea>", $s);

    $s = str_replace("@[u]", "<u>", $s);
    $s = str_replace("@[/u]", "</u>", $s);

    $s = str_replace("@[b]", "<b>", $s);
    $s = str_replace("@[/b]", "</b>", $s);

    $s = str_replace("&quot;", "\"", $s); // Allows the use of double quote for attributes.

    return $s;
}

function wrapViewBody($s) {
    $s = "<pre class='bbs'>$s</pre>";
    $s = homecoxHtmlEncode( $s );
    return $s;
}

function getFooter($_last_edit, $T_source, $_ip, $manage) {
    global $_BBS_JIA_THIS_POST, $_BBS_POST_SRC;

    if ($_BBS_JIA_THIS_POST == 0) {

        $footer = <<<EOF
<br>$_last_edit
<br>※ $T_source: $_BBS_POST_SRC &nbsp;$_ip

$manage
EOF;

    } else {

        $footer = <<<EOF
<table border="0" width="100%"><tr><td valign="top">
$_last_edit
<br>※ $T_source: $_BBS_POST_SRC &nbsp;$_ip

$manage

</td><td align="right" valign="top"><br/>

<!-- JiaThis Button BEGIN -->
<div class="jiathis_style" style="width:190px;" align="right">
        <a class="jiathis_button_qzone"></a>
        <a class="jiathis_button_tsina"></a>
        <a class="jiathis_button_tqq"></a>
        <a class="jiathis_button_weixin"></a>
        <a class="jiathis_button_renren"></a>
        <a class="jiathis_button_xiaoyou"></a>
        <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jtico jtico_jiathis" target="_blank"></a>
        <a class="jiathis_counter_style"></a>
</div>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
<!-- JiaThis Button END -->

</td></tr></table>
EOF;

    }

    return $footer;
}

// Can edit if is the author, or bm/admin.
function writeViewForm($info, $row, $showReadThread = 0) {
    global $forum_title, $thread_id, $forum_id, $url_params, $mode, $post_count, $can_reply;
    global $T_reply, $T_edit, $T_remove, $T_back_forum, $T_title, $T_post_time, $T_author, 
           $T_keywords, $T_forum, $T_source, $_TIMEZONE, $T_last_edit, $T_read_thread,
           $T_no_remove_has_replies, $_BBS_IP_NTH;

    $ID = $info['ID'];
    $_title = db_htmlEncode( $info['title'] );
    $_body = db_htmlEncode( $info['body'] );
    $_time = db_htmlEncode( $info['submit_timestamp'] );
    $_ip = db_htmlEncode( getIPPart( $info['submit_ip'], $_BBS_IP_NTH ) );
    $_username = db_htmlEncode( $info['user_name'] );
    $_keywords = wrapKeywords( db_htmlEncode( $info['keywords'] ) );

    $_body = wrapViewBody($_body);
    //$_body = "<pre class='bbs'>$_body</pre>";

    $_last_edit = "";
    if ($info['last_edit_user_name'] != "") {
        $_last_edit = "<br>$T_last_edit: " . db_htmlEncode($info['last_edit_user_name'])
                      . " on " . db_htmlEncode($info['last_edit_timestamp'] . " $_TIMEZONE");
    }

    $reply = "[$T_reply]";
    if (can_reply($info['user_id'], $info['readonly'], $mode)) {
        $reply = <<<EOF
[<a href="reply.php?$url_params&t=$thread_id&id=$ID">$T_reply</a>]
EOF;

        if ($row == 1) $can_reply = 1; // for the page bottom reply form.
    }

    $modify = " [$T_edit] [$T_remove] ";
    if (can_modify($info['user_id'], $info['readonly'])) {
        $modify = " [<a href=\"edit.php?$url_params&t=$thread_id&id=$ID\">$T_edit</a>] ";

        if ($ID == $thread_id && $post_count > 1) {
            $modify .= " [<span title='$T_no_remove_has_replies'>$T_remove</span>] ";
        } else {
            $modify .= " [<a href=\"remove.php?$url_params&t=$thread_id&id=$ID\">$T_remove</a>] ";
        }
    }

    $manage = "";
    if ($mode == "1" && can_manage()) {
        $manage = "<table class='bbs_post_manage'><tr class='bbs_post_manage_head'>" . writeManageHeader() . "</tr><tr>" .
                  getManageFunc($info, $row) . "</tr></table>";
    }

    $sameThread = "";
    if ($showReadThread) {
        $sameThread = "[<a href=\"view.php?$url_params&t=$thread_id\">$T_read_thread</a>] ";
    }

    $footer = getFooter($_last_edit, $T_source, $_ip, $manage);

    // get icon for user.
    $icon = get_avatar_by_id( $info['user_id'] );
    
    $attachment = get_attachment($forum_id, $ID, $info['user_name'], $info['salt']); // in attachment.php

    $s = <<<EOF
<table class="bbs_post_view">
<tr>
<td class="bbs_post_view_left">
<a href="user.php?u=$_username" class="bbs_user">$_username
<br><img src="$icon" width="80" border="0"></a>
</td>
<td class="bbs_post_view_right">

<table class="bbs_post_view_content">
<tr class="bbs_post_view_content_head">
<td>
$reply
$modify
$sameThread
[<a href="forum.php?$url_params">$T_back_forum</a>]
</td>
<td align="right">$row &nbsp;</td></tr>

<tr><td colspan="2">

$T_author: <a href="user.php?u=$_username" class="bbs_user">$_username</a>, 
$T_forum: $forum_title, $T_post_time: $_time $_TIMEZONE<br>
$T_title: $_title<br>
$T_keywords: $_keywords<br>
<br>
$_body
$attachment
<br/>
<br/>--
$footer

</td></tr></table>

</td></tr></table>
<br>
EOF;

    print $s;
}


// 
// For privacy, hide later part of IP.
// nth - show until the n-th section delimited by '.'.
// 
function getIPPart($ip, $nth = 0) {
    if ($nth == 0) return "";

    global $T_from;
    $offset = 0;
    for ($i = 0; $i < $nth; ++ $i) {
        $pos = strpos($ip, ".", $offset);
        if ($pos === false) return "[$T_from: $ip]"; // false and position '0' are different.
        $offset = $pos + 1;
    }
    $ip = substr($ip, 0, $offset);
    $ip = "[$T_from: $ip]";

    return $ip;
}


function getBoard() {
    global $forum_tbl, $forum_title;
    $f = U_REQUEST_INT("f");

    if ($_LANG == "cn" ) {
        if ($f == "1") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "2") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "3") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "4") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "5") {
            $forum_tbl = "";
            $forum_title = "";
        }
    }
    else {
        if ($f == "1") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "2") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "3") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "4") {
            $forum_tbl = "";
            $forum_title = "";
        }
        else if ($f == "5") {
            $forum_tbl = "";
            $forum_title = "";
        }
    }
}


// Get a string representing a board's attributes:
// [readonly, hidden, private]
function getBoardAttrib($readonly, $hidden, $private, $disabled, $forum_id="") {
    global $T_readonly, $T_hidden, $T_private, $T_disabled, $T_priv_board_member_list;

    $T_priv = $T_private;
    if ($forum_id != "") {
        if (IsAdmin() || is_private_board_member($forum_id)) {
            $T_priv = "<a href=\"../users/forum_mem.php?f=$forum_id\" title=\"$T_priv_board_member_list\">$T_priv</a>";
        } 
    }

    $a = "";
    if ($readonly) { $a .= $T_readonly; }
    if ($hidden)   { $a .= ($a == "") ? $T_hidden : ", $T_hidden"; }
    if ($private)  { $a .= ($a == "") ? $T_priv : ", $T_priv"; }
    if ($disabled)  { $a .= ($a == "") ? $T_disabled : ", $T_disabled"; }
    if ($a != "")  { $a = " [$a]"; }
    return $a;
}

//
// Functions to control if a user can see hidden or private board.
//
function can_see_hidden_board($forum_id, $is_hidden) {
    if (! $is_hidden) return 1; // not hidden board, can always see.
    if (IsAdmin()) return 1;
    if ( is_private_board_member($forum_id) ) return 1;
    if ($is_hidden) return 0;
    return 1;
}

function is_private_board_member($forum_id) {
    //print "membership: ". $_SESSION['bbs_PrivateMembership'];
    if (! isset($_SESSION['bbs_PrivateMembership'])) return 0;
    return ! ( strpos($_SESSION['bbs_PrivateMembership'], "|$forum_id|") === false );
}

// If a board is disabled, only admin can see.
function can_see_disabled_board($is_disabled) {
    if (! $is_disabled) return 1; // not disabled board, can always see.
    if (IsAdmin()) return 1;
    return 0;
}


////////////////////////////////////////////////
// Functions used by IMail and EMail.
////////////////////////////////////////////////

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


?>


