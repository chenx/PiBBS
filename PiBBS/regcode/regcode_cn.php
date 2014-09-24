<h3>我的注册码</h3>

<p>您可以把注册码送给你的朋友，这样他们可以在本站注册。</p>

<p>
<?php
$ct = 0;
$s = getRegCode($_SESSION['ID'], $ct);
if ($s == "") {
    $s = "<font color='#999999'>您没有未使用的注册码。</font>";
}
else {
    $s = "您有" . "<b>$ct</b>" . "个未使用的注册码：<br><br><font color='green'>$s</font>";
}

print $s;
?>
</p>
