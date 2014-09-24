<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable.php");

$tbl = isset($_REQUEST['tbl']) ? $_REQUEST['tbl'] : "";
$tbl_uc1 = ucfirst($tbl);
$tbl_lc  = strtolower($tbl);

$page_title = "Admin - Manage Table $tbl_uc1 - Add New";
?>

<?php include("../theme/header.php"); ?>

<h3>Add New To Table <?php echo $tbl_uc1 ?></h3>

<form method="post">
<?php

if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>The New Entry has been successfully added.</font></p>";
    print "<a href='admin_tbl_add.php?tbl=$tbl'>Add Another</a> | <a href='admin_list.php?tbl=$tbl'>Back To List</a>";
    exit();
}

db_open();

$cols_pwd = array('passwd');
$cols_default = array('gid');
$cls_tbl = new Cls_DBTable($tbl, "ID", $cols_pwd, $cols_default);

$msg = "";
if (isset($_REQUEST['btnSubmit'])) {
    $msg = $cls_tbl->insertNew(1);
    if ($msg != "" && strpos($msg, "Error") > 0) {
        //print "$msg";
        print $cls_tbl->writeNewForm(1, "admin_list.php?tbl=$tbl", 0, 0);
        //print "<p><a href='admin_list.php?tbl=$tbl'>Cancel Add New</a></p>";
    } else {
        header("Location: admin_tbl_add.php?tbl=$tbl&ok=Y");
        exit();
    }
} else {
    print $cls_tbl->writeNewForm(1, "admin_list.php?tbl=$tbl", 0, 0);
    //print "<p><a href='admin_list.php?tbl=$tbl'>Cancel Add New</a></p>";
}

db_close();
?>
</form>

<?php include("../theme/footer.php"); ?>

