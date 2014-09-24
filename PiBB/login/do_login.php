<?php
session_start();

require_once("login_func.php");

if ( isset($_SESSION['username']) && ! empty($_SESSION['username']) ) {
    header("Location: $_LOGIN_REDIRECT_URL");
    exit();
}

$error = "";
$username = "";
$linkedin_error = U_REQUEST("le"); // For linkedIn sign in error.

if ( isset($_POST["doSubmit"]) ) {
    $captcha_equal = (useLoginCaptcha() ? $_SESSION['captcha'] == $_POST['txtCaptcha'] : 1);

    LoginValidation($_POST['username'], $_POST['userpass'], $captcha_equal);

    if ($error == "") {
        if ( isset($_REQUEST['s']) && $_REQUEST['s'] != "" ) {
            header('Location: ' . urldecode( $_REQUEST['s'] ));
        } else {
            header('Location: ../bbs');
        }
        exit();
    }
}

?>
