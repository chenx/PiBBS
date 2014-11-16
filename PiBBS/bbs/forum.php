<?php
include_once("bbs_inc.php");
include_once("bbs_mgr_func.php");

$page_title = "$forum_title";
include_once("../theme/header.php"); 

db_open();
?>

<center>

<?php write_breadcrumb(); ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><?php /*include "list_left_pane.php";*/ ?><br></td>
<td class="bbs_list_mid">

<?php
if ($is_mobile && can_new()) {
    print "<div style='text-align: left;'><a href=\"new.php?$url_params\">$T_add_new_post</a></div><br/>";
}

?>

<table class="bbs_list_head"><tr><td>
<?php 
if (! $is_mobile && can_new()) { 
    print "<a href=\"new.php?$url_params\">$T_add_new_post</a><br/>"; 
/*  
    $s = <<<EOF
<a href="#" title="<?php echo $T_bbs_help; ?>" onclick="javascript: var o = window.open('../help/q.php?q=bbs_help', 'helpWin', 'height=500,width=500,modal=yes,alwaysRaised=yes,toolbar=0,location=0,menubar=0,scrollbars=yes'); o.focus(); return false;"><img src="../image/help01.png" border="0" style="vertical-align: middle;"></a>
EOF;
    print $s; // help icon.
*/
}

if (! $is_mobile) { print "</td><td><br></td><td align=\"right\">"; }

$PAGE_SIZE = 50;
$cls_page = new ClsPage(getForumThreadCount($forum_id, $mode), U_REQUEST_INT('p'), $PAGE_SIZE, 10, "p", $_LANG);
print $cls_page->writeNavBar($T_page, $T_threads);

?>
</td></tr></table>

<form method="POST">
<input type='hidden' id='deleteID' name='deleteID' value=''/>
<input type="hidden" id="action" name="action" value='' />

<?php

if (isset($_REQUEST['deleteID']) && $_REQUEST['deleteID']  != "" && can_manage()) {
    $msg = deleteThread($_REQUEST['deleteID']);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        print $msg;
    }
    else {
        //print "<script type='text/javascript'>alert('Deleted has finished successfully.');</script>";
    }
}

showList();

?>
</form>

</td>
<td class="bbs_list_right"><?php /*include "list_right_pane.php";*/ ?><br></td>
</tr></table>

</center>

<?php
if (can_manage()) {
?>
<script type="text/javascript">
function deleteThread(id, rank) {
    if (confirm('Are you sure to delete the entire thread (row ' + rank + ')? This cannot be reversed.')) {
        $("#deleteID").val(id);
        document.forms[0].submit();
    }
}
</script>
<?php
}

db_close();
include("../theme/footer.php");

//
// Functions.
//

function deleteThread($id) {
    global $forum_id, $forum_tbl;
    $thread_id = $id;

    $post_count = getThreadPostCount($forum_tbl, $thread_id, '0');
    $post_count_admin = getThreadPostCount($forum_tbl, $thread_id, '1');

    $query = "DELETE FROM $forum_tbl WHERE thread_id = " . db_encode($id);
    $msg = executeNonQuery($query);

    $post_count2 = getThreadPostCount($forum_tbl, $thread_id, '0');
    $post_count_admin2 = getThreadPostCount($forum_tbl, $thread_id, '1');

    $thread_count_change = 0;
    $thread_count_admin_change = 0;
    if ($post_count > 0 && $post_count2 == 0) $thread_count_change = -1;
    if ($post_count_admin > 0 && $post_count_admin2 == 0) $thread_count_admin_change = -1;

    updateCount_delete($forum_id, $post_count2 - $post_count, $thread_count_change,
                       $post_count_admin2 - $post_count_admin, $thread_count_admin_change);

    return $msg;
}


//
// Get row number: http://stackoverflow.com/questions/2520357/mysql-get-row-number-on-select
//
function showList() {
    global $link, $forum_tbl, $PAGE_SIZE, $cls_page, $mode;
    global $_DEBUG;

    $PAGE_START = $cls_page->getStart() - 1;

    $use_hidden = " AND hidden = 0 ";
    if ($mode == 1 && can_manage()) $use_hidden = ""; // Manage mode.

    $query = <<<EOF
SELECT ID, thread_id, title, user_id, user_name, DATE(submit_timestamp) AS submit_timestamp,
       click_count, reply_count, last_reply_user_name, DATE(last_reply_timestamp) AS last_reply_timestamp,
       marked, digested, top, readonly, hidden,
       CASE WHEN last_reply_timestamp is NULL then submit_timestamp ELSE last_reply_timestamp END AS timestamp
FROM $forum_tbl WHERE ID = thread_id $use_hidden
ORDER BY top DESC, timestamp DESC
LIMIT $PAGE_START, $PAGE_SIZE
EOF;

    //echo $query;
 
    $msg = "";
    try {
        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            if (mysql_num_rows($result) > 0) {
                $i = 0;
                $s = writeTitle();
                while ($info = mysql_fetch_array($result)) {
                    ++ $i;
                    $s .= writeRow($info, $PAGE_START + $i);
                }
                $s = "<table id=\"bbs_forum_list\" class=\"bbs_list\">$s</table>";
                print $s;
            }

        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    if ($msg != "") {
        print "<font color='red'>$msg</font>";
    }
}

function getStateName($v) {
    $s = "";
    if ($v == 1) $s = "New";
    else if ($v == 2) $s = "Submitted";
    else if ($v == 3) $s = "Reviewed";
    else if ($v == 4) $s = "Deployed";
    else $s = "(Unknown)";
    return $s;
}

function writeTitle() {
    global $T_title, $T_post_date, $T_author, $T_click, $T_reply, $T_last_reply;
    $s =  writeCol("")
        . writeCol("<br>")
        . writeCol($T_title)
        . writeCol($T_post_date, " class='desktop'")
        . writeCol($T_author, " class='desktop'")
        . writeCol($T_click)
        . writeCol($T_reply)
        . writeCol($T_last_reply, " class='desktop'")
        . writeCol($T_author, " class='desktop'")
        ;

    global $mode;
    if ($mode == "1" && can_manage()) $s .= list_writeManageHeader();

    return "<tr>$s</tr>";
}

function getMsgLabel($marked, $digested) {
    if ($marked == "1" && $digested == "1") {
        $v = "b";
    } 
    else if ($marked == "1") {
        $v = "m";
    } 
    else if ($digested == "1") {
        $v = "g";
    }
    else {
        $v = "";
    }
    return $v;
}

function getRank($rank, $top) {
    if ($top) {
        $rank = "<span style='color:red;' title='Top article'>$rank&uarr;</span>";
    }
    return $rank;
}

// Highlight top posts title in red in the forum post list.
function highlightTopTitle($title, $top) {
    global $_HIGHLIGHT_TOP_TITLE;
    if ($_HIGHLIGHT_TOP_TITLE && $top) {
        $title = "<font color=\"red\">$title</font>";
    }
    return $title;
}

// can use getMBStrMaxSize() function to fit size of title.
function writeRow($info, $rank) {
    global $url_params;
    $s =  writeCol( getRank($rank, $info['top']) )
        . writeCol( getMsgLabel($info['marked'], $info['digested']) )
        . writeCol("<a href='view.php?$url_params&t=" . $info['thread_id'] . "'>" .
              highlightTopTitle(db_htmlEncode($info['title']), $info['top']) . "</a>")         
        . writeCol($info['submit_timestamp'], " class='desktop'")
        . writeCol("<a href='user.php?u=$info[user_name]' class='bbs_user'>" . $info['user_name'] . "</a>", " class='desktop'")
        . writeCol($info['click_count'])
        . writeCol($info['reply_count'])
        . writeCol($info['last_reply_timestamp'], " class='desktop'")
        . writeCol("<a href='user.php?u=$info[last_reply_user_name]' class='bbs_user'>" . 
                   $info['last_reply_user_name'] . "</a>", " class='desktop'")
        ;

    global $mode;
    if ($mode == "1" && can_manage()) $s .= list_getManageFunc($info, $rank);

    return "<tr>$s</tr>";
}


function writeCol($v, $style="") {
    return "<td$style>$v</td>";
}

?>


