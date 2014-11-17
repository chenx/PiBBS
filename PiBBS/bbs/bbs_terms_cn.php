<?php
$T_home = "主页";
$T_forum = "论坛";
$T_forumList = "论坛列表";
$T_threads = "主题数";
$T_posts = "文章数";
$T_manager = "管理员";
$T_none = "无";
$T_bbs_help = "发贴帮助";

$T_send_group_imail = "群发站内信";
$T_send_group_imail_desc = "发送站内信到本版所有成员";

$T_send_group_email = "&#32676;&#21457;&#31449;&#22806;&#20449;";
$T_send_group_email_desc = "&#32676;&#21457;&#31449;&#22806;&#20449;&#21040;&#26412;&#29256;&#25152;&#26377;&#25104;&#21592;";

$T_attachment = "&#38468;&#20214;";

$T_priv_board_member_list = "会员列表";

$T_please = "请";
$T_first = "先";
$T_login = "登录";

// For user.php
$T_userinfo = "用户信息";
$T_username = "用户名";
$T_usertype = "用户身份";
$T_note = "签名档";
$T_bbs_score = "论坛积分";
$T_bbs_new_posts = "论坛新发文章数";
$T_bbs_reply_posts = "论坛回复文章数";
$T_bbs_mark_posts = "论坛马克文章数";
$T_bbs_digest_posts = "论坛文摘文章数";

$T_add_new_post = "发表文章";
$T_new_post = "新文章";
$T_reply_post = "回复文章";
$T_edit_post = "修改文章";
$T_remove_post = "删除文章";
$T_no_empty = "不能为空";
$T_submit_confirm = "您的文章已经成功发表。";
$T_remove_confirm = "文章已经成功删除。";
$T_remove = "删除";
$T_remove_prompt = "您确定要删除本文吗？";
$T_no_remove_has_replies = "本文有回复, 不能删除";

$T_page = "分页";
$T_go = "前往";

$T_title = "标题";
$T_body  = "内容";
$T_keywords = "关键字";
$T_keywords_title = "选填, 关键字之间请用逗号隔开";
$T_post_date = "发表日期";
$T_author = "作者";
$T_click = "点击";
$T_reply = "回复";
$T_last_reply = "最后回复";
$T_time = "时间";
$T_post_time = "发表时间";
$T_source = "来源";
$T_last_edit = "最后修改";
$T_from = "来自";

$T_search_title = "搜索本版标题, 内容及关键字";
$T_search_placeholder = "搜索";

$T_search_keyword = "匹配关键字";
$T_search_keyword_title = "搜索本版关键字";
$T_search_keyword_placeholder = "关键字搜索";
$T_fuzzy_match = "模糊匹配";

$T_submit = "提交";
$T_preview = "预览";
$T_edit = "修改";

$T_back_forum = "返回版面";
$T_back_thread = "返回主题";
$T_back = "返回";

$T_read_thread = "同主题阅读";

$T_turn_on_manage_mode = "打开管理模式";
$T_turn_off_manage_mode = "关闭管理模式";

$T_board = "版面";
$T_digest_area = "文摘区";
$T_mark_area = "马克区";

$T_minute_ago = "分钟前";
$T_minutes_ago = "分钟前";
$T_hour_ago = "小时前";
$T_hours_ago = "小时前";
$T_today = "今天";
$T_yesterday = "昨天";
$T_2days_ago = "前天";
$T_days_ago = "天前";

$T_readonly = "&#21482;&#35835;";
$T_hidden = "&#38544;&#34255;";
$T_private = "&#20250;&#21592;";
$T_disabled = "&#20851;&#38381;";

$T_currentTime = "当前时间";

$T_forum_help = <<<EOF
<div style="text-align: left;">
<p><b><a href="#" onclick="javascript:toggle_help();return false;" style="color:#666;">
发贴帮助[<span id="bbs_help_mode">+</span>]</a></b></p>
<span id="bbs_help" style="display:none;">
<ul>
<li>显示超链接: 
<br/>@[a href="{your link}"] {text} @[/a]
<li>显示图像: 
<br/>@[img src="{image link}"]
<br/>如果要调整显示的宽度和高度: 
<br/>@[img src="{image link}" width="400" height="200"]
<li>显示录像(Youtube, Youku等):
<br/>例如, 原链接为: &lt;iframe width="560" height="315" src=".." .. allowfullscreen&gt;&lt;/iframe&gt;
<br/>改为: @[video width="560" height="315" src=".." .. allowfullscreen]
<li>显示内嵌网页:
<br/>例如, 网页链接为url
<br/>改为: @[iframe width="100%" height="1000" src="url" style="border: 0px;"]
<br/>注意: 对于录像，用@[iframe]不用@[vframe]可能在手机上显示不美观。
<li>显示一段代码: 
<br/>@[code] {your code} @[/code] 
<li>显示一段代码于可调节高度的Textarea:
<br/>@[codearea rows=n] {your code} @[/codearea]
<li>显示粗体文本: 
<br/>@[b] {text} @[/b]
<li>显示带下划线的文本: 
<br/>@[u] {text} @[/u]
</ul>
</span>
</div>
EOF;

function getReplyHead($username, $time) {
    $s ="\n\n\n【 在 $username 发表于 $time 的大作中提到: 】\n";
    return $s;
}

?>
