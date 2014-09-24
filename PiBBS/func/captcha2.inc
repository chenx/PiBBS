<!--2 columns-->

<tr>
<td>&nbsp;Enter code in image:&nbsp;</td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/> <font color='red'>*</font>#errMsg</td>
</tr>
<tr>
<td><br></td>
<td>
<img id='imgCaptcha' src='../func/captcha_cn.php' border='1' style='vertical-align: middle;' title='Captcha image' width='150' height='20'>
<img id='btnChange' src='../image/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">

<script type='text/javascript'>
function changeCaptcha(o) {
    document.getElementById('imgCaptcha').src = "../func/captcha_cn.php?" + Math.random();
}

</script>

</td>
</tr>


