<?php
session_start();
$page_title = "Member List";
$root_path = "..";
$custom_header = <<<EOF
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> $(document).ready(function() { $("#code_list").tablesorter(); } ); </script>
<script type='text/javascript' src='../func/ClsPage.js'></script>
EOF;

include_once("../theme/header.php");

include_once("../menu/menu.php");
include_once("../func/db.php");
include_once("../func/util.php");
include_once("terms_users.php");
require_once("../func/ClsPage.php");
?>

<table width="98%" border="0"><tr>
<td align="center"><h3><?php print $T_user_list; ?></h3></td>
</tr>
</table>

<table class="user_list_nav">
<tr>
<td align="right">
<?php
$PAGE_SIZE = 100;
$cls_page = new ClsPage(getUserCount(), U_REQUEST_INT('p'), $PAGE_SIZE, 10, "p", $_LANG);
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
<th>$T_login</th>
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
           $T_send_email, $T_send_imail, $is_mobile;
    $PAGE_START = $cls_page->getStart() - 1;

    // ID 1 is for Admin, don't show Admin.
    if ($is_mobile) {
        $query = <<<EOF
SELECT ID, login, DATE_FORMAT(reg_date, '%Y-%m-%d') AS reg_date, 
       DATE_FORMAT(last_login, '%Y-%m-%d') AS last_login 
FROM User WHERE ID > 1 ORDER BY ID DESC
LIMIT $PAGE_START, $PAGE_SIZE
EOF;
    } else {
        $query = <<<EOF
SELECT ID, login, reg_date, last_login 
FROM User WHERE ID > 1 ORDER BY ID DESC 
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

function getUserCount() {
    $query = "SELECT Count(*) AS ct FROM User WHERE ID > 1";
    return executeScalar($query);
}

?>

</table>


<?php include_once("../theme/footer.php");  ?>

