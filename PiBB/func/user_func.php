<?php
// 
// User functions.
//

if (isset($root_path)) { 
    include_once("$root_path/func/db.php");
} else {
    include_once("../func/db.php");
}

//
// Get unused registration codes.
//
function getRegCode($ID, &$ct) {
    $query = "SELECT code FROM code_register WHERE is_used = 0 AND owner_user_id = " . db_encode($ID);
    //echo "$query<br/>";

    db_open();
    $a = executeScalarArray($query, "code");
    db_close();

    $ct = count($a);
    //echo "ct = $ct<br>";
    
    $s = "";
    for ($i = 0; $i < $ct; ++ $i) {
        $s .= ($i + 1) . ". $a[$i]<br>";
    }

    return $s;
}

//
// Get number of unused registration codes.
//
function getRegCodeCount($ID) {
    $query = "SELECT count(code) as ct FROM code_register WHERE is_used = 0 AND owner_user_id = " . db_encode($ID);

    $a = executeScalar($query, "ct");

    return $a;
}

?>
