<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable_Custom.php");

$page_name = "Report - Site Activity Statistics";
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

$query = "select action, count(*) as Count, Date(timestamp) as Date from log_site group by action, date(timestamp) order by date(timestamp) DESC";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Type Through All Time");

$query = "select cast(timestamp as date) as Date, count(*) as Count from log_site group by cast(timestamp as date) ORDER BY timestamp DESC";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Date");

$query = "select CONCAt(CONCAT(Month(timestamp), '-'), Year(timestamp)) AS Month, count(*) as Count from log_site group by Month(timestamp), Year(timestamp) ORDER BY timestamp DESC";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Month");

$query = "select Year(timestamp) as Year, count(*) as Count from log_site group by Year(timestamp) ORDER BY timestamp DESC";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Year");


db_close();

print "</td></tr></table>";
include("../theme/footer.php");

?>


