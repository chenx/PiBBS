<?php 
session_start(); 

if ( isset($_SESSION['username']) && ! empty($_SESSION['username']) ) {
    header('Location: ../login/');
    exit();
}
if (! isset($_SESSION['linkedin_id']) || $_SESSION['linkedin_id'] == '') {
    // no linkedin credential, exis this page.
    header('Location: ../login/');
    exit();
}

require_once("../func/util.php");
require_once("../func/db.php");
require_once("linkedin_signin_func.php");


$actionType = U_REQUEST('btnActionType');
$userType = U_REQUEST('userType');
$newLogin = U_REQUEST('txtNewLogin');
$oldLogin = U_REQUEST('txtOldLogin');
$oldPwd = U_REQUEST('txtOldPwd');
$error = "";

if ($actionType == 'new' && $newLogin != '') {
    $error = addNewUser($newLogin);
}
else if ($actionType == 'old' && $oldLogin != '' && $oldPwd != '') {
    $error = linkOldUser($oldLogin, $oldPwd);
}
else if ($actionType == "giveup") {
    clear_LinkedIn_Session();
    header("Location: ../login/");
    exit();
}

function addNewUser($newLogin) {
    $sql = "SELECT ID FROM User WHERE login = " . db_encode($newLogin);
    $id = executeScalar($sql);
    if ($id != '') {
        return "Error: this account name already exists.";
    }
    else {
        try {
            $newPwd = getRandStr(10, 3);
            insertUser($_SESSION['firstName'], $_SESSION['lastName'], 
                       $_SESSION['email'], $newLogin, $newPwd);
            $user_id = getUserIdByLogin($newLogin);
            insertUser_LinkedIn($_SESSION['linkedin_id'], $user_id);
            LinkedIn_User_Login($user_id);
            //exit(); // should be redirected here.
            return "addNewUser.LinkedIn_User_Login didn't redirect";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

function linkOldUser($oldLogin, $oldPwd) {
    $sql = "SELECT ID FROM User WHERE login = " . db_encode($oldLogin) . 
           " AND passwd = " . db_encode( md5($oldPwd) );
    $id = executeScalar($sql);
    if ($id == '') {
        return "Invalid account informatoin"; //getLoginError(5); 
    } else {
        try {
            insertUser_LinkedIn($_SESSION['linkedin_id'], $id);
            LinkedIn_User_Login($id);
            //exit(); // redirected.
            return "linkOldUser.LinkedIn_User_Login didn't redirect";
        } catch (Exception $e) {
            //print "err: " . $e->getMessage();
            return $e->getMessage();
        }
    }
}


$page_title = "Sign up";
$root_path = "..";
include_once("../theme/header.php"); 
?>

<table width="90%"><tr><td> 

<h3>Sign in with LinkedIn</h3>

<form method="POST">

<p>Dear <font color="green"><?php print $_SESSION['firstName']; ?></font>, 
thank you for signing in using LinkedIn for the first time. You are just one step away. Please choose:

<p>
<input type="radio" id="userTypeN" name="userType" value='N' onclick="javascript: changeUserType('N');">I don't have an existing account<br/>
<input type="radio" id="userTypeY" name="userType" value='Y' onclick="javascript: changeUserType('Y');">I have an existing account
</p>

<div id="divNewUser" style="display: none;">
<!--<p><b>New visitor</b></p>-->

<p>
Please choose a permanent account name. This will be used as your ID when post in the forum.

<p>
Account name:<font color="red">*</font> 
<input type="text" id="txtNewLogin" name="txtNewLogin" value="<?php print db_htmlEncode($newLogin); ?>" />
<input type="button" id="btnSubmit" onclick="javascript: validateNewLogin(this);" value="Submit"/>
<p><div id="e_newLogin" style="color:red;"></div></p>

</div>

<div id="divOldUser" style="display: none;">
<!--<p><b>Visitor who already has an account</b></p>-->
<p>
You can link to your existing account now.

<p>
<span style='display: inline-block; width: 80px;'>Login:<font color="red">*</font></span> 
<input type="text" id="txtOldLogin" name="txtOldLogin" value="<?php print db_htmlEncode($oldLogin); ?>" />
<span id="e_oldLogin" style="color:red;"></span><br/>
<span style='display: inline-block; width: 80px;'>Password:<font color="red">*</font></span>
<input type="password" id="txtOldPwd" name="txtOldPwd" />
<span id="e_oldPwd" style="color:red;"></span>
<br/>
<input type="button" id="btnSubmit" onclick="javascript: validateOldLogin(this);" value="Link to my existing account"/>

<input type="hidden" id="s" name="s" value="<?php print isset($_REQUEST["s"]) ? $_REQUEST["s"] : ""; ?>"/>
<input type="hidden" id="btnActionType" name="btnActionType" value=""/>
</form>

<?php
//
// Show this to user when API_KEY is changed.
// Since user's linkedin_id changees when API_KEY is changed, user needs to relink account.
//
$msg = <<<EOF
<p>
Note: If you have logged in using LinkedIn before, you see this message again probably 
because we have changed the LinkedIn API_KEY or LinkedIn had some internal change, 
so you need to link your account again. 

<p>In this case if you  never used a password for this website (which means you already log in 
using LinkedIn, you can <a href="../getpwd">request a password</a> to be sent to
your email address.

<p>Please contact the web master for any questions.
EOF;
?>

</div>

<?php
if ($error != "") {
    $msg = $error; 
    if (preg_match("/duplicate entry .* for key 'email'/i", $error)) {
        $msg .= "<br/>Your linkedin email is associated with an existing account.";
    }
    $msg = "<p><div id='lblErr'><font color='red'>$msg</font></div></p>";
    print $msg;
}
?>

<p style="color: #999;"><br/><br/>If you do not want to sign in with LinkedIn, <a href="javascript:giveup();">click here</a></p>

</td></tr></table>


<script type="text/javascript">
function giveup() {
    document.getElementById('btnActionType').value = 'giveup';
    document.forms[0].submit();
}

function validateNewLogin(src) {
    clearMsg();

    var o = document.getElementById("txtNewLogin");
    var e = document.getElementById("e_newLogin");
    if (o.value == '') {
        e.innerHTML = "Account name cannot be empty.";
        o.focus();
    }
    else {
        src.disabled = true;
        document.getElementById('btnActionType').value = 'new';
        document.forms[0].submit();
    }
}

function validateOldLogin(src) {
    clearMsg();

    var eo = null;

    o = document.getElementById("txtOldPwd");
    e = document.getElementById("e_oldPwd");
    if (o.value == '') {
        e.innerHTML = "Password cannot be empty.";
        eo = o;
    }

    var o = document.getElementById("txtOldLogin");
    var e = document.getElementById("e_oldLogin");
    if (o.value == '') {
        e.innerHTML = "Login cannot be empty.";
        eo = o;
    }

    if (eo != null) { eo.focus(); }
    else { 
        src.disabled = true;
        document.getElementById('btnActionType').value = 'old';
        document.forms[0].submit(); 
    }
}

function changeUserType(v, src) {
    var n = document.getElementById('divNewUser');
    var o = document.getElementById('divOldUser');

    if (v == 'N') {
        n.style.display = 'block';
        o.style.display = 'none';
    } else {
        n.style.display = 'none';
        o.style.display = 'block';
    }
}

function clearMsg() {
    var e = document.getElementById('lblErr');
    if (e) { e.innerHTML = ''; }
}

$(document).ready(function() {
<?php if ($userType == "N") { ?>
    $('#userTypeN').click();
<?php } else if ($userType == "Y") { ?>
    $('#userTypeY').click();
<?php } ?>
});
</script>

<?php include_once("../theme/footer.php");  ?>

