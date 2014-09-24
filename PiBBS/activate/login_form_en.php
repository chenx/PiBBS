<?php if ($activate_status == "invalid_activation_code") { ?>
    <p>This registration code does not exist.</p> 
    <p><a href="../">Back To Home</a></p>
<?php } else if ($activate_status == "ok") { ?>
    <p>Congratulations! Your account is activated.</p>
    <p><a href="../">Go to Homepage</a></p>
<?php } else { ?>

<h3>Activate Account</h3>
<form name="frmLogin" method="POST" action="">

<table>
<tr>
<td>Username: </td>
<td><input type="text" id="username" name="username" value="<?php echo $username; ?>" /> <font color="red">* <span id="e_username"/></font></td>
</tr>

<tr>
<td>Password: </td>
<td><input type="password" id="userpass" name="userpass" value="" /> <font color="red">* <span id="e_userpass"/></font></td>
</tr>

<?php if ($_USE_CAPTCHA) { ?>
<tr>
<td>Captcha: </td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/> <font color="red">* <span id="e_txtCaptcha"/></font></td>
</tr>

<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha_cn.php' border='1' style='vertical-align: middle;' title='Captcha image' width='150' height='20'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">
</td>
</tr>
<?php } ?>

<tr>
<td><br></td>
<td align="left">
<input type="hidden" name="doSubmit" value="Y" />
<input type="button" name="btnSubmit" value="Submit" onclick="javascript:validate();" />
<input type="hidden" name="c" value="<?php echo db_htmlEncode( $reg_code );?>"/>
</td>
</tr>
</table>
</form>

<?php
if ($error != "") {
    print "<p><font color='red'>$error</font></p>";
}
?>

<br/>
<a href='../register/'>Register</a> | <a href='../getpwd/'>Forgot Password</a>

<p><a href="../">Back To Homepage</a></p>


<script type='text/javascript'>

$("#username").focus(); // do this upon page load.

function validate() {
    var ok = 1;

    if ($("#e_txtCaptcha").length == 1) { $("#e_txtCaptcha").html(""); }
    $("#e_userpass").html("");
    $("#e_username").html("");

    if ($("#e_txtCaptcha").length == 1 && $.trim( $("#txtCaptcha").val() ) == "") {
        $("#e_txtCaptcha").html("Cannot be empty");
        $("#txtCaptcha").focus();
        ok = 0;
    }
    if ($.trim( $("#userpass").val() ) == '') {
        $("#e_userpass").html("Cannot be empty");
        $("#userpass").focus();
        ok = 0;
    }
    if ($.trim( $("#username").val() ) == '') {
        $("#e_username").html("Cannot be empty");
        $("#username").focus();
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

<?php } ?>

