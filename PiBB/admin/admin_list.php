<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable.php");

$tbl = trim( $_REQUEST['tbl'] );
$tbl_uc1 = ucfirst($tbl);
$tbl_lc  = strtolower($tbl);

$page_title = "Admin - Manage Table $tbl_uc1";

$custom_header = <<<EOF
<script type="text/javascript" src="../func/ClsPage.js"></script>
<script type="text/javascript" src="../oj/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> $(document).ready(function() { $(".sortable").tablesorter(); } ); </script>
EOF;

$div_main_id = "main_full_width";
include("../theme/header.php"); 
?>
<table width="100%"><tr><td>

<P><a href="./">Admin</a> &gt; Manage Table <?php echo $tbl_uc1; ?></P>

<script type="text/javascript">

function tbl_del(tbl, id) {
    var r = confirm("Are you sure to delete entry " + id + "?");
    if (r) {
        document.getElementById('deleteTbl').value = tbl;
        document.getElementById('deleteID').value = id;
        document.forms[0].submit();
    }
}

</script>

<form method="POST">
<input type='hidden' id='deleteTbl' name='deleteTbl' value''/>
<input type='hidden' id='deleteID' name='deleteID' value=''/>
</form>

<?php
db_open();

$cls_tbl = new Cls_DBTable($tbl, "ID");

if (isset($_REQUEST['deleteID']) && $_REQUEST['deleteID']  != "") {
    $msg = $cls_tbl->deleteEntry($_REQUEST['deleteID']);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        print $msg;
    }
    else {
        //print "<script type='text/javascript'>alert('Deleted has finished successfully.');</script>";
    }
}


$fields = "*";
$order = "DESC";
$doManage = 0; // 1 - add, 2 - edit, 4 - delete.
$PAGE_SIZE = 100;

getTableOption($tbl_lc);

$cls_tbl->manage_table_by_page($tbl, "List Of Table $tbl_uc1", $fields, $order, $doManage, $PAGE_SIZE);

//print_r ( $cls_tbl->getDBTableColumnsAsArray("$tbl") );

db_close();

print "</td></tr></table>";
include("../theme/footer.php");

//
// Functions.
//

//
// table specific setting for fields and order. 
//
function getTableOption($tbl) {
    global $fields, $order, $doManage;

    if ($tbl == "user") {
        $fields = "ID, first_name, last_name, email, login, gid, reg_date ,last_login, last_ip, approved, enabled, activated";
        $doManage = 7;
    }
    else if ($tbl == "user_linkedin") {
        $doManage = 7;
    }
    else if ($tbl == "usergroup") {
        $doManage = 0;
    }
    else if ($tbl == "log_site") {
        $doManage = 0;
    }
    else if ($tbl == "log_oj") {
        $doManage = 0;
    }
    else if ($tbl == "code_register") {
        $doManage = 7;
    }
    else if ($tbl == "schema_tblcol") {
        $doManage = 0;
    } 
    else if ($tbl == "bbs_boardlist") {
        $doManage = 7;
    }
    else if ($tbl == "bbs_boardgroups") {
        $doManage = 7;
    }
    else if ($tbl == "bbs_boardmanager") {
        $doManage = 7;
    }
    else if ($tbl == "bbs_privatemembership") {
        $doManage = 7;
    }
    else {
        $doManage = 0;
    }
}

?>


