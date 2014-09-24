<?php
/*
 * Synopsis:
 *   To use this file, you call it in an iframe from another file, which contains:
 *   1) this in <head>:
<script type="text/javascript">
function iframe_upload_autoResize(h){
    $('#iframe_upload').height(h + 20);
}
</script>
 *
 *   2) this in <body> (value of sub_dir can change, or can omit it):
 *
<iframe id="iframe_upload" src="file_upload.php?sub_dir=" style="width:99%;height:50px;margin:0px;" marginwidth="0" frameborder="0">
</iframe>
 *
 * @Note:
 * get_upload_folder() and get_post_salt() are customized functions, 
 * can modify or ignore In another situation.
 *
 * @url params:
 *  - debug : if not empty, shows debug info (output path)
 *  - readonly: if not empty, don't show upload button.
 *
 *  - mode : 1 - new upload (in tmp/), 2 - existing file (in bbs/)
 *  - user : username
 *  - fid : forum_id
 *  - pid : post_id
 */
session_start();

if (! isset($_SESSION['username']) || $_SESSION['username'] == "") {
    print "Unauthorized user.";
    exit();
}

require_once("../imail/attachment_func.php");


//
// Get parameters.
//
$mode = U_REQUEST("mode"); // 1 - new (dir: tmp/), 2 - final (dir: bbs/)
$mid = U_REQUEST("mid");
$user = U_REQUEST("user");
$readonly = (U_REQUEST("readonly") == "" || U_REQUEST("readonly") == "0") ? 0 : 1;
$debug = (U_REQUEST("debug") == "" || U_REQUEST("debug") == "0") ? 0 : 1;
$error = "";

if ($debug) {
    print "mode=$mode, mid=$mid, user=$user, readonly=$readonly, debug=$debug<br/>";
}

$dir = get_upload_folder($mode, $mid, $user);
if ($dir == "") {
    print "Unknown upload directory: $error. Please contact system administrator";
    exit();
}

$msg = "";
if (isset($_REQUEST['btnUpload']) && $_REQUEST['btnUpload'] != "") {
    $msg = file_upload($dir);
}
else if (isset($_REQUEST['btnDelete']) && $_REQUEST['btnDelete'] != "") {
    $msg = file_delete($dir . "/" . $_REQUEST['btnDelete']);
}
?>

<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="../js/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type="text/javascript">
function uploadFile(v) {
    document.getElementById('icon_loading').style.display = 'inline-block';
    document.getElementById('btnUpload').value = 'Upload';
    document.forms[0].submit();
    return false;
}

function deleteFile(file) {
    var r = confirm("Are you sure to delete file " + file + "?");
    if (r) {
        document.getElementById('icon_loading').style.display = 'inline-block';
        document.getElementById('btnDelete').value = file;
        document.forms[0].submit();
    }
}

$(document).ready(function() {
    var h = $("#divMe").height();
    window.top.iframe_upload_autoResize( h );
});
</script>
</head>
<body style="margin: 0px 0px 0px 0px;">
<div id="divMe">

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

<!-- Max Upload file size is <?php echo ($max_file_size) / 1024 ?> KB. <br/> -->

<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size?>"> 
<!--max size: in Byte-->

<span title='Upload image' style="display: inline;">

<?php if (! $readonly) { ?>

<input type='hidden' id='btnUpload' name='btnUpload' value='' style="display:inline;" />
<input name="files[]" type="file" multiple size="20" onchange="javascript: uploadFile(this);" style="display: inline;">
<span id="icon_loading" style="display: none;">
<img src='../image/loading_16_16.gif' style='vertical-align:middle;'> wait ...</span> 
<?php } ?>

<?php
if ($dir != "") {
    $dir_handle = getSubFilesAsArray($dir);
    if (is_array($dir_handle)) { 
        //show_backup_list($dir_handle); 
        get_files($dir_handle);
    }
}

if ($msg != "") {
    print "<br/><font color='red'>$msg</font>";
}
?>

</span>

<input type='hidden' id='btnDelete' name='btnDelete' value='' />
<input type='hidden' name='mode' value='<?php print $mode; ?>' />
<input type='hidden' name='user' value='<?php print $user; ?>' />
<input type='hidden' name='mid' value='<?php print $mid; ?>' />
<input type='hidden' name='readonly' value='<?php print $readonly; ?>' />
<input type='hidden' name='debug' value='<?php print $debug; ?>' />
</form>

</div>
</body>
</html>

<?php

//
// Note the definition of folders are in ../conf/upload_func.php
//
function get_upload_folder($mode, $mail_id, $username) {
    global $error;
    if ($username == "") {
        $error = "no name";
        return "";
    }

    $dir = "";
    if ($mode == "1") {
        $dir = get_tmp_upload_dir($username); // "$root/tmp/$username";
    } else if ($mode == "2") {
        if ($mail_id == "") { $error = "no id";  return ""; }
        $salt = get_post_salt($mail_id);
        if ($salt == "") { $error = "no salt";  return ""; }

        $dir = get_bbs_upload_dir($username, $mail_id, $salt);
    }

    return $dir;
}


function get_post_salt($mail_id) {
    $sql = "SELECT salt FROM IMail WHERE ID = " . db_encode($mail_id);
    $salt = executeScalar($sql);
    //print "$forum_id:$forum_name:$post_id, salt = $salt <br/>";
    return $salt;
}


function get_files($dir_handle) {
    global $dir, $readonly;
    $ct = count($dir_handle);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $f = $dir_handle[$i];

        if ($s != "") $s .= ", ";
        $size = getFileSize( filesize("$dir/$f") );
        $s .= "<a href='$dir/$f' target='_new'>$f</a>$size";
        if (! $readonly) {
            $s .= " <a href='#' onclick='javascript: deleteFile(\"$f\");' title='Delete'><img src='../image/delete.gif' border='0' style='vertical-align:middle;'></a>";
        }
    }

    print $s;
    //return $s;
}


function file_upload($dir) {
    dir_create($dir);

    $s = "";
    if ( is_array( $_FILES['files']['tmp_name'] ) ) {
        $ct = count( $_FILES['files']['tmp_name'] );
        for ($i = 0; $i < $ct; ++ $i) {
            $tmpname = $_FILES['files']['tmp_name'][$i];
            $filename = $_FILES['files']['name'][$i];
            //echo $filename . "<br/>";

            if (! is_wanted_type($filename)) {
                $s .= "Not an acceptable file: $filename<br/>";
            } else {
                move_uploaded_file($tmpname, "$dir/$filename");
                //$s .= "Successfully uploaded file: $filename\\n";
            }
        }
    }

    if ($s != "") {
        global $max_file_size_MB;
        $s .= "Allowed file types: " . get_allowed_types() . 
              ". Max file size: $max_file_size_MB MB";
    }
    return $s;
}

function is_wanted_type($filename) {
    //$extensions = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'tiff',
    //                    'txt', 'doc', 'docx', 'pdf', 'xls', 'ppt');
    global $extensions;
    $ext = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
    return in_array($ext, $extensions);
}

function get_allowed_types() {
    global $extensions;
    $ct = count($extensions);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        if ($s != "") $s .= ", ";
        $s .= $extensions[$i];
    }
    return $s;
}

?>
