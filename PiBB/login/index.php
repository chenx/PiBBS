<?php 
include_once("../login/do_login.php"); 

$page_title = "Login";
include_once("../theme/header.php");

$redirect_url = U_REQUEST("s");
?>

<div id="main" style="background: white;">

<center>
<p class="desktop"><br></p>
<table class="loginForm">
<tr>
<td class="loginCell">

<table>
<tr>
<td class="desktop" width="400" valign="top" align="right"><img src="../image/login.png" width="250" border="0"></td>
<td class="desktop" width="50">&nbsp;</td>
<td align="center">

<?php include_once("../login/login_form_$_LANG.php"); ?>
</td></tr></table>

</td>
</tr>
</table>
</center>


<?php 
$_BBS_JIA_THIS_THREAD = 0; // disable jiathis bar. This prevents login to work.
include_once("../theme/footer.php"); 
?>
