<?php
//
// Detect if the browser is on a mobile device.
// From: http://stackoverflow.com/questions/6524301/detect-mobile-browser
//
function isMobile() {
    if (! isset($_SERVER["HTTP_USER_AGENT"]) ) return false;
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
?>
