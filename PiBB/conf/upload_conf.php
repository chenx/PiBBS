<?php
//
// Used by:
// ../bbs/attachment_func.php
// ../bbs/file_upload.php
//

$max_file_size_MB = 30;
$max_file_size = 31457280; //1024 * 1024 * $max_file_size_MB; 
$extensions = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'tiff',
                    'txt', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx',
                    'zip', 'rar', 'tar', 'gz');
$_UPLOAD_ROOT = "../upload";

// For UI notes.
$_UPLOAD_CONSTRAINTS = "gif, jpeg, jpg, png, bmp, tiff, txt, doc, docx, pdf, xls, xlsx, ppt, pptx, zip, rar, tar, gz. < 30MB";
?>
