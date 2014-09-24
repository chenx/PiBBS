<?php
//require_once("../func/db.php");

function get_bbs_digests() {
    global $T_bbs_digest, $_BBS_DIGEST_FORMAT;

    $s = get_bbs_digests_table( $_BBS_DIGEST_FORMAT ); // format: 1, 2. Default is 1.
    if ($s != "") {
        //print "<hr style=\"size: 1px; color: #eee;\">";
        print "<h3>$T_bbs_digest</h3>$s";
    }
}

function get_bbs_digests_table($format) {
    global $T_title, $T_post_time, $T_author, $T_forum, $T_click, $T_reply, $root_path, $_LANG;
    $s = "";

    $title = $_LANG == "en" ? "title_en" : "title";
    $query = "SELECT ID, GroupID, name, $title AS title, private, hidden, readonly, disabled FROM BBS_BoardList ORDER BY GroupID ASC, ID ASC";

    db_open();
    $ret = executeAssociateDataTable($query);

    foreach ($ret as $key => $val) {
        $forum_id = $val['ID'];
        $forum_name = $val['name'];
        $forum_title = $val['title'];

        if (! can_see_hidden_board($val['ID'], $val['hidden'])) { continue;  }
        if (! can_see_disabled_board($val['disabled'])) { continue;  }

        $t = get_bbs_forum_digests_table($forum_id, $forum_name, $forum_title, $format);
        if ($format == 2 && $t != "") {
            $attrib = getBoardAttrib($val['readonly'], $val['hidden'], $val['private'], $val['disabled']);
            $s .= <<<EOF
</table><table class="digest_forum">
<tr><td class="bbs_digest_group" colspan="5">
    <a href="$root_path/bbs/forum.php?f=$forum_id" class="forum">$forum_title$attrib</td></tr>
EOF;
        }
        $s .= $t;
    }

    db_close();

    $hide = ($format == 2) ? " class=\"hide\"" : "";

    if ($s != "") {
        $s = <<<EOF
<table class="digest_forum">
<tr style="background-color:#ccccff;/*for IE.*/">
<td class="desktop"><br/></td><td>$T_title</td><td class="desktop">$T_post_time</td>
<td class="desktop">$T_author</td><td>$T_reply</td><td$hide>$T_forum</td>
</tr>
$s
</table>
EOF;
    }

    return $s;
}


function get_bbs_forum_digests_table($forum_id, $forum_name, $forum_title, $format) {
    global $root_path;
    global $T_title, $T_post_time, $T_author, $T_forum;
    $len = 200; // max length of body string to get from database and display.
                // Another place to change is digest.css, td.title width.
    //print "$forum_title: $forum_name<br>";

    $N = 0; // number of posts to show in BBS digest on homepage.

    global $_BBS_SHOW_NON_DIGEST, $_BBS_SHOW_NON_DIGEST_N, $_BBS_SHOW_DIGEST_N;
    if ($_BBS_SHOW_NON_DIGEST) {
        $N = $_BBS_SHOW_NON_DIGEST_N;
        $query_non_digest = <<<EOF
UNION 
(SELECT ID, thread_id, title, Substring(body, 1, $len) as body, user_name, submit_timestamp, 
        reply_count, digested 
FROM $forum_name
WHERE hidden = '0' AND digested = '0' AND ID = thread_id order by ID DESC limit $N)
EOF;
    }

    $N += $_BBS_SHOW_DIGEST_N;
    // This query lists digested articles in the front.
    $query = <<<EOF
(SELECT ID, thread_id, title, Substring(body, 1, $len) as body, user_name, submit_timestamp, 
        reply_count, digested 
FROM $forum_name 
WHERE hidden = '0' AND digested = '1' ORDER BY ID DESC)
 $query_non_digest
order by digested DESC, ID DESC limit $N
EOF;
    $ret = executeAssociateDataTable($query);

    $hide = ($format == 2) ? " class=\"hide\"" : "";
    $s = "";
    foreach ($ret as $key => $val) {
        $title = db_htmlEncode($val['title']);
        $body = db_htmlEncode( removeBBSTag( $val['body'] ) );
        //$body = removeBBSTag( $val['body'] );
        //$body = $val['body'];
        $body = " &nbsp;<font color='#999999'>$body</font>";
        $author = $val['user_name'];
        $author = "<a href=\"$root_path/bbs/user.php?u=$author\" class=\"bbs_user\">$author</a>";
        $date = customDate( $val['submit_timestamp'] );
        $id = $val['ID'];
        $thread_id = $val['thread_id'];
        $reply = $val['reply_count'];

        if ($id == $thread_id) {
            $t = "<a href=\"$root_path/bbs/view.php?f=$forum_id&t=$thread_id\">$title</a>";
        } else {
            $t = "<a href=\"$root_path/bbs/post.php?f=$forum_id&t=$thread_id&i=$id\">$title</a>";
        }
        $t = <<<EOF
<td class="desktop" align="center"><img src="../image/file_22_22.png" class="iconFile"/></td>
<td class="title">$t $body</td>
<td class="desktop">$date</td><td class="desktop">$author</td><td>$reply</td>
<td$hide><a href="$root_path/bbs/forum.php?f=$forum_id">$forum_title</a></td>
EOF;
        $s .= "<tr class='digest_forum_post'>$t</tr>";
    }

    return $s;
}

function removeBBSTag($s) {
    //$s = "abc@[a href='http://xyz']nnn@[/a]123@[a href='.'456"; // for test.
    //return $s;

    //$s = preg_replace("/\r/", "", $s);
    //$s = preg_replace("/\n/", " ", $s);
    $s = preg_replace("/[ |\t|\r|\n]+/", " ", $s);

    $pos = strpos($s, "@[");
    if ($pos === false) return $s; // to save unnecessary work.

    $s = str_replace("@[b]", "", $s);
    $s = str_replace("@[/b]", "", $s);

    $s = str_replace("@[u]", "", $s);
    $s = str_replace("@[/u]", "", $s);

    $s = str_replace("@[code]", "", $s);
    $s = str_replace("@[/code]", "", $s);

    $s = preg_replace("/@\[codearea rows=(.+?)\]/", "", $s);
    $s = str_replace("@[/codearea]", "", $s);

    $s = preg_replace("/@\[img src='(.+?)'\]/", "", $s);

    $s = preg_replace("/@\[iframe (.+?)\]/", "", $s);

    $s = preg_replace("/@\[a href='(.+?)'\]/", "", $s);
    $s = str_replace("@[/a]", "", $s);

    $pos = strpos($s, "@[");
    if ($pos !== false) {
        $s = substr($s, 0, $pos);
    }

    return $s;
}

function customDate($s) {
    global $T_minute_ago, $T_minutes_ago, $T_hour_ago, $T_hours_ago, 
           $T_today, $T_yesterday, $T_2days_ago, $T_days_ago;

    //$date = date('Y-m-d H:i:s', time());
    $t = strtotime($s);
    $d = date('Y-m-d', $t);
    //$h = date('h:i a', $t);
    //$today = date('Y-m-d', time());
    //$yesterday = date('Y-m-d', strtotime('-1 days'));
    //$two_days_ago = date('Y-m-d', strtotime('-2 days'));

    $diff_sec = time() - $t;
    $diff_min = round( $diff_sec / 60 );
    $diff_hour = round( $diff_sec / 3600 );
    $diff_day = round( $diff_sec / 86400 );


    if ($diff_min < 60) {
        $s = "$diff_min " . ( ($diff_min > 1) ? $T_minutes_ago : $T_minute_ago );
    }
    else if ($diff_hour <= 5) {
        $s = "$diff_hour " . ( ($diff_hour > 1) ? $T_hours_ago : $T_hour_ago );
    }
    else if ($diff_day == 0) { // ($d == $today) {
        $s = $T_today;
    }
    else if ($diff_day == 1) { // ($d == $yesterday) {
        $s = $T_yesterday;
    }
    else if ($diff_day == 2) { // ($d == $two_days_ago) {
        $s = $T_2days_ago;
    }
    else if ($diff_day <= 10) {
        $s = "$diff_day $T_days_ago";
    }
    else { 
        $s = $d;
    }

    //$s .= " - $d : $today : yesterday = $yesterday : $two_days_ago. diff_min: $diff_min<br>";
    return $s;
}

?>
