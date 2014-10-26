<?php
//
// Same as db.sql, but uses mysqli connection instead of mysql connection. 
//
// @Author: Tom Chen
// @Created on: 10/25/2014
// @Last modified: 10/25/2014
//
// References:
// [1] http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
// [2] http://codular.com/php-mysqli
// [3] http://php.net/manual/en/mysqli-result.fetch-array.php
// [4] http://php.net/manual/en/mysqli-result.fetch-fields.php
//

$__FUNC_DB_MYSQLI__ = 1; // Use this to decide if db.php has been included.

if (isset($root_path)) {
    include_once("$root_path/func/setting.php");
} else {
    include_once("../func/setting.php");
}

include_once("$_DB_CONF_PATH");


/**
 * Object oriented way to open database (recommended way).
 */
function db_open() {
    global $host, $db_usr, $db_pwd, $db, $link;
    $link = new mysqli($host, $db_usr, $db_pwd, $db);
 
    // check connection
    if ($link->connect_error) {
        //trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
        doExit('Database connection failed: ' . $link->connect_error);
    }
}


/**
 * Procedural way to open database (not recommended).
 * Disable this function and not use it.
 */
/*
function db_open2() {
    global $host, $db_usr, $db_pwd, $db, $link;
    $link = mysqli_connect($host, $db_usr, $db_pwd, $db);
 
    // check connection
    if (mysqli_connect_errno()) {
        //trigger_error('Database connection failed: '  . mysqli_connect_error(), E_USER_ERROR);
        doExit('Database connection failed: ' . mysqli_connect_error());
    }
}
*/


/**
 * Close database connection. Used for both OO and procedural ways.
 */
function db_close() {
    //print "<br/>Enter db_close() ...<br/>";
    global $link;
    $link->close();
    $link = '';
    //print "<br/>db_close(): db closed<br/>";
}


/**
 * Return a single element in the first row of the returned set.
 * If $col is empty, use the first element; else, use the named element.
 */
function getScalar($query, $col = '') {
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();
    //echo $query;

    $rs = $link->query($query);
 
    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        // if has more than 1 row, use the first row.
        if ($rs->num_rows == 0) { $v = ""; }
        else {
            $rs->data_seek(0);
            $row = $rs->fetch_row();
            $v = ($col == '') ? $row[0] : $row[$col];
        }
    }

    if (! $db_is_open) db_close();

    return $v;
}


function executeScalar($query, $col = '') {
    return getScalar($query, $col);
}


/**
 * Execute a query that does not return any data.
 */
function executeNonQuery($query) {
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();

    $rs = $link->query($query);
        
    if ($rs === false) {
        //doExit('Invalid query: ' . mysql_error());
        throw new Exception( 'Invalid query: ' . $link->error);
    }

    if (! $db_is_open) db_close();

    return $result;
}


/**
 * Return number of rows in the returned data set.
 */
function executeRowCount($query) {
    global $link;
    $rs = $link->query($query);

    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        return $rs->num_rows;
    }
}


/**
 * Exit execution of PHP.
 */
function doExit($msg) {
    die($msg . ". Please contact your system administrator.");
    exit();
}


/**
 * Return an array of the given field in the query.
 */
function executeScalarArray($query, $col) {
    $a = array();
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();

    $rs = $link->query($query);

    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        $rs->data_seek(0);
        while ($row = $rs->fetch_assoc()) {
            array_push($a, $row[$col]);
        }
        $rs->free(); // optional statement.
    }

    if (! $db_is_open) db_close();

    return $a;
}


/**
 * Return entire table (requested in query) as a DataTable.
 * First row is for column names. 
 * The rest rows are data.
 */
function executeDataTable($query) {
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();

    $ret = array();
    $rs = $link->query($query);

    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        $rs->data_seek(0);

        // First get column names.
        $fields = $rs->fetch_fields();
        $header = array();
        foreach ($fields as $val) {
            array_push($header, $val->name);
        }
        array_push($ret, $header);

        // Then get data.
        array_push($ret, $rs->fetch_array(MYSQLI_NUM));

        $rs->free(); 
    }

    if (! $db_is_open) db_close();
    return $ret;
}


/**
 * Return entire table (requested in query) as an associate array. 
 * Compared to executeDataTable, this shifts the processing to calling function.
 *
 * Synopsis:
 *    $t = executeAssociateDataTable_2($sql);
 *    $len = count($t);
 *    for ($i = 0; $i < $len; ++ $i) {
 *        $row = $t[$i];
 *        foreach ($row as $key => $val) {
 *            print "$key => $val, or value is: $row[$key]<br/>";
 *        }
 *    }
 */
function executeAssociateDataTable($query) {
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();

    $ret = array();
    $rs = $link->query($query);

    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        $rs->data_seek(0);
        array_push($ret, $rs->fetch_array(MYSQLI_BOTH));
        $rs->free();
    }

    if (! $db_is_open) db_close();
    return $ret;
}

/**
 * First row is for header columns, other rows are for data.
 */
function executeAssociateDataTable_2($query) {
    global $link;
    $db_is_open = ($link != '');
    if (! $db_is_open) db_open();

    $ret = array();
    $rs = $link->query($query);

    if ($rs === false) {
        //trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        doExit('Invalid query: ' . $link->error);
    } else {
        $rs->data_seek(0);

        // First get column names.
        $fields = $rs->fetch_fields();
        $header = array();
        foreach ($fields as $val) {
            $header[$val->name] = $val->name;
        }
        array_push($ret, $header);

        // Then get data.
        array_push($ret, $rs->fetch_array(MYSQLI_ASSOC));

        $rs->free();
    }

    if (! $db_is_open) db_close();
    return $ret;
}


//
// Given a query, return a html string, showing the return table.
// $query: value is string. the query string.
// $property: value is string. attribute of the table, e.g., class.
// $show_count: value is boolean. whether to show number of row: 1,2, ... 
// $do_htmlencode: value is boolean. whether apply db_htmlencode to contents.
//
function executeDataTable_ToHtmlTable($query, $property="", $show_count, $do_htmlencode) {
    $t = executeDataTable($query);
    $rows = count($t);
    if ($rows == 0) return "";

    $cols = count($t[0]);
    $s = "";

    // Show title row.
    $s_row = "";
    if ($show_count) { $s_row = "<td><br/></td>"; }
    for ($j = 0; $j < $cols; ++ $j) {
        $s_row .= "<td>" . ucfirst( db_htmlencode($t[0][$j]) ). "</td>";
    }
    $s .= "<tr>$s_row</tr>";

    $s_row = "";
    for ($i = 1; $i < $rows; ++ $i) {
        $s_row = "";
        if ($show_count) { $s_row = "<td>$i</td>"; }
        for ($j = 0; $j < $cols; ++ $j) {
            $content = $t[$i][$j];
            if ($do_htmlencode) { db_htmlencode($content); }
            $s_row .= "<td>$content</td>";
        }
        $s .= "<tr>$s_row</tr>";
    }

    $s = "<table$property>$s</table>";
    return $s;
}


//
// To display html/xml tag symbols from database in browsers.
//
function db_htmlEncode($s) {
    // Converts the smallest set of entities possible to generate valid html.
    // htmlentities(input, [quote_style, [charset]]); // Programming PHP, p83.

    //return htmlspecialchars($s); // replaced with the following.

    // Use this, so when UTF-8 chars exists, return won't be empty.
    return htmlspecialchars($s, ENT_COMPAT, 'ISO-8859-1', true);

    // Converts all chars with HTML entity equivalents into those equivalents.
    // htmlentities(input, [quote_style, [charset]]);
    //return htmlentities($s, ENT_QUOTES);

    //same as below. 
    //$s = str_replace("&", "&amp;", $s);
    //$s = str_replace("<", "&lt;", $s);
    //$s = str_replace(">", "&gt;", $s);
    //$s = str_replace("\"", "&quot;", $s);
    //return $s;
}

//
// To insert/update value to database.
//
function db_encode($s) {
    $s = trim($s);
    if ($s == "") {
        $s = "NULL";
    }
    else {
        //$s = addslashes($s); // This does not work.
        $s = str_replace("\\", "\\\\", $s);
        $s = str_replace("'", "''", $s);
        $s = "'$s'";
    }
    return $s;
}


//
// Global setting for valid site username and password.
//
function IsValidUsername($s) {
    return ctype_alnum($s);
}

function IsValidPassword($s) {
    return ctype_alnum($s);
}

//
// Password rules:
// - contains only alphabeta and numeric letters
// - length >= 8
// - contains at least 1 of upper case, lower case, and number characters.
//
function validate_password_rule($v, $v2="", $use_passwd2=0) {
    $e = "";
    if ($v == "") { $e = 1; }
    else if ($use_passwd2 && ( strlen($v) != strlen($v2) )) { $e = 2; }
    else if (! ctype_alnum($v) ) { $e = 3; }
    else if (strlen($v) < 8) { $e = 4; }

    global $_LANG;
    if ($_LANG == "cn") {
        if ($e == 1) $e = "不能为空";
        else if ($e == 2) $e = "两个密码不相同";
        else if ($e == 3) $e = "必须是字母或数字";
        else if ($e == 4) $e = "长度应大于等于8";
        else $e = "";
    }
    else { // en
        if ($e == 1) $e = "Cannot be empty";
        else if ($e == 2) $e = "Two passwords not equal";
        else if ($e == 3) $e = "Should be alphanumeric";
        else if ($e == 4) $e = "Length should >= 8";
        else $e = "";
    }

    return $e;
}

function validate_login($v) {
    $e = "";
    if ($v == "") { $e = 1; }
    else if (! preg_match("/^[a-zA-Z]/", $v) ) { $e = 2; }
    else if (! ctype_alnum($v) ) { $e = 3; }
    else if (strlen($v) < 4) { $e = 4; }

    global $_LANG;
    if ($_LANG == "cn") {
        if ($e == 1) $e = "不能为空";
        else if ($e == 2) $e = "应以字母开头";
        else if ($e == 3) $e = "必须是字母或数字";
        else if ($e == 4) $e = "长度应大于等于4";
        else $e = "";
    }
    else { // en
        if ($e == 1) $e = "Cannot be empty";
        else if ($e == 2) $e = "Should start with a letter";
        else if ($e == 3) $e = "Should be alphanumeric";
        else if ($e == 4) $e = "Length should >= 4";
        else $e = "";
    }

    return $e;
}

//
// Log site event, when the ID session exists.
//
// @params:
//  - ID: user ID, must be an integer.
//    Note: ctype_digit(v) - check if all chars are number, v should be of string type.
      See:  http://www.php.net/manual/en/function.ctype-digit.php
//  - action: action name.
//  - note: so far, is used to record the captcha the user typed in.
//  - link: if a database connection is already open, use it; otherwise open a connection.
//
function log_event($ID, $action, $note, $link = '') {
    if ( $ID == "" || ! ctype_digit("$ID") ) { return; }

    $date = date('Y-m-d H:i:s', time());
    $ip = $_SERVER['REMOTE_ADDR'];

    $query = "INSERT INTO log_site (user_id, action, ip, note, timestamp) VALUES ("
             . db_encode( $ID ) . ", "
             . db_encode( $action ) . ", "
             . db_encode( $ip ) . ", "
             . db_encode( $note ) . ", "
             . db_encode( $date ). ")";

    executeNonQuery($query);
}


function log_oj_event($user_id, $question_id, $lang, $mode, $score, $action, $tmp_dir, &$message, &$code) { 
    if ( $user_id == "" || ! ctype_digit("$user_id") ) { return; }

    $date = date('Y-m-d H:i:s', time());
    $ip = $_SERVER['REMOTE_ADDR'];

    $query = "INSERT INTO log_oj (user_id, question_id, lang, mode, score, action, tmp_dir, ip, message, code, timestamp) VALUES ("
             . db_encode( $user_id ) . ", "
             . db_encode( $question_id ) . ", "
             . db_encode( $lang ) . ", "
             . db_encode( $mode ) . ", "
             . db_encode( $score ) . ", "
             . db_encode( $action ) . ", "
             . db_encode( $tmp_dir ) . ", "
             . db_encode( $ip ) . ", "
             . db_encode( $message ) . ", "
             . db_encode( $code ) . ", "
             . db_encode( $date ). ")";

    executeNonQuery($query);
}


//
// Create a dropdown list from given query.
// @parameters:
//  - query         The query.
//  - id            The html ID property.
//  - name          The html name property.
//  - empty_value   The value of the "-- SELECT --" item.
//  - default_value The current value of the list.
//
function getDropdownListFromDb($query, $id, $name, $empty_value, $default_val, $property="") {
    $t = executeDataTable($query);
    $len = count($t);

    $s = "<OPTION value=\"$empty_value\">-- SELECT --</OPTION>";
    // note: first row is for column names.
    for ($i = 1; $i < $len; ++ $i) {
         $selected = (strcmp($default_val, $t[$i][0]) == 0) ? " SELECTED" : "";
        $s .= "<OPTION value=\"" . $t[$i][0] . "\"$selected>" . $t[$i][1] . "</OPTION>";
    }

    $s = "<SELECT id=\"$id\" name=\"$name\"$property>$s</SELECT>";
    return $s;
}


?>
