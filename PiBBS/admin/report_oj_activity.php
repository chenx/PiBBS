<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable_Custom.php");

$page_name = "Report - OJ Activity Statistics";
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

$query = "select action, count(*), Date(timestamp) as Date from log_oj group by action, date(timestamp) order by date(timestamp) DESC";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Type Through All Time");

$query = "select Year(timestamp) as Year, count(*) as Count from log_oj group by Year(timestamp)";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Year");

$query = "select CONCAt(CONCAT(Month(timestamp), '-'), Year(timestamp)) AS Month, count(*) as Count from log_oj group by Month(timestamp), Year(timestamp)";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Month");

$query = "select cast(timestamp as date) as Date, count(*) as Count from log_oj group by cast(timestamp as date)";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Date");

$query = "SELECT A.Date, A.Pass, B.Fail, C.Error, D.Test FROM (select Date, count as Pass from v_oj_activity where action='pass' group by date) AS A, (select Date, count as Fail from v_oj_activity where action='fail' group by date) AS B, (select Date, count as Error from v_oj_activity where action='error' group by date) AS C, (select Date, count as Test from v_oj_activity where action='test' group by date) AS D  WHERE A.Date = B.Date AND A.Date = C.Date AND A.Date = D.Date";
print $cls_tbl->getDBTableAsHtmlTable($query, "Activity By Date - Demo pivot");


db_close();

print "</td></tr></table>";
include("../theme/footer.php");

?>


