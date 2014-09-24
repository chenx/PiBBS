<?php
session_start();

require_once("../func/auth.php");
require_once("../func/user_func.php");

$page_title = "Registration Code";
$page_name = $_SERVER['PHP_SELF'];
include_once("../theme/header.php");
?>

<p><br></p>

<img src="../image/pi_pie2.jpg" style="float:left; width: 400px;" />

<table style="width: 500px;"><tr>
<td>
<?php  include_once("regcode_$_LANG.php"); ?>
</td>

</tr></table>

<?php
include_once("../theme/footer.php");
?>

