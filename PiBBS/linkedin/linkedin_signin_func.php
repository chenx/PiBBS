<?php
require_once("../conf/conf.php");
require_once("../login/login_func.php");


function LinkedIn_User_Login($user_id) {
    clear_LinkedIn_Session();

    $error = User_Login($user_id);
    if ($error == "") {
        if ( isset($_REQUEST['s']) && $_REQUEST['s'] != "" ) {
            header('Location: ' . urldecode( $_REQUEST['s'] ));
        } else {
            global $_LOGIN_REDIRECT_URL;
            header("Location: $_LOGIN_REDIRECT_URL");
        }
        exit();
    }
    else {
        header("Location: ../login/?le=$error");
    }
}


function clear_LinkedIn_Session() {
    session_unset();
    $_SESSION = array();
/*
    $_SESSION['firstName'] = '';
    $_SESSION['lastName'] = '';
    $_SESSION['email'] = '';
    $_SESSION['linkedin_id'] = '';
    $_SESSION['state'] = '';
    $_SESSION['expires_in'] = '';
    $_SESSION['expires_at'] = '';
    $_SESSION['access_token'] = '';
*/
}

function User_Login($user_id) {
    global $link;
    db_open();

    // Must filter input to avoid injection attack! Filter is done by db_encode().
    $sql = "select ID, login, gid, approved, enabled, activated from User where ID = " .
           db_encode($user_id);
    //print $sql;
    $error = "";

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
                $_SESSION['username'] = $info['login']; // "$username";
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

    return $error;
}


function insertUser($first_name, $last_name, $email, $login, $passwd) {
    $date = date('Y-m-d H:i:s', time());
    $ip   = $_SERVER['REMOTE_ADDR'];

    $activated = "1";
    $activation_code = "";
    $activation_date = $date;

    $sql = 
 "INSERT INTO User ("
 . "first_name, last_name, email, login, passwd, note, reg_date, last_login, last_ip, "
 . "approved, approve_date, enabled, activated, activation_code, activation_date"
 . ") VALUES ("
 . db_encode( $first_name ) . ", "
 . db_encode( $last_name ) . ", "
 . db_encode( $email ) . ", "
 . db_encode( $login ) . ", "
 . db_encode( MD5( $passwd ) ) . ", "
 . db_encode( "" ) . ", "
 . db_encode( $date ) . ", "
 . db_encode( $date ) . ", "
 . db_encode( $ip ) . ", "
 . "1" . ", "
 . db_encode( $date ) . ", "
 . "1" . ", "
 . db_encode( $activated ) . ", "
 . db_encode( $activation_code ) . ", "
 . db_encode( $activation_date )
 . ")";

    executeNonQuery($sql);
}


function getUserIdByLogin($login) {
    $sql = "SELECT ID From User WHERE login = " . db_encode($login);
    $user_id = executeScalar($sql);
    return $user_id;
}


function insertUser_LinkedIn($linkedin_user_id, $fk_user_id) {
    $a = db_encode($linkedin_user_id);
    $b = db_encode($fk_user_id);

    // Delete previous entry for $fk_user_id.
    // This could happen if the website changes API_KEY,
    // in that case the user's user_id is linked to another linkedin_id.
    $sql = "DELETE FROM User_LinkedIn WHERE fk_user_id = $b";
    executeNonQuery($sql);

    // Insert the new linkage.
    $sql = "INSERT INTO User_LinkedIn (linkedin_id, fk_user_id) VALUES ($a, $b)";
    //print $sql;
    executeNonQuery($sql);
}

?>
