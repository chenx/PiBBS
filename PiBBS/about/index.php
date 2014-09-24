<?php 
session_start(); 
$page_title = "About";
$root_path = "..";
include_once("../theme/header.php"); 
?>

<table width="100%"><tr><td> 

<div class="desktop">
<p><br></p>
<img src="../image/about.jpg" style="float:left; padding:0px;">
</div>

<?php

include_once("../func/setting.php");
include_once( "about_$_LANG.php" );

?>

</td></tr></table>

<?php include_once("../theme/footer.php");  ?>

