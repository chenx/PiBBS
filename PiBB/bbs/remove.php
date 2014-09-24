<?php
include_once("bbs_inc.php");

$page_title = "Remove Post";
$thread_id = U_REQUEST_INT('t');
$id = U_REQUEST_INT('id'); // post's ID in db table.
include("../theme/header.php");
?>

<center>

<?php write_breadcrumb(); ?>

<?php if (! hasPermission()) { ?>
<p><font color="red">You have no permission for this action.</font></p>
<?php } else {?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td>

<table class="bbs_post_head"><tr><td><?php print $T_remove_post; ?></td></tr></table>

<form method="POST">

<?php if (! is_loggedIn()) { 
    writeLoginLink("../bbs/remove.php?$url_params&t=$thread_id&id=$id");
} else if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>$T_remove_confirm</font></p>";
} else {
    $title = ""; $body = ""; $keywords = ""; $username = ""; 
    $e_title = ""; $e_body = ""; $e_keywords = "";
    $error = "";

    if (U_REQUEST('IsPostBack') == "") { getDBVal(); }
    //else { getPostVal(); }

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

    if ( isset($_POST['btnRemove']) ) {
        $error = DoRemove();
        if ($error == "") { header("Location: remove.php?ok&t=$thread_id&id=$id&$url_params");  }
        else {
            print $error;
            getPostVal();
            writePreviewForm();
        }
    } else {
        writePreviewForm();
    }
}


function getPostVal() {
    global $title, $body, $keywords;

    $title = U_POST('txt_title');
    $body = U_POST('txt_body');
    $keywords = U_POST('txt_keywords');
}

function getDBVal() {
    global $title, $body, $keywords, $username;
    global $id, $can_modify;
    global $_DEBUG, $forum_tbl; // defined at the top of this page.

    $query = "SELECT ID, user_name, title, body, keywords, submit_timestamp FROM $forum_tbl WHERE ID = " . db_encode($id);
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
                $username = $info['user_name'];
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
function DoRemove() {
    global $_DEBUG, $forum_id, $forum_tbl, $thread_id, $id; 

    $query = "UPDATE $forum_tbl SET hidden = 1 WHERE ID = " . db_encode($id);
    //echo "$query<br>"; exit(0);

    $msg = "";

    db_open();
    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        }
        else {
            // update forum thread/post count.
            $query = "UPDATE BBS_BoardList SET post_count = post_count - 1 WHERE ID = " . db_encode($forum_id);
            mysql_query($query);

            // if this thread's post count is 0, decrement thread id.
            $query = "select count(*) AS ct from $forum_tbl WHERE thread_id = " . db_encode($thread_id) . " AND hidden = 0";
            $ct = executeScalar($query, "ct");
            if ($ct == 0) {
                $query = "UPDATE BBS_BoardList SET thread_count = thread_count - 1 WHERE ID = " . db_encode($forum_id);
                mysql_query($query);
            }
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


function writePreviewForm() {
    global $title, $body, $keywords, $username;
    global $forum_title, $forum_id, $id;
    global $T_forum, $T_author, $T_title, $T_body, $T_keywords, $T_remove, $T_remove_prompt;

    $_title = db_htmlEncode($title);
    $_body = db_htmlEncode($body);
    $_keywords = db_htmlEncode($keywords);
    $_username = db_htmlEncode($username);

    //$_body_disp = "<pre class='bbs'>$_body</pre>";
    $_body_disp = wrapViewBody( $_body );

    $attachment = get_form_attachment_row("?readonly=1&mode=2&user=$_username&fid=$forum_id&pid=$id");

    $s = <<<EOF
<table id="bbs_post" class="bbs_post">
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
<input type="button" onclick="javascript: doDelete();" value="$T_remove" class="btn_bbs">
<input type="hidden" id="btnRemove" name="btnRemove" value="Y">
</div>

<script type="text/javascript">
function doDelete() {
    var o = confirm("$T_remove_prompt");
    if (o) {
        document.forms[0].submit();
    }
}
</script>
EOF;

    print $s;
}


?>


