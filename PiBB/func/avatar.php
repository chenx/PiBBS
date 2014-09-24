<?php
//
// Get user avatar.
//

require_once("setting.php");
require_once("db.php");

//
// Get avatar by email.
//
function get_avatar($email, $img = false) {
    return get_avatar_by_email($email, $img);
}

//
// Get avatar by user's id.
//
function get_avatar_by_id($id, $img = false) {
    global $_USE_GRAVATAR;
    if ($id == "" || ! $_USE_GRAVATAR) return default_avatar();

    $sql = "SELECT email from User WHERE ID = " . db_encode($id);
    $email = executeScalar($sql); //print "$email";

    return get_avatar_by_email($email, $img);
}

//
// Get avatar by user's email.
//
function get_avatar_by_email($email, $img = false) {
    global $_USE_GRAVATAR;
    if ($email == "" || ! $_USE_GRAVATAR) return default_avatar();

    $link = get_gravatar($email);
    if ($img) { return "<img src=\"$link\" border=\"0\"/>"; }
    else { return $link; }
}

function default_avatar() {
    global $_DEFAULT_AVATAR;
    $default_avatar = "<img src=\"$_DEFAULT_AVATAR\" border=\"0\"/>";
    return $default_avatar;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar | retro ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'retro', $r = 'g', $img = false, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}
?>
