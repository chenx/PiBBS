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
<td  class="loginCell">

<h3>Register</h3>

<form method="POST">

<?php
if (isset($_REQUEST['ok'])) {
    if ($_USE_ACCOUNT_ACTIVATION) { ?>

    <p><font color='green'>An activation email has been sent to your mailbox.<br>
    You can visit this site after activation your account.</font></p>

    <?php } else { ?>

    <p><font color='green'>Your registration has been successfully received.<br>
    Now you can log into the site.</font></p>

    <?php
    }
} else {
    $first_name = ""; $last_name = ""; $email = ""; 
        $login = ""; $passwd = ""; $passwd2 = ""; $reg_code = "";
    $e_first_name = ""; $e_last_name = ""; $e_email = ""; 
        $e_login = ""; $e_passwd = ""; $e_reg_code = ""; $e_captcha = "";
    $error = "";

    getPostVal();   

    if ( isset($_POST['btnVerify']) ) {
        $error = DoVerify();
        if ($error == "") { writeVerifyForm(); /* include_once("reg_form_verify_cn.php");*/ }
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

<p><a href='../bbs'>Back to Homepage</a></p>
</td>
</tr></table>

</center>

<script type="text/javascript" src="../register/register_en.js"></script>
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
        if ($_USE_REG_CODE && ! reg_code_exist($reg_code)) { return "Reg code does not exist"; }

        $result = mysql_query($query);
        if (! $result) {
            $msg = ($_DEBUG ? mysql_error() : "Database error");
        } else {
            //$msg = "fake error"; // for testing only.
            $ID = getScalar("SELECT ID FROM User WHERE login = " . db_encode($login), "ID");
            global $link;
            log_event($ID, "register", $_SESSION['captcha'], $link);
 
            // Update reg_code as used.
            if ($_USE_REG_CODE) { reg_code_set_used($reg_code, $ID, $date); }

            if ($_USE_ACCOUNT_ACTIVATION) {
                $msg = send_activation_email("Dear $first_name $last_name,", $email, $activation_code);
                if ($msg != "" && ! $_DEBUG) { $msg = "Error sending activation email"; }
            }
        }
    } catch (Exception $e) {
        $msg = ($_DEBUG ? $e->getMessage() : "Database exception");
    }

    db_close();

    if (isset( $_SESSION['captcha'] )) unset( $_SESSION['captcha'] );

    if ($msg != "") {
        $msg = "<font color='red'>Error: $msg</font>";
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

    if ($first_name == "") $e_first_name = "Cannot be empty";
    if ($last_name == "") $e_last_name = "Cannot be empty";

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
        return "Form error, see above for details.";
    } 
}


function verify_reg_code($s) {
    $e = "";
    if ($s == "") {
        $e = "Cannot be empty";
    }
    else if (! reg_code_exist($s)) {
        $e = "Invalid reg code";
    }
    return $e;
}


function verify_login($s) {
    $e = validate_login($s); // in ../func/db.php
    if ($e == "") {
        // check if this login already exists.
        $query = "SELECT login FROM User WHERE login = " . db_encode($s);
        if (executeRowCount($query) > 0) {
            $e = "Login already exists";
        }
    }
    return $e;
}

function verify_email($s) {
    $e = "";
    if ($s == "") $e = "Cannot be empty";
    else if ( ! preg_match("/^([a-zA-Z0-9]+[._-])*[a-zA-Z0-9]+@[a-zA-Z0-9-_\.]+\.[a-zA-Z]+$/", $s) ) $e = "Invalid email address";
    else {
        // check if this email already exists.
        $query = "SELECT email FROM User WHERE email = " . db_encode($s);
        if (executeRowCount($query) > 0) {
            $e = "Email already exists";
        }
    }
    return $e;
}

function verify_passwd($s, $s2) {
    $e = "";
    if ($s != $s2) $e = "Two passwords do not match";
    else $e = validate_password_rule($s);
    return $e;
}

function verify_captcha() {
    global $_USE_CAPTCHA;
    if (! $_USE_CAPTCHA) { return ""; }
    $e = "";
    if ($_SESSION['captcha'] != $_POST['txtCaptcha']) $e = "Wrong captcha";
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
<td$td_h>&nbsp;Reg code: <img src="../image/question.gif" title="Need registration code to register"/></td>
<td><input tpe="text" id="txt_reg_code" name="txt_reg_code", width="50" value="$reg_code" onblur="javascript: check_valid('reg_code');"> <font color="red">* <span id="e_reg_code">$e_reg_code</span></font></td>
</tr>
EOF;
    }

    global $_USE_CAPTCHA;
    $captcha_row = "";
    if ($_USE_CAPTCHA) {
        $captcha_row = <<<EOF
<tr>
<td$td_h>&nbsp;Captcha code: <img src="../image/question.gif" title="Please enter the characters in the picture below" style="vertical-align: top;"/>&nbsp;</td>
<td><input type='text' id='txtCaptcha' id="txtCaptcha" name='txtCaptcha' value=''/> <font color='red'>* <span id="e_captcha">$e_captcha</span></font></td>
</tr>
<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha.php' border='1' style='vertical-align: middle;' title='Captcha image' width='180' height='30'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">
EOF;
    }

    $s = <<<EOF
<table class="loginTable">
<tr><td$td_h>&nbsp;First Name: &nbsp;</td><td><input type="text" id="txt_first_name" name="txt_first_name" width="50" value="$first_name"> <font color='red'>* <span id="e_first_name">$e_first_name</span></font></td></tr>
<tr><td$td_h>&nbsp;Last Name: &nbsp;</td><td><input type="text" id="txt_last_name" name="txt_last_name" width="50" value="$last_name"> <font color='red'>* <span id="e_last_name">$e_last_name</span></font></td></tr>
<tr><td$td_h>&nbsp;Email: &nbsp;</td><td><input type="text" id="txt_email" name="txt_email" width="50" value="$email" onblur="javascript: check_valid('email');"> <font color='red'>* <span id="e_email">$e_email</span></font></td></tr>
<tr><td$td_h>&nbsp;Login: <img src="../image/question.gif" title="Login should only contain letters and digits, and start with a letter" style="vertical-align: top;"/>&nbsp;</td><td><input type="text" id="txt_login" name="txt_login" width="50" value="$login" onblur="javascript: check_valid('login');"> <font color='red'>* <span id="e_login">$e_login</span></font></td></tr>
<tr><td$td_h>&nbsp;Password: <img src="../image/question.gif" title="Password should only contain letters and digits, and length &gt;= 8" style="vertical-align: top;"/>&nbsp;</td><td><input type="password" id="txt_passwd" name="txt_passwd" width="50" value=""> <font color='red'>* <span id="e_passwd">$e_passwd</span></font></td></tr>
<tr><td style="width: 1px; white-space:nowrap;">&nbsp;Password:  (repeat)&nbsp;</td><td><input type="password" id="txt_2_passwd" name="txt_2_passwd" width="50" value=""> <font color='red'>*</font></td></tr>

$reg_code_row
$captcha_row

</td>
</tr>

</table>
<br/>
<input type="button" onclick="javascript:validate_reg();" value="Submit"><input type="reset" value="Reset">
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
<tr><td style="width: 1px; white-space:nowrap;">&nbsp;Enter captcha code:&nbsp;</td><td$td_h>OK</td></tr>
EOF;
    }

    $s = <<<EOF
<table class="loginTable">
<tr><td$td_h>&nbsp;First Name: &nbsp;</td><td><input type="hidden" name="txt_first_name" value="$first_name"> $first_name</td></tr>
<tr><td$td_h>&nbsp;Last Name: &nbsp;</td><td><input type="hidden" name="txt_last_name" value="$last_name"> $last_name</td></tr>
<tr><td$td_h>&nbsp;Email: &nbsp;</td><td><input type="hidden" name="txt_email" value="$email"> $email</td></tr>
<tr><td$td_h>&nbsp;Login: &nbsp;</td><td><input type="hidden" name="txt_login" value="$login"> $login</td></tr>
<tr><td$td_h>&nbsp;Password: &nbsp;</td><td><input type="hidden" name="txt_passwd" value="$passwd"> ********</td></tr>
<tr><td$td_h>&nbsp;Password:  (repeat)&nbsp;</td><td> ********</td></tr>
$reg_code_row
$captcha_row
</table>
<br/>
<input type="submit" name="btnSubmit" value="Final Submit"><input type="submit" value="Edit More">
EOF;

    print $s;
}


?>


