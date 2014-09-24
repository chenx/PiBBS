<?php 
session_start();
$page_title = "Help";
$root_path = "..";
include_once("../theme/header.php"); 
?>

<table width="100%"><tr><td> 

<div class="desktop">
<p><br></p>
<img src="../image/help2.gif" style="float: left; width: 300px;">
</div>

<?php

include_once("../func/setting.php");
include_once( "help_$_LANG.php" );

?>
</td></tr></table>

<?php include_once("../theme/footer.php");  ?>

