<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable.php");

$tbl = isset($_REQUEST['tbl']) ? $_REQUEST['tbl'] : "";
$tbl_uc1 = ucfirst($tbl);
$tbl_lc  = strtolower($tbl);

$id  = isset($_REQUEST['pk'])  ? $_REQUEST['pk']  : "";

$page_title = "Admin - Manage Table $tbl_uc1 - Edit";
?>

<?php include("../theme/header.php"); ?>

<h3>Edit Entry In Table <?php echo $tbl_uc1 ?></h3>

<form method="post">
<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>The Entry has been successfully updated.</font></p>";
    print "<a href='admin_tbl_edit.php?tbl=$tbl&pk=$id'>Edit Again</a> | <a href='admin_list.php?tbl=$tbl'>Back To List</a>";
    exit();
}

db_open();


//
// table specific settings. Manually set here.
//
if ($tbl_lc == "user") {
    $cols_pwd = array('passwd');
    $cols_default = array('reg_date', 'last_login', 'last_ip', 'login', 'activation_code', 'activation_date', 'approve_date');
    $cls_tbl = new Cls_DBTable($tbl, "ID", $cols_pwd, $cols_default);
} 
else {
    $cls_tbl = new Cls_DBTable($tbl, "ID");
}

$msg = "";
if (isset($_REQUEST['btnSubmit'])) {
    $msg = $cls_tbl->update($id, 1);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        print "$msg";
    }
    else {
        header("Location: admin_tbl_edit.php?tbl=$tbl&pk=$id&ok=Y");
    }
}

print $cls_tbl->writeEditForm($id, 1, "admin_list.php?tbl=$tbl");
//print "<p><a href='admin_list.php?tbl=$tbl'>Cancel Edit</a></p>";

db_close();
?>
</form>

<?php include("../theme/footer.php"); ?>
