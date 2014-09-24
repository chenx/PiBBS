<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/Cls_DBTable.php");

$page_title = "Admin";
$page_name = $_SERVER['PHP_SELF'];

$div_main_id = "main_full_width";
include_once("../theme/header.php");
?>

<table width="100%"><tr><td>

<h3>Admin</h3>

<p>Database tables to manage (add/edit/delete).</p>

<ul>
<li><a href="admin_list.php?tbl=User">User (Users)</a></li>
<li><a href="admin_list.php?tbl=UserGroup">UserGroup (User Group/Role)</a></li>
<!--
<li><a href="admin_list.php?tbl=log_site">Log_site (Site Activity Log)</a></li>
-->
<li><a href="admin_list.php?tbl=code_register">Code_register (Registration Code)</a></li>
<!--<li><a href="admin_list.php?tbl=Schema_TblCol">Schema_TblCol (Schema of Table columns)</a></li>-->
<li><a href="admin_list.php?tbl=User_LinkedIn">User_LinkedIn (Linkedin Account Binding)</a></li>
</ul>

<p>BBS management</p>
<ul>
<li><a href="bbs_board.php">Manage BBS Board Tables (add/remove board/forum)</a>
<li><a href="admin_list.php?tbl=BBS_BoardList">BBS_BoardList (BBS Board List)</a></li>
<li><a href="admin_list.php?tbl=BBS_BoardGroups">BBS_BoardGroups (BBS Board Group)</a></li>
<li><a href="admin_list.php?tbl=BBS_BoardManager">BBS_BoardManager (BBS Board Manager)</a></li>
<li><a href="admin_list.php?tbl=BBS_PrivateMembership">BBS_PrivateMembership</a></li>
</ul>

<p>IMail management</p>
<ul>
<li><a href="admin_list.php?tbl=IMail">IMail</a>
<li><a href="admin_list.php?tbl=IMailRecv">IMailRecv</a> (Check receive activity)
<li><a href="admin_list.php?tbl=IMailRecvNotify">IMailRecvNotify</a>
<li><a href="admin_list.php?tbl=IMailState">IMailState</a>
<li><a href="../imail/imail_notify.php?code=ABNDfja3s238dfljF5xiLkl2lE8DfF">IMail email notification</a>
</ul>

<p>Views</p>
<ul>
<li><a href="admin_list.php?tbl=v_log_site">View Log_site (Site Activity Log)</a></li>
</ul>

<p>Management functions</p>

<ul>
<li><a href="backup_db.php">Backup Database</a></li>
<li><a href="gen_reg_code.php">Generate registration code</a></li>
</ul>

<p>Reports</p>

<ul>
<li><a href="report_reg_code.php">Registration Code Statistics</a></li>
<li><a href="report_site_activity.php">Site Activity</a></li>
</ul>

</td></tr></table>

<?php
include_once("../theme/footer.php");
?>
