<?php
require_once('../func/db.php');
require_once('../func/util.php');
require_once('../func/email.php');

session_start();

$msg = "";
$login = U_POST('txtLogin');
$email = U_POST('txtEmail');

if ($login != "" && $email != "") {
    if ($_USE_CAPTCHA && ( $_SESSION['captcha'] != $_POST['txtCaptcha'] )) {
        $msg = writeP("验证码错误", 0);
    } else {
        db_open();
        $query = "SELECT ID, email, approved, activated, enabled FROM User WHERE login = " 
                 . db_encode( $login ) . " AND email = " . db_encode( $email );
        //$email = getScalar($query, "email");
        $result = mysql_query($query, $link);
        if (! $result) {
            //doExit('Invalid query: ' . mysql_error()); // for security, do not show mysql_error.
            $msg = "Database error";
        }
        else {
            if (mysql_num_rows($result)!= 1) {
                $msg = "此帐号不存在";
            } else {
                $info = mysql_fetch_array($result);

                if ($info['approved'] != '1') {
                    $msg = "帐号正在等待审核";
                }
                else if ($info['enabled'] != '1') {
                    $msg = "帐号处于关闭状态";
                }
                else if ($info['activated'] != '1') {
                    $msg = "帐号尚未激活";
                }
                else {
                    $email = $info['email'];
                    $newPwd = updatePwd($login, $email);

                    global $link;
                    log_event($info['ID'], "getpwd", $_SESSION['captcha'], $link);

                    $subj = "Your new password";
                    $txt = "This is your new password: $newPwd";

                    try {
                        send_email($email, $subj, $txt);

                        $loc = $_SERVER['PHP_SELF'] . "?ok";
                        header("Location: $loc");
                    } catch (Exception $e) {
                        $msg = "Error: " . $e->getMessage();
                    }
                }
            }
        }
        db_close();
    }

    if (isset( $_SESSION['captcha'] )) unset( $_SESSION['captcha'] );
    if ($msg != "") { $msg = writeP($msg, 0); }
}

function updatePwd($login, $email) {
    $s = getRandStr(16, 1); 
    $query = "UPDATE User SET passwd = MD5(" . db_encode($s) . 
             ") WHERE login = " . db_encode( $login ) .
             " AND email = " . db_encode( $email );
    executeNonQuery($query);
    return $s;
}

$page_title = "Retrieve Password";
include("../theme/header.php"); 
?>

<center>

<p class="desktop"><br></p>

<table class="loginForm">
<tr>
<td class="loginCell">

<h3>忘记密码</h3>

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>邮件已经发往您的邮箱</font></p> ";
} else {
?>

<form method="post">

<table class="loginTable">
<tr><td>帐号：</td><td><input type='text' id='txtLogin' name='txtLogin' value='<?php echo $login;?>'/> <font color="red">* <span id="e_txtLogin"/></font></td></tr>
<tr><td>电子邮箱：</td><td><input type='text' id='txtEmail' name='txtEmail' value=''/> <font color="red">* <span id="e_txtEmail"/></font></td></tr>

<?php if ($_USE_CAPTCHA) {?>
<tr>
<td>验证码：</td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/> <font color="red">* <span id="e_txtCaptcha"/></font></td>
</tr>

<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha_cn.php' border='1' style='vertical-align: middle;' title='验证码' width='180' height='30'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='更新验证码' onclick="javascript: changeCaptcha();">
</td>
</tr>
<?php } ?>

<tr>
<td>&nbsp;</td>
<td>
<input type='button' name='btnSubmit' value='发送新密码到我的邮箱' onclick='javascript: validate();'>
</td>
</tr>
</table>

</form>

<?php
}

if ($msg != "") {
    print $msg;
}
?>

<p><a href='../bbs'>回到主页</a></p>

</td>
</tr></table>

</center>

<script type='text/javascript'>

$("#txtLogin").focus(); // do this upon page load.

function validate() {
    var ok = 1;

    if ($("#e_txtCaptcha").length == 1) { $("#e_txtCaptcha").html(""); }
    $("#e_txtEmail").html("");
    $("#e_txtLogin").html("");

    if ($("#e_txtCaptcha").length == 1 && $.trim( $("#txtCaptcha").val() ) == "") {
        $("#e_txtCaptcha").html("请输入验证码");
        $("#txtCaptcha").focus();
        ok = 0;
    }
    if ($.trim( $("#txtEmail").val() ) == '') {
        $("#e_txtEmail").html("请输入电子邮箱");
        $("#txtEmail").focus();
        ok = 0;
    }
    if ($.trim( $("#txtLogin").val() ) == '') {
        $("#e_txtLogin").html("请输入帐号");
        $("#txtLogin").focus();
        ok = 0;
    }

    if (ok) {
        document.forms[0].submit();
    }
}

function changeCaptcha(o) {
    document.getElementById('imgCaptcha').src = "../func/captcha_cn.php?" + Math.random();
}

</script>

<?php include("../theme/footer.php"); ?>

