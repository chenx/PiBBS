<?php
//
// Site range settings.
//

///////////////////////////////////////////
// Site wide debug option. Will output sensitive information when on.
$_DEBUG = 0; 

///////////////////////////////////////////
// Location of database configuration file.
$_DB_CONF_PATH = "../conf/db_conf.php";

///////////////////////////////////////////
// Location of database dump.
$_DB_BAK_PATH = "../conf/db_bak"; 


///////////////////////////////////////////
// Language of the site.
// Note: if you use captcha, you may want to set a 
// good length for captcha according to language.
$_LANG = "cn"; 
//$$_LANG = "en";


///////////////////////////////////////////
// Redirect url after login in.
// Either root ../, or bbs: ../bbs
$_LOGIN_REDIRECT_URL = "../bbs";


///////////////////////////////////////////
// Value of $_USE_CAPTCHA is the length of captcha.
// If the value is 0, then do not use captcha.
// Note that for captcha length, 1 or 2 can be enough for Chinese,
// but for English 5 or 6 may be better.
$_USE_CAPTCHA = 1;

///////////////////////////////////////////
// Login is different from register or getpwd.
// Login happens often, the other two happens rarely.
// From ease-of-use point of view, it may be a good
// choice to use captcha for register or getpwd, but not for login.
$_USE_CAPTCHA_FOR_LOGIN = 0;


///////////////////////////////////////////
// Whether the user is required to enter registration code.
$_USE_REG_CODE = 0;

// Length of registration code. Up to 20 (storage field size, in code_register)
$_REG_CODE_LEN = 20; 

// Hide registration code page link when there is no registration code.
$_HIDE_REG_CODE_PAGE_WHEN_NONE = 1;

///////////////////////////////////////////
// Whether use account activation function.
// If 1, will send an account activation email;
// If 0, directly set new registered account as activated.
$_USE_ACCOUNT_ACTIVATION = 1;

// Length of activation code, up to 50 (database storage field size, in User).
$_ACTIVATION_CODE_LEN = 40; 


///////////////////////////////////////////
// Whether to actually send email or not. 1 - Yes, 0 - No. Used by func/email.php.
$_USE_EMAIL = 1;

///////////////////////////////////////////
// If 1, use user's real email as from address.
// Otherwise, use a dummy email as from address.
$_USE_USER_EMAIL = 0;

///////////////////////////////////////////
// If true, for group email, email each person individually.
// Otherwise, send as group email.
$_EMAIL_INDIVIDUALLY = 1;


// Default from email address when sending emails.
$_HOST_EMAIL = "webmaster@xcbbs.com";

// Site name, will be used in email as part of url.
$_SITE_NAME = "cssauh.com/xcbbs";

///////////////////////////////////////////
// Time zone setting. Used by date() function.
date_default_timezone_set('America/Los_Angeles');

$_TIMEZONE = "PST";

///////////////////////////////////////////
// Show link to contact_us.
$_USE_CONTACT_US = 0;


/////////////////////////////////////////////
//
// BBS setting
//

// if 0, don't show menu entry and homepage digest, but still accessible at "/bbs".
$_USE_BBS = 1; 

// Show this at the bottom of a post.
$_BBS_POST_SRC = "homecox.com";

// If 1, then only logged in users can see. 
// - unlogged in user will be redirected to logout.php, and then homepage.
//   so the user clicks on a bbs link and sees nothing. This is not very nice UI.
//   So, this should be used in combination with _USE_BBS.
//   In general, BBS_IS_PRIVATE = USE_BBS. But you may want BBS_IS_PRIVATE = 1 but
//   USE_BBS is 0 when you want to access it by yourself while keep it invisible to public.
// If 0, everyone (including those who did not log in) can see.
$_BBS_AUTH_USER_ONLY = 0;

// If 1, only Admin can visit, other users are redirected to homepage.
// This can be used together with $_USE_BBS when only admin is supposed to access BBS.
$_BBS_ADMIN_ONLY = 0; 

// In homepage bbs digest, number of digest posts to show per board.
$_BBS_SHOW_DIGEST_N = 2;

// If this is 1, in the homepage digest, show non-digest posts after digest posts.
// else, only show digest posts.
$_BBS_SHOW_NON_DIGEST = 1;

// Number of non-digest posts to show per board, if BBS_SHOW_NON_DIGEST = 1.
$_BBS_SHOW_NON_DIGEST_N = 2;

// At end of post, show IP until the n-th section delimited by '.'.
$_BBS_IP_NTH = 1;

// Whether show the share bar from http://www.jiathis.com at the end of post.
$_BBS_JIA_THIS_POST = 0;

// Whether show the sharing bar from http://www.jiathis.com for a thread.
$_BBS_JIA_THIS_THREAD = 0;

// Whether to include google analytics.
$_BBS_INCLUDE_ANALYTICS = 1;

// Format of BBS digest table.
$_BBS_DIGEST_FORMAT = 2; // Avalable values: 1, 2. Default is 1.

// Use attachment.
$_BBS_USE_ATTACHMENT = 1;

// Whether to show image as image in attachment.
$_BBS_DISP_ATTACHED_IMAGE = 1;


/////////////////////////////////////////////
// Gravatar
// gravatar.com
$_USE_GRAVATAR = 1;

/////////////////////////////////////////////
$_DEFAULT_AVATAR = "../image/user_icon.png";


/////////////////////////////////////////////
// IMail - Internal Mail
$_USE_IMAIL = 1;

/////////////////////////////////////////////
// If 1, send notify email.
// Else if 0, don't actually send email.
$_IMAIL_NOTIFY = 1;

/////////////////////////////////////////////
// Number of days to wait before another
// email notification.
// If 0, notify only once.
$_IMAIL_NOTIFY_INTERVAL_DAYS = 0;

/////////////////////////////////////////////
// Body message length in notification.
$_IMAIL_NOTIFY_DIGEST_LEN = 102;


/////////////////////////////////////////////
// Whether to use linked in signup/signin. 1 - yes, 0 - no.
$_USE_LINKEDIN_SIGNUP = 0;

?>
