<?php
// Show forum list.
// Included by bbs_funcs.php

//
// Show a list of forums.
//
function showForums() {
    showForums_v2();
}


function showForums_v2() {
    global $T_forumList, $T_forum, $T_threads, $T_posts, $_LANG, $T_manager;

    db_open();

    $title = $_LANG == "en" ? "title_en" : "title";
    $group = $_LANG == "en" ? "description_en" : "description";
    $query = <<<EOF
SELECT B.ID, B.GroupID, G.$group AS Grp, B.name, B.$title AS title, B.description, B.managers,
       B.thread_count, B.post_count, B.readonly, B.private, B.hidden, B.disabled 
FROM BBS_BoardList B
LEFT OUTER JOIN BBS_BoardGroups G ON B.GroupID = G.ID
ORDER BY B.GroupID ASC, B.ID ASC
EOF;

    //$ret = executeDataTable($query);
    $ret = executeAssociateDataTable($query);
    db_close();

    //print "<br>"; print_r($ret);

    $group = "";
    $s = "<tr><td><br></td><td>&nbsp; $T_forum </td><td>$T_threads</td><td>$T_posts</td></tr>";
    $i = 1;
    foreach ($ret as $key => $val) {
        if (! can_see_hidden_board($val['ID'], $val['hidden'])) { continue;  }
        if (! can_see_disabled_board($val['disabled'])) { continue;  }

        $t = "";
        if ($group != $val['Grp']) {
            $group = $val['Grp'];
            $t = "<tr><td class=\"bbs_forum_list_group\" colspan=\"4\">&nbsp;$group</td></tr>";
        }

        $attrib = getBoardAttrib($val['readonly'], $val['hidden'], $val['private'], $val['disabled']);
        $managers = ($val['managers'] == '') ? "" : "$T_manager:" . writeManagers($val['managers']);
        
        $t .= <<<EOF
<tr><td width='50'><img src='../image/folder_blue.png'></td>
<td align='left'>&nbsp;<a href='forum.php?f=$val[ID]'>$val[title]</a>$attrib
<br/>&nbsp;<span style='font-size:11px; color:#666666;'>$val[description]
<br/>&nbsp;$managers
</span>
</td>
<td width='50'>$val[thread_count]</td>
<td width='50'>$val[post_count]</td>
</tr>
EOF;
        $s .= $t;
        ++ $i;
    }

    $s = "<table id=\"bbs_forum_list\" class=\"bbs_forum_list\">$s</table>";

    print $s;
}


function showForums_v1() {
    global $T_forumList, $T_forum, $T_threads, $T_posts, $_LANG;

    db_open();
    $query = "SELECT ID, GroupID, name, title, thread_count, post_count, readonly, private, hidden, disabled FROM BBS_BoardList ORDER BY GroupID ASC, ID ASC";

    if ($_LANG == "en") {
        $query = "SELECT ID, GroupID, name, title_en AS title, thread_count, post_count, readonly, private, hidden, disabled FROM BBS_BoardList ORDER BY GroupID ASC, ID ASC";
    }
    //$ret = executeDataTable($query);
    $ret = executeAssociateDataTable($query);
    db_close();

    //print "<br>"; print_r($ret);

    $s = "<tr><td><br></td><td>&nbsp; $T_forum </td><td>$T_threads</td><td>$T_posts</td></tr>";
    $i = 1;
    foreach ($ret as $key => $val) {
        if (! can_see_hidden_board($val['ID'], $val['hidden'])) { continue;  }
        if (! can_see_disabled_board($val['disabled'])) { continue;  }
        $attrib = getBoardAttrib($val['readonly'], $val['hidden'], $val['private'], $val['disabled']);
        $t = "<td>$i</td>"
             . "<td><a href='forum.php?f=$val[ID]'>$val[title]</a>$attrib</td>"
             . "<td>$val[thread_count]</td>"
             . "<td>$val[post_count]</td>";
        $s .= "<tr>$t</tr>";
        ++ $i;
    }

    $s = "<table id=\"bbs_forum_list\" class=\"bbs_forum_list\">$s</table>";

    print $s;
}


?>
