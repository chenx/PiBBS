<?php
include_once("bbs_inc.php");

$page_title = "Reply Post";
$thread_id = U_REQUEST_INT('t');
$id = U_REQUEST_INT('id'); // post's ID in db table.
include("../theme/header.php");
?>

<center>

<?php write_breadcrumb(); ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td class="bbs_list_mid">

<table class="bbs_post_head"><tr><td><?php print $T_reply_post; ?></td></tr></table>

<form method="POST">

<?php if (! is_loggedIn()) { 
    writeLoginLink("../bbs/reply.php?$url_params&t=$thread_id&id=$id");
} else if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>$T_submit_confirm</font></p>";
} else {
    $title = ""; $body = ""; $keywords = ""; 
    $e_title = ""; $e_body = ""; $e_keywords = "";
    $error = "";

    if (U_REQUEST('IsPostBack') == "") { getDBVal(); }
    else { getPostVal(); }    

    if ( isset($_POST['btnPreview']) ) {
        $error = DoVerify();
        if ($error == "") {
            if ($_POST['btnPreview'] == "submit") {
                $error = DoReply();
                if ($error == "") { header("Location: reply.php?ok&t=$thread_id&$url_params");  }
                else {
                    print $error;
                    writeEditForm();
                }
            } else {
                writePreviewForm();
            }
        } else { writeEditForm(); }
    } else if ( isset($_POST['btnSubmit']) ) {
        $error = DoReply();
        if ($error == "") { header("Location: reply.php?ok&t=$thread_id&$url_params");  }
        else { 
            print $error; 
            writePreviewForm(); 
        }
    } else {
        writeEditForm();
    }
}
?>

<input type="hidden" name="IsPostBack" value="Y">
</form>

</td>
<td class="bbs_list_right"><br></td>
</tr></table>

<p>
<?php
print "<a href='view.php?t=$thread_id&$url_params'>$T_back_thread</a> | <a href='forum.php?$url_params'>$T_back_forum</a>";
?>
</p>
</center>

<script type="text/javascript">$('#txt_body').focus();</script>
<?php include("../theme/footer.php"); ?>


<?php
//
// Functions.
//

function getPostVal() {
    global $title, $body, $keywords;

    $title = U_POST('txt_title');
    $body = U_POST('txt_body');
    $keywords = U_POST('txt_keywords');

    $keywords = str_replace("，", ",", $keywords);
}


function getDBVal() {
    global $title, $body, $keywords;
    global $id;
    global $_DEBUG, $forum_tbl; // defined at the top of this page.

    $query = "SELECT user_name, title, body, submit_timestamp FROM $forum_tbl WHERE ID = " . db_encode($id);
    //echo "$query<br>"; exit(0);

    $msg = "";

    db_open();

    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            if ($info = mysql_fetch_array($result)) {
                $title = $info['title']; 
                $body = prepare_body($info['body']);
                $keywords = ""; // $info['keywords']; // don't retrieve this, let user fills it.

                if (! startsWith($title, "Re: ") ) {
                    $title = "Re: $title";
                }
                //$body = "\n\n\n【 $info[user_name]'s post on $info[submit_timestamp] mentioned: 】\n" . $body;
                $body = getReplyHead($info['user_name'], $info['submit_timestamp']) . $body;
            }
            else {
                $msg = "Post not found";
            }
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    db_close();

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg. $query</font>";
        print $msg;
    }
    //return $msg;
}

function prepare_body($s) {
    $a = explode("\n", $s);

    $t = "";
    for ($i = 0, $len = min(5, count($a)); $i < $len; ++ $i) {
        $t .= ": $a[$i]\n";
    }

    return $t;
}

//
// Return: empty string if succeed. error message otherwise.
// state: 1 for new.
//
function DoReply() {
    global $_DEBUG, $forum_id, $forum_tbl, $id, $thread_id, $_username; // defined at the top of this page.
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
 . db_encode( $id ) . ", "
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

    db_open();
    $post_id = "";

    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            $query = "SELECT LAST_INSERT_ID() FROM $forum_tbl";
            $post_id = executeScalar($query); // get the pk_id of the new post.

            // update article $thread_id's reply count.
            $query = <<<EOF
UPDATE $forum_tbl SET reply_count = reply_count + 1,
       last_reply_user_id = '$user_id',
       last_reply_user_name = '$user_name',
       last_reply_timestamp = '$date'
WHERE ID = $id
EOF;
            mysql_query($query);

            // update article $thread_id's reply count.
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
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    db_close();

    try {
        insert_attachment($forum_id, $post_id, $_username, $salt); // in attachment.php
    } catch (Exception $e) {
        $msg .= $e->getMessage();
    }

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg. $query</font>";
    }
    return $msg;
}


function DoVerify() {
    global $title, $body, $keywords;
    global $e_title, $e_body, $e_keywords;

    if ($title == "") $e_title = "Cannot be empty";
    if ($body == "") $e_body = "Cannot be empty";

    if ( $e_title == "" && $e_body == "" && $e_keywords == "") {
        return "";
    } else {
        return "Form error, see above for details.";
    } 
}


function writeEditForm() {
    global $title, $body, $keywords;
    global $e_title, $e_body, $e_keywords;
    global $forum_title, $_username, $T_forum_help; 
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_keywords_title, $T_submit, $T_preview;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    $attachment = get_form_attachment_row("?mode=1&user=$_username");

    $s = <<<EOF
<table id="bbs_post" class="bbs_post">
<tr><td>$T_forum: </td><td>$forum_title</td></tr>
<tr><td>$T_author: </td><td>$_username</td></tr>
<tr><td>$T_title: </td><td><input type="text" id="txt_title" name="txt_title" class="bbs_title" maxlength="256" value="$_title"> <br><font color='red'> <span id="e_title">$e_title</span></font></td></tr>
<tr><td>$T_body: </td><td><textarea id="txt_body" name="txt_body" class="bbs_body" wrap="virtual">$_body</textarea> <br><font color='red'> <span id="e_body">$e_body</span></font></td></tr>
<tr><td>$T_keywords: <img src="../image/question.gif" title="$T_keywords_title" style="vertical-align: top;"/><br></td><td><input type="text" id="txt_keywords" name="txt_keywords" class="bbs_keywords" maxlength="256" value="$_keywords"> <font color='red'> <span id="e_keywords">$e_keywords</span></font></td></tr>

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


function writePreviewForm() {
    global $title, $body, $keywords;
    global $forum_title, $_username; 
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_submit, $T_edit;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    $_body_disp = wrapViewBody( $_body );
    //$_body_disp = "<pre class='bbs'>$_body</pre>";
    //$_body = str_replace("\n", "<br>", $_body);
    //$_body = str_replace(" ", "&nbsp;", $_body);

    $attachment = get_form_attachment_row("?readonly=1&mode=1&user=$_username");

    $s = <<<EOF
<table class="bbs_post">
<tr><td>$T_forum: </td><td>$forum_title</td></tr>
<tr><td>$T_author: </td><td>$_username</td></tr>
<tr><td>$T_title: </td><td><input type="hidden" name="txt_title" value="$_title">$_title</td></tr>
<tr><td>$T_body: </td><td>$_body_disp<input type="hidden" name="txt_body" value="$_body"></td></tr>
<tr><td>$T_keywords: </td><td><input type="hidden" name="txt_keywords" value="$_keywords">$_keywords</td></tr>

$attachment

</table>
<br/>

<br>
<div class="bbs_buttons">
<input type="submit" name="btnSubmit" value="$T_submit" class="btn_bbs">
<input type="submit" value="$T_edit" class="btn_bbs">
</div>
EOF;

    print $s;
}


?>


