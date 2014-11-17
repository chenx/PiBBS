<?php
$T_home = "Home";
$T_forum = "Forum";
$T_forumList = "Forum List";
$T_threads = "Threads";
$T_posts = "Posts";
$T_manager = "Manager";
$T_none = "none";
$T_bbs_help = "BBS Help";

$T_send_group_imail = "Send Group IMail";
$T_send_group_imail_desc = "Send IMail to all forum members";

$T_send_group_email = "Send Group Email";
$T_send_group_email_desc = "Send EMail to All forum members";

$T_attachment = "Attachment";

$T_priv_board_member_list = "Board Member List";

$T_please = "Please";
$T_first = "first";
$T_login = "log in";

// For user.php
$T_userinfo = "User Information";
$T_username = "Username";
$T_usertype = "User Type";
$T_note = "Signature";
$T_oj_pass_count = "OJ Pass Count";
$T_bbs_score = "BBS Score";
$T_bbs_new_posts = "BBS New Posts";
$T_bbs_reply_posts = "BBS Reply Posts";
$T_bbs_mark_posts = "BBS Marked Posts";
$T_bbs_digest_posts = "BBS Digested Posts";

$T_add_new_post = "Add New Post";
$T_new_post = "New Post";
$T_reply_post = "Reply Post";
$T_edit_post = "Edit Post";
$T_remove_post = "Remove Post";
$T_no_empty = "Cannot be empty";
$T_submit_confirm = "Your post has been successfully submitted.";
$T_remove_confirm = "This post has been successfully removed.";
$T_remove = "Remove";
$T_remove_prompt = "Are you sure to remove this post?";
$T_no_remove_has_replies = "Cannot remove since there are replies";

$T_page = "Page";
$T_go = "Go";

$T_title = "Title";
$T_body  = "Body";
$T_keywords = "Keywords";
$T_keywords_title = "Optional. Please separate keywords with comma(,)";
$T_post_date = "Post Date";
$T_author = "Author";
$T_click = "Click";
$T_reply = "Reply";
$T_last_reply = "Last Reply";
$T_time = "Time";
$T_post_time = "Post Time";
$T_source = "Source";
$T_last_edit = "Last edited";
$T_from = "From";

$T_search_title = "Search Title, Body and Keyword in this forum";
$T_search_placeholder = "Text to search";

$T_search_keyword = "Match keyword";
$T_search_keyword_title = "Search Keyword in this forum";
$T_search_keyword_placeholder = "Keyword to search";
$T_fuzzy_match = "Fuzzy match";

$T_submit = "Submit";
$T_preview = "Preview";
$T_edit = "Edit";

$T_back_forum = "Back To Forum";
$T_back_thread = "Back To Thread";
$T_back = "Back";

$T_read_thread = "Entire Thread";

$T_turn_on_manage_mode = "Turn On Manage Mode";
$T_turn_off_manage_mode = "Turn Off Manage Mode";

$T_board = "Forum";
$T_digest_area = "Digests";
$T_mark_area = "Marks";

$T_minute_ago = "minute ago";
$T_minutes_ago = "minutes ago";
$T_hour_ago = "hour ago";
$T_hours_ago = "hours ago";
$T_today = "today";
$T_yesterday = "yesterday";
$T_2days_ago = "2 days ago";
$T_days_ago = " days ago";

$T_readonly = "readonly";
$T_hidden = "hidden";
$T_private = "private";
$T_disabled = "disabled";

$T_currentTime = "Current Time";

$T_forum_help = <<<EOF
<div style="text-align: left;">
<p><b><a href="#" onclick="javascript:toggle_help();return false;" style="color:#666;">
Post Help[<span id="bbs_help_mode">+</span>]</a></b></p>
<span id="bbs_help" style="display:none;">
<ul>
<li>To show a hyperlink:
<br/>@[a href="{your link}"] {text} @[/a]
<li>To show an image:
<br/>@[img src="{image link}"]
<br/>You can also specify the display size:
<br/>@[img src="{image link}" width="400" height="200"]
<li>To show a video (Youtube, Youku etc.):
<br/>E.g., the original link is &lt;iframe width="560" height="315" src=".." .. allowfullscreen&gt;&lt;/iframe&gt;
<br/>Change to: @[video width="560" height="315" src=".." .. allowfullscreen]
<li>To display an embedded webpage:
<br/>E.g., the webpage address is: url
<br/>Change to: @[iframe width="100%" height="1000" src="url" style="border: 0px;"]
<br/>Note: For videos, use @[iframe] instead of @[video] may cause inconsistent formatting on mobile devices.
<li>To show a segment of code:
<br/>@[code] {your code} @[/code]
<li>To show a segment of code is a textarea with adjustable height:
<br/>@[codearea rows=n] {your code} @[/codearea]
<li>To show bold text:
<br/>@[b] {text} @[/b]
<li>To show underlined text:
<br/>@[u] {text} @[/u]
</ul>
</span>
</div>
EOF;

function getReplyHead($username, $time) {
    $s ="\n\n\n【 $username's post on $time mentioned: 】\n";
    return $s;
}

?>
