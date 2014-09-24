<?php
include_once("bbs_inc.php");
include_once("bbs_mgr_func.php");

$thread_id = U_REQUEST_INT('t');
$_title = "";
$page_title = "View";

db_open();
incClickCount();

include("../theme/header.php");

$post_count = 0;
$can_reply = 0;
?>

<center>

<?php write_breadcrumb(); ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td class="bbs_list_mid">

<form method="POST">
<input type='hidden' id='deleteID' name='deleteID' value=''/>
<input type="hidden" id="action" name="action" value='' />

<?php 
if (can_manage()) {
?>
<script type="text/javascript">
function deletePost(id, rank) {
    if (confirm('Are you sure to delete this post (row ' + rank + ')? This cannot be reversed.')) {
        $("#deleteID").val(id);
        document.forms[0].submit();
    }
}
</script>
<?php
}


if (! is_loggedIn()) { 
    writeViewList();
    print "<table class=\"bbs_post_head\"><tr><td>Reply</td></tr></table>";
    writeLoginLink("../bbs/view.php?$url_params&t=$thread_id");
} else if (isset($_REQUEST['ok'])) {
    $s = <<<EOF
    <p><font color='green'>$T_submit_confirm</font></p>
    <p align="center"><a href="view.php?$url_params&t=$thread_id">$T_back_thread</a> | 
       <a href="forum.php?$url_params">$T_back_forum</a></p>
EOF;
    print $s;
} else if (isset($_REQUEST['delete_ok'])) {
?>
    <p><font color='green'>The post has been successfully deleted.</font></p>
    <p align="center"><a href="view.php?<?php print "$url_params&t=$thread_id"; ?>">Back to Thread</a> |
       <a href="forum.php?<?php print $url_params; ?>">Back to This Forum</a></p>
<?php
} else {
    $title = ""; $body = ""; $keywords = ""; 
    $e_title = ""; $e_body = ""; $e_keywords = "";
    $error = "";

    getPostVal();   

    if (isset($_REQUEST['deleteID']) && $_REQUEST['deleteID']  != "" && can_manage()) {
        $msg = deletePost($_REQUEST['deleteID']);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            print $msg;
        }
        else {
            //print "<script type='text/javascript'>alert('Deleted has finished successfully.');</script>";
            //print "There are no more posts in this thread. <a href='forum.php?$url_params'>Back To Forum</a>";
            header("Location: view.php?delete_ok&$url_params&t=$thread_id");
        }
    }
    else if ( isset($_POST['btnPreview']) ) {
        $error = DoVerify();
        if ($error == "") {
            if ($_POST['btnPreview'] == "submit") {
                $error = DoInsert();
                if ($error == "") { header("Location: view.php?ok&$url_params&t=$thread_id");  }
                else {
                    print $error;
                    writeEditForm();
                }
            } else {
                writePreviewForm();
            }
        } else { writeEditForm(); }
    } else if ( isset($_POST['btnSubmit']) ) {
        $error = DoInsert();
        if ($error == "") { header("Location: view.php?ok&$url_params&t=$thread_id");  }
        else { 
            print $error; 
            writePreviewForm(); 
        }
    } else {
        writeViewList();
        if ($can_reply && $post_count > 0) writeEditForm();
    }
}
?>

<input type="hidden" name="IsPostBack" value="Y">
</form>

</td>
<td class="bbs_list_right"><br></td>
</tr></table>

</center>

<?php 
db_close();
include("../theme/footer.php"); 
?>


<?php
//
// Functions.
//

function deletePost($id) {
    global $forum_id, $forum_tbl, $thread_id;

    $query = "SELECT user_name, salt FROM $forum_tbl WHERE ID = " . db_encode($id);
    //$_username = executeScalar($query);
    $t = executeDataTable($query);
    $_username = $t[1][0]; 
    $salt = $t[1][1];

    $post_count = getThreadPostCount($forum_tbl, $thread_id, '0');
    $post_count_admin = getThreadPostCount($forum_tbl, $thread_id, '1');

    $query = "DELETE FROM $forum_tbl WHERE ID = " . db_encode($id);
    $msg = executeNonQuery($query);

    //if ($msg != "") return $msg;

    $post_count2 = getThreadPostCount($forum_tbl, $thread_id, '0');
    $post_count_admin2 = getThreadPostCount($forum_tbl, $thread_id, '1');

    $thread_count_change = 0;
    $thread_count_admin_change = 0;
    if ($post_count > 0 && $post_count2 == 0) $thread_count_change = -1;
    if ($post_count_admin > 0 && $post_count_admin2 == 0) $thread_count_admin_change = -1;

    updateCount_delete($forum_id, $post_count2 - $post_count, $thread_count_change,
                       $post_count_admin2 - $post_count_admin, $thread_count_admin_change);

    // Delete attachment files from file system.
    delete_attachment($forum_id, $id, $_username, $salt);
    //$msg .= "fake Error: del: $forum_id, $id, $_username, $salt";

    return $msg;
}


// Same as in new.php
function getPostVal() {
    global $title, $body, $keywords;

    $title = U_POST('txt_title');
    $body = U_POST('txt_body');
    $keywords = U_POST('txt_keywords');

    $keywords = str_replace("，", ",", $keywords);
}

//
// Return: empty string if succeed. error message otherwise.
// state: 1 for new.
//
function DoInsert() {
    global $_DEBUG, $forum_id, $forum_tbl, $thread_id; // defined at the top of this page.
    global $title, $body, $keywords;

    $user_id = $_SESSION['ID'];
    $user_name = $_SESSION['username'];
    $date = date('Y-m-d H:i:s', time());
    $salt = get_salt();

    $query =
 "INSERT INTO $forum_tbl ("
 . "thread_id, reply_to_id, user_id, user_name, title, body, keywords, salt, submit_timestamp, submit_ip"
 . ") VALUES ("
 . db_encode( $thread_id ) . ", "
 . db_encode( $thread_id ) . ", "
 . db_encode( $user_id ) . ", "
 . db_encode( $user_name ) . ", "
 . db_encode( $title ) . ", "
 . db_encode( $body ) . ", "
 . db_encode( prepareKeywords($keywords) ) . ", "
 . db_encode( $salt ) . ", "
 . db_encode( $date ) . ", "
 . db_encode( $_SESSION['ip'] )
 . ")";

    //echo "$query<br>"; exit(0);

    $msg = "";
    $post_id = "";

    //db_open();

    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            $query = "SELECT LAST_INSERT_ID() FROM $forum_tbl";
            $post_id = executeScalar($query);

            $query = <<<EOF
UPDATE $forum_tbl SET reply_count = reply_count + 1, 
       last_reply_user_id = '$user_id', 
       last_reply_user_name = '$user_name',
       last_reply_timestamp = '$date' 
WHERE ID = $thread_id
EOF;
            mysql_query($query);

            // upsert keywords
            upsertKeywords($keywords);

            // update forum thread/post count.
            $query = "UPDATE BBS_BoardList SET post_count = post_count + 1, post_count_admin = post_count_admin + 1 WHERE ID = " . db_encode($forum_id);
            mysql_query($query);

            // update user's bbs score
            bbs_addUserScore($user_id, 10);
            bbs_addUserReplyCount($user_id, 1);
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() . " ($query)" : "Database exception");
    }

    //db_close();

    try {
        insert_attachment($forum_id, $post_id, $user_name, $salt); // in attachment.php
    } catch (Exception $e) {
        $msg .= $e->getMessage();
    }

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg.</font>";
    }
    return $msg;
}


// same as new.php
function DoVerify() {
    global $title, $body, $keywords;
    global $e_title, $e_body, $e_keywords;

    if ($title == "") $e_q_title = "Cannot be empty";
    if ($body == "") $e_body = "Cannot be empty";

    if ( $e_title == "" && $e_body == "" && $e_keywords == "") {
        return "";
    } else {
        return "Form error, see above for details.";
    } 
}

// same as new.php, except title, and "reply" header.
function writeEditForm() {
    global $title, $body, $keywords, $_title;
    global $e_title, $e_body, $e_keywords;
    global $forum_title, $_username, $T_forum_help;
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_keywords_title, $T_submit, $T_preview, $T_reply_post;

    if (U_REQUEST('IsPostBack') == "") {
        $_title = "Re: " . db_htmlEncode($_title);
    } else {
        $_title = db_htmlEncode($title);
    }
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    $attachment = get_form_attachment_row("?mode=1&user=$_username");

    $s = <<<EOF
<table class="bbs_post_head"><tr><td>$T_reply_post</td></tr></table>
<table id="bbs_post" class="bbs_post">
<tr><td>$T_forum: </td><td>$forum_title</td></tr>
<tr><td>$T_author: </td><td>$_username</td></tr>
<tr><td>$T_title: </td><td><input type="text" id="txt_title" name="txt_title" class="bbs_title" maxlength="256" value="$_title"> <br><font color='red'> <span id="e_title">$e_title</span></font></td></tr>
<tr><td>$T_body: </td><td><textarea id="txt_body" name="txt_body" class="bbs_body" wrap="virtual">$_body</textarea> <br><font color='red'> <span id="e_body">$e_body</span></font></td></tr>
<tr><td>$T_keywords: <img src="../image/question.gif" title="$T_keywords_title" style="vertical-align: top;"/></td><td><input type="text" id="txt_keywords" name="txt_keywords" class="bbs_keywords" maxlength="256" value="$_keywords"> <font color='red'> <span id="e_keywords">$e_keywords</span></font></td></tr>

$attachment

</table>
<br/>

<div class="bbs_buttons">
<input type="button" onclick="javascript:validate(1);" value="$T_submit" class="btn_bbs">
<input type="button" onclick="javascript:validate(0);" value="$T_preview" class="btn_bbs">
<input type="hidden" id="btnPreview" name="btnPreview" value="preview"/>
</div>

$T_forum_help
EOF;

    print $s;
}

// same in new.php, except the Reply header, and end link.
function writePreviewForm() {
    global $title, $body, $keywords;
    global $forum_title, $_username, $url_params, $thread_id; 
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_submit, $T_edit, $T_back_thread, $T_reply_post;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    $_body_disp = wrapViewBody( $_body );
    //$_body_disp = "<pre class='bbs'>$_body</pre>";

    $attachment = get_form_attachment_row("?readonly=1&mode=1&user=$_username");

    $s = <<<EOF
<table class="bbs_post_head"><tr><td>$T_reply_post</td></tr></table>
<table class="bbs_post">
<tr><td>$T_forum: </td><td>$forum_title</td></tr>
<tr><td>$T_author: </td><td>$_username</td></tr>
<tr><td>$T_title: </td><td><input type="hidden" name="txt_title" value="$_title">$_title</td></tr>
<tr><td>$T_body: </td><td>$_body_disp<input type="hidden" name="txt_body" value="$_body"></td></tr>
<tr><td>$T_keywords: </td><td><input type="hidden" name="txt_keywords" value="$_keywords"> $_keywords</td></tr>

$attachment

</table>
<br/>

<br>
<div class="bbs_buttons">
<input type="submit" name="btnSubmit" value="$T_submit" class="btn_bbs">
<input type="submit" value="$T_edit" class="btn_bbs">
</div>

<p><a href="view.php?$url_params&t=$thread_id">$T_back_thread</a></p>
EOF;

    print $s;
}


function writePageNav(&$cls_page) {
    global $T_page, $T_posts;
    $nav = $cls_page->writeNavBar($T_page, $T_posts);

    $s = <<<EOF
<table class="bbs_list_head"><tr>
<td class="bbs_list_nav">$nav</td>
</tr></table>
EOF;
    print $s;
}


function writeViewList() {
    global $thread_id, $_title, $mode, $url_params, $post_count, $_LANG;
    global $_DEBUG, $forum_tbl; // defined at the top of this page.

    $PAGE_SIZE = 20; // number of posts per page.
    $post_count = getThreadPostCount($forum_tbl, $thread_id, $mode);
    if ($post_count == 0) {
        print "<p style='color: green;'>There is no posts in this thread. <a href='forum.php?$url_params'>Back To Forum</a></p>";
        return;
    }

    $cls_page = new ClsPage($post_count, U_REQUEST_INT('pg'), $PAGE_SIZE, 10, "pg", $_LANG);

    $use_hidden = " AND hidden = 0 ";
    if ($mode == "1" && can_manage()) $use_hidden = "";

    $PAGE_START = $cls_page->getStart() - 1;

    $query = <<<EOF
SELECT ID, user_id, user_name, title, body, keywords, salt, submit_timestamp, submit_ip, 
       last_edit_user_name, last_edit_timestamp,
       marked, digested, top, readonly, hidden
FROM $forum_tbl WHERE thread_id = $thread_id $use_hidden 
ORDER BY ID ASC
LIMIT $PAGE_START, $PAGE_SIZE
EOF;
    //echo "$query<br>"; exit(0);

    writePageNav($cls_page);

    $msg = "";
    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            $i = 0;
            while ($info = mysql_fetch_array($result)) {
                ++ $i;
                writeViewForm($info, $i);
                if ($info['ID'] == $thread_id) { $_title = $info['title']; }
            }


        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg. $query</font>";
    }
    return $msg;
}


//
// Increment click count of this thread. Use cookie to detection past visit.
//
function incClickCount() {
    global $forum_id, $forum_tbl, $thread_id;
    $cookie_name = "_f" . $forum_id . "_" . $thread_id;
    if (isset($_COOKIE[$cookie_name])) { // print "cookie is set: $_COOKIE[$cookie_name]";
        return;
    }
    setcookie($cookie_name);

    $query = "UPDATE $forum_tbl SET click_count = click_count + 1 WHERE ID = " . db_encode($thread_id);
    executeNonQuery($query);
}


function writeCol($v, $style="") {
    return "<td$style>$v</td>";
}

?>


