<?php
ob_start();
session_start();

require_once("../func/auth.php");
require_once("../func/Cls_DBTable_Custom.php");
require_once("../func/user_func.php");
require_once("../func/avatar.php");

$page_title = "Avatar";
$page_name = "../profile/avatar.php"; 
include_once("../theme/header.php");
?>

<p class="desktop"><br></p>

<?php include("avatar_$_LANG.php"); ?>

<?php
include_once("../theme/footer.php");
?>

