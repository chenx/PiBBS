<?php
require_once("../func/db.php");
require_once("../func/util.php");


//
// Functions.
//


function useLoginCaptcha() {
    global $_USE_CAPTCHA, $_USE_CAPTCHA_FOR_LOGIN;
    return $_USE_CAPTCHA && $_USE_CAPTCHA_FOR_LOGIN;
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
    $sql = "select ID, gid, approved, enabled, activated from User where login=" .
           db_encode($username) . " and passwd=" . db_encode($userpass);
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
            else if ($info['activated'] != '1') {
                $error = getLoginError(8);
            }
            else {
                $_SESSION['username'] = "$username";
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                //other data needed to navigate the site or to authenticate the user.
                $_SESSION['role'] = getRole( $info['gid'] );
                $_SESSION['ID'] = $info['ID'];
                $_SESSION['bbs_role'] = getBBSRole( $info['ID'] );
                $_SESSION['bbs_PrivateMembership'] = getPrivateMembership($_SESSION['ID']);

                log_login( $_SESSION['ID'], $_SESSION['ip'], date('Y-m-d H:i:s', time()) );
            }
        }
    }

    db_close();

    if (isset( $_SESSION['captcha'] )) unset( $_SESSION['captcha'] );
}


//
// store the user's private boards in a session variable.
// This is faster than query it each time visting the private board.
//
function getPrivateMembership($user_id) {
    $sql = "SELECT forum_id FROM BBS_PrivateMembership WHERE user_id = " . db_encode($user_id);
    $t = executeScalarArray($sql, "forum_id");
    $s = "|";
    for ($i = 0, $len = count($t); $i < $len; ++ $i) {
        $s .= $t[$i] . "|";
    }
    return $s;
}


//
// Format: |forum_id_1,role_1|forum_id_2,rol_2|..
// role = 1 (manager), or 2 (vice manager).
//
function getBBSRole($ID) {
    global $link;
    $query = "SELECT CONCAT(forum_id, ',', role) AS role FROM BBS_BoardManager WHERE user_id = " . db_encode($ID);
    $a = executeScalarArray($query, 'role');

    $s = "";
    for ($i = 0, $len = count($a); $i < $len; ++ $i) {
        $s .= "|$a[$i]";
    }
    if ($s != "") $s .= "|";

    return $s;
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

    $captcha = '';
    if ( isset($_SESSION['captcha']) ) $captcha = $_SESSION['captcha'];

    // insert to log_site table.
    log_event($ID, "login", $captcha, $link);
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
            else if ($n == 7) { $e = "帐号处于关闭状态"; }
            else if ($n == 8) { $e = "帐号尚未激活"; }
            else {  
                if (useLoginCaptcha()) { $e = "帐号，密码或验证码错误"; }
                else { $e = "帐号或密码错误"; }
            }
            $e = "登录失败：$e";
        } else {
            if ($n == 6) { $e = "Account is waiting for approval"; }
            else if ($n == 7) { $e = "Account has been disabled"; }
            else if ($n == 8) { $e = "Account has not been activated"; }
            else { 
                if (useLoginCaptcha()) { $e = "Username, password or captcha is incorrect"; }
                else { $e = "Username or password is incorrect"; }
            }
            $e = "Login failed: $e";
        }
        return $e;
    }

    // Return more detailed information only for debug purpose.
    if ($_LANG == "cn") { // _LANG is set in ../func/db.php
        if ($n == 1) { // actually: wrong captcha
           if (useLoginCaptcha()) { $e = "帐号，密码或验证码错误"; } 
           else { $e = "帐号或密码错误"; }
        }
        else if ($n == 2) { $e = "帐号和密码不能为空"; }
        else if ($n == 3) { $e = "帐号或密码非法"; }
        else if ($n == 4) { $e = "内部错误"; }          // actually: query error.
        else if ($n == 5) { $e = "内部返回错误"; }      // actually: query exception.
        else if ($n == 6) { $e = "帐号正在等待审核"; }
        else if ($n == 7) { $e = "帐号处于关闭状态"; }
        else if ($n == 8) { $e = "帐号尚未激活"; }
        else { $e = "未知错误"; }

        $e = "登录失败($n)：$e";
    } else { // Default is English.
        if ($n == 1) {
           if (useLoginCaptcha()) { $e = "Username, password or captcha is incorrect"; }
           else { $e = "Username or password is incorrect"; }
        }
        else if ($n == 2) { $e = "Username or password cannot be empty"; }
        else if ($n == 3) { $e = "Invalid username or password"; }
        else if ($n == 4) { $e = "Internal error"; }
        else if ($n == 5) { $e = "Internal return error"; }
        else if ($n == 6) { $e = "Account is waiting for approval"; }
        else if ($n == 7) { $e = "Account has been disabled"; }
        else if ($n == 8) { $e = "Account has not been activated"; }
        else { $e = "unkown error"; }

        $e = "Login failed($n): $e";
    }

    return $e;
}


?>
