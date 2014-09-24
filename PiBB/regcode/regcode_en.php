<h3>My Registraion Code</h3>

<p>You can distribute these registration codes to your friends, so they can register in this site.<p>

<p>
<?php
$ct = 0;
$s = getRegCode($_SESSION['ID'], $ct);
if ($s == "") {
    $s = "<font color='#999999'>You have no unused registration code.</font>";
}
else {
    $p = ($ct > 1) ? "s" : "";
    $s = "You have <b>$ct</b> unused registration code$p:<br><br><font color='green'>$s</font>";
}

print $s;
?>
</p>

