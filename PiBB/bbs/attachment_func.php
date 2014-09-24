<?php
include_once("../conf/upload_conf.php");
require_once("../bbs/bbs_func.php");
require_once("../func/util_fs.php");


//
// The temp storage folder for BBS upload.
//
function get_tmp_upload_dir($username) {
    global $_UPLOAD_ROOT;
    return "$_UPLOAD_ROOT/bbs/tmp/$username";
}

//
// The final storage folder of BBS upload of a user.
//
function get_bbs_usr_upload_dir($username) {
    global $_UPLOAD_ROOT;
    return "$_UPLOAD_ROOT/bbs/fin/$username";
}

//
// The final storage folder for BBS upload of a user's post in a forum.
//
function get_bbs_upload_dir($username, $forum_id, $post_id, $salt) {
    $usr_dir = get_bbs_usr_upload_dir($username);
    return "$usr_dir/f$forum_id" . "_$post_id" . "_$salt";
}


//
// Show attachment files of a post, in alphabeta order of file names.
// Note definition of tmp/bbs upload folders are in ../conf/upload_conf.php
//
function get_attachment($forum_id, $post_id, $username, $salt) {
    global $_BBS_DISP_ATTACHED_IMAGE;

    $dir = get_bbs_upload_dir($username, $forum_id, $post_id, $salt);
    $files = getSubFilesAsArray($dir);

    $ct = count($files);
    if ($ct == 0) { return ""; }

    sort($files);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $entry = $files[$i];
        $size = getFileSize( filesize("$dir/$entry") );
        $item = <<<EOF
<br/>
<img src='../image/clip.png' border='0' style='height: 20px; vertical-align: middle;' title='Attachment'>
<a href='$dir/$entry' target='_new'>$entry</a>$size &nbsp;
EOF;

        if ($_BBS_DISP_ATTACHED_IMAGE && is_image_ext($entry)) {
            $item .= "<br/><a href='$dir/$entry' title='Click to show original image' target='_new'><img src='$dir/$entry' border='0' class=\"media_img_upload\"/></a> &nbsp;";
        }

        $s .= $item;
    }

    if ($s != "") { $s = "$s"; }
    return $s;
}


//
// Note: will have to delete contents first before can delete folder.
//
function delete_attachment($forum_id, $post_id, $username, $salt) {
    $dir = get_bbs_upload_dir($username, $forum_id, $post_id, $salt);
    unlinkRecursive($dir, true);
}


//
// Move from tmp folder to target folder.
//
function insert_attachment($forum_id, $post_id, $username, $salt) {
    if ($forum_id == "" || $post_id == "") return 0;

    global $_UPLOAD_ROOT;
    $dir_src = get_tmp_upload_dir($username); 
    $dir_dst = get_bbs_upload_dir($username, $forum_id, $post_id, $salt);

    //print "$dir_src, $dir_dst<br/>";
    if (! dir_is_empty($dir_src)) {
        if (! is_dir($dir_dst)) { mkdir($dir_dst, 0755, true); }
        if (! is_dir($dir_dst) || ! is_writable($dir_dst)) {
            throw new Exception(
                "Your post is added. But attachment cannot be moved. Please contact admin");
        }
        rename($dir_src, $dir_dst);

        // create an index.html file in user's root, for privacy.
        createFile( get_bbs_usr_upload_dir($username), "index.html");

        return 1;
    }
    return 0;
}


//
// Return a section of editForm, viewForm etc.
//
function get_form_attachment_row($param) {
    global $_BBS_USE_ATTACHMENT, $_UPLOAD_CONSTRAINTS;
    if (! $_BBS_USE_ATTACHMENT) return "";

    global $T_attachment;
    $attachment = <<<EOF
<tr>
<td style="vertical-align: top; line-height: 22px;">$T_attachment: 
<img src="../image/question.gif" title="$_UPLOAD_CONSTRAINTS" style="vertical-align:middle;"> 
</td>
<td>
<iframe id="iframe_upload" src="file_upload.php$param" style="width:99%;height:50px;margin:0px;" marginw
idth="0" frameborder="0">
</iframe>
</td>
</tr>
EOF;
    return $attachment;
}

?>

