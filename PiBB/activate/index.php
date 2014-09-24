<?php 
include_once("../activate/do_login.php"); 

$page_title = "Activate";
include_once("../theme/header.php");
?>

<div id="main" style="background: white;">

<center>
<p><br></p>
<table border="0" width="850">
<tr>
<td width="450"><img src='../image/pi_dish.jpg'></td>
<td width="50">&nbsp;</td>
<td>

<?php include_once("../activate/login_form_$_LANG.php"); ?>

</td>
</tr>
</table>
</center>


<?php include_once("../theme/footer.php"); ?>
