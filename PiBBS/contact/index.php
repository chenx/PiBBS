<?php 
session_start(); 
include_once("../func/setting.php");
require_once("../func/email.php");
require_once("../func/util.php");
//$_LANG = "en"; // for testing purpose.
require_once("terms_contact.php");

$page_title = "Contact Us";
$root_path = "..";
include_once("../theme/header.php"); 

$msg = "";
$title = trim(U_POST("title"));
if ($title != "") {
    $body = trim(U_POST("body"));
    $name = trim(U_POST("name"));
    $email = trim(U_POST("email"));

    try {
        send_email("homecoxoj@gmail.com", $title, "From: $name <$email>\n\n$body", $email);
        $msg = "";
        header("Location: index.php?ok");
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }
}

?>

<table width="100%" border="0"><tr><td> 
<p><br></p>

<?php writeContactForm(); ?>

</td></tr></table>

<script type="text/javascript">
function submitForm() {
    var name = $.trim($("#name").val());
    var email = $.trim($("#email").val());
    var title = $.trim($("#title").val());
    var body = $.trim($("#body").val());

    $("#e_title").html('');
    $("#e_body").html('');
    $('#e_email').html('');

    if (title == '') {
        $("#e_title").html('<?php print $T_no_empty; ?>');
        $("#title").focus();
    } else if (body == '') {
        $("#e_body").html('<?php print $T_no_empty; ?>');
        $("#body").focus();
    } else if (email != '' &&  ! /^([a-zA-Z0-9]+[._-])*[a-zA-Z0-9]+@[a-zA-Z0-9-_\.]+\.[a-zA-Z]+$/.test( email ) ) {
        $('#e_email').html('<?php print $T_invalid_email; ?>');
    } else {
        document.forms[0].submit();
    }
}
</script>

<?php include_once("../theme/footer.php");  ?>


<?php 
function writeContactForm() {
    global $T_greeting, $T_title, $T_body, $T_name, $T_email, $T_submit, $T_email_sent, $T_email_error, $T_back;
    global $msg;
    print "<img src=\"../image/contactus.jpg\" style=\"float:left; width: 347px;\">";

    if (isset($_REQUEST['ok'])) {
        if ($msg == "") {
            print "<font color='green'>$T_email_sent<br><br><a href='./'>$T_back</a></font>";
        } else {
            print "<font color='red'>$T_email_error<br><br>$msg</font>";
        }
    } else { 
        $s = <<<EOF
<p>$T_greeting</p>

<form method="post">
<table>
<tr><td>$T_name: </td><td><input type="text" id="name" name="name"></td></tr>
<tr><td>$T_email: </td><td><input type="text" id="email" name="email"><br>
        <span id="e_email" style="color: red;"></span></td></tr>
<tr><td>$T_title: <font color="red">*</font></td><td><input type="text" id="title" name="title"><br>
        <span id="e_title" style="color: red;"></span></tr></td>
<tr><td>$T_body: <font color="red">*</font></td>
    <td><textarea id="body" name="body" style="width: 500px; height: 300px;"></textarea><br>
        <span id="e_body" style="color: red;"></span></td></tr>
<tr><td><br></td><td><input type="button" value="$T_submit" onclick="javascript: submitForm();" style="width: 120px;"></td></tr>
</table>
</form>
EOF;

        print $s;
    }
}

?>

