<?php
// Change these
/*
define('API_KEY',      '75n90idzn6frtw'); 
define('API_SECRET',   'fgpAp0hAaVRjR5Rt'); 
define('REDIRECT_URI', 'http://cssauh.com/xc/xcbbs/linkedin/'); 
define('SCOPE',        'r_emailaddress r_fullprofile'); 
*/
require_once("../conf/linkedin_conf.php");

$redirect_url = isset($_REQUEST['s']) ? $_REQUEST['s'] : "";
$reg_url = "signup.php?s=$redirect_url"; 


//session_name('linkedin');
session_start();
require_once("linkedin_signin_func.php");

$url_self = $_SERVER["PHP_SELF"];

// OAuth 2 Control Flow
if (isset($_GET['error'])) { // LinkedIn returned an error.
    //print $_GET['error']; // . ': ' . $_GET['error_description'];
    //print "<br/>Please <a href=\"$url_self\">log in with linked in</a>";
    header("Location: ../login/?le=" . urlencode( "LinkedIn sign in error: " . $_GET['error'] ));
    print "Error: " . $_GET['error'];
    exit;
} elseif (isset($_GET['code'])) { // User authorized your application.
    //print $_SESSION['state'] . " ==? " . $_GET['state'] . "<br>";
    if (isset($_SESSION['state']) && $_SESSION['state'] == $_GET['state']) {
        getAccessToken(); // Get token so you can make API calls.
        // now fall through.
    } else {
        header("Location: ../login/?le=" . urlencode("State session does not match Get param."));
        exit; // CSRF attack? Or did you mix up your states?
    }
} elseif (isset($_GET['logout'])) { // This branch is for teting only.
    session_unset();
    $_SESSION = array();
    print "You have been logged out. <a href='$url_self'>Log in</a>";
    exit;
} elseif (isset($_GET['login'])) {
    if ((empty($_SESSION['expires_at'])) || (time() > $_SESSION['expires_at'])) {
        $_SESSION = array();  // Token has expired, clear the state
    }
    if (empty($_SESSION['access_token'])) {
        getAuthorizationCode();  // Start authorization process
        print "Error: did not redirect after get authorization.";
        exit();
    }
    //else { // should not happen.
    //    print ".";
    //    session_unset();
    //    $_SESSION = array();
    //}
} 


// id - is dependent on API_KEY. 
// see: http://developer.linkedin.com/forum/different-id-same-user-using-oauth
$user = fetch('GET', '/v1/people/~:(id,firstName,lastName,email-address)');

if ((! is_object($user) ) || $user->id == '' || $user->firstName == '' 
    || $user->lastName == '' || $user->emailAddress == '') { 
    header("Location: $url_self?login=1");
    exit;
} else {
    try {
        LinkedIn_User_Signup($user);
        print "Error: did not redirect after linkedin user signup.";
        exit;
    } catch (Exception $e) {
        print "Exception: " . $e->getMessage();
        exit;
    }
/*
    //header("Location: $reg_url");
    //exit;
    print "user_id: " . $user->id . "<br/>";
    print "Hello $user->firstName, Now send you to <a href='$reg_url'>registration form</a>.";
    print "<br/><a href='$url_self?logout=1'>logout</a>";
    exit;
*/
}


// // Table User_LinkedIn: (ID, LinkedIn_ID, fk_user_id)
// If (linkedIn_ID, fk_user_id) exists in User_LinkedIn, sign in.
// else {
//     ask for an account/login name;
//     if not get account name, exit;
//     else, insert (LinkedIn_ID, login) to User_LinkedIn, sign in.
// }
function LinkedIn_User_Signup($user) {
    global $reg_url;

    $sql = "SELECT fk_user_id FROM User_LinkedIn WHERE LinkedIn_ID = " . db_encode($user->id);
    //print $sql; exit;
    $user_id = executeScalar($sql);
    if ($user_id != '') {
        LinkedIn_User_Login($user_id);
    }
    else {
        $_SESSION['firstName'] = $user->firstName;
        $_SESSION['lastName'] = $user->lastName;
        $_SESSION['email'] = $user->emailAddress;
        $_SESSION['linkedin_id'] = $user->id;
        header("Location: $reg_url");
    }
}


function getAuthorizationCode() {
    $_SESSION['state'] = uniqid('', true); // unique long string.
    $params = array('response_type' => 'code',
                    'client_id' => API_KEY,
                    'scope' => SCOPE,
                    'state' => $_SESSION['state'], 
                    'redirect_uri' => REDIRECT_URI,
              );
 
    // Authentication request
    $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
     
    // Needed to identify request when it returns to us
    $_SESSION['state'] = $params['state'];
 
    // Redirect user to authenticate
    header("Location: $url");
    //exit;
}
     
function getAccessToken() {
    $params = array('grant_type' => 'authorization_code',
                    'client_id' => API_KEY,
                    'client_secret' => API_SECRET,
                    'code' => $_GET['code'],
                    'redirect_uri' => REDIRECT_URI,
              );
     
    // Access Token request
    $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
     
    // Tell streams to make a POST request
    $context = stream_context_create(
                    array('http' =>
                        array('method' => 'POST',
                        )
                    )
                );
 
    // Retrieve access token information
    $response = file_get_contents($url, false, $context);
 
    // Native PHP object, please
    $token = json_decode($response);
 
    // Store access token and expiration time
    $_SESSION['access_token'] = $token->access_token; // guard this!
    $_SESSION['expires_in']   = $token->expires_in; // relative time (in seconds)
    $_SESSION['expires_at']   = time() + $_SESSION['expires_in']; // absolute time
     
    return true;
}
 
function fetch($method, $resource, $body = '') {
    $access_token = isset( $_SESSION['access_token'] ) ? $_SESSION['access_token'] : "";
    $params = array('oauth2_access_token' => $access_token,
                    'format' => 'json',
              );
     
    // Need to use HTTPS
    $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
    // Tell streams to make a (GET, POST, PUT, or DELETE) request
    $context = stream_context_create(
                    array('http' =>
                        array('method' => $method,
                        )
                    )
                );
 
    try { 
        // Hocus Pocus
        $response = file_get_contents($url, false, $context);
 
        // Native PHP object, please
        return json_decode($response);
    } catch (Exception $e) {
        return "";
    }
}

?>

