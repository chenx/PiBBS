<?php
ob_start();
session_start();

require_once("../func/auth.php");
require_once("../func/Cls_DBTable_Custom.php");
require_once("../func/user_func.php");

$page_title = "Profile";
$page_name = "../profile"; //$_SERVER['PHP_SELF'];
include_once("../theme/header.php");
?>

<p class="desktop"><br></p>

<!--img src="../image/pi_pie2.jpg" style="float:left; width: 400px;" /-->

<h3>My Profile</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>Your profile has been successfully updated.</font></p>";
    print "<a href='$page_name'>Go to My Profile</a>";
} else {
    db_open();

    $cols_pwd = array('passwd');
    $cols_default = array('login');

    $fields = array("first_name", "last_name", "email", "login", "passwd", "note");
    $titles = array("first_name"=>"First Name:", "last_name"=>"Last Name:", "email"=>"Email:",          
                        "login"=>"Login:", "passwd"=>"Password:", "note"=>"Signature:");

    $cls_tbl = new Cls_DBTable_Custom();
    $cls_tbl->init("User", "ID", $cols_pwd, $cols_default, $fields, $titles);
    $cls_tbl->setTBStyle("class='profileForm'");
    $cls_tbl->setLang($_LANG);

    $id = $_SESSION['ID'];
    $mode = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : '';

    $msg = "";
    if ($mode == "edit") {
        if (isset($_REQUEST['btnVerify'])) {
            $msg = $cls_tbl->verifyForm(0, 0, 0);
            if ($msg != "" && strpos($msg, "Error") > 0) {
                print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
                //print "<p>$msg</p>";
            } else {
                print $cls_tbl->writeVerifyForm(1, $page_name);
            }
        }
        else if (isset($_REQUEST['btnSubmit'])) {
            $msg = $cls_tbl->update($id, 0);
            if ($msg != "" && strpos($msg, "Error") > 0) {
                print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
                print "<p>$msg</p>";
            } else {
                header("Location: $page_name?ok");
                exit();
            }
        }
        else {
            print $cls_tbl->writeEditForm($id, 0, $page_name, 1, 0);
        }
    } else {
        print $cls_tbl->writeViewForm($id, 0);
        showRegCodeMsg();
    }

    db_close();
}


function showRegCodeMsg() {
    $a = getRegCodeCount($_SESSION['ID']);
    if ($a == 0) {
        $s = "You have no unused registration code now";

        global $_HIDE_REG_CODE_PAGE_WHEN_NONE;
        if ($_HIDE_REG_CODE_PAGE_WHEN_NONE) { $s = ""; }
    } else {
        $p = ($a > 1) ? "s" : "";
        $s = "You have <b>$a</b> unused registration code$p";
    }

    print "<p><a href='../regcode/'><font color='green'>$s</font></a></p>";
}
?>

</form>


<?php
include_once("../theme/footer.php");
?>

