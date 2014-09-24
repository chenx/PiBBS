<?php
ob_start();
session_start();

require_once("../func/auth.php");
require_once("../func/Cls_DBTable_Custom.php");
require_once("../func/user_func.php");
require_once("../func/avatar.php");

$page_title = "Profile";
$page_name = "../profile"; 
include_once("../theme/header.php");
?>

<p class="desktop"><br></p>

<!--img src="../image/ocean.jpg" style="float:left; width: 200px;" /-->

<h3>我的个人资料</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>您的个人资料已经更新。</font></p>";
    print "<a href='$page_name'>回到我的个人资料</a>";
} else {
    db_open();

    $cols_pwd = array('passwd');
    $cols_default = array('login');

    $fields = array("first_name", "last_name", "email", "login", "passwd", "note");
    $titles = array("first_name"=>"名字:", "last_name"=>"姓氏:", "email"=>"电子邮箱:", 
                        "login"=>"帐号:", "passwd"=>"密码:", "note"=>"签名档:");

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
        $s = "您没有未使用的注册码";
        
        global $_HIDE_REG_CODE_PAGE_WHEN_NONE;
        if ($_HIDE_REG_CODE_PAGE_WHEN_NONE) { $s = ""; }
    } else {
        $s = "您有" . "<b>$a</b>" . "个未使用的注册码";
    }

    print "<p><a href='../regcode/'><font color='green'>$s</font></a></p>";
}
?>

</form>


<?php
include_once("../theme/footer.php");
?>

