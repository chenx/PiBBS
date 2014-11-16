<?php
//
// File system utility.
//

//
// Get list of sub-files (non-directory) of dir.
// http://php.net/manual/en/function.readdir.php
//
// @param:
//  $order: 0 - don't order, 1 - ASC, 2 - DESC
//
function getSubFilesAsArray($dir, $order=0) {
    $a = array();
    if (is_dir($dir) && $handle = opendir("$dir")) {
        // This is the correct way to loop over the directory. 
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && ! is_dir("$dir/$entry")) {
                //echo "$entry<br/>";
                array_push($a, $entry);
            }
        }

        // This is the WRONG way to loop over the directory. 
        //while ($entry = readdir($handle)) {
        //    echo "$entry\n";
        //}

      
        if ($order == 1) { sort($a); }
        else if ($order == 2) { rsort($a); }

        closedir($handle);
    }
    else {
        //return "<p><font color='red'>Error: Can not open directory: $dir</font></p>";
    }
    return $a;
}

//
// Get list of sub-directories of dir.
//
function getSubDirsAsArray($dir) {
    $a = array();
    if ($dir == "") return $a;

    if ($handle = opendir("$dir")) {
        // This is the correct way to loop over the directory.
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && is_dir("$dir/$entry")) {
                //echo "$entry<br/>";
                array_push($a, $entry);
            }
        }

        closedir($handle);
    }
    else {
        //return "<p><font color='red'>Error: Can not open directory: $dir</font></p>";
    }
    return $a;
}

function dir_is_empty($dir) {
    if ($dir == "") return 0;

    if (! $handle = opendir($dir)) {  // if dir does not exist, return true.
        return 1;
    }

    $is_empty = 1;
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") { 
            $is_empty = 0;
            break;
        }
        //echo $file . "<br/>";
    }
    closedir($handle);

    return $is_empty;
}


function dir_create($dir) {
    if ($dir == "") return "";

    if ( file_exists( $dir ) ) {
        $s = "directory already exist: $dir";
    }
    else {
        mkdir($dir, 0755, true); // use "true" so it does so recursively.
        $s = "Successfully created new directory: $dir";
    }
    //print $s;
    return $s;
}


function dir_rm($dir) {
    if ($dir == "") return "";

    if (! file_exists($dir)) {
        $s = "Cannot delete non-existent folder: $dir";
    } else if (! dir_is_empty($dir)) {
        $s = "Cannot delete non-empty folder: $dir";
    } else {
        rmdir($dir);
        $s = "Successfully deleted folder: $dir";
    }

    return $s;
}


//
// Create file $filename under $dir.
//
function createFile($dir, $filename, $contents="") {
    if (! is_dir($dir)) return;

    $filename = "$dir/$filename";
    if ( file_exists($filename) ) { return; }
    $fp = fopen("$filename", "w") or die("Unable to open file!");
    if ($contents != "") { fwrite($fp, $contents); }
    fclose($fp);
}


function file_delete($file) {
    $s = "";
    $filename = basename($file);
    try {
        if (! file_exists($file)) {
            //$s = "File $filename does not exist.";
        }
        unlink($file);
        //$s = "File $filename has been successfully deleted.";
    } catch (Exception $e) {
        $s = $e->getMessage();
    }

    return $s;
}


/**
 * Recursively delete a directory
 *
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 *
 * From:
 * http://stackoverflow.com/questions/1334398/how-to-delete-a-folder-with-contents-using-php
 */
function unlinkRecursive($dir, $deleteRootToo)
{
    if (! is_dir($dir)) { return; }

    if(!$dh = @opendir($dir))
    {
        return;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        {
            continue;
        }

        if (!@unlink($dir . '/' . $obj))
        {
            unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);

    if ($deleteRootToo)
    {
        @rmdir($dir);
    }

    return;
}


/*
function getFilenameFromPath($path) {
    //echo $filename;
    $path_parts = pathinfo($path);
    $filename  = $path_parts['basename'];
    return $filename;
}
*/

/*
//
// http://www.bitrepository.com/how-to-validate-an-image-upload.html
//
function is_image($filename, $tmpname) {
    if (! is_image_ext($filename) ) return 0;

    $mimes = array('image/gif','image/jpeg','image/pjpeg','image/png');

    $mime = getimagesize($tmpname);
    //print $filename; print_r($mime);
    $mime = $mime['mime'];

    //echo "[$ext] mine: $mime<br/>";
    return in_array($mime, $mimes);
}
*/


function is_image_ext($filename) {
    $extensions = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'tiff');
    $ext = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
    return in_array($ext, $extensions);
}


?>

