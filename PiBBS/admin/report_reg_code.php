<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable_Custom.php");

$page_name = "Report - Registration Code Statistics";
$page_title = "Admin - $page_name";

$div_main_id = "main_full_width";
include("../theme/header.php"); 
?>
<table width="100%"><tr><td>

<P><a href="./">Admin</a> &gt; <?php echo $page_name; ?></P>

<?php
db_open();

$cls_tbl = new Cls_DBTable_Custom();
$cls_tbl->setTBStyle(" border=1");

$query = "select U.ID, U.login, count(code) from User U LEFT OUTER JOIN code_register C " 
         . "ON U.ID = C.owner_user_id group by U.ID";
print $cls_tbl->getDBTableAsHtmlTable($query, "<font color='red'><u>Total</u></font> Registration Code Stats");

$query = "select U.ID, U.login, count(code) from User U LEFT OUTER JOIN code_register C "
         . "ON U.ID = C.owner_user_id AND C.is_used = 0 group by U.ID";
print $cls_tbl->getDBTableAsHtmlTable($query, "<font color='red'><u>Unused</u></font> Registration Code Stats");

$query = "select U.ID, U.login, count(code) from User U LEFT OUTER JOIN code_register C "
         . "ON U.ID = C.owner_user_id AND C.is_used = 1 group by U.ID";
print $cls_tbl->getDBTableAsHtmlTable($query, "<font color='red'><u>Used</u></font> Registration Code Stats");

db_close();

print "</td></tr></table>";
include("../theme/footer.php");

?>


