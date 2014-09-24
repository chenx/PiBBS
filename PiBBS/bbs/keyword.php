<?php
include_once("bbs_inc.php");

$thread_id = U_REQUEST_INT('t');
$page_title = "Keywords - $forum_title";
include_once("../theme/header.php"); 

db_open();
?>

<center>

<?php write_breadcrumb(); ?>

<table class="bbs_box"><tr>
<td class="bbs_list_left"><br></td>
<td>

<table class="bbs_list_head">
<tr>
<td>
<?php
    $k = db_htmlEncode( U_REQUEST("k") );

    $check_fuzzy = '';
    if (U_REQUEST('fuzzy') != "") { $check_fuzzy = " checked"; }

    $s = <<<EOF
<a href="#" onclick="javascript: GoBack();">$T_back</a> | $T_search_keyword: 
<input type="search" results id="searchKeyword" value="$k" onkeyup="javascript: OnEnterKeyword(event);" 
       placeholder="$T_search_keyword_placeholder" title="$T_search_keyword_title" style="width: 150px;">
<img src="../image/search.png" border="0" style="height:20px; vertical-align:top;"      
     onclick="javascript: DoSearchKeyword();" title="$T_search_keyword_title">
<input type="checkbox" id="cbFuzzy" value="Y" style="vertical-align:middle;"$check_fuzzy>$T_fuzzy_match 
EOF;
    print $s;
?>
</td>
<td><br></td>
<td align="right">
<?php 
$PAGE_SIZE = 50;
$cls_page = new ClsPage(getSearchCount(), U_REQUEST_INT('p'), $PAGE_SIZE, 10, "p", $_LANG);
print $cls_page->writeNavBar($T_page, $T_posts);
?>
</td>
</tr>
</table>

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
<td class="bbs_list_right"><br></td>
</tr></table>

</center>

<script type="text/javascript">
<?php if (can_manage()) { ?>
function deleteThread(id, rank) {
    if (confirm('Are you sure to delete the entire thread (row ' + rank + ')? This cannot be reversed.')) {
        $("#deleteID").val(id);
        document.forms[0].submit();
    }
}
<?php } ?>

function DoSearchKeyword() {
    var v = encodeURIComponent( $.trim( $("#searchKeyword").val() ) );
    if (v == '') {
        $("#searchKeyword").focus();
        return;
    }

    var fuzzy = '';
    if ($("#cbFuzzy").is(':checked')) { fuzzy = '&fuzzy=Y' }

    window.location = 'keyword.php?f=<?php print $forum_id; ?>&k=' + v + 
                      '<?php if ($thread_id != 0) print "&t=$thread_id"; ?>' + fuzzy;
}

function OnEnterKeyword(e) {
    var key = e.keyCode || e.which;

    if (key === 13) {
        DoSearchKeyword();
    }
    return false;
}

function GoBack() {
    window.location = 'view.php?f=<?php print $forum_id; ?>&t=<?php print $thread_id; ?>';
}

$("#searchKeyword").focus();
</script>

<?php

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

function matchKeyword() {
    $k = U_REQUEST('k');
    if (U_REQUEST('fuzzy') == "") { // accurate match.
        $s = "keywords like " . db_encode( "%,$k,%" );
    } else {
        $s = "keywords like " . db_encode( "%$k%" );
    }
    return $s;
}

function getSearchCount() {
    global $forum_tbl, $mode;

    $use_hidden = " AND hidden = 0 ";
    if ($mode == 1 && can_manage()) $use_hidden = ""; // Manage mode.

    $k = matchKeyword();

    $query = "SELECT COUNT(ID) as ct FROM $forum_tbl WHERE $k $use_hidden ";
    return executeScalar($query);
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

    $k = matchKeyword(); // db_encode( "%" . U_REQUEST('k') . "%" );

    $query = <<<EOF
SELECT ID, thread_id, title, user_id, user_name, DATE(submit_timestamp) AS submit_timestamp,
       click_count, reply_count, last_reply_user_name, DATE(last_reply_timestamp) AS last_reply_timestamp,
       marked, digested, top, readonly, hidden,
       submit_timestamp AS timestamp
FROM $forum_tbl WHERE $k $use_hidden
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
        . writeCol($T_post_date)
        . writeCol($T_author)
        . writeCol($T_click)
        . writeCol($T_reply)
        . writeCol($T_last_reply)
        . writeCol($T_author)
        ;

    //global $mode;
    //if ($mode == "1" && can_manage()) $s .= writeManageHeader();

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

function writeRow($info, $rank) {
    global $url_params;
    $s =  writeCol( getRank($rank, $info['top']) )
        . writeCol( getMsgLabel($info['marked'], $info['digested']) )
        . writeCol("<a href='post.php?$url_params&t=" . $info['thread_id'] . "&i=" . $info['ID'] . "'>" . db_htmlEncode($info['title']) . "</a>") 
        . writeCol($info['submit_timestamp'])
        . writeCol("<a href='user.php?u=$info[user_name]' class='bbs_user'>" . $info['user_name'] . "</a>")
        . writeCol($info['click_count'])
        . writeCol($info['reply_count'])
        . writeCol($info['last_reply_timestamp'])
        . writeCol("<a href='user.php?u=$info[last_reply_user_name]' class='bbs_user'>" . $info['last_reply_user_name'] . "</a>")
        ;

    //global $mode;
    //if ($mode == "1" && can_manage()) $s .= getManageFunc($info, $rank);

    return "<tr>$s</tr>";
}


function writeCol($v, $style="") {
    return "<td$style>$v</td>";
}

?>


