<?php
//
// Site range settings.
//

if (isset($root_path)) { // for top level files. e.g., /index.php
    require_once("$root_path/conf/conf.php");
}
else { 
    require_once("../conf/conf.php");
}

?>
