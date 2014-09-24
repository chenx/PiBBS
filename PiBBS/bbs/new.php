<?php
include_once("bbs_inc.php");

$page_title = "New Post";
include("../theme/header.php");
?>

<center>

<?php write_breadcrumb(); ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td class="bbs_list_mid">

<table class="bbs_post_head"><tr><td><?php print $T_new_post; ?></td></tr></table>

<form method="POST">

<?php 
$insert_tid = ""; // thread id of the new inserted entry.
$goto_thread = "";

if (! is_loggedIn()) { 
    writeLoginLink("../bbs/new.php?$url_params");
} else if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>$T_submit_confirm</font></p>";
    $thread_id = U_REQUEST('t');
    $goto_thread = ($thread_id == "") ? "" : 
         "<a href='../bbs/view.php?f=$forum_id&t=$thread_id'>$T_back_thread</a> | ";
} else {
    $title = ""; $body = ""; $keywords = ""; 
    $e_title = ""; $e_body = ""; $e_keywords = "";
    $error = "";

    getPostVal();   

    if ( isset($_POST['btnPreview']) ) {
        $error = DoVerify();
        if ($error == "") {
            if ($_POST['btnPreview'] == "submit") {
                $error = DoInsert();
                if ($error == "") { header("Location: new.php?ok&t=$insert_tid&$url_params");  }
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
        if ($error == "") { header("Location: new.php?ok&t=$insert_tid&$url_params");  }
        else { 
            print $error; 
            writePreviewForm(); 
        }
    } else {
        writeEditForm();
    }
}
?>

</form>
</td>
<td class="bbs_list_right"><br></td>
</tr></table>

<p><?php print $goto_thread; ?><a href='forum.php?<?php print $url_params; ?>'><?php print "$T_back_forum"; ?></a></p>
</center>

<script type="text/javascript">$('#txt_title').focus();</script>
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

//
// Return: empty string if succeed. error message otherwise.
// state: 1 for new.
//
function DoInsert() {
    global $_DEBUG, $forum_id, $forum_tbl, $_username; // defined at the top of this page.
    global $title, $body, $keywords;

    $user_id = $_SESSION['ID'];
    $date = date('Y-m-d H:i:s', time());
    $salt = get_salt(); 

    $query =
 "INSERT INTO $forum_tbl ("
 . "user_id, user_name, title, body, keywords, salt, submit_timestamp, submit_ip"
 . ") VALUES ("
 . db_encode( $user_id ) . ", "
 . db_encode( $_username ) . ", "
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

    db_open();

    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            $query = "SELECT LAST_INSERT_ID() FROM $forum_tbl";
            $post_id = executeScalar($query); // get the pk_id of the new post.

            global $insert_tid;
            $insert_tid = $post_id;

            $query = "UPDATE $forum_tbl SET thread_id = ID, reply_to_id = 0 WHERE ID = $post_id";
            mysql_query($query);

            // upsert keywords
            upsertKeywords($keywords);

            $f_id = db_encode($forum_id);
            // update forum thread/post count.
            $query = <<<EOF
UPDATE BBS_BoardList SET thread_count = thread_count + 1, post_count = post_count + 1, 
       thread_count_admin = thread_count_admin + 1, post_count_admin = post_count_admin + 1
WHERE ID = $f_id
EOF;
            mysql_query($query);

            // update user's bbs score
            bbs_addUserScore($user_id, 10);
            bbs_addUserNewCount($user_id, 1);
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() . " ($query)" : "Database exception");
    }

    db_close();

    try {
        insert_attachment($forum_id, $post_id, $_username, $salt); // in attachment.php
    } catch (Exception $e) {
        $msg .= $e->getMessage();
    }

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg.</font>";
    }
    return $msg;
}


function DoVerify() {
    global $title, $body, $keywords;
    global $e_title, $e_body, $e_keywords, $T_no_empty;

    if ($title == "") $e_title = $T_no_empty;
    if ($body == "") $e_body = $T_no_empty; 

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
<tr><td>$T_body:</td><td><textarea id="txt_body" name="txt_body" class="bbs_body" wrap="virtual">$_body</textarea> <br><font color='red'> <span id="e_body">$e_body</span></font></td></tr>
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
    //echo writeP($error, 0); 
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
<tr><td>$T_body:</td><td>$_body_disp<input type="hidden" name="txt_body" value="$_body"></td></tr>
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


