<?php
include_once("bbs_inc.php");

$page_title = "Edit Post";
$thread_id = U_REQUEST_INT('t');
$id = U_REQUEST_INT('id'); // post's ID in db table.
include("../theme/header.php");
?>

<center>

<?php write_breadcrumb(); ?>

<?php if (! hasPermission()) { ?>
<p><font color="red">You have no permission for this action.</font></p>
<?php } else { ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td class="bbs_list_mid">

<table class="bbs_post_head"><tr><td><?php print $T_edit_post; ?></td></tr></table>

<form method="POST">

<?php if (! is_loggedIn()) { 
    writeLoginLink("../bbs/edit.php?$url_params&t=$thread_id&id=$id");
} else if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>$T_submit_confirm</font></p>";
} else {
    $title = ""; $body = ""; $keywords = ""; 
    $e_title = ""; $e_body = ""; $e_keywords = "";
    $error = "";

    if (U_REQUEST('IsPostBack') == "") { getDBVal(); }
    else { getPostVal(); }
    process(); 
}
?>

<input type="hidden" name="IsPostBack" value="Y">
<script type="text/javascript">$('#txt_title').focus();</script>
</form>

</td>
<td class="bbs_list_right"><br></td>
</tr></table>

<?php } ?>

<p>
<?php
print "<a href='view.php?t=$thread_id&$url_params'>$T_back_thread</a> | <a href='forum.php?$url_params'>$T_back_forum</a>";
?>
</p>
</center>

<?php include("../theme/footer.php"); ?>


<?php
//
// Functions.
//

function hasPermission() {
    global $forum_tbl, $id;
    if ($id == "") return 0;

    $sql = "SELECT user_id, readonly FROM $forum_tbl WHERE ID = " . db_encode($id);

    db_open();
    $user_id = executeScalar($sql, "user_id");
    $readonly = executeScalar($sql, "readonly");
    db_close();

    return can_modify($user_id, $readonly);
}

function process() {
    global $error, $thread_id, $id, $url_params;

    if ( isset($_POST['btnPreview']) ) {
        $error = DoVerify();
        if ($error == "") {
            if ($_POST['btnPreview'] == "submit") {
                $error = DoUpdate();
                if ($error == "") { header("Location: edit.php?ok&t=$thread_id&id=$id&$url_params");  }
                else {
                    print $error;
                    writeEditForm();
                }
            } else {
                writePreviewForm();
            }
        } else { writeEditForm(); }
    } else if ( isset($_POST['btnSubmit']) ) {
        $error = DoUpdate();
        if ($error == "") { header("Location: edit.php?ok&t=$thread_id&id=$id&$url_params");  }
        else {
            print $error;
            writePreviewForm();
        }
    } else {
        writeEditForm();
    }
}


function getPostVal() {
    global $title, $body, $keywords;

    $title = U_POST('txt_title');
    $body = U_POST('txt_body');
    $keywords = U_POST('txt_keywords');

    $keywords = str_replace("，", ",", $keywords);
}

function getDBVal() {
    global $title, $body, $keywords;
    global $id, $can_modify;
    global $_DEBUG, $forum_tbl; // defined at the top of this page.

    $query = "SELECT ID, user_id, user_name, title, body, keywords, submit_timestamp FROM $forum_tbl WHERE ID = " . db_encode($id);
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
                $body = $info['body'];
                $keywords = $info['keywords'];
            }
            else {
                $msg = "Record not found";
            }
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    db_close();

    if ($msg != "") {
        $msg = "<p><font color='red'>Error: $msg.</font></p>";
        print $msg;
    }
}

//
// Return: empty string if succeed. error message otherwise.
// state: 1 for new.
//
function DoUpdate() {
    global $_DEBUG, $forum_tbl, $id, $_username; // defined at the top of this page.
    global $title, $body, $keywords;

    $date = date('Y-m-d H:i:s', time());

    $query =
 "UPDATE $forum_tbl SET "
 . " title = " . db_encode( $title )
 . ", body = " . db_encode( $body )
 . ", keywords = " . db_encode( prepareKeywords($keywords) )
 . ", last_edit_user_id = " . db_encode( $_SESSION['ID'] )
 . ", last_edit_user_name = " . db_encode( $_SESSION['username'] )
 . ", last_edit_timestamp = " . db_encode( $date )
 . " WHERE ID = " . db_encode($id);

    //echo "$query<br>"; exit(0);

    $msg = "";

    db_open();

    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    db_close();

    if ($msg != "") {
        $msg = "<p><font color='red'>Error: $msg</font></p>";
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
    global $forum_title, $_username, $T_forum_help, $forum_id, $id; 
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_keywords_title, $T_submit, $T_preview, $T_reply_post;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    $attachment = get_form_attachment_row("?mode=2&user=$_username&fid=$forum_id&pid=$id");

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
    global $forum_title, $_username, $forum_id, $id; 
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_submit, $T_edit, $T_back_thread, $T_reply_post;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);

    //$_body_disp = "<pre class='bbs'>$_body</pre>";
    $_body_disp = wrapViewBody( $_body );

    $attachment = get_form_attachment_row("?readonly=1&mode=2&user=$_username&fid=$forum_id&pid=$id");

    $s = <<<EOF
<table class="bbs_post">
<tr><td>$T_forum: </td><td>$forum_title</td></tr>
<tr><td>$T_author: </td><td>$_username</td></tr>
<tr><td>$T_title: </td><td><input type="hidden" name="txt_title" value="$_title">$_title</td></tr>
<tr><td>$T_body</td><td>$_body_disp<input type="hidden" name="txt_body" value="$_body"></td></tr>
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


