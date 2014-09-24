<?php
session_start();

require_once("../func/auth.php");
require_once("../func/auth_board_manager.php");
require_once("../func/db.php");
require_once("../func/Cls_DBTable.php");


$page_title = "Board Managment";
$forum_id = U_REQUEST_INT("forum_id");
//$is_private_forum = 0;
$frm_action = U_REQUEST("frmAction");
$msg = "";

//print $_SESSION['bbs_role'] . ", fid = $forum_id<br/>";
$pos = strpos($_SESSION['bbs_role'], "|$forum_id,");
if ($forum_id != "" && $pos === false) { // not a manager of this board.
    header("Location: ../bbs/");
}

if ($frm_action == "add_mem") {
    add_member($forum_id, U_REQUEST("frmActionValue") );
}
else if ($frm_action == "del_mem") {
    del_member($forum_id, U_REQUEST("frmActionValue") );
}

$div_main_id = "main_full_width";
$root_path = "..";
include_once("../theme/header.php");

?>

<div id="main_panel">
<div id="main">

<table class="board_management"><tr><td>
<center><h3>Board Management</h3></center>

<p class="desktop"><br/></p>


<p><b>1. My Boards</b></p>

<p>List of boards for which I am a manager.</p>

<?php
show_my_boards_as_table($_SESSION['ID']);
?>

<form method="POST">

<p><b>2. Manage Private Board Members</b></p>
<p>In a private board, only member users can post.</p>
<?php 
show_my_private_boards($_SESSION['ID']);

if ($forum_id != 0) {
    show_board_details($forum_id);
}
?>

<input type="hidden" id="frmAction" name="frmAction" value=""/>
<input type="hidden" id="frmActionValue" name="frmActionValue" value=""/>
</form>

</td></tr></table>
</div>
</div>

<script type="text/javascript">
function submit_board(v) {
    //alert(v);
    document.forms[0].submit(); 
}

function add_mem(o) {
    var n = document.getElementById('txtNewMem');
    if (n.value == '') {
        alert('Please enter a username');
        n.focus();
        return;
    }
    o.disabled = true;
    document.getElementById('frmAction').value = 'add_mem';
    document.getElementById('frmActionValue').value = n.value;
    document.forms[0].submit();
}

function del_mem(o, id, name) {
    var r = confirm('Are you sure to remove member: ' + name + ' from this private board?');
    if (r) {
        o.disabled = true;
        document.getElementById('frmAction').value = 'del_mem';
        document.getElementById('frmActionValue').value = id;
        document.forms[0].submit();
    }
}

function toggle_status(f, col, val) {
    //alert('forum: ' + f + ', col: ' + col + ', val = ' + val);
    $('#divMsgToggle').html('');

    var c = 0;
    if (col == 'private') { c = 1; }
    else if (col == 'hidden') { c = 2; }
    else if (col == 'readonly') { c = 3; } 

    var v = val ? 1 : 0;

    $.post("forum_mgr.php", { f: f, c: c, v: v }, function(data, status) {
        if (status == "success") {
            //alert(data);
            // data == 1: success, show color as green; else show color as pink.
            //var color = (data == 1) ? '#eeffee' : '#ffeeee';
            //$("#m" + action + "_" + id).css('background-color', color);
            //return data == 1; // if 1, valid, else not.
            $('#divMsgToggle').html("<br/><font color='#009900'>The value has been successfully updated.</font>");
            return 1;
        } else {
            //alert("cannot connect to server");
            //$("#m" + action + "_" + id).css('background-color', '#ffeeee');
            $('#divMsgToggle').html("<br/><font color='red'>Error: cannot connect to server.</font>");
            return 1;
        }
    }, 5);
}
</script>

<?php 
include_once("../theme/footer.php");  

function show_my_private_boards($user_id) {
    show_my_boards($user_id, 1);
}

//
// Show boards for which this user is board manager.
// 
// @parameters:
// - $user_id: User Id of the board manager
// - $show_private_only: If 1, show all boards of the manager; else, show private boards only.
//
function show_my_boards($user_id, $show_private_only = 0) {
    global $_LANG, $forum_id;
    $title = $_LANG == "en" ? "title_en" : "title";
    $uid = db_encode($user_id);
    $private_only = $show_private_only ? " AND L.private = 1" : "";

    $sql = <<<EOF
SELECT M.forum_id, L.title AS title 
FROM BBS_BoardManager M, BBS_BoardList L 
WHERE M.forum_id = L.ID AND M.user_id = $uid AND M.disabled = 0 $private_only
EOF;
    $property = " onchange='javascript: submit_board(this.value);'";
    $s = "Choose Private Board: " 
         . getDropdownListFromDb($sql, "forum_id", "forum_id", "", $forum_id, $property);
    print $s;
}


function show_my_boards_as_table($user_id) {
    global $_LANG;
    $title = $_LANG == "en" ? "title_en" : "title";
    $uid = db_encode($user_id);

    $sql = <<<EOF
SELECT L.ID, L.title AS Forum, L.private, L.hidden, L.readonly
FROM BBS_BoardManager M, BBS_BoardList L
WHERE M.forum_id = L.ID AND M.user_id = $uid AND M.disabled = 0 
EOF;
    $property = " class='private_board_userlist' border='1'";

    $t = executeDataTable($sql);
    $rows = count($t);
    if ($rows == 0) return "";

    $cols = count($t[0]);
    $s = "";

    // Show title row.
    $s_row = "<td><br/></td>"; 
    for ($j = 1; $j < $cols; ++ $j) {
        $s_row .= "<td>" . ucfirst( $t[0][$j] ). "</td>";
    }
    $s .= "<tr>$s_row</tr>";

    $s_row = "";
    for ($i = 1; $i < $rows; ++ $i) {
        $s_row = "<td>$i</td>"; 
        $s_row .= "<td><a href=\"../bbs/forum.php?f=" . $t[$i][0] . "\">" . $t[$i][1] . "</a></td>";
        for ($j = 2; $j < $cols; ++ $j) {
            $content = writeCheckbox( $t[$i][0], $t[0][$j], $t[$i][$j] );
            $s_row .= "<td>$content</td>";
        }
        $s .= "<tr>$s_row</tr>";
    }

    $s = "<table$property>$s</table><div id=\"divMsgToggle\"></div>";
    print $s;

    showNotes();
}


function showNotes() {
    $s = <<<EOF
<p>Notes:
<ul>
<li>Only board members can post in a Private board.</li>
<li>A Hidden board can be seen only by logged in board members.</li>
<li>No one can post in a Readonly board.</li>
</ul>
</p>
EOF;
    print $s;
}


function writeCheckbox($forum_id, $field, $val) {
    $checked = $val ? " checked" : "";
    $s = <<<EOF
<input type='checkbox' id='' name='' value='Y' $checked 
       onchange="javascript:toggle_status($forum_id, '$field', this.checked);"/>
EOF;
    return $s;
}


function show_board_details($forum_id) {
    global $_LANG;
    $title = $_LANG == "en" ? "title_en" : "title";
    $sql = "SELECT $title FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    $forum_name = executeScalar($sql);
    $is_private_forum = is_private_board($forum_id);
    $private = $is_private_forum ? " [Private]" : "";

    //print "<p>Forum: $forum_name$private</p>";

    //show_board_attributes($forum_id);

    //global $is_private_forum;
    if ($is_private_forum) {
        show_private_board_members($forum_id, $forum_name);
    }
}


function is_private_board($forum_id) {
    $sql = "SELECT private FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    $is_private = executeScalar($sql);
    return $is_private;
}


/*
function show_board_attributes($forum_id) {
    $sql = "SELECT readonly, private, hidden FROM BBS_BoardList WHERE ID = " . db_encode($forum_id);
    $t = executeAssociateDataTable_2($sql);
    $len = count($t);
    if ($len > 0) {
        global $is_private_forum;
        $is_private_forum = $t[1]['private']; 
        //print "is private: " . $t[1]['private'] . "<br/>";

        print "Readonly: " . $t[1]['readonly'] . "<br/>";
        print "Private: " . $t[1]['private'] . "<br/>";
        print "Hidden: " . $t[1]['hidden'] . "<br/>";
    }
}
*/

function show_private_board_members($forum_id, $forum_name) {
    $fid = db_encode($forum_id);
    $sql = <<<EOF
SELECT M.user_id, U.login AS `Username`, 
       CONCAT_WS(' ', U.first_name, U.last_name) AS `Realname` 
FROM BBS_PrivateMembership M, User U 
WHERE M.user_id = U.ID AND M.FORUm_id = $fid
EOF;
    $t = executeDataTable($sql);

    $s = "";
    $len = count($t);
    if ($len > 0) {
        $row = $t[0];
        $cols = count($row);

        $s_row = "<td><br/></td>";
        for ($j = 1; $j < $cols; ++ $j) {
            $s_row .= "<td>" . db_htmlencode($row[$j]) . "</td>";
        }
        $s .= "<tr>$s_row<td>Remove</td></tr>";

        for ($i = 1; $i < $len; ++ $i) {
            $row = $t[$i];
            $s_row = "<td>$i</td>";
            $s_row .= "<td><a href=\"../users/user.php?u=$row[1]\">" . $row[1] . "</a></td>";
            for ($j = 2; $j < $cols; ++ $j) {
                $s_row .= "<td>" . db_htmlencode($row[$j]) . "</td>";
            }

            $name = "$row[1] ($row[2])";
            $del = <<<EOF
<td><input type="button" value="Remove" 
onclick="javascript:del_mem(this, $row[0], '$name');"></td>
EOF;
            $s .= "<tr>$s_row$del</tr>";
        }
    }

    if ($s == "") { 
        $empty = "(none)"; 
    }
    else { 
        $empty = "";
        $s = "</p><table class='private_board_userlist' border='1'>$s</table>"; 
    }

    $val = U_REQUEST("txtNewMem");
    $add_new = <<<EOF
Username: <input type="text" id="txtNewMem" name="txtNewMem" value="$val"/>
<input type="button" name="btnSubmitNewMem" value="Add New Member" onclick="javascript:add_mem(this);"/>
EOF;
    $err = writeError();

    global $_USE_IMAIL, $_USE_EMAIL;
    $send_imail = $_USE_IMAIL ? 
        " [<a href='../imail/compose.php?f=$forum_id' title='Send Internal Mail'>I-Mail</a>]" : "";
    $send_email = $_USE_EMAIL ?
        " [<a href='../imail/compose.php?f=$forum_id' title='Send External Mail'>E-Mail</a>]" : "";

    print "<p>Forum: $forum_name [Private]</p>";
    print "Send mail to all members: $send_imail $send_email";
    print "<p>Members in this Forum: $empty</p>$add_new $err $s";
}


function add_member($forum_id, $login) {
    global $link, $msg;

    try {
        $sql = "SELECT ID FROM User WHERE login = " . db_encode($login);
        $user_id = executeScalar($sql);
        if ($user_id == "") {
            throw new Exception("Username '$login' does not exist.");
        }

        $sql = "SELECT COUNT(*) AS ct FROM BBS_PrivateMembership WHERE forum_id = "
               . db_encode($forum_id)
               . " AND user_id = " . db_encode($user_id);
        $ct = executeScalar($sql);
        if ($ct > 0) {
            throw new Exception("Username '$login' already exists in this private board.");
        }

        $sql = "INSERT INTO BBS_PrivateMembership (forum_id, user_id) VALUES (" 
               . db_encode($forum_id) . ", " . db_encode($user_id) .")";
        executeNonQuery($sql);

        $msg = "Member '$login' has been added to this private board.";
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }
}

function del_member($forum_id, $user_id) {
    global $link, $msg;
    try {
        $sql = "DELETE FROM BBS_PrivateMembership WHERE forum_id = " . db_encode($forum_id) 
               . " AND user_id = " . db_encode($user_id);
        executeNonQuery($sql);

        $sql = "SELECT login FROM User WHERE ID = " . db_encode($user_id);
        $login = executeScalar($sql);

        $msg = "Member '$login' has been deleted from this private board.";
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }
}


function writeError() {
    global $msg;
    if ($msg != "") {
        if (startsWith($msg, "Error")) {
            $msg = "<p><font color='red'>$msg</font></p>";
        }
        else {
            $msg = "<p><font color='green'>$msg</font></p>";
        }
    }
    return $msg;
}
?>

