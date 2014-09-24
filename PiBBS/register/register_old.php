<?php
session_start();
require_once("../func/Cls_DBTable.php");

$page_title = "Register";

include("../theme/header.php");
?>

<center>

<p><br></p>

<table border="0" width="850">
<tr>
<td width="300" valign="top"><img src='../image/pi_letter.png' border="0"></td>
<td width="50">&nbsp;</td>
<td>

<h3>Register</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>Your registration has been successfully received.<br>";
    print "Now you can log into the site.</font></p>";
} else {
    db_open();

    $cols_pwd = array('passwd');
    $cols_default = array('gid');
    $cols_hidden = array('note', 'reg_date', 'last_login', 'last_ip', 'approved', 'enabled');
    $cls_tbl = new Cls_DBTable("User", "ID", $cols_pwd, $cols_default, $cols_hidden);

    $msg = "";
    if (isset($_REQUEST['btnVerify'])) {
        $msg = $cls_tbl->verifyForm(0, 1); // $cls_tbl->insertNew(1);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            //print "$msg";
            print $cls_tbl->writeNewForm(0, '', 1, 1, 2);
        } else {
            print $cls_tbl->writeVerifyForm(0, "../register");
        }
    }
    else if (isset($_REQUEST['btnSubmit'])) {
        // date_default_timezone_set('Australia/Melbourne'); set in func/db.php.
        $date = date('Y-m-d H:i:s', time());
        $ip   = $_SERVER['REMOTE_ADDR'];
        $extra_fields = array('note', '',
                              'reg_date', $date, 
                              'last_login', $date, 
                              'last_IP', $ip, 
                              'approved', '1', 
                              'enabled', '1');
        $msg = $cls_tbl->insertNew(0, $extra_fields);
        if ($msg != "" && strpos($msg, "Error") > 0) {
            //print "<p>$msg</p>";
            print $cls_tbl->writeNewForm(0, '', 1, 1, 2);
            print "<p>$msg</p>";
        } else {
            db_close();
            header('Location: ../register/?ok');
            exit();
        }
    } else {
        print $cls_tbl->writeNewForm(0, '', 1, 1, 2);
    }

    db_close();
}
?>
</form>

<!--p><br><a href="../"><img src="../image/home.png" border="0" title="Back to Homepage"></a></p-->
<p><a href='../'>Back to Homepage</a></p>

</td></tr></table>

</center>


<?php
include("../theme/footer.php");
?>
