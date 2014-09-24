<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/util.php");

$page_name = "Generate Registration Code";
$page_title = "Admin - Generate Registration Code";

$div_main_id = "main_full_width";
include("../theme/header.php"); 
?>
<table width="100%"><tr><td>

<P><a href="./">Admin</a> &gt; <?php echo $page_name; ?></P>

<h3><?php echo $page_name; ?></h3>

<p>
Choose the owner user and number of registration code to generated. 
Check "Insert" box to actually insert to the registration code table, otherwise only list the queries.
</p>

<form method="POST">

<p>
<?php 
$a = array(1, 2, 3, 4, 5, 10, 20, 50, 100);
$s = genCountList("count", "count", $a, $a); 
$u = getUserList();

print "Generate $s registration code(s) for user $u";

$user_id = U_POST('user');
$count = U_POST('count');
$doInsert = U_POST('DoInsert') != "" ? 1 : 0;
?>
<span title="Check this box to insert">
<input type="checkbox" name="DoInsert" value="Y" <?php if ($doInsert) { echo "checked"; } ?>>Insert
</span>
<input type="submit" value="Submit" id="btnSubmit" name="btnSubmit">
</p>

<?php 
if (isset( $_POST['btnSubmit'] ) && $user_id != '' && $count != '') {
    genRegCode($user_id, $count, $doInsert);
}
?>

</form>

</td></tr></table>
<?php include("../theme/footer.php"); ?>


<?php

//
// Functions.
//


//
// Note: In theory it's possible to have duplicates, and that's not favored.
// In practice this chance is close to zero. 
// It's using type 1 random string: 0-9a-zA-Z, and length is 20, 
// then chance is (1/(26+26+10))^20 = (1/62)^20
//
function genRegCode($user_id, $count, $doInsert) {
    global $_REG_CODE_LEN;
    $date =  date('Y-m-d H:i:s', time());

    db_open(); 

    $i = 0;
    while ( $i < $count ) {
        $s = getRandStr($_REG_CODE_LEN, 1);

        // Check to make sure this code does not exist.
        $query = "SELECT code FROM code_register WHERE code = " . db_encode($s);
        $ct = executeRowCount($query);
        //print "[ct = $ct] ";
        if ($ct > 0) {
            print "A generated code already exists previously. This rarely happens. Ignore: $s.<br>"; 
            continue; 
        }

        // else, this code does not exist. Continue to use it.
        ++ $i;
        $query = "INSERT INTO code_register " 
                 . "(code, owner_user_id, create_date, is_used, use_user_id, use_date) VALUES (" 
                 . db_encode($s) . ", " 
                 . db_encode($user_id) . ", "
                 . db_encode($date) . ", "
                 . "0" . ", "
                 . db_encode("") . ", "
                 . db_encode("")
                 . ")";
        if ($doInsert) {
            executeNonQuery($query);
            print "INSERT: ";
        }
        print "$query<br>";
    }

    db_close(); 
}

function getUserList() {
    $logins = array();
    $IDs = array();

    $query = "SELECT login, ID FROM User order by ID ASC";

    global $link;
    db_open();
    $result = mysql_query($query, $link);
    if (! $result) {
        doExit('Invalid query: ' . mysql_error()); // for security, do not show mysql_error.
    }
    else {
        //
        // 2nd param: MYSQL_BOTH, MYSQL_ASSOC, MYSQL_NUM.
        // See: http://php.net/manual/en/function.mysql-fetch-array.php
        //
        while ($info = mysql_fetch_array($result, MYSQL_ASSOC)) {
            array_push($logins, $info['login'] . " (ID=" . $info['ID'] . ")");            
            array_push($IDs, $info['ID']);
        }
    }
    db_close();

    return genCountList("user", "user", $logins, $IDs);
}

function genCountList($id, $name, $items, $values) {
    $s = "<option value=\"\">-- Select One --</option>";
    $len = count($items);
    for ($i = 0; $i < $len; ++ $i) {
        $selected = (U_REQUEST($name) == $values[$i]) ? "selected" : "";
        //print "" . U_REQUEST($name) . " ?= " . $values[$i] . "<br>";
        $s .= "<option value='$values[$i]' $selected>$items[$i]</option>";
    }

    $s = "<select id=\"$id\" name=\"$name\">$s</select>";
    return $s;
}

?>
