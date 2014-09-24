<?php
require_once('../func/db.php');
require_once('../func/util.php');

session_start();

$msg = "";
$login = U_POST('txtLogin');
$email = U_POST('txtEmail');

if ($login != "" && $email != "") {
    if ($_USE_CAPTCHA && ( $_SESSION['captcha'] != $_POST['txtCaptcha'] )) {
        $msg = "Captcha code is wrong";
    } else {
        db_open();
        $query = "SELECT email, approved, activated, enabled FROM User WHERE login = "
                 . db_encode( $login ) . " AND email = " . db_encode( $email );
        //$email = getScalar($query, "email");
        $result = mysql_query($query, $link);
        if (! $result) {
            //doExit('Invalid query: ' . mysql_error()); // for security, do not show this.
            $msg = "Database error";
        }
        else {
            if (mysql_num_rows($result)!= 1) {
                $msg = "This account is not found";
            } else {
                $info = mysql_fetch_array($result);

                if ($info['approved'] != '1') {
                    $msg = "Account not approved";
                }
                else if ($info['enabled'] != '1') {
                    $msg = "Account not enabled";
                }
                else if ($info['activated'] != '1') {
                    $msg = "Account not activated";
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

<h3>Forgot Password</h3>

<?php
if (isset($_REQUEST['ok'])) {
    print "<p><font color='green'>An email has been sent to your mailbox</font></p> ";
} else {
?>

<form method="post">

<table class="loginTable">
<tr><td>Username: </td><td><input type='text' id='txtLogin' name='txtLogin' value='<?php echo $login;?>'/> <font color="red">* <span id="e_txtLogin"/></font></td></tr>
<tr><td>Email: </td><td><input type='text' id='txtEmail' name='txtEmail' value=''/> <font color="red">* <span id="e_txtEmail"/></font></td></tr>

<?php if ($_USE_CAPTCHA) { ?>
<tr>
<td>Captcha: </td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/> <font color="red">* <span id="e_txtCaptcha"/></font></td>
</tr>

<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha.php' border='1' style='vertical-align: middle;' title='Captcha image' width='180' height='30'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">
</td>
</tr>
<?php } ?>

<tr>
<td>&nbsp;</td>
<td>
<input type='button' name='btnSubmit' value='Send new password to my email' onclick='javascript: validate();'>
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

<!--p><br><a href="../"><img src="../image/home.png" border="0" title="Back to Homepage"></a></p-->
<p><a href='../bbs'>Back To Homepage</a></p>

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
        $("#e_txtCaptcha").html("Cannot be empty");
        $("#txtCaptcha").focus();
        ok = 0;
    }
    if ($.trim( $("#txtEmail").val() ) == '') {
        $("#e_txtEmail").html("Cannot be empty");
        $("#txtEmail").focus();
        ok = 0;
    }
    if ($.trim( $("#txtLogin").val() ) == '') {
        $("#e_txtLogin").html("Cannot be empty");
        $("#txtLogin").focus();
        ok = 0;
    }

    if (ok) {
        document.forms[0].submit();
    }
}

function changeCaptcha(o) {
    document.getElementById('imgCaptcha').src = "../func/captcha.php?" + Math.random();
}

</script>

<?php include("../theme/footer.php"); ?>

