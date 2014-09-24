<?php
header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once("themes.php");
?>

<!DOCTYPE html>
<html>
<head>
<?php if ($is_mobile) { ?><meta name="viewport" content="width=device-width"><?php } ?>
<meta name="description" content="<?php print $PAGE_DESCRIPTION; ?>">
<meta name="keywords" content="<?php print $PAGE_KEYWORDS; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo "$page_title"; ?></title>
<script type="text/javascript" src="../js/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type='text/javascript' src='../js/menu.js'></script>
<?php print getThemeCss(); ?>
</head>
<body>

<?php
$root_path = "..";
include_once("../menu/menu.php");
?>

<?php
if (isMobile() && $_USE_IMAIL && isset($_SESSION['ID'])) {
    $unread_imail = getUnReadEmails($_SESSION['ID']);
    if ($unread_imail != "") {
        print "<br/><div style='width: 100%; text-align: center;'>$unread_imail</div>";
    }
}
?>

<center>

<?php 
if (isset($div_main_id) && $div_main_id != "") { 
    print "<div id=\"$div_main_id\">";
} else { ?>
<div id="main_panel">
<div id="main">
<?php } ?>

