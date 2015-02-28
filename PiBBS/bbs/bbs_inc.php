<?php
ob_start(); // avoid "Cannot modify header information - headers already sent" issue.
session_start();

require_once("bbs_func.php");

$is_mobile = isMobile();
$bbs_css = $is_mobile ? "bbs_mobile.css" : "bbs.css";

$custom_header = <<<EOF
<script type="text/javascript" src="bbs_$_LANG.js"></script>
<script type="text/javascript" src="../func/ClsPage.js"></script>
<script type="text/javascript">
function iframe_upload_autoResize(h){
    $('#iframe_upload').height(h + 20);
}
</script>
<link type="text/css" rel="stylesheet" href="../css/$bbs_css" />
<!--[if IE]><script type="text/javascript" src="bbs_ie.js"></script><![endif]-->
EOF;

//
// Variables.
//

//$_DEBUG = 1; // defined in conf/conf.php.
$_username = get_username();
$forum_id = "";
$forum_tbl = "";
$forum_title = "";
$forum_thread_ct = 0;
$forum_post_ct = 0;
$forum_managers = '';
$forum_readonly = 0;
$forum_hidden = 0;
$forum_private = 0;
$forum_disabled = 0;
$mode = U_REQUEST_INT('m'); // 1 - "manage" mode, or "" for regular mode.
$page = U_REQUEST_INT('p');

getBoard_DB();

//$forum_tbl = "";
if ($forum_id == "" || $forum_tbl == "" || $forum_title == "" ||
    ! can_see_hidden_board($forum_id, $forum_hidden) ||
    ! can_see_disabled_board($forum_disabled)
   ) {
    include_once("../theme/header.php");
    print "<p>Unknown Board. <a href='./'>Back to forum list</a></p>";
    include_once("../theme/footer.php");
    exit();
}

$url_params = get_url_params();

// Obtain page_title for view.php and post.php.
$thread_id = U_REQUEST_INT('t');
if ($thread_id != 0 && $forum_tbl != '') {
    $query = "SELECT title FROM $forum_tbl WHERE id = " . db_encode($thread_id); //print $query;
    $page_title = executeScalar($query) . " - $forum_title";
}
?>


