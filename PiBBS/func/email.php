<?php

include_once("../func/setting.php");

function send_email($to, $subject, $body, $from = "", $signature = "") {
    global $_HOST_EMAIL, $_SITE_NAME, $_USE_EMAIL;

    if ( ! $_USE_EMAIL ) return;

    if ($from == "") {
        $headers = "From: $_HOST_EMAIL"; //webmaster@homecox.com";
    }
    else {
        $headers = "From: $from";
    }

    if ($signature == "") {
        $body .= "\n\n--\nThis is a system generated email. Please do not reply.\nSent from $_SITE_NAME\n";
    } else {
        $body .= "$signature";
    }

    mail($to, $subject, $body, $headers);
}

?>
