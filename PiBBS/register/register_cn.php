<?php
session_start();
require_once("../func/Cls_DBTable.php");
require_once("../func/email.php");

$page_title = "Register";
include("../theme/header.php");

$_DEBUG = 1; // for debug of this page.
$td_h = " style=\"height:25px; width: 1px; white-space:nowrap;\""; // table row height.
?>

<center>

<p class="desktop"><br></p>

<table class="loginForm">
<tr>
<td class="loginCell">

<h3>注册</h3>

<form method="POST">

<?php 
if (isset($_REQUEST['ok'])) { 
    if ($_USE_ACCOUNT_ACTIVATION) { ?>

    <p><font color='green'>帐号激活邮件已经发往您的电子邮箱。<br>
    帐号激活之后即可以登录本站。</font></p>

    <?php } else { ?>

    <p><font color='green'>祝贺您注册成功完成。<br>
    现在即可以登录本站。</font></p>

    <?php 
    } 
} else {
    $first_name = ""; $last_name = ""; $email = ""; 
        $login = ""; $passwd = ""; $passwd2 = ""; $reg_code = "";
    $e_first_name = ""; $e_last_name = ""; $e_email = ""; 
        $e_login = ""; $e_passwd = ""; $e_reg_code; $e_captcha = "";
    $error = "";

    getPostVal();   

    if ( isset($_POST['btnVerify']) ) {
        $error = DoVerify();
        if ($error == "") { writeVerifyForm(); }
        else { writeEditForm(); }
    } else if ( isset($_POST['btnSubmit']) ) {
        $error = DoInsert();
        if ($error == "") { header("Location: ../register/?ok");  }
        else { writeEditForm(); }
    } else {
        writeEditForm();
    }
}
?>

</form>

<!--
<p><br/><a href="../linkedin/"><img src="../image/linkedin.png" border="0"></a></p>
-->

<p><a href='../bbs'>回到主页</a></p>
</td>
</tr></table>

</center>

<script type="text/javascript" src="../register/register_cn.js"></script>
<?php include("../theme/footer.php"); ?>


<?php
//
// Functions.
//

function getPostVal() {
    global $first_name, $last_name, $email, $login, $passwd, $passwd2, $reg_code;

    $first_name = db_htmlEncode( U_POST('txt_first_name') );
    $last_name = db_htmlEncode( U_POST('txt_last_name') );
    $email = db_htmlEncode( U_POST('txt_email') );
    $login = db_htmlEncode( U_POST('txt_login') );
    $passwd = U_POST('txt_passwd');
    $passwd2 = U_POST('txt_2_passwd');
    $reg_code = U_POST('txt_reg_code');
}

function reg_code_exist($c) {
    $query = "SELECT code FROM code_register WHERE is_used = 0 AND code = " . db_encode($c);
    $ct = executeRowCount($query);
    return $ct == 1;
}

function reg_code_set_used($c, $user_id, $date) {
    $query = "UPDATE code_register SET is_used = 1, use_user_id = " 
             . db_encode($user_id) . ", use_date = " . db_encode($date)
             . " WHERE code = " . db_encode($c);
    executeNonQuery($query);
}

//
// Return: empty string if succeed. error message otherwise.
// Note: gid - group id, default to 1 (user), omitted from here.
//
function DoInsert() {
    global $_DEBUG; // defined at the top of this page.
    global $_USE_REG_CODE;
    global $first_name, $last_name, $email, $login, $passwd, $reg_code;
    $date = date('Y-m-d H:i:s', time());
    $ip   = $_SERVER['REMOTE_ADDR'];

    $activated = "1";
    $activation_code = "";
    $activation_date = $date;
    global $_USE_ACCOUNT_ACTIVATION, $_ACTIVATION_CODE_LEN;
    if ($_USE_ACCOUNT_ACTIVATION) {
        $activated = "0";
        $activation_code = getRandStr($_ACTIVATION_CODE_LEN, 1);
        $activation_date = "";
    }

    $query =
 "INSERT INTO User ("
 . "first_name, last_name, email, login, passwd, note, reg_date, last_login, last_ip, approved, approve_date, enabled, activated, activation_code, activation_date"
 . ") VALUES ("
 . db_encode( $first_name ) . ", " 
 . db_encode( $last_name ) . ", " 
 . db_encode( $email ) . ", "
 . db_encode( $login ) . ", "
 . db_encode( MD5( $passwd ) ) . ", "
 . db_encode( "" ) . ", " 
 . db_encode( $date ) . ", " 
 . db_encode( $date ) . ", "
 . db_encode( $ip ) . ", "
 . "1" . ", "
 . db_encode( $date ) . ", "
 . "1" . ", "
 . db_encode( $activated ) . ", "
 . db_encode( $activation_code ) . ", "
 . db_encode( $activation_date ) 
 . ")";

    //echo "$query<br>"; exit(0);

    $msg = "";

    db_open();

    try {
        // Check reg_code: if not exists. return with error.
        if ($_USE_REG_CODE && ! reg_code_exist($reg_code)) { return "注册码无效"; }

        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "数据库错误");
        } else {
            //$msg = "fake error"; // for testing only.
            $ID = getScalar("SELECT ID FROM User WHERE login = " . db_encode($login), "ID");
            global $link;
            log_event($ID, "register", $_SESSION['captcha'], $link);

            // Update reg_code as used.
            if ($_USE_REG_CODE) { reg_code_set_used($reg_code, $ID, $date); }

            if ($_USE_ACCOUNT_ACTIVATION) {
                $msg = send_activation_email("Dear $first_name $last_name,", $email, $activation_code);
                if ($msg != "" && ! $_DEBUG) { $msg = "发送激活码电子邮件错误"; }
            }
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "数据库例外");
    }

    db_close();

    if (isset( $_SESSION['captcha'] )) unset( $_SESSION['captcha'] );

    if ($msg != "") {
        $msg = "<font color='red'>错误：$msg</font>";
    }
    return $msg;
}


function send_activation_email($title, $to, $activation_code) {
    global $_SITE_NAME;

    $subj = "Account Activation";
    $txt = "$title\n\nPlease visit this link to activate your account: \n\nhttp://$_SITE_NAME/activate/index.php?c=$activation_code";

    $msg = "";
    try {
        send_email($to, $subj, $txt);
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }

    return $msg;
}


function DoVerify() {
    global $first_name, $last_name, $email, $login, $passwd, $passwd2, $reg_code;
    global $e_first_name, $e_last_name, $e_email, $e_login, $e_passwd, $e_reg_code, $e_captcha;

    if ($first_name == "") $e_first_name = "不能为空";
    if ($last_name == "") $e_last_name = "不能为空";

    db_open();
    $e_email = verify_email($email);
    $e_login = verify_login($login);

    global $_USE_REG_CODE;
    if ($_USE_REG_CODE) { $e_reg_code = verify_reg_code($reg_code); }
    else { $e_reg_code = ""; }

    db_close();

    $e_passwd = verify_passwd($passwd, $passwd2); 
    $e_captcha = verify_captcha();

    if ( $e_first_name == "" && $e_last_name == "" && $e_email == "" &&
         $e_login == "" && $e_passwd == "" && $e_reg_code == "" && $e_captcha == "" ) {
        return "";
    } else {
        return "验证错误，内容见上。";
    } 
}


function verify_reg_code($s) {
    $e = "";
    if ($s == "") {
        $e = "不能为空";
    }
    else if (! reg_code_exist($s)) {
        $e = "注册码无效";
    }
    return $e;
}


function verify_login($s) {
    $e = validate_login($s); // in ../func/db.php
    if ($e == "") {
        // check if this login already exists.
        $query = "SELECT login FROM User WHERE login = " . db_encode($s);
        if (executeRowCount($query) > 0) {
            $e = "帐号已存在";
        }
    }
    return $e;
}

function verify_email($s) {
    $e = "";
    if ($s == "") $e = "不能为空";
    else if ( ! preg_match("/^([a-zA-Z0-9]+[._-])*[a-zA-Z0-9]+@[a-zA-Z0-9-_\.]+\.[a-zA-Z]+$/", $s) ) $e = "邮箱地址无效";
    else {
        // check if this email already exists.
        $query = "SELECT email FROM User WHERE email = " . db_encode($s);
        if (executeRowCount($query) > 0) {
            $e = "邮箱已存在";
        }
    }
    return $e;
}

function verify_passwd($s, $s2) {
    $e = "";
    if ($s != $s2) $e = "两个密码不相同";
    else $e = validate_password_rule($s);
    return $e;
}

function verify_captcha() {
    global $_USE_CAPTCHA;
    if (! $_USE_CAPTCHA) { return ""; }
    $e = "";
    if ($_SESSION['captcha'] != $_POST['txtCaptcha']) $e = "验证码错误";
    return $e;
}


function writeEditForm() {
    global $first_name, $last_name, $email, $login, $passwd, $passwd2, $reg_code;
    global $e_first_name, $e_last_name, $e_email, $e_login, $e_passwd, 
           $e_reg_code, $e_captcha, $error;
    global $td_h; // td height.

    global $_USE_REG_CODE;
    $reg_code_row = "";
    if ($_USE_REG_CODE) {
        $reg_code_row = <<<EOF
<tr>
<td$td_h>&nbsp;注册码：<img src="../image/question.gif" title="需要注册码才能注册"/></td>
<td><input tpe="text" id="txt_reg_code" name="txt_reg_code", width="50" value="$reg_code" onblur="javascript: check_valid('reg_code');"/> <font color="red">* <span id="e_reg_code">$e_reg_code</span></font></td>
</tr>
EOF;
    }

    global $_USE_CAPTCHA;
    $captcha_row = "";
    if ($_USE_CAPTCHA) {
        $captcha_row = <<<EOF
<tr>
<td$td_h>&nbsp;验证码：<img src="../image/question.gif" title="请输入下图所示字符" style="vertical-align: top;"/>&nbsp;</td>
<td><input type='text' id='txtCaptcha' id='txtCaptcha' name='txtCaptcha' value=''/> <font color='red'>* <span id="e_captcha">$e_captcha</span></font></td>
</tr>
<tr>
<td$td_h>&nbsp;</td>
<td>
<img id='imgCaptcha' src='../func/captcha_cn.php' border='1' style='vertical-align: middle;' title='验证码' width='180' height='30'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='更新验证码' onclick="javascript: changeCaptcha();">
</td>
</tr>
EOF;
    }

    $s = <<<EOF
<table class="loginTable">
<tr><td$td_h>&nbsp;名字：&nbsp;</td><td><input type="text" id="txt_first_name" name="txt_first_name" width="50" value="$first_name"> <font color='red'>* <span id="e_first_name">$e_first_name</span></font></td></tr>
<tr><td$td_h>&nbsp;姓氏：&nbsp;</td><td><input type="text" id="txt_last_name" name="txt_last_name" width="50" value="$last_name"> <font color='red'>* <span id="e_last_name">$e_last_name</span></font></td></tr>
<tr><td$td_h>&nbsp;电子邮箱：&nbsp;</td><td><input type="text" id="txt_email" name="txt_email" width="50" value="$email" onblur="javascript: check_valid('email');"> <font color='red'>* <span id="e_email">$e_email</span></font></td></tr>
<tr><td$td_h>&nbsp;帐号：<img src="../image/question.gif" title="帐号应由字母和数字组成，以字母开头，长度大于等于4" style="vertical-align: top;"/>&nbsp;</td><td><input type="text" id="txt_login" name="txt_login" width="50" value="$login" onblur="javascript: check_valid('login');"/> <font color='red'>* <span id="e_login">$e_login</span></font></td></tr>
<tr><td$td_h>&nbsp;密码：<img src="../image/question.gif" title="密码应由字母和数字组成，长度大于等于8" style="vertical-align: top;"/>&nbsp;</td><td><input type="password" id="txt_passwd" name="txt_passwd" width="50" value=""> <font color='red'>* <span id="e_passwd">$e_passwd</span></font></td></tr>
<tr><td$td_h>&nbsp;密码：(重复)&nbsp;</td><td><input type="password" id="txt_2_passwd" name="txt_2_passwd" width="50" value=""> <font color='red'>*</font></td></tr>

$reg_code_row
$captcha_row

</table>
<br/>
<input type="button" onclick="javascript:validate_reg();" value="验证"><input type="reset" value="重置">
<input type="hidden" name="btnVerify" value="Y"/>
EOF;

    print $s;
    echo writeP($error, 0); 
}


function writeVerifyForm() {
    global $first_name, $last_name, $email, $login, $passwd, $reg_code;
    global $td_h; // td height.

    global $_USE_REG_CODE;
    $reg_code_row = "";
    if ($_USE_REG_CODE) {
        $reg_code_row = <<<EOF
<tr><td$td_h>&nbsp;Reg code:</td><td><input type="hidden" name="txt_reg_code" value="$reg_code"> $reg_code</td></tr>
EOF;
    }

    global $_USE_CAPTCHA;
    $captcha_row = "";
    if ($_USE_CAPTCHA) {
        $captcha_row = <<<EOF
<tr><td style="width: 100px; white-space:nowrap;">&nbsp;验证码：&nbsp;</td><td$td_h>正确</td></tr>
EOF;
    }

    $s = <<<EOF
<table class="loginTable">
<tr><td$td_h>&nbsp;名字：&nbsp;</td><td><input type="hidden" name="txt_first_name" value="$first_name"> $first_name</td></tr>
<tr><td$td_h>&nbsp;姓氏：&nbsp;</td><td><input type="hidden" name="txt_last_name" value="$last_name"> $last_name</td></tr>
<tr><td$td_h>&nbsp;电子邮箱：&nbsp;</td><td><input type="hidden" name="txt_email" value="$email"> $email</td></tr>
<tr><td$td_h>&nbsp;帐号：&nbsp;</td><td><input type="hidden" name="txt_login" value="$login"> $login</td></tr>
<tr><td$td_h>&nbsp;密码：&nbsp;</td><td><input type="hidden" name="txt_passwd" value="$passwd"> ********</td></tr>
<tr><td$td_h>&nbsp;密码：(重复) &nbsp;</td><td> ********</td></tr>
$reg_code_row
$captcha_row
</table>
<br/>
<input type="submit" name="btnSubmit" value="提交"><input type="submit" value="修改">
EOF;

    print $s;
}


?>


