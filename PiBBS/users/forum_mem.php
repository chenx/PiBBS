<?php
session_start();
$page_title = "Forum Member List";
$root_path = "..";
$custom_header = <<<EOF
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> $(document).ready(function() { $("#code_list").tablesorter(); } ); </script>
<script type='text/javascript' src='../func/ClsPage.js'></script>
EOF;

include_once("../func/db.php");
include_once("../func/util.php");
include_once("terms_users.php");
require_once("../func/ClsPage.php");
require_once("../bbs/bbs_func.php");

$forum_id = U_REQUEST_INT("f");
if ($forum_id == 0) {
    header("Location: ../bbs");
    exit();
}
if (! (IsAdmin() || is_private_board_member($forum_id))) {
    header("Location: ../bbs"); // no permission to see this page.
    exit();
}
$forum_title = getForumTitleFromID($forum_id);

function getForumTitleFromID($forum_id) {
    global $_LANG;
    $title = ($_LANG == "en") ? "title_en" : "title";
    $sql = "SELECT $title FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    return executeScalar($sql);
}

include_once("../theme/header.php");
include_once("../menu/menu.php");
?>

<table width="98%" border="0"><tr>
<td align="center"><h3><?php print "$forum_title [$T_private_member_list]"; ?></h3></td>
</tr>
</table>

<table class="user_list_nav">
<tr>
<td align="right">
<?php
$PAGE_SIZE = 100;
$cls_page = new ClsPage(getUserCount($forum_id), U_REQUEST_INT('p'), $PAGE_SIZE, 10, "p", $_LANG);
print $cls_page->writeNavBar($T_page, $T_total);
?>
</td>
</tr>
</table>

<table id="code_list" class="user_list">
<?php

$s = <<<EOF
<thead title="$T_click_to_sort" style="background-color: #ccccff; cursor:pointer; text-align: center;">
<th width="20"><img src="../image/sorter.gif"></th>
<th>$T_username</th>
<th>$T_reg_date</th>
<th>$T_last_login</th>
<th>$T_send_email</th>
<th>$T_send_imail</th>
</thead>
EOF;

print $s;

getUserList();

function getUserList() {
    global $PAGE_SIZE, $cls_page, $T_click_to_sort, $question_id, 
           $T_send_email, $T_send_imail, $is_mobile, $forum_id;
    $PAGE_START = $cls_page->getStart() - 1;
    $fid = db_encode($forum_id);

    // ID 1 is for Admin, don't show Admin.
    if ($is_mobile) {
        $query = <<<EOF
SELECT U.ID, U.login, DATE_FORMAT(U.reg_date, '%Y-%m-%d') AS reg_date, 
       DATE_FORMAT(U.last_login, '%Y-%m-%d') AS last_login 
FROM User U, BBS_PrivateMembership M WHERE          
U.ID = M.user_id AND M.forum_id = $fid
ORDER BY U.ID DESC
LIMIT $PAGE_START, $PAGE_SIZE
EOF;
    } else {
        $query = <<<EOF
SELECT U.ID, U.login, U.reg_date, U.last_login 
FROM User U, BBS_PrivateMembership M WHERE 
U.ID = M.user_id AND M.forum_id = $fid
ORDER BY U.ID DESC 
LIMIT $PAGE_START, $PAGE_SIZE
EOF;
    }
    $t = executeDataTable($query);

    $len = count($t);
    if ($len > 0) {
        // write rows.
        for ($i = 1; $i < $len; ++ $i) {
            $username = $t[$i][1];
            $reg_date = $t[$i][2];
            $last_login = $t[$i][3];

            $s = "<td align='center'>" . ($PAGE_START + $i) . "</td>";
            $s .= "<td><a href=\"user.php?u=$username\">$username</a></td>";
            $s .= "<td align='center'>$reg_date</td>";
            $s .= "<td align='center'>$last_login</td>";
            $s .= "<td align='center'><a href='../email/?to=$username'>$T_send_email</a></td>";
            $s .= "<td align='center'><a href='../imail/compose.php?to=$username'>$T_send_imail</a></td>";
            print "<tr>$s</tr>";
        }
    }
}

function getUserCount($forum_id) {
    //$query = "SELECT Count(*) AS ct FROM User WHERE ID > 1";
    $query = <<<EOF
SELECT COUNT(*) FROM User U, BBS_PrivateMembership M WHERE
U.ID = M.user_id AND M.forum_id = $forum_id
EOF;
    return executeScalar($query);
}

?>

</table>


<?php include_once("../theme/footer.php");  ?>

