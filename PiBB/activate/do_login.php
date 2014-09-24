<?php
session_start();

include_once("../func/db.php");

if ( isset($_SESSION['username']) && ! empty($_SESSION['username']) ) {
    header('Location: ../');
    exit();
}

$error = "";
$username = "";
$activation_code = "";

if (isset($_REQUEST['c'])) {
    $activation_code = $_REQUEST['c']; // registration code.
}
$activate_status = "";

if (! activation_code_exist($activation_code))  {
    $activate_status = "invalid_activation_code";
}

if ( isset($_POST["doSubmit"]) ) {
    global $_USE_CAPTCHA;
    $captcha_equal = ($_USE_CAPTCHA ? $_SESSION['captcha'] == $_POST['txtCaptcha'] : 1);

    LoginValidation($_POST['username'], $_POST['userpass'], $captcha_equal);

    if ($error == "") {
        $activate_status = "ok";
    }
}


//
// Functions.
//


//
// Return whether the registration code exists in database;
//
function activation_code_exist($c) {
    global $_ACTIVATION_CODE_LEN;
    if ($c == "" || strlen($c) != $_ACTIVATION_CODE_LEN) return 0;

    $query = "SELECT * FROM User WHERE activated = 0 AND activation_code = " . db_encode($c);
    //echo $query;
    db_open();
    $ct = executeRowCount($query);
    db_close();

    //echo $query;
    return $ct == 1;
}

//
// Do login validation.
//
// @Input:
//  - link: from db.php, for database connection.
//  - uname: user name.
//  - upass: user password.
//  - captcha_equal: _POST['txtCaptcha'] == _SESSION['captcha']. Can use 1 if not use captcha.
// @Output:
//  - username: will be echoed in UI's login name textbox.
//  - error: login error message, empty string if no error happens.
// 
function LoginValidation($uname, $upass, $captcha_equal) {
    global $link;
    global $username, $error; // used by UI.

    $username = trim( $uname );
    $userpass = trim( $upass );

    if ( ! $captcha_equal ) { // && ( $_SESSION['captcha'] != $_POST['txtCaptcha'] ) ) {
        $error = getLoginError(1); // The captcha code entered is not correct.
        return;
    }

    if ($username == "" || $userpass == "") {
        $error = getLoginError(2);
        return;
    }

    // Avoid injection attack.
    if ( ! IsValidUsername($username) || ! IsValidPassword($userpass) ) {
        $error = getLoginError(3);
        return;
    }

    $userpass = md5( $userpass );

    db_open();

    // Must filter input to avoid injection attack! Filter is done by db_encode().
    global $activation_code;
    $sql = "select ID, gid, approved, enabled from User where login=" 
           . db_encode($username) . " and passwd=" . db_encode($userpass)
           . " AND activated = '0' AND activation_code = " . db_encode($activation_code) ;
    //echo "query: $sql";
    $result = mysql_query($sql, $link);
    if (! $result) {
        //doExit('Invalid query: ' . mysql_error()); // for security, do not show mysql_error.
        $error = getLoginError(4);
    }
    else {
        if (mysql_num_rows($result)!= 1) {
            $error = getLoginError(5); // "Login failed";
        } else {
            $info = mysql_fetch_array($result);

            if ($info['approved'] != '1') {
                $error = getLoginError(6);
            }
            else if ($info['enabled'] != '1') {
                $error = getLoginError(7);
            }
            else {
                $user_id = $info['ID'];
                $date = date('Y-m-d H:i:s', time());

                // set activated to true, and clear activation_code. 
                //$query = "UPDATE User SET activated = '1', activation_code = NULL WHERE ID = " . db_encode($user_id);
                // No need to clear activatioin code. No conflict occurs, since when looking for
                // unactivated user, always coupled with checking "activated = 0".
                $query = "UPDATE User SET activated = '1', activation_date = " 
                         . db_encode($date) . " WHERE ID = " . db_encode($user_id);
                executeNonQuery($query);

                $_SESSION['username'] = "$username";
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['role'] = getRole( $info['gid'] );
                $_SESSION['ID'] = $user_id;

                // log activate event.
                log_event($user_id, "activate", "", $link);
                log_login( $user_id, $_SESSION['ip'], $date );
            }
        }
    }

    db_close();

    if (isset( $_SESSION['captcha'] )) unset( $_SESSION['captcha'] );
}


//
// Log login datetime and IP.
//
function log_login($ID, $ip, $date) {
    global $link;
    $query = "UPDATE User SET last_login = " . db_encode($date) 
             . ", last_ip = " . db_encode($ip) 
             . " WHERE ID = " . db_encode($ID);
    //echo $query; exit(0);
    executeNonQuery($query);

    // insert to log_site table.
    log_event($ID, "login", $_SESSION['captcha'], $link);
}


//
// Get role of user.
//
function getRole($gid) {
    $role = "";

    if ($gid == 0) { $role = "admin"; }
    else if ($gid == 1) { $role = "user"; }
    else { $role = ""; }

    return $role;
}


//
// Get login error message.
//
// For security, only most general information is given in general.
// More details can be given by setting $_DEBUG to 1.
//
// @param:
//  - $n: error number.
//
function getLoginError($n) {
    global $_LANG;
    $e = "";

    $_DEBUG = 0; // Whether to debug login function. Used in this function only.
    if (! $_DEBUG) {
        if ($_LANG == "cn") {
            if ($n == 6) { $e = "帐号正在等待审核"; }
            else if ($n == 7) { $e = "帐号尚未激活"; }
            else { $e = "帐号，密码或验证码错误"; }
            $e = "激活失败：$e";
        } else {
            if ($n == 6) { $e = "Account is waiting for approval"; }
            else if ($n == 7) { $e = "Account has not been enabled"; }
            else { $e = "Username, password or captcha is incorrect"; }
            $e = "Activation failed: $e";
        }
        return $e;
    }

    // Return more detailed information only for debug purpose.
    if ($_LANG == "cn") { // _LANG is set in ../func/db.php
        if ($n == 1) { $e = "帐号，密码或验证码错误"; } // actually: wrong captcha 
        else if ($n == 2) { $e = "帐号和密码不能为空"; }
        else if ($n == 3) { $e = "帐号或密码非法"; }
        else if ($n == 4) { $e = "内部错误"; }          // actually: query error.
        else if ($n == 5) { $e = "内部返回错误"; }      // actually: query exception.
        else if ($n == 6) { $e = "帐号正在等待审核"; }
        else if ($n == 7) { $e = "帐号尚未激活"; }
        else { $e = "未知错误"; }

        $e = "激活失败($n)：$e";
    } else { // Default is English.
        if ($n == 1) { $e = "Username, password or captcha is incorrect"; }
        else if ($n == 2) { $e = "Username or password cannot be empty"; }
        else if ($n == 3) { $e = "Invalid username or password"; }
        else if ($n == 4) { $e = "Internal error"; }
        else if ($n == 5) { $e = "Internal return error"; }
        else if ($n == 6) { $e = "Account is waiting for approval"; }
        else if ($n == 7) { $e = "Account has not been enabled"; }
        else { $e = "unkown error"; }

        $e = "Activation failed($n): $e";
    }

    return $e;
}

?>
