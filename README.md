
<h1>PiBBS Documentation</h1>
<div class="show_image_option">
Image Display Options:
<a title="No display" onclick="javascript: disp_img(0);" href="#">none</a>
|
<a title="Width: 100px" onclick="javascript: disp_img(1);" href="#">icon</a>
|
<a title="Width: 300px" onclick="javascript: disp_img(2);" href="#">small</a>
|
<a title="Width: 1000px. Is default size." onclick="javascript: disp_img(3);" href="#">large</a>
</div>
<div style="font-style: italic;"> Created on: 10/27/2014, Last modified: 10/27/2014 </div>
<hr>
<div id="toc" style="background: #eee;">
<h3>Table of Contents</h3>
<ul>
<ul>
<li>
<a href="#About">About</a>
</li>
<li>
<a href="#History">History</a>
</li>
<li>
<a href="#License">License</a>
</li>
<li>
<a href="#User_Documentation">User Documentation</a>
</li>
<ul>
<li>
<a href="#U.1_The_homepage">U.1 The homepage</a>
</li>
<li>
<a href="#U.2_Interface_of_a_board">U.2 Interface of a board</a>
</li>
<li>
<a href="#U.3_Post_special_tags">U.3 Post special tags</a>
</li>
<li>
<a href="#U.4_Register_An_Account">U.4 Register An Account</a>
</li>
<li>
<a href="#U.5_Sign_in">U.5 Sign in</a>
</li>
<li>
<a href="#U.6_User_menu_and_Profile_after_sign_in">U.6 User menu and Profile after sign in</a>
</li>
<li>
<a href="#U.7_User_Avatar">U.7 User Avatar</a>
</li>
<li>
<a href="#U.8_Internal_Mail:_I-Mail">U.8 Internal Mail: I-Mail</a>
</li>
<li>
<a href="#U.9_Compose_I-Mail">U.9 Compose I-Mail</a>
</li>
<li>
<a href="#U.10_User_List">U.10 User List</a>
</li>
<li>
<a href="#U.11_External_Mail:_E-Mail">U.11 External Mail: E-Mail</a>
</li>
</ul>
<li>
<a href="#Board_Master_Documentation">Board Master Documentation</a>
</li>
<ul>
<li>
<a href="#B.1_Manage_Board_Permission">B.1 Manage Board Permission</a>
</li>
<li>
<a href="#B.2_Manage_Private_Board_Members">B.2 Manage Private Board Members</a>
</li>
<li>
<a href="#B.3_Manage_Posts">B.3 Manage Posts</a>
</li>
</ul>
<li>
<a href="#System_Administrator_Documentation">System Administrator Documentation</a>
</li>
<ul>
<li>
<a href="#S.1_System_admin_interface">S.1 System admin interface</a>
</li>
<li>
<a href="#S.2_Manage_users">S.2 Manage users</a>
</li>
<li>
<a href="#S.2_BBS_Management">S.2 BBS Management</a>
</li>
<ul>
<li>
<a href="#S.2.1_Manage_BBS_board_tables.">S.2.1 Manage BBS board tables.</a>
</li>
<li>
<a href="#S.2.2_Manage_boards">S.2.2 Manage boards</a>
</li>
</ul>
<li>
<a href="#S.3_IMail_Management">S.3 IMail Management</a>
</li>
<li>
<a href="#S.4_Views">S.4 Views</a>
</li>
<li>
<a href="#S.5_Management_functions">S.5 Management functions</a>
</li>
<ul>
<li>
<a href="#S.5.1_Backup_Database">S.5.1 Backup Database</a>
</li>
</ul>
<li>
<a href="#S.6_Reports">S.6 Reports</a>
</li>
<li>
<a href="#S.7_configuration_file">S.7 configuration file</a>
</li>
<ul>
<li>
<a href="#S.7.1_conf.php">S.7.1 conf.php</a>
</li>
<li>
<a href="#S.7.2_db_conf.php">S.7.2 db_conf.php</a>
</li>
<li>
<a href="#S.7.3_linkedin_conf.php">S.7.3 linkedin_conf.php</a>
</li>
<li>
<a href="#S.7.4_upload_conf.php">S.7.4 upload_conf.php</a>
</li>
</ul>
</ul>
<li>
<a href="#Developer_Documentation">Developer Documentation</a>
</li>
<ul>
<li>
<a href="#D.1_Database_development">D.1 Database development</a>
</li>
<li>
<a href="#D.2_Other_library_functions">D.2 Other library functions</a>
</li>
<li>
<a href="#D.3_Authentication">D.3 Authentication</a>
</li>
<li>
<a href="#D.4_Themes">D.4 Themes</a>
</li>
<li>
<a href="#D.5_File_Upload">D.5 File Upload</a>
</li>
<li>
<a href="#D.6_Miscellaneous">D.6 Miscellaneous</a>
</li>
<ul>
<li>
<a href="#D.6.1_Google_analytics">D.6.1 Google analytics</a>
</li>
<li>
<a href="#D.6.2_Social_media_promotion_icons">D.6.2 Social media promotion icons</a>
</li>
</ul>
</ul>
<li>
<a href="#To-do_List">To-do List</a>
</li>
<ul>
<li>
<a href="#IMail">IMail</a>
</li>
<li>
<a href="#EMail">EMail</a>
</li>
<li>
<a href="#Wiki_post">Wiki post</a>
</li>
<li>
<a href="#More_of_user_profile">More of user profile</a>
</li>
<li>
<a href="#BBS_post_tag_buttons">BBS post tag buttons</a>
</li>
<li>
<a href="#Database_table_name_prefix">Database table name prefix</a>
</li>
<li>
<a href="#Site-wide_Improvements">Site-wide Improvements</a>
</li>
</ul>
<li>
<a href="#Author">Author</a>
</li>
</ul>
</ul>
</div>
<hr>
<div id="contents">
<h2>
<a name="About">About</a>
</h2>
<p>A web framework, and Forum/BBS web application. It is light-weighted, easy to deploy and extend. It displays well in both desktop computer and mobile phones. </p>
<p>Features include: </p>
<ul>
<li>Provide a web framework for sign up, sign in/out, user profile management. </li>
<li>Provide a forum/BBS. </li>
<li>Allow fine control of permissions of board and articles. </li>
<li>Provide an internal mailbox. </li>
<li>Allow sending external emails and internal emails. </li>
<li>Interface works well on desktop, ipad, and mobile devices. </li>
<li>Allow creation of new themes, mostly by changing files in /theme and /css. </li>
<li>Allow customizable settings, in /conf/conf.php. </li>
<li>Allow English or Chinese version. Easy to extend to other languages. </li>
</ul>
<hr>
<h2>
<a name="History">History</a>
</h2>
<p>
Development first initiated in Summer 2013 as a forum for
<a href="http://homecox.com">homecox.com</a>
. It stopped for a while, and was picked up again in Summer 2014.
</p>
<hr>
<h2>
<a name="License">License</a>
</h2>
<p>Released under Apache/MIT/BSD/GPLv2 license. </p>
<hr>
<h2>
<a name="User_Documentation">User Documentation</a>
</h2>
<h3>
<a name="U.1_The_homepage">U.1 The homepage</a>
</h3>
<p> The default installation contains five boards: Computer Science, Programming World, News, Just for fun, Forum Administration. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/home1_en.png">
<img class="screenshot" alt="homepage screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/home1_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.2_Interface_of_a_board">U.2 Interface of a board</a>
</h3>
<p> This is the "Forum" view of a board. The "Digests" view shows articles labeled as digests, which will show in the homepage digest list. The "Marks" view shows articles marked as important. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/board1_en.png">
<img class="screenshot" alt="board screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/board1_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.3_Post_special_tags">U.3 Post special tags</a>
</h3>
<p> Post allows only plain text initially. Then special tags are added to make posts more informative. These include: </p>
<ul>
<li>
To show a hyperlink:
<br>
@[a href="{your link}"] {text} @[/a]
</li>
<li>
To show an image:
<br>
@[img src="{image link}"]
<br>
You can also specify the display size:
<br>
@[img src="{image link}" width="400" height="200"]
</li>
<li>
To show a video (Youtube, Youku etc.):
<br>
E.g., the original link is <iframe width="560" height="315" src=".." .. allowfullscreen></iframe>
<br>
Change to: @[iframe width="560" height="315" src=".." .. allowfullscreen]
</li>
<li>
To show a segment of code:
<br>
@[code] {your code} @[/code]
</li>
<li>
To show a segment of code is a textarea with adjustable height:
<br>
@[codearea rows=n] {your code} @[/codearea]
</li>
<li>
To show bold text:
<br>
@[b] {text} @[/b]
</li>
<li>
To show underlined text:
<br>
@[u] {text} @[/u]
</li>
</ul>
<p></p>
<p>For example, this post contains a video, two images, an underlined line and a line in bold text. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/post_tags.png">
<img class="screenshot" alt="post tags screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/post_tags.png" style="display: block; width: 1000px;">
</a>
<p> Note that these tags used by BBS are also used identically by I-Mail. </p>
<h3>
<a name="U.4_Register_An_Account">U.4 Register An Account</a>
</h3>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/register_en.png">
<img class="screenshot" alt="register screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/register_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.5_Sign_in">U.5 Sign in</a>
</h3>
<p>There are 2 choices: 1) regular sign in, 2) Linkedin sign in.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/login_en.png">
<img class="screenshot" alt="login screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/login_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.6_User_menu_and_Profile_after_sign_in">U.6 User menu and Profile after sign in</a>
</h3>
<p>Figure below shows the user menu, and the basic profile page. Note only those users who are board managers can see the "Manage Boards" entry. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/profile_en.png">
<img class="screenshot" alt="profile screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/profile_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.7_User_Avatar">U.7 User Avatar</a>
</h3>
<p>The user avatar uses universal icon from gravatar.com.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/avatar_en.png">
<img class="screenshot" alt="avatar screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/avatar_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.8_Internal_Mail:_I-Mail">U.8 Internal Mail: I-Mail</a>
</h3>
<p>There is a internal mailbox (I-Mail) users can use for private communication with other registered users.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/mailbox_en.png">
<img class="screenshot" alt="I-Mail screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/mailbox_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.9_Compose_I-Mail">U.9 Compose I-Mail</a>
</h3>
<p>This shows the interface to compose an internal mail. The user can send group I-Mail to multiple users. Attachment is allowed.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/imail_compose_en.png">
<img class="screenshot" alt="I-Mail Compose screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/imail_compose_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.10_User_List">U.10 User List</a>
</h3>
<p>Under the "Forum" menu there is a "User List" submenu. Click on this shows a list of registered users.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/user_list_en.png">
<img class="screenshot" alt="user list screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/user_list_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="U.11_External_Mail:_E-Mail">U.11 External Mail: E-Mail</a>
</h3>
<p>From the user list one can send an external email to another registered user. The user's registration email is used.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/email_compose_en.png">
<img class="screenshot" alt="email compose screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/email_compose_en.png" style="display: block; width: 1000px;">
</a>
<hr>
<h2>
<a name="Board_Master_Documentation">Board Master Documentation</a>
</h2>
<p>Some users are appointed as board masters by system administrator. A board master manages users and article permissions in a board.</p>
<p> The possible options on the management of a post are: </p>
<ul>
<li>Top - a top post is shown at the beginning of a board, with an upward arrow. </li>
<li>Readonly - a readonly post cannot be editted or deleted by its author. </li>
<li>Mark - a post labeled as "Mark" when it is of value, and will show in the "Marks" view of the board. </li>
<li>Digest - a post labeled as "Digest" when it is of value to be known by general forum visitors, and will show in the "Digests" view of the board. </li>
<li>Hide - a hidden post is not shown on the board view. </li>
<li>Delete - a deleted post is gone. </li>
</ul>
<p></p>
<h3>
<a name="B.1_Manage_Board_Permission">B.1 Manage Board Permission</a>
</h3>
<p> Under the user profile menu there is a "Manage Boards" submenu. Click on this shows the page to manage board permissions. A board can be Private, Hidden, and/or Readonly. </p>
<p>The rules are: </p>
<ul>
<li>Only board members can post in a Private board. </li>
<li>A Hidden board can be seen only by logged in board members. </li>
<li>No one can post in a Readonly board. </li>
</ul>
<p></p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/manage_board_en.png">
<img class="screenshot" alt="manage board screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/manage_board_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="B.2_Manage_Private_Board_Members">B.2 Manage Private Board Members</a>
</h3>
<p>If a board is private, only approved members can post in this board. The board master can add or remove a member to/from the board's members. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/manage_board_members_en.png">
<img class="screenshot" alt="manage board members screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/manage_board_members_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="B.3_Manage_Posts">B.3 Manage Posts</a>
</h3>
<p>The board master can manage posts on the board. A board master will see the "Turn On Manage Mode" link below. </p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_board_en.png">
<img class="screenshot" alt="boardmaster screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_board_en.png" style="display: block; width: 1000px;">
</a>
<p>Turn On Manage Mode, the board master sees the below options:</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_board_manage_en.png">
<img class="screenshot" alt="boardmaster manage screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_board_manage_en.png" style="display: block; width: 1000px;">
</a>
<p>Click into the post, the board master sees the below options to manage each post individually:</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_post_manage_en.png">
<img class="screenshot" alt="boardmaster post manage screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/bm_post_manage_en.png" style="display: block; width: 1000px;">
</a>
<hr>
<h2>
<a name="System_Administrator_Documentation">System Administrator Documentation</a>
</h2>
<p> The system administrator manages all the users, boards and posts in the forum. The system administrator by default has all the permissions of a board master. </p>
<h3>
<a name="S.1_System_admin_interface">S.1 System admin interface</a>
</h3>
<p>The figure below shows current functions a system administrator has access to.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_en.png">
<img class="screenshot" alt="admin screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="S.2_Manage_users">S.2 Manage users</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>User - manage users, including add/update/delete </li>
<li>UserGroup - manage user group. Two basic groups are: admin, user. </li>
<li>
Code register - used only when the registration code feature is used.
<br>
When the registration code feature is enabled, only invited people with a registration code can register.
</li>
<li>User_LinkedIn - manage linkedin binding of users who sign in using their linked in account. </li>
</ul>
<p></p>
<h3>
<a name="S.2_BBS_Management">S.2 BBS Management</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>Manage BBS Board Tables (add/remove board/forum). This allows to create/edit new board. See S.2.2. </li>
<li>BBS_BoardList (BBS Board List). This lists all the boards. </li>
<li>BBS_BoardGroups (BBS Board Group). This allows to manage board groups. See S.2.1. </li>
<li>BBS_BoardManager (BBS Board Manager). The allows to manage board managers. See S.2.2. </li>
<li>BBS_PrivateMembership. This lists the members in each private board. </li>
</ul>
<p></p>
<h4>
<a name="S.2.1_Manage_BBS_board_tables.">S.2.1 Manage BBS board tables.</a>
</h4>
<p>A board belongs to a group. Use the "BBS BoardGroups (BBS Board Group)" link to manage groups, including add/udpate/delete.</p>
<h4>
<a name="S.2.2_Manage_boards">S.2.2 Manage boards</a>
</h4>
<p>The "Manage BBS Board Tables (add/remove board/forum)" page allows a new board to be created.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_manage_boards_en.png">
<img class="screenshot" alt="admin manage board screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_manage_boards_en.png" style="display: block; width: 1000px;">
</a>
<p> A new board can be added here. </p>
<p> A board can have 0, 1 or more than 1 managers. Two places are relevant: table BBS_BoardManager and table column BBS_BoardList.managers. </p>
<p> In table 'BBS_BoardList', the 'managers' column format is: user_id,user_name,role[|user_id,user_name,role]* </p>
<h3>
<a name="S.3_IMail_Management">S.3 IMail Management</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>IMail - mail information (read only). </li>
<li>IMailRecv (Check receive activity) - view receive status (read only). </li>
<li>IMailRecvNotify - view mail notification information (read only). </li>
<li>IMailState - constants for mail state (read only). </li>
<li>
IMail email notification - this can send mail notification immediately.
<br>
This can be set up such that it is done by crontab job automatically periodically.
</li>
</ul>
<p></p>
<h3>
<a name="S.4_Views">S.4 Views</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>View_Log_site (Site Activity Log) - shows site activity log (read only). </li>
</ul>
<p></p>
<h3>
<a name="S.5_Management_functions">S.5 Management functions</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>Backup Database - see S.5.1. </li>
<li>Generate registration code - used only when the registration code feature is on. </li>
</ul>
<p></p>
<h4>
<a name="S.5.1_Backup_Database">S.5.1 Backup Database</a>
</h4>
<p>The allow backup site database from web interface.</p>
<a href="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_backup_db_en.png">
<img class="screenshot" alt="admin backup database screenshot" src="http://cssauh.com/xc/PiBBS/INSTALL/doc/image/admin_backup_db_en.png" style="display: block; width: 1000px;">
</a>
<h3>
<a name="S.6_Reports">S.6 Reports</a>
</h3>
<p> Links involved are: </p>
<ul>
<li>Registration Code Statistics - used only when the registration code feature is on. </li>
<li>Site Activity - show site activites (read only). </li>
</ul>
<p></p>
<h3>
<a name="S.7_configuration_file">S.7 configuration file</a>
</h3>
<p> Under the forum directory there is a /conf folder, which contains these configuration files: </p>
<ul>
<li>conf.php </li>
<li>linkedin_conf.php </li>
<li>upload_conf.php </li>
</ul>
<p></p>
<h4>
<a name="S.7.1_conf.php">S.7.1 conf.php</a>
</h4>
<p>This allows many settings, for example: </p>
<ul>
<li>Site debug mode (if true, show more details in error/warning information) </li>
<li>Database config file location. </li>
<li>Language. Currently support Chinese and English. </li>
<li>Captcha. Use or not. Length of Captch string. </li>
<li>Registration code. Use or not. </li>
<li>User account email activation. Use or not. </li>
<li>Email. Actually send email or not. </li>
<li>Site name, and site master email. </li>
<li>Time zone. </li>
<li>BBS settings. Various. </li>
<li>Gravatar. Use or not. </li>
<li>IMail settings. Various. </li>
</ul>
<p></p>
<h4>
<a name="S.7.2_db_conf.php">S.7.2 db_conf.php</a>
</h4>
<p>This file contains database connection parameters: </p>
<ul>
<li>host name </li>
<li>database name </li>
<li>database user </li>
<li>database password </li>
</ul>
<p></p>
<h4>
<a name="S.7.3_linkedin_conf.php">S.7.3 linkedin_conf.php</a>
</h4>
<p>This file contains linkedin parameters: </p>
<ul>
<li>API_KEY </li>
<li>API_SECRET </li>
<li>REDIRECT_URL </li>
<li>SCOPE </li>
</ul>
<p></p>
<h4>
<a name="S.7.4_upload_conf.php">S.7.4 upload_conf.php</a>
</h4>
<p> This file contains upload parameters: </p>
<ul>
<li>Max file size. </li>
<li>Allowed file extensions. </li>
<li>Upload file root directory. </li>
</ul>
<p></p>
<hr>
<h2>
<a name="Developer_Documentation">Developer Documentation</a>
</h2>
This is a light-weighted, easy to deploy, use and modify website framework. This section talks about several important aspects if one wants to develop based on this framework.
<h3>
<a name="D.1_Database_development">D.1 Database development</a>
</h3>
<p>To work on the database, one just needs to include either /func/db.php or /func/db_mysqli.php. </p>
<p>These two files implement exactly the same set if API functions. /func/db.php uses the mysql connection system calls, which is deprecated by MySQL since 2012. /func/db_mysqli.php uses the mysqli connection system calls and is preferred. </p>
<p>The API functions are: </p>
<ul>
<li>
db_open()
<br>
Open a database connection.
</li>
<li>
db_close()
<br>
Close a database connection.
</li>
<li>
getScalar($query, $col = '')
<br>
Return a single element in the first row of the returned set.
<br>
If $col is empty, use the first element; otherwise use the named element.
</li>
<li>
executeScalar($query, $col = '')
<br>
Same as getScalar().
</li>
<li>
executeNonQuery($query)
<br>
Execute a query that does not return any data.
</li>
<li>
executeRowCount($query)
<br>
Return number of rows in the returned data set.
</li>
<li>
executeScalarArray($query, $col)
<br>
Return an array of the given field in the query.
</li>
<li>
executeDataTable($query)
<br>
Return entire table (requested in query) as a DataTable.
<br>
First row is for column names.
<br>
The rest rows are data.
</li>
<li>
executeAssociateDataTable($query)
<br>
Return entire table (requested in query) as an associate array.
<br>
Compared to executeDataTable, this shifts the processing to calling function.
<br>
Synopsis:
<pre> $t = executeAssociateDataTable($sql); $len = count($t); for ($i = 0; $i < $len; ++ $i) { $row = $t[$i]; foreach ($row as $key => $val) { print "$key => $val, or value is: $row[$key]"; } } </pre>
</li>
<li>
executeAssociateDataTable_2($query)
<br>
Similar to executeAssociateDataTable(), but the first row is for header columns, other rows are for data.
</li>
<li>
executeDataTable_ToHtmlTable($query, $property="", $show_count, $do_htmlencode)
<br>
Given a query, return a html string, showing the return table.
<br>
$query: value is string. the query string.
<br>
$property: value is string. attribute of the table, e.g., class.
<br>
$show_count: value is boolean. whether to show number of row: 1,2, ...
<br>
$do_htmlencode: value is boolean. whether apply db_htmlencode to contents.
</li>
</ul>
<p> Basically, these functions hide database access details. Returned data are either scalar variable or array, thus easy to munipulate. </p>
<p>Other related functions: </p>
<ul>
<li>
db_htmlEncode($s)
<br>
To display html/xml open/close tags and other special characters from database in browsers correctly.
</li>
<li>
db_encode($s)
<br>
To encode a query to database, avoid special characters or sql injection.
</li>
</ul>
<h3>
<a name="D.2_Other_library_functions">D.2 Other library functions</a>
</h3>
<p>Other library function files also reside in /func/ directory: </p>
<ul>
<li>
ClsPage.php, ClsPage.js
<br>
A class for paging of a long list.
</li>
<li>
Cls_DBTable.php
<br>
A class for manipulating a database table.
<br>
This class will read from database schema and automatically build the view/edit/verify forms.
</li>
<li>
Cls_DBTable_Custom.php
<br>
A class for manipulating a database table.
<br>
<br>
This class will read from database schema and automatically build the view/edit/verify forms.
<br>
Customized from Cls_DBTable with this change:
<br>
1) Instead of providing a list of hidden fields, provide a list of given fields and titles.
<br>
2) Added styles for TB, TR, TD.
<br>
3) Added en/cn languages for buttons.
<br>
<br>
This is convenient when you want to use custom field titles, and in different languages.
</li>
<li>
avatar.php
<br>
Use avatar from gravatar.com
</li>
<li>
captcha.php
<br>
Function to create and use a captcha image made of English letters and digits.
</li>
<li>
captcha_cn.php
<br>
Function to create and use a captcha image made of Chinese characters.
</li>
<li>
email.php
<br>
Email functions.
</li>
<li>
mobile.php
<br>
Decide if the browser client is on mobile device.
</li>
<li>
util.php
<br>
Various utility functions, such as:
<br>
- Getting GET/POST/REQUEST parameters.
<br>
- String functions: startsWith( $haystack, $needle ), endsWith( $haystack, $needle ), str_truncate($s, $maxlen).
<br>
- Get random string: getRandStr($len, $type=1)
<br>
- Convert array to/from selection list.
</li>
<li>
util_fs.php
<br>
File system utility functions. Such as:
<br>
- create/delete directory/file
<br>
- decide if a directory is empty
<br>
- return files under a directory
</li>
</ul>
<h3>
<a name="D.3_Authentication">D.3 Authentication</a>
</h3>
To use the authentication mechanism of this framework is very easy, just include certain authentication check files under directory /func.
<ul>
<li>
/func/auth.php
<br>
Check if a user has signed in.
<br>
Synopsis:
<pre><?php session_start(); require_once("../func/auth.php"); ?> </pre>
</li>
<li>
/func/auth_board_manager.php
<br>
Check if a signed in user is a board master.
<br>
Synopsis:
<pre><?php session_start(); require_once("../func/auth.php"); require_once("../func/auth_board_manager.php"); ?> </pre>
</li>
<li>
/func/auth_admin.php
<br>
Check if a signed in user is a system administrator.
<br>
Synopsis:
<pre><?php session_start(); require_once("../func/auth.php"); require_once("../func/auth_admin.php"); ?> </pre>
</li>
</ul>
<h3>
<a name="D.4_Themes">D.4 Themes</a>
</h3>
<p> This framework allows a central place to create and use new themes in the /theme and /css directory. </p>
<p>The /theme folder contains these files: </p>
<ul>
<li>
header.php, footer.php
<br>
To be included in any other web interface files, for a consistent look.
</li>
<li>
themes.php
<br>
This is where a theme is defined and used.
<br>
Based on the theme name, different css files from /css folder is used.
<br>
Here you can define page title, keywords, description, forum's top row banner image and other elements.
</li>
<li>
share.php
<br>
Include this to display the social media share icons from jiathis.com.
<br>
This file is included in footer.php by default. You can include it in other places too.
</li>
</ul>
<p></p>
<p>The /css folder contains css files for different themes. </p>
<ul>
<li>Right now there are 2 themes: 1) plain and 2) blue. "blue" is the default theme. </li>
</ul>
<p></p>
<p>To create a new theme, you need to: </p>
<ul>
<li>Change /theme/themes.php, for forum's top row banner image and other elements </li>
<li>
In /css, create a new sub-directory, with these css files:
<br>
a) digest.css (for BBS digest),
<br>
b) menu.css (for menu),
<br>
c) bbs.css, bbs_mobile.css (for BBS, both desktop and mobile versions),
<br>
d) site.css, site_mobile.css (for entire site, both desktop and mobile versions).
</li>
</ul>
<p></p>
<h3>
<a name="D.5_File_Upload">D.5 File Upload</a>
</h3>
<p>There are currently two places that upload is used: 1) BBS, 2) I-Mail.</p>
<p> Currently file upload works this way: </p>
<ul>
<li>A file is first uploaded to a temprary folder, then copied to final folder when the user submit the BBS post or I-Mail. </li>
<li>The temp folder is in /upload/[function]/tmp, and final folder is in /upload/[function]/fin. </li>
<li>Under the /tmp or /fin directory, user's username is used as storage folder. </li>
</ul>
<p></p>
<p> Security: </p>
<ul>
<li>For security purpose, an uploaded file's url cannot be easily guessed. </li>
<li>For this, a salt, which is a random string of length ~10 is generated for each BBS post or email, and used as the part of the the storage folder name. </li>
<li>This way it's very hard to guess the url of the uploaded file. </li>
</ul>
<p></p>
<p>To create a new upload function, one needs to: </p>
<ul>
<li>Add corresponding storage folders in /upload </li>
<li>Include files file_upload.php, attachment_func.php as in /bbs and /imail, and make corresponding changes. </li>
<li>BBS and I-Mail's upload functions can be used as examples to understand how to implement a new upload function. </li>
</ul>
<p></p>
<h3>
<a name="D.6_Miscellaneous">D.6 Miscellaneous</a>
</h3>
<h4>
<a name="D.6.1_Google_analytics">D.6.1 Google analytics</a>
</h4>
You can include your analytics code in /js/analytics.php, which is included in theme/footer.php by default.
<h4>
<a name="D.6.2_Social_media_promotion_icons">D.6.2 Social media promotion icons</a>
</h4>
The jiathis.com icon panel is used. The code is in /theme/share.php and included in theme/footer.php by default.
<hr>
<h2>
<a name="To-do_List">To-do List</a>
</h2>
<h3>
<a name="IMail">IMail</a>
</h3>
<ul>
<li>email notification on imail (done. also set up cron job for this) </li>
<li>admin send mail to all members </li>
<li>draft </li>
<li>upload progress by javascript </li>
<li>cc, bcc </li>
<li>receipt notification to sender </li>
</ul>
<h3>
<a name="EMail">EMail</a>
</h3>
<ul>
<li>group mail (done) </li>
<li>attachment </li>
</ul>
<h3>
<a name="Wiki_post">Wiki post</a>
</h3>
<ul>
<li>wiki post: everyone can edit. </li>
</ul>
<h3>
<a name="More_of_user_profile">More of user profile</a>
</h3>
<ul>
<li>In addition to current basic profile, provide a page to get more user details. </li>
</ul>
<h3>
<a name="BBS_post_tag_buttons">BBS post tag buttons</a>
</h3>
<ul>
<li>So user no need to type tags manually </li>
<li>email notification of response </li>
<li>email notification of new post </li>
</ul>
<h3>
<a name="Database_table_name_prefix">Database table name prefix</a>
</h3>
<ul>
<li>
Wordpress and Drupal both allow to use prefix for database tables.
<br>
This avoids potential name conflict, and is a nice feature to have.
</li>
</ul>
<h3>
<a name="Site-wide_Improvements">Site-wide Improvements</a>
</h3>
<ul>
<li>Improve on the modular design of the site, to allow easy addition of new modules. </li>
</ul>
<hr>
<h2>
<a name="Author">Author</a>
</h2>
X. Chen
<br>
Copyright © 2013-2014
<br>
Contact:
<a href="mailto: homecoxoj@gmail.com">Email</a>
|
<a href="https://github.com/chenx/PiBBS">Download</a>
</div>


<script type="text/javascript">

/**
 * Adjust image display.
 */
function disp_img(v) {
    var imgs = document.getElementsByClassName('screenshot');

    for (var i = 0; i < imgs.length; ++i) {
        var item = imgs[i];  

        if (v == 0) { 
            item.style.display = 'none'; 
        }
        else if (v == 1) {  
            item.style.display = 'block';
            item.style.width = '100px'; 
        }
        else if (v == 2) {  
            item.style.display = 'block';
            item.style.width = '300px'; 
        }
        else { 
            item.style.display = 'block';
            item.style.width = '1000px'; 
        }
    }
}
</script>
