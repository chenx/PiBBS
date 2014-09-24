<?php
$news = <<<EOF
<font color="green">
</font>
EOF;

if ( isset($_SESSION["username"]) ) { 
    $username = $_SESSION['username'];

    $bbs_link = "";
    if ($_USE_BBS) { $bbs_link = "<a href=\"bbs/\">$T_visit_bbs</a> | "; }

    $regcode_link = getRegCodeLink();
    if ($regcode_link != "") { $regcode_link = " | $regcode_link"; }

    $s = <<<EOF
<P>$T_hello, $username, $T_welcome!</P>

<p>
<hr style="size: 1px; color: #eee;">
$bbs_link
<a href="profile/">$T_update_profile</a>
$regcode_link
</p>

<p>
<hr style="size: 1px; color: #eee;">
<h3>$T_news</h3>
$news
<br><!--a href="news/">$T_more ...</a-->
</p>

EOF;

} else { 

    $s = <<<EOF
<a href="login/"><font size="+2">$T_login</font></a> | <a href='register/'>$T_register</a> | <a href='getpwd/'>$T_getpasswd</a>

<p>
<hr style="size: 1px; color: #eee;">
<h3>$T_news</h3>
$news
<br><!--a href="news/">$T_more ...</a-->
</p>
EOF;

} 

print $s;


function getRegCodeLink() {
    global $T_my_regcode;
    $s = "";
    $ct = getRegCodeCount($_SESSION['ID']);

    global $_HIDE_REG_CODE_PAGE_WHEN_NONE;
    if ($ct == 0 && $_HIDE_REG_CODE_PAGE_WHEN_NONE) {
        $s = "";
    }
    else {
        $s = "<a href=\"regcode\">$T_my_regcode ($ct)</a>";
    }
    return $s;
}
?>
