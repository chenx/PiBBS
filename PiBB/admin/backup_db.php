<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/util.php");
require_once("../func/util_fs.php");

$page_name = "Backup DB";
$page_title = "Admin - $page_name";

$div_main_id = "main_full_width";
include("../theme/header.php"); 
?>

<table width="100%"><tr><td>
<P><a href="./">Admin</a> &gt; <?php echo $page_name; ?></P>

<script type="text/javascript">
function deleteFile(file) {
    var r = confirm("Are you sure to delete file " + file + "?");
    if (r) {
        document.getElementById('btnDelete').value = file;
        document.forms[0].submit();
    }
}
function viewFile(file) {
    document.getElementById('btnView').value = file;
    document.forms[0].submit();
}
</script>

<h3>Backup Database</h3>

<p>
Back up database using the mysqldump command into a sql file.
To recover, you have to go to console.
</p>

<form method="post">
<p>
<input type='submit' name='btnSubmit' value='Backup Database' />
<input type='button' value='Refresh Page' onclick="javascript: window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>';" />
</p>
<input type='hidden' id='btnDelete' name='btnDelete' value='' />
<input type='hidden' id='btnView' name='btnView' value='' />
<?php

$dir_bak = $_DB_BAK_PATH; 

if (isset($_REQUEST['btnSubmit']) && $_REQUEST['btnSubmit'] != "") {
    backup_db($dir_bak);
}
else if (isset($_REQUEST['btnDelete']) && $_REQUEST['btnDelete'] != "") {
    delete_backup($dir_bak . "/" . $_REQUEST['btnDelete']);
}

$dir = getSubFilesAsArray($dir_bak, 2); // order = 2, sort DESC.
if (! is_array($dir)) {
    echo $dir;
} else {
    //print_r($dir);
    show_backup_list($dir);
}

if (isset($_REQUEST['btnView']) && $_REQUEST['btnView'] != "") {
    view_backup($_REQUEST['btnView']);
}

?>
</form>

</td></tr></table>
<?php
include("../theme/footer.php");
?>

<?php

function view_backup($filename) {
    global $dir_bak;
    $s = file_get_contents( "$dir_bak/$filename" );
    echo "<br/>$filename<br/>";
    echo "<TEXTAREA style='width: 100%; height: 200px;'>$s</TEXTAREA>";
}

//
// $ASC: 1 - in ascending order (of time), 0 - in descending order (default).
//
function show_backup_list($dir, $ASC = 0) {
    $ct = count($dir);
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        if (! endsWith($dir[$i], ".sql")) continue; // only download sql file.
        $t = "<a href='#' onclick='javascript: deleteFile(\"$dir[$i]\");'>Delete</a>&nbsp;"
           . "<a href='#' onclick='javascript: viewFile(\"$dir[$i]\");'>View</a>&nbsp;"
           . " $dir[$i]<br/>";

        if ($ASC == 0) $s = $s . $t;
        else $s = $t . $s;
    }
    print $s;
}

function delete_backup($file) {
    try {
        if (! file_exists($file)) {
            $s = "File $file does not exist.";
            $color = "green";
        }
        unlink($file);
        $s = "File $file has been successfully deleted.";
        $color = 'green';
    } catch (Exception $e) {
        $s = $e->getMessage();
        $color = 'red';
    }
    echo "<p><font color='$color'>$s</font></p>";
}

function backup_db($dir_bak) {
    //global $_CONF_PATH;
    global $db, $db_usr, $db_pwd;

    $backup_name = $db . "_" . date('y-m-d-h-i-s') . ".sql";
    $shell_cmd = <<<EOF
mysqldump -u $db_usr -p"$db_pwd" $db > $dir_bak/$backup_name
EOF;

    system($shell_cmd);
}

?>

