<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_admin.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable.php");

$page_title = "Admin - Manage Forum Table";

$custom_header = <<<EOF
<script type="text/javascript" src="../func/ClsPage.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> $(document).ready(function() { $(".sortable").tablesorter(); } ); </script>
EOF;

db_open();

$tbl_uc1 = "";
$div_main_id = "main_full_width";

$msg = "";
if (U_REQUEST('btnAction') == 'add') {
  $msg = add_bbs_table();
}
else if (U_REQUEST('btnAction') == 'delete' && isset($_REQUEST['deleteTbl'])) {
  $msg = del_bbs_table();
}

include("../theme/header.php");

$star = "<font color='red'>*</font>";
?>
<table width="100%"><tr><td>

<P><a href="./">Admin</a> &gt; Manage BBS Table <?php echo $tbl_uc1; ?></P>

<script type="text/javascript">

function tbl_del(id) {
  var r = confirm("Are you sure to PERMANENTLY delete table " + id + "?");
  if (r) {
    document.getElementById('btnAction').value = 'delete';
    document.getElementById('deleteTbl').value = id;
    document.forms[0].submit();
  }
}

function validate_tbl_add() {
  var e = '';
  var eo;
  var o = document.getElementById('txtTitleE');
  if (o.value == '') {
    e = 'Title (English) cannot be empty\n';
    eo = o;
  }

  o = document.getElementById('txtTitle');
  if (o.value == '') {
    e = 'Title cannot be empty\n';
    eo = o;
  }

  o = document.getElementById('txtGroupID');
  if (o.value == '') {
    e = 'GroupID cannot be empty\n';
    eo = o;
  }

  o = document.getElementById('txtTableName');
  if (o.value == '') {
    e = 'Table name cannot be empty\n';
    eo = o;
  }

  if (e != '') {
    alert(e);
    eo.focus();
    return 0;
  }

  return 1;
}

function tbl_add() {
  if (! validate_tbl_add()) return;

  document.getElementById('btnAction').value = 'add';
  document.forms[0].submit();
}

</script>

<?php
if ($msg != "") { 
    $color = (strpos($msg, "success") > 0) ? "green" : "red"; 
    print "<p><font color=\"$color\">$msg</font>. <a href=\"" . $_SERVER['PHP_SELF'] . "\">OK</a>.</p>"; 
}
?>

<form method="POST">
<input type='hidden' id='btnAction' name='btnAction' value=''/>
<input type='hidden' id='deleteTbl' name='deleteTbl' value=''/>

<h3>New BBS Table:</h3> 
<table style="background-color: #99ff99; padding:2px;">
<tr><td>Name:<?php print $star; ?></td>
<td>BBS_F_<input type='text' id='txtTableName' name='txtTableName' size="12" value='<?php echo U_REQUEST("txtTableName"); ?>'></td></tr>
<tr><td>ID: </td><td><input type='text' name='txtID' value='<?php echo U_REQUEST('txtID'); ?>'></td></tr>
<tr><td>Group:<?php print $star; ?> </td><td>
<?php 
print getDropdownListFromDb("SELECT ID, name FROM BBS_BoardGroups ORDER BY ID ASC", 
		    "txtGroupID", "txtGroupID", "", U_REQUEST('txtGroupID')); ?></td></tr>
<tr><td>Title:<?php print $star; ?> </td>
<td><input type='text' id='txtTitle' name='txtTitle' value='<?php echo U_REQUEST('txtTitle'); ?>'></td></tr>
<tr><td>Title (English):<?php print $star; ?> </td>
<td><input type='text' id='txtTitleE' name='txtTitleE' value='<?php echo U_REQUEST('txtTitleE'); ?>'></td></tr>
<tr><td>Managers: </td>
<td><input type='text' name='txtManagers' value='<?php echo U_REQUEST('txtManagers'); ?>'>
Format: user_id,user_name,role[|user_id,user_name,role]*
</td></tr>
</table>
<input type='button' id='btnAdd' name='btnAdd' value='Add BBS Table' onclick='javascript: tbl_add();'/>
</form>

<?php

show_bbs_table_list();

print "<br/><br/><h3>Relevant Tables:</h3>";
show_tbl("BBS_BoardList");
show_tbl("BBS_BoardManager");
show_tbl("BBS_BoardGroups");

db_close();

print "</td></tr></table>";
include("../theme/footer.php");

//
// Functions.
//

function show_tbl($tbl) {
    //print "<hr>";
    $doManage = 0;
    $cls_tbl = new Cls_DBTable($tbl, "ID");
    $sql = "SELECT * FROM $tbl ORDER BY ID ASC";

    print $cls_tbl->getDBTableAsHtmlTable($sql, "List Of Table $tbl", $doManage);
    print "[<a href=\"admin_list.php?tbl=$tbl\">Edit Table $tbl</a>]<br/><br/>";
}


function show_bbs_table_list() {
    global $db;
    $sql = <<<EOF
SELECT B.ID, G.name AS Grp, T.table_name AS NAME, B.title_en, B.title, B.thread_count 
FROM information_schema.tables T LEFT OUTER JOIN BBS_BoardList B 
ON T.table_name = B.name 
JOIN BBS_BoardGroups G ON B.GroupID = G.ID
WHERE T.table_schema = '$db' AND T.table_name LIKE 'BBS_F_%'
EOF;
    //$sql = "SELECT ID, name, title, thread_count FROM BBS_BoardList";
    //print $sql;
    $tbls = executeDataTable($sql);
    $ct = count($tbls);
    $s = "<tr><th>Seq</th><th>ID</th><th>Group</th><th>Name</th><th>Title (En)</th><th>Title</th><th>Thread_count</th><th>Delete</th></tr>";
    for ($i = 1; $i < $ct; ++ $i) {
        $disabled = (0 == $tbls[$i][5]) ? "" : " disabled title='Cannot delete since this table is not empty.'";
        $s .= "<tr><td>$i</td><td>" . $tbls[$i][0] . "</td><td>" . 
              $tbls[$i][1] . "</td><td>" . $tbls[$i][2] . "</td><td>" . $tbls[$i][3] . "</td>" .
              "<td>" . $tbls[$i][4] . "</td><td>" . $tbls[$i][5] . "</td>" .
              "<td><input type='button' value='Delete BBS Table' onClick='javascript:tbl_del(\"" . 
              $tbls[$i][2] . "\");'$disabled></td></tr>" ;
    }
    if ($s != "") {
        $s = <<<EOF
<h3>Existing BBS Tables:</h3>
<p>Only empty tables can be deleted. 
To update an existing table's attributes, use <a href="admin_list.php?tbl=BBS_BoardList">this link</a>.</p>
<table border='1' cellpadding='2' style='border:1px solid; background-color: #FADA81; text-align:center;' class='sortable'>$s</table>
EOF;
    }
    print $s;
}


//
// well, it seems TRANSACTION does not work here.
// if an intermediate step goes wrong, it does not roll back prevous changes.
//
function del_bbs_table() {
    $tbl_name = $_REQUEST['deleteTbl'];
    if ( ! isset($tbl_name) || $tbl_name == "") {
        return "Nothing to delete";
    }
    //print "delete BBS table: $tbl_name<br>";

    if (! db_table_exists($tbl_name)) {
        return "Table [$tbl_name] does not exist.";
    }

    $msg = "";
    try {
        executeNonQuery("START TRANSACTION");

        $sql = "SELECT ID FROM BBS_BoardList WHERE name = " . db_encode($tbl_name);
        $forum_id = executeScalar($sql);

        $sql = "DELETE FROM BBS_BoardManager WHERE forum_id = " . db_encode($forum_id);
        executeNonQuery($sql);

        $sql = "DELETE FROM BBS_BoardList WHERE name = '$tbl_name'";
        executeNonQuery($sql);

        $sql = "DROP TABLE $tbl_name";
        executeNonQuery($sql);

        executeNonQuery("COMMIT");
        $msg = "Table [$tbl_name] has been successfully deleted.";
    } catch (Exception $e) {
        executeNonQuery("ROLLBACK");
        $msg = "Error when delete [$tbl_name]. Exception: " . $e->getMessage();
    }
    return $msg;
}


function add_bbs_table() {
    $tbl_name = $_REQUEST['txtTableName'];
    if ( ! isset($tbl_name) || $tbl_name == "") {
        return "Please enter table name";
    }
    $tbl_name = "BBS_F_" . strtoupper( $tbl_name );
    //print "create BBS table: $tbl_name<br>";

    if (db_table_exists($tbl_name)) {
        return "Table [$tbl_name] already exists. Please use a different table name.";
    }

    try {
        executeNonQuery("START TRANSACTION");

        $sql = "CALL create_forum_table('$tbl_name')";
        executeNonQuery($sql);

        if (! db_table_exists($tbl_name)) {
            throw new Exception("Table [$tbl_name] was not created.");      
        }

        addForumToBBSBoardList($tbl_name);
        addForumManager($tbl_name); 

        executeNonQuery("COMMIT");
        $msg = "Table [$tbl_name] has been successfully created.";
    } catch (Exception $e) {
        executeNonQuery("ROLLBACK");
        $msg = "Error when create [$tbl_name]: " . $e->getMessage();
    }
    return $msg;
}


function addForumToBBSBoardList($tbl_name) {
    $ID = db_encode( U_REQUEST('txtID') );
    $groupID = db_encode( U_REQUEST('txtGroupID') );
    $tbl = db_encode( $tbl_name );
    $title = db_encode( U_REQUEST('txtTitle') );
    $titleE = db_encode( U_REQUEST('txtTitleE') );
    $managers = db_encode( U_REQUEST('txtManagers') );
    if ($ID == "") {
        $sql = <<<EOF
INSERT INTO BBS_BoardList (GroupID, name, title, title_en, managers)
VALUES ($groupID, $tbl, $title, $titleE, $managers)
EOF;
    } else {
        $sql = <<<EOF
INSERT INTO BBS_BoardList (ID, GroupID, name, title, title_en, managers)
VALUES ($ID, $groupID, $tbl, $title, $titleE, $managers)
EOF;
    }
    //print $sql;
    executeNonQuery($sql);
}


function addForumManager($tbl_name) {
    $managers = U_REQUEST('txtManagers');
    if ($managers == "") return;

    // Get forum_id.
    $sql = "SELECT ID FROM BBS_BoardList WHERE name = " . db_encode($tbl_name);
    $forum_id = executeScalar($sql);
    if ($forum_id == "") {
        throw new Exception("Forum [$tbl_name] is not found.");
    }
    $forum_id = db_encode( $forum_id );

    $mgrs = explode("|", $managers);
    $len = count($mgrs);

    for ($i = 0; $i < $len; ++ $i) {
        $fields = explode(",", $mgrs[$i]);
        if (count($fields) != 3) {
            throw new Exception("addForumManager() exception: format: user_id,user_name,role. Is: " . $mgrs[$i]);
        }
        $user_id = db_encode($fields[0]);
        $user_name = db_encode($fields[1]);
        $role = db_encode($fields[2]);
        $start_date = db_encode( date("Y-m-d H:i:s") );
        $sql = <<<EOF
INSERT INTO BBS_BoardManager (forum_id, user_id, user_name, role, start_date) 
VALUES ($forum_id, $user_id, $user_name, $role, $start_date)
EOF;
        //print $sql;
        executeNonQuery($sql);
    }
}


function db_table_exists($tbl) {
    global $db;
    $sql = "SELECT COUNT(*) FROM information_schema.tables where table_schema = '$db' AND table_name = '$tbl'";
    $ct = executeScalar($sql);
    //print "ct = $ct<br>";
    return $ct == 1;
}

?>


