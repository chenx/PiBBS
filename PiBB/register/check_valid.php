<?php

//
// Used by ajax call in register_c/en.php to find out if a field is valid.
//
// @By: Xin Chen
// @Created on: 7/25/2013
// @Last modified: 7/25/2013
//

include_once("../func/db.php");

$field = isset($_POST['f']) ? $_POST['f'] : "";
$value = isset($_POST['v']) ? $_POST['v'] : "";

if ($field != "" && $value != "") {
    print check_valid($field, $value);
}

//
// Check if a field value is valid.
// Note for reg_code, it's valid when it exists.
// But for login/email, it's valid when it does not exist.
//
function check_valid($field, $value) {
    if ($field == "login") {
        $query = "SELECT login FROM User where login = " . db_encode($value);  
    } else if ($field == "email") {
        $query = "SELECT email FROM User where email = " . db_encode($value);
    } else if ($field == "reg_code") {
        $query = "SELECT code FROM code_register WHERE is_used = 0 AND code = " . db_encode($value);
    } else {
        return 0;
    }

    //echo $query . "<br>";
    db_open();
    $ct = executeRowCount($query);
    db_close();

    if ($field == "reg_code") return $ct == 1 ? 1 : 0;
    else return $ct == 1 ? 0 : 1;
}

?>
