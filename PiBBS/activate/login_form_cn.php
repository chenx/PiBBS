<?php if ($activate_status == "invalid_activation_code") { ?>
    <p>激活码不存在。</p> 
    <p><a href="../">回到主页</a></p>
<?php } else if ($activate_status == "ok") { ?>
    <p>祝贺！您的帐号已经激活。</p> 
    <p><a href="../">前往主页</a></p>
<?php } else { ?>

<h3>激活帐号</h3>
<form name="frmLogin" method="POST" action="">

<table>
<tr>
<td>帐号: </td>
<td><input type="text" id="username" name="username" value="<?php echo $username; ?>" /> <font color="red">* <span id="e_username"/></font></td>
</tr>

<tr>
<td>密码: </td>
<td><input type="password" id="userpass" name="userpass" value="" /> <font color="red">* <span id="e_userpass"/></font></td>
</tr>

<?php if ($_USE_CAPTCHA) { ?>
<tr>
<td>验证码: </td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/> <font color="red">* <span id="e_txtCaptcha"/></font></td>
</tr>

<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha_cn.php' border='1' style='vertical-align: middle;' title='验证码' width='150' height='20'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='更新验证码' onclick="javascript: changeCaptcha();">
</td>
</tr>
<?php } ?>

<tr>
<td><br></td>
<td align="left">
<input type="hidden" name="doSubmit" value="Y" />
<input type="button" name="btnSubmit" value="提交" onclick="javascript:validate();" />
<input type="hidden" name="c" value="<?php echo db_htmlEncode( $activation_code );?>"/>
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
<a href='../register/'>注册</a> | <a href='../getpwd/'>忘记密码</a>

<p><a href="../">回到主页</a></p>


<script type='text/javascript'>

$("#username").focus(); // do this upon page load.

function validate() {
    var ok = 1;

    if ($("#e_txtCaptcha").length == 1) { $("#e_txtCaptcha").html(""); }
    $("#e_userpass").html("");
    $("#e_username").html("");

    if ($("#e_txtCaptcha").length == 1 && $.trim( $("#txtCaptcha").val() ) == "") {
        $("#e_txtCaptcha").html("请输入验证码");
        $("#txtCaptcha").focus();
        ok = 0;
    }
    if ($.trim( $("#userpass").val() ) == '') {
        $("#e_userpass").html("请输入密码");
        $("#userpass").focus();
        ok = 0;
    }
    if ($.trim( $("#username").val() ) == '') {
        $("#e_username").html("请输入帐号");
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
