<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Restricted access');

// 1.6.0

define('_KUNENA_NO_REPLIES', 'No Replies');
define('_KUNENA_SHOW_AVATAR_ON_CAT', 'Show Avatar on Categories view, Recent discussions and My Discussions?');
define('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Set to &quot;Yes&quot; if you want to show user avatar on Categories view, Recent discussions and My Discussions.');

define('_COM_A_SHOWIMGFORGUEST', 'Show images for guests');
define('_COM_A_SHOWIMGFORGUEST_DESC', 'Set to &quot;Yes&quot; if you want to show images for guests.');
define('_KUNENA_BBCODE_HIDEIMG', 'This image is hidden for guests. Please login or register to see it.');
define('_COM_A_SHOWFILEFORGUEST', 'Show attachments for guests');
define('_COM_A_SHOWFILEFORGUEST_DESC', 'Set to &quot;Yes&quot; if you want to show attachments for guests.');
define('_KUNENA_BBCODE_HIDEFILE', 'This attachment is hidden for guests. Please login or register to see it.');

//Poll
define('_KUNENA_POLL_CANNOT_VOTE_NEW_TIME', 'You cannot vote an new time to this poll');
define('_KUNENA_POLL_TIME_TO_LIVE', 'Time for the life of the poll');
define('_KUNENA_POLL_FORGOT_TITLE_OPTIONS', 'You have forget to enter a title and options for the poll');
define('_KUNENA_POLL_ADD', 'Add a new poll');
define('_KUNENA_POLL_TITLE', 'Poll title');
define('_KUNENA_ADMIN_POLLS', 'Polls');
define('_KUNENA_POLL_ADD_OPTION', 'Add a new option');
define('_KUNENA_POLL_REM_OPTION', 'Remove an option');
define('_KUNENA_POLL_OPTION_NAME', 'Option');
define('_KUNENA_POLL_BUTTON_VOTE', 'Vote');
define('_KUNENA_POLL_BUTTON_RESULT', 'View results');
define('_KUNENA_POLL_NO_VOTE', 'No vote');
define('_KUNENA_POLL_VOTERS_TOTAL', 'Total number of voters: ');
define('_KUNENA_POLL_HITS_OPTIONS', 'Number of people who voted for this poll');
define('_KUNENA_A_POLL_NUMBER_OPTIONS', 'Number of options for the polls');
define('_KUNENA_POLL_RESET_VOTES', 'Reset votes of the poll');
define('_KUNENA_A_POLL_NUMBER_OPTIONS_DESC', 'Set the number maximum allowed for the field options for the polls when the users create a new post');
define('_KUNENA_POLL_SAVE_ALERT_OK', 'Your vote has been saved with succes');
define('_KUNENA_POLL_SAVE_ALERT_ERROR', 'A problem has prevented your vote from being saved');
define('_KUNENA_POLL_SAVE_VOTE_ALREADY', 'You have already voted for this poll!');
define('_KUNENA_A_POLL_ALLOW_ONE_VOTE', 'Allow user to vote one time for a poll');
define('_KUNENA_A_POLL_ALLOW_ONE_VOTE_DESC', 'Allow user to vote only one time for a poll');
define('_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW', 'The maximum numbers of options has been reached');
define('_KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK', 'You must check a box for vote to this poll!');
define('_KUNENA_A_POLL_ENABLED', 'Enabled');
define('_KUNENA_A_ENABLED_DESC', 'Disable or enable the polls');
define('_KUNENA_POLL_NAME', 'Poll: ');
define('_KUNENA_POLL_OPTIONS', 'Poll options');
define('_KUNENA_A_POLL_TITLE', 'Polls Settings');
define('_KUNENA_POLL_NAME_URL_RESULT', 'Return to the topic');
define('_KUNENA_A_POLL_CATEGORIES_ALLOWED', 'Set the allowed categories for the poll');
define('_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC', 'Set the allowed categories for the poll, this can be a categories or more, add the allowed categories by filling the field below with numbers that you need(with coma between numbers, example: 1,2,3,4)');
define('_KUNENA_POLL_NOT_LOGGED', 'You can not participate to this poll you are not logged.');
define('_KUNENA_USRL_VOTES', 'Votes');
define('_KUNENA_POLLSPOP', 'Number of Popular Polls');
define('_KUNENA_POLLSSTATS', 'Show Popular Poll Stats ');
define('_KUNENA_POLLSTATSDESC', ' If you want to show the Popular Polls, select "Yes." ');
define('_STAT_POPULAR_POLLS_KGSG', 'Polls');
define('_KUNENA_A_POLL_TIME_VOTES', 'Set the time between two votes');
define('_KUNENA_A_POLL_TIME_VOTES_DESC', 'For avoid overload of your server a set a time between two votes of the same user, example 00:15:00 means 15 minutes between two votes');
define('_KUNENA_POLL_WAIT_BEFORE_VOTE', 'You need to wait before vote a new time');
define('_KUNENA_A_POLL_NUMBER_VOTES_BY_USER', 'Set the maximum number for the votes allowed for one user');
define('_KUNENA_A_POLL_NUMBER_VOTES_BY_DESC', 'Set the maximum number for the votes allowed for one user');

define('_KUNENA_POST_EMAIL_NOTIFICATION1', 'A new post has been made to a topic to which you have subscribed on the');
define('_KUNENA_POST_EMAIL_NOTIFICATION2', 'You can administer your subscriptions by following the \'My Profile\' link on the Forum home page after you have logged in on the site. From your profile you can also unsubscribe from the topic.');
define('_KUNENA_POST_EMAIL_NOTIFICATION3', 'Do not answer to this e-mail notification as it is a generated e-mail.');
define('_KUNENA_POST_EMAIL_MOD1', 'A new post has been made to a forum to which you have assigned as moderator on the');
define('_KUNENA_POST_EMAIL_MOD2', 'Please have a look at it after you have logged in on the site.');

// 1.5.8

define('_KUNENA_USRL_REALNAME', 'Real Name');
define('_KUNENA_SEO_SETTINGS', 'SEO Settings');
define('_KUNENA_SEF', 'Search Engine Friendly URLs');
define('_KUNENA_SEF_DESC', 'Select whether or not URLs are optimized for Search Engines. NOTE: Kunena accepts SEF URLs even if this feature has been turned off.');
define('_KUNENA_SEF_CATS', 'Do Not Use Category IDs');
// Please use words from your own (or nearby) language in the next URL, but only using a-z:
define('_KUNENA_SEF_CATS_DESC', 'Slightly better looking URLs: http://www.domain.com/forum/category/123-message . WARNING: If set to "No", Kunena will no longer accept these URLs!');
define('_KUNENA_SEF_UTF8', 'Enable UTF8 Support');
// Please use words from your own (or nearby) language in the next URL, but make sure that they contain UTF8 letters:
define('_KUNENA_SEF_UTF8_DESC', 'Use this option if your SEF URLs are not readable. Result: http://www.domain.com/forum/2-Catégorie/123-Meßage . NOTE: Kunena accepts UTF8 URLs even if this feature has been turned off.');
define('_KUNENA_SYNC_USERS_OPTIONS', 'Options');
define('_KUNENA_SYNC_USERS_CACHE', 'Clean user cache');
define('_KUNENA_SYNC_USERS_CACHE_DESC', 'This function allows user to see hidden forums right away, if you change user group in Joomla (Registered, Author etc).');
define('_KUNENA_SYNC_USERS_ADD', 'Add user profiles to everyone');
define('_KUNENA_SYNC_USERS_ADD_DESC', 'Kunena adds new user profiles only if user enters to the forum. This function makes default profiles to all existing users.');
define('_KUNENA_SYNC_USERS_DEL', 'Remove user profiles from deleted users');
define('_KUNENA_SYNC_USERS_DEL_DESC', 'Kunena does not remove user profiles from deleted users, it just hides them. This option allows you to remove all deleted profiles.');
define('_KUNENA_SYNC_USERS_RENAME', 'Update user names in messages');
define('_KUNENA_SYNC_USERS_RENAME_DESC', 'This option will reset all author names in posts to username or real name depending on your Kunena configuration.');
define('_KUNENA_SYNC_USERS_DO_CACHE', 'User cache cleaned');
define('_KUNENA_SYNC_USERS_DO_ADD', 'User profiles added:');
define('_KUNENA_SYNC_USERS_DO_DEL', 'User profiles removed:');
define('_KUNENA_SYNC_USERS_DO_RENAME', 'Messages updated:');

// 1.5.7

define('_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1', 'created a new topic');
define('_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2', 'in the forums.');
define('_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1', 'replied to the topic');
define('_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2', 'in the forums.');

define('_KUNENA_AUP_ALPHAUSERPOINTS', 'AlphaUserPoints');
define('_KUNENA_AUP_ENABLED_POINTS_IN_PROFILE', 'Enabled Points in profile');
define('_KUNENA_AUP_ENABLED_POINTS_IN_PROFILE_DESC', 'If you have AlphaUserPoints installed, you can configure Kunena to show a user&#700;s current points in their profiles.');
define('_KUNENA_AUP_ENABLED_RULES', 'Enabled Rules for Points');
define('_KUNENA_AUP_ENABLED_RULES_DESC', 'You can use the pre-installed rules in AlphaUserPoints to attribute points on new topics and replies. You must have AlphaUserPoints 1.5.3 or later installed. If you have an older version, you&#700;ll need to manually install the rules (see the documentation for AlphaUserPoints).');
define('_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY', 'Minimum characters on reply');
define('_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY_DESC', 'Minimum characters in reply text to earn points on reply topic.');
define('_KUNENA_AUP_MESSAGE_TOO_SHORT', 'Your response was too short to receive any new points.');
define('_KUNENA_AUP_POINTS', 'Points:');

// 1.0.11 and 1.5.4
define('_KUNENA_MOVED', 'Moved');

// 1.0.11 and 1.5.3
define('_KUNENA_VERSION_SVN', 'SVN Revision');
define('_KUNENA_VERSION_DEV', 'Development Snapshot');
define('_KUNENA_VERSION_ALPHA', 'Alpha Release');
define('_KUNENA_VERSION_BETA', 'Beta Release');
define('_KUNENA_VERSION_RC', 'Release Candidate');
define('_KUNENA_VERSION_INSTALLED', 'You have installed Kunena %s (%s).');
define('_KUNENA_VERSION_SVN_WARNING', 'Never use an SVN revision for anything else other than software development!');
define('_KUNENA_VERSION_DEV_WARNING', 'This internal release should be used only by developers and testers!');
define('_KUNENA_VERSION_ALPHA_WARNING', 'This release should not be used on live production sites.');
define('_KUNENA_VERSION_BETA_WARNING', 'This release is not recommended to be used on live production sites.');
define('_KUNENA_VERSION_RC_WARNING', 'This release may contain bugs, which will be fixed in the final version.');
define('_KUNENA_ERROR_UPGRADE', 'Upgrading Kunena to version %s has failed!');
define('_KUNENA_ERROR_UPGRADE_WARN', 'Your forum may be missing some important fixes and some features may be broken.');
define('_KUNENA_ERROR_UPGRADE_AGAIN', 'Please try to upgrade again. If you cannot upgrade to Kunena %s, you can easily downgrade to the latest working version.');
define('_KUNENA_PAGE', 'Page');
define('_KUNENA_RANK_NO_ASSIGNED', 'No Rank Assigned');
define('_KUNENA_INTEGRATION_CB_WARN_GENERAL', 'Problems detected in Community Builder integration:');
define('_KUNENA_INTEGRATION_CB_WARN_INSTALL', 'Community Builder integration only works if you have Community Builder version %s or higher installed.');
define('_KUNENA_INTEGRATION_CB_WARN_PUBLISH', 'Community Builder Profile integration only works if Community Builder User profile has been published.');
define('_KUNENA_INTEGRATION_CB_WARN_UPDATE', 'Community Builder Profile integration only works if you are using Community Builder version %s or higher.');
define('_KUNENA_INTEGRATION_CB_WARN_XHTML', 'Community Builder Profile integration only works if Community Builder is in W3C XHTML 1.0 Trans. compliance mode.');
define('_KUNENA_INTEGRATION_CB_WARN_INTEGRATION', 'Community Builder Profile integration only works if the forum integration plugin has been enabled in Community Builder.');
define('_KUNENA_INTEGRATION_CB_WARN_HIDE', 'Saving the Kunena configuration will disable integration and hide this warning.');

// 1.0.10
define('_KUNENA_BACK', 'Back');
define('_KUNENA_SYNC', 'Sync');
define('_KUNENA_NEW_SMILIE', 'New Smilie');
define('_KUNENA_PRUNE', 'Prune');
// Editor
define('_KUNENA_EDITOR_HELPLINE_BOLD', 'Bold text: [b]text[/b]');
define('_KUNENA_EDITOR_HELPLINE_ITALIC', 'Italic text: [i]text[/i]');
define('_KUNENA_EDITOR_HELPLINE_UNDERL', 'Underline text: [u]text[/u]');
define('_KUNENA_EDITOR_HELPLINE_STRIKE', 'Strikethrough Text: [strike]Text[/strike]');
define('_KUNENA_EDITOR_HELPLINE_SUB', 'Subscript Text: [sub]Text[/sub]');
define('_KUNENA_EDITOR_HELPLINE_SUP', 'Superscript Text: [sup]Text[/sup]');
define('_KUNENA_EDITOR_HELPLINE_QUOTE', 'Quote text: [quote]text[/quote]');
define('_KUNENA_EDITOR_HELPLINE_CODE', 'Code display: [code]code[/code]');
define('_KUNENA_EDITOR_HELPLINE_UL', 'Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items');
define('_KUNENA_EDITOR_HELPLINE_OL', 'Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items');
define('_KUNENA_EDITOR_HELPLINE_LI', 'List Item: [li] list item [/li]');
define('_KUNENA_EDITOR_HELPLINE_ALIGN_LEFT', 'Align left: [left]Text[/left]');
define('_KUNENA_EDITOR_HELPLINE_ALIGN_CENTER', 'Align center: [center]Text[/center]');
define('_KUNENA_EDITOR_HELPLINE_ALIGN_RIGHT', 'Align right: [right]Text[/right]');
define('_KUNENA_EDITOR_HELPLINE_IMAGELINK', 'Image link: [img size=400]http://www.google.com/images/web_logo_left.gif[/img]');
define('_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE', 'Image link: Size');
define('_KUNENA_EDITOR_HELPLINE_IMAGELINKURL', 'Image link: URL of the image link');
define('_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY', 'Image link: Apply image link');
define('_KUNENA_EDITOR_HELPLINE_LINK', 'Link: [url=http://www.zzz.com/]This is a link[/url]');
define('_KUNENA_EDITOR_HELPLINE_LINKURL', 'Link: URL of the link');
define('_KUNENA_EDITOR_HELPLINE_LINKTEXT', 'Link: Text / Description of the link');
define('_KUNENA_EDITOR_HELPLINE_LINKAPPLY', 'Link: Apply link');
define('_KUNENA_EDITOR_HELPLINE_HIDE','Hidden text: [hide]any hidden text[/hide] - hide part of message from Guests');
define('_KUNENA_EDITOR_HELPLINE_SPOILER', 'Spoiler: Text is only shown after you click the spoiler');
define('_KUNENA_EDITOR_HELPLINE_COLOR', 'Color: [color=#FF6600]text[/color]');
define('_KUNENA_EDITOR_HELPLINE_FONTSIZE', 'Fontsize: [size=1]text size[/size] - Tip: sizes range from 1 to 5');
define('_KUNENA_EDITOR_HELPLINE_FONTSIZESELECTION', 'Fontsize: Select Fontsize, mark text and press the button left from here');
define('_KUNENA_EDITOR_HELPLINE_EBAY', 'eBay: [ebay]ItemId[/ebay]');
define('_KUNENA_EDITOR_HELPLINE_VIDEO', 'Video: Select Provider or URL - modus');
define('_KUNENA_EDITOR_HELPLINE_VIDEOSIZE', 'Video: Size of the video');
define('_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH', 'Video: Width of the video');
define('_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT', 'Video: Height of the video');
define('_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER', 'Video: Select video provider');
define('_KUNENA_EDITOR_HELPLINE_VIDEOID', 'Video: ID of the video - you can see it in the video URL');
define('_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1', 'Video: [video size=100 width=480 height=360 provider=clipfish]3423432[/video]');
define('_KUNENA_EDITOR_HELPLINE_VIDEOURL', 'Video: URL of the video');
define('_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2', 'Video: [video size=100 width=480 height=360]http://myvideodomain.com/myvideo[/video]');
define('_KUNENA_EDITOR_HELPLINE_IMGPH', 'Insert [img] placeholder in the post for attached image');
define('_KUNENA_EDITOR_HELPLINE_FILEPH', 'Insert [file] placeholder in the post for attached file');
define('_KUNENA_EDITOR_HELPLINE_SUBMIT', 'Click here to submit your message');
define('_KUNENA_EDITOR_HELPLINE_PREVIEW', 'Click here to see what your message will look like when submitted');
define('_KUNENA_EDITOR_HELPLINE_CANCEL', 'Click here to cancel your message');
define('_KUNENA_EDITOR_HELPLINE_HINT', 'bbCode Help - Tip: bbCode can be used on selected text!');
define('_KUNENA_EDITOR_LINK_URL', ' URL: ');
define('_KUNENA_EDITOR_LINK_TEXT', ' Text: ');
define('_KUNENA_EDITOR_LINK_INSERT', 'Insert');
define('_KUNENA_EDITOR_IMAGE_SIZE', ' Size: ');
define('_KUNENA_EDITOR_IMAGE_URL', ' URL: ');
define('_KUNENA_EDITOR_IMAGE_INSERT', 'Insert');
define('_KUNENA_EDITOR_VIDEO_SIZE', 'Size: ');
define('_KUNENA_EDITOR_VIDEO_WIDTH', 'Width: ');
define('_KUNENA_EDITOR_VIDEO_HEIGHT', 'Height:');
define('_KUNENA_EDITOR_VIDEO_URL', 'URL: ');
define('_KUNENA_EDITOR_VIDEO_ID', 'ID: ');
define('_KUNENA_EDITOR_VIDEO_PROVIDER', 'Provider: ');
define('_KUNENA_BBCODE_HIDDENTEXT', '<span class="fb_quote">Something is hidden for guests. Please log in or register to see it.</span>');

define('_KUNENA_PROFILE_BIRTHDAY', 'Birthday');
define('_KUNENA_DT_MONTHDAY_FMT','%m/%d');
define('_KUNENA_CFC_FILENAME','CSS file to be modified');
define('_KUNENA_CFC_SAVED','CSS file saved.');
define('_KUNENA_CFC_NOTSAVED','CSS file not saved.');
define('_KUNENA_JS_WARN_NAME_MISSING','Your name is missing');
define('_KUNENA_JS_WARN_UNAME_MISSING','Your username is missing');
define('_KUNENA_JS_WARN_VALID_AZ09','Field contains forbidden letters');
define('_KUNENA_JS_WARN_MAIL_MISSING','E-mail address is missing');
define('_KUNENA_JS_WARN_PASSWORD2','Please enter valid password');
define('_KUNENA_JS_PROMPT_UNAME','Please retype your new username');
define('_KUNENA_JS_PROMPT_PASS','Please retype your new password');
define('_KUNENA_DT_LMON_DEC', 'December');
define('_KUNENA_DT_MON_DEC', 'Dec');
define('_KUNENA_NOGENDER', 'Unknown');
define('_KUNENA_ERROR_INCOMPLETE_ERROR', 'Your Kunena installation is incomplete!');
define('_KUNENA_ERROR_INCOMPLETE_OFFLINE', 'Because of the above errors your Forum is now Offline and Forum Administration has been disabled.');
define('_KUNENA_ERROR_INCOMPLETE_REASONS', 'Possible reasons for this error:');
define('_KUNENA_ERROR_INCOMPLETE_1', '1) Kunena installation process has failed or timed out (try to install it again)');
define('_KUNENA_ERROR_INCOMPLETE_2', '2) You have manually modified or removed some of the Kunena tables from your database');
define('_KUNENA_ERROR_INCOMPLETE_3', 'You can find solutions to the most common issues on our community documentation wiki: <a href="http://docs.kunena.com/index.php/Installation_Issues">Kunena Documentation Wiki</a>');
define('_KUNENA_ERROR_INCOMPLETE_SUPPORT', 'Our support forum can be found from:');

// 1.0.9
define('_KUNENA_INSTALLED_VERSION', 'Installed version');
define('_KUNENA_COPYRIGHT', 'Copyright');
define('_KUNENA_LICENSE', 'License');
define('_KUNENA_PROFILE_NO_USER', 'User does not exist.');
define('_KUNENA_PROFILE_NOT_FOUND', 'User has not yet visited forum and has no profile.');

// Search
define('_KUNENA_SEARCH_RESULTS', 'Search Results');
define('_KUNENA_SEARCH_ADVSEARCH', 'Advanced Search');
define('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Search by Keyword');
define('_KUNENA_SEARCH_KEYWORDS', 'Keywords');
define('_KUNENA_SEARCH_SEARCH_POSTS', 'Search entire posts');
define('_KUNENA_SEARCH_SEARCH_TITLES', 'Search titles only');
define('_KUNENA_SEARCH_SEARCHBY_USER', 'Search by User Name');
define('_KUNENA_SEARCH_UNAME', 'User Name');
define('_KUNENA_SEARCH_EXACT', 'Exact Name');
define('_KUNENA_SEARCH_USER_POSTED', 'Messages posted by');
define('_KUNENA_SEARCH_USER_STARTED', 'Threads started by');
define('_KUNENA_SEARCH_USER_ACTIVE', 'Activity in threads');
define('_KUNENA_SEARCH_OPTIONS', 'Search Options');
define('_KUNENA_SEARCH_FIND_WITH', 'Find Threads with');
define('_KUNENA_SEARCH_LEAST', 'At least');
define('_KUNENA_SEARCH_MOST', 'At most');
define('_KUNENA_SEARCH_ANSWERS', 'Answers');
define('_KUNENA_SEARCH_FIND_POSTS', 'Find Posts from');
define('_KUNENA_SEARCH_DATE_ANY', 'Any date');
define('_KUNENA_SEARCH_DATE_LASTVISIT', 'Last visit');
define('_KUNENA_SEARCH_DATE_YESTERDAY', 'Yesterday');
define('_KUNENA_SEARCH_DATE_WEEK', 'A week ago');
define('_KUNENA_SEARCH_DATE_2WEEKS', '2 weeks ago');
define('_KUNENA_SEARCH_DATE_MONTH', 'A month ago');
define('_KUNENA_SEARCH_DATE_3MONTHS', '3 months ago');
define('_KUNENA_SEARCH_DATE_6MONTHS', '6 months ago');
define('_KUNENA_SEARCH_DATE_YEAR', 'A year ago');
define('_KUNENA_SEARCH_DATE_NEWER', 'And newer');
define('_KUNENA_SEARCH_DATE_OLDER', 'And older');
define('_KUNENA_SEARCH_SORTBY', 'Sort Results by');
define('_KUNENA_SEARCH_SORTBY_TITLE', 'Title');
define('_KUNENA_SEARCH_SORTBY_POSTS', 'Number of posts');
define('_KUNENA_SEARCH_SORTBY_VIEWS', 'Number of views');
define('_KUNENA_SEARCH_SORTBY_START', 'Thread start date');
define('_KUNENA_SEARCH_SORTBY_POST', 'Posting date');
define('_KUNENA_SEARCH_SORTBY_USER', 'User name');
define('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
define('_KUNENA_SEARCH_SORTBY_INC', 'Increasing order');
define('_KUNENA_SEARCH_SORTBY_DEC', 'Decreasing order');
define('_KUNENA_SEARCH_START', 'Jump to Result Number');
define('_KUNENA_SEARCH_LIMIT5', 'Show 5 Search Results');
define('_KUNENA_SEARCH_LIMIT10', 'Show 10 Search Results');
define('_KUNENA_SEARCH_LIMIT15', 'Show 15 Search Results');
define('_KUNENA_SEARCH_LIMIT20', 'Show 20 Search Results');
define('_KUNENA_SEARCH_SEARCHIN', 'Search in Categories');
define('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'All Categories');
define('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Also search in child forums');
define('_KUNENA_SEARCH_SEND', 'Search');
define('_KUNENA_SEARCH_CANCEL', 'Cancel');
define('_KUNENA_SEARCH_ERR_NOPOSTS', 'No messages containing all your search terms were found.');
define('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'At least one keyword should be over 3 characters long!');

// 1.0.8
define('_KUNENA_CATID', 'ID');
define('_POST_NOT_MODERATOR', 'You don\'t have moderator permissions!');
define('_POST_NO_FAVORITED_TOPIC', 'This thread has <b>NOT</b> been added to your favorites');
define('_COM_C_SYNCEUSERSDESC', 'Sync the Kunena user table with the Joomla user table');
define('_POST_FORGOT_EMAIL', 'You forgot to include your e-mail address.  Click your browser&#146s back button to go back and try again.');
define('_KUNENA_POST_DEL_ERR_FILE', 'Everything deleted. Some attachment files were missing!');
// New strings for initial forum setup. Replacement for legacy sample data
define('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
define('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Main Forum');
define('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.');
define('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'In order to provide additional information for you guests and members, the forum header can be leveraged to display text at the very top of a particular category.');
define('_KUNENA_SAMPLE_FORUM1_TITLE', 'Welcome Mat');
define('_KUNENA_SAMPLE_FORUM1_DESC', 'We encourage new members to post a short introduction of themselves in this forum category. Get to know each other and share you common interests.
');
define('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Welcome to the Kunena forum![/b]

Tell us and our members who you are, what you like and why you became a member of this site.
We welcome all new members and hope to see you around a lot!
');
define('_KUNENA_SAMPLE_FORUM2_TITLE', 'Suggestion Box');
define('_KUNENA_SAMPLE_FORUM2_DESC', 'Have some feedback and input to share?
Don\'t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.');
define('_KUNENA_SAMPLE_FORUM2_HEADER', 'This is the optional Forum header for the Suggestion Box.
');
define('_KUNENA_SAMPLE_POST1_SUBJECT', 'Welcome to Kunena!');
define('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Welcome to Kunena![/b][/size]

Thank you for choosing Kunena for your community forum needs in Joomla.

Kunena, translated from Swahili meaning "to speak", is built by a team of open source professionals with the goal of providing a top-quality, tightly unified forum solution for Joomla. Kunena even supports social networking components like Community Builder and JomSocial.


[size=4][b]Additional Kunena Resources[/b][/size]

[b]Kunena Documentation:[/b] [url]http://www.kunena.com/docs[/url]

[b]Kunena Support Forum[/b]: [url]http://www.kunena.com/forum[/url]

[b]Kunena Downloads:[/b] [url]http://www.kunena.com/downloads[/url]

[b]Kunena Blog:[/b] [url]http://www.kunena.com/blog[/url]

[b]Submit your feature ideas:[/b] [url]http://www.kunena.com/uservoice[/url]

[b]Follow Kunena on Twitter:[/b] [url]http://www.kunena.com/twitter[/url]
');

// 1.0.6
define('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
define('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
define('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the Kunena code tag highlighting Javascript. If your members post PHP or other code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from becoming malformed.');
define('_COM_A_RSS_TYPE', 'Default RSS type');
define('_COM_A_RSS_TYPE_DESC', 'Choose between RSS feeds &quot;By Thread &quot; or &quot;By Post.&quot; &quot;By Thread &quot; means that only one entry per thread will be listed in the RSS feed independent of how many posts have been made within that thread. &quot;By Thread&quot; creates a shorter, more compact RSS feed but will not list every reply.');
define('_COM_A_RSS_BY_THREAD', 'By Thread');
define('_COM_A_RSS_BY_POST', 'By Post');
define('_COM_A_RSS_HISTORY', 'RSS History');
define('_COM_A_RSS_HISTORY_DESC', 'Select how much history should be included in the RSS feed. The default is one month, but it is recommended to limit it to one week on high volume sites.');
define('_COM_A_RSS_HISTORY_WEEK', '1 Week');
define('_COM_A_RSS_HISTORY_MONTH', '1 Month');
define('_COM_A_RSS_HISTORY_YEAR', '1 Year');
define('_COM_A_FBDEFAULT_PAGE', 'Default Kunena Page');
define('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default Kunena page that is displayed when a forum link is clicked or the forum is initially entered. Default is Recent Discussions. Should be set to Categories for templates other than default. If My Discussions is selected, guests will default to Recent Discussions.');
define('_COM_A_FBDEFAULT_PAGE_RECENT', 'Recent Discussions');
define('_COM_A_FBDEFAULT_PAGE_MY', 'My Discussions');
define('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categories');
define('_KUNENA_BBCODE_HIDE', 'The following is hidden from unregistered users:');
define('_KUNENA_BBCODE_SPOILER', 'Warning: Spoiler!');
define('_KUNENA_FORUM_SAME_ERR', 'The parent forum must not be the same.');
define('_KUNENA_FORUM_OWNCHILD_ERR', 'The parent forum is one of its own children.');
define('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID does not exist.');
define('_KUNENA_RECURSION', 'Recursion detected.');
define('_POST_FORGOT_NAME_ALERT', 'You forgot to enter your name.');
define('_POST_FORGOT_EMAIL_ALERT', 'You forgot to enter your e-mail.');
define('_POST_FORGOT_SUBJECT_ALERT', 'You forgot to enter a subject.');
define('_KUNENA_EDIT_TITLE', 'Edit Your Details');
define('_KUNENA_YOUR_NAME', 'Your Name:');
define('_KUNENA_EMAIL', 'E-mail:');
define('_KUNENA_UNAME', 'User Name:');
define('_KUNENA_PASS', 'Password:');
define('_KUNENA_VPASS', 'Verify Password:');
define('_KUNENA_USER_DETAILS_SAVE', 'User details have been saved.');
define('_KUNENA_TEAM_CREDITS', 'Credits');
define('_COM_A_BBCODE', 'BBCode');
define('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
define('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
define('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
define('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
define('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
define('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
define('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
define('_COM_A_TRIMLONGURLS', 'Trim long URLs');
define('_COM_A_TRIMLONGURLS_DESC', 'Set to &quot;Yes&quot; if you want long URLs to be trimmed. See URL trim front and back settings.');
define('_COM_A_TRIMLONGURLSFRONT', 'Front portion of trimmed URLs');
define('_COM_A_TRIMLONGURLSFRONT_DESC', 'Number of characters for front portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
define('_COM_A_TRIMLONGURLSBACK', 'Back portion of trimmed URLs');
define('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
define('_COM_A_AUTOEMBEDYOUTUBE', 'Auto embed YouTube videos');
define('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Set to &quot;Yes&quot; if you want youtube video urls to get automatically embedded.');
define('_COM_A_AUTOEMBEDEBAY', 'Auto embed eBay items');
define('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
define('_COM_A_EBAYLANGUAGECODE', 'eBay widget language code');
define('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
define('_COM_A_KUNENA_SESSION_TIMEOUT', 'Session Lifetime');
define('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and the NEW indicator are reset.');

// Advanced administrator merge-split functions
define('_GEN_MERGE', 'Merge');
define('_VIEW_MERGE', '');
define('_POST_MERGE_TOPIC', 'Merge this thread with');
define('_POST_MERGE_GHOST', 'Leave ghost copy of thread');
define('_POST_SUCCESS_MERGE', 'Thread successfully merged.');
define('_POST_TOPIC_NOT_MERGED', 'Merge failed.');
define('_GEN_SPLIT', 'Split');
define('_GEN_DOSPLIT', 'Go');
define('_VIEW_SPLIT', '');
define('_POST_SUCCESS_SPLIT', 'Thread split successfully.');
define('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Topic successfully changed.');
define('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Topic change failed.');
define('_POST_TOPIC_NOT_SPLIT', 'Split failed.');
define('_POST_DUPLICATE_IGNORED', 'Duplicate. Identical message has been ignored.');
define('_POST_SPLIT_HINT', '<br />Tip: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
define('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
define('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
define('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
define('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
define('_POST_MERGE', 'merge');
define('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
define('_POST_INVERSE_MERGE', 'inverse merge');
define('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
define('_POST_UNFAVORITED_TOPIC', 'This thread has been removed from your favorites.');
define('_POST_NO_UNFAVORITED_TOPIC', 'This thread has <b>NOT</b> been removed from your favorites.');
define('_POST_SUCCESS_UNFAVORITE', 'Your request to remove from favorites has been processed.');
define('_POST_UNSUBSCRIBED_TOPIC', 'This thread has been removed from your subscriptions.');
define('_POST_NO_UNSUBSCRIBED_TOPIC', 'This thread has <b>NOT</b> been removed from your subscriptions.');
define('_POST_SUCCESS_UNSUBSCRIBE', 'Your request to remove from subscriptions has been processed.');
define('_POST_NO_DEST_CATEGORY', 'No destination category was selected. Nothing was moved.');
// Default_EX template
define('_KUNENA_ALL_DISCUSSIONS', 'Recent Discussions');
define('_KUNENA_MY_DISCUSSIONS', 'My Discussions');
define('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
define('_KUNENA_CATEGORY', 'Category:');
define('_KUNENA_CATEGORIES', 'Categories');
define('_KUNENA_POSTED_AT', 'Posted');
define('_KUNENA_AGO', 'ago');
define('_KUNENA_DISCUSSIONS', 'Discussions');
define('_KUNENA_TOTAL_THREADS', 'Total Threads:');
define('_SHOW_DEFAULT', 'Default');
define('_SHOW_MONTH', 'Month');
define('_SHOW_YEAR', 'Year');

// 1.0.4
define('_KUNENA_COPY_FILE', 'Copying "%src%" to "%dst%"...');
define('_KUNENA_COPY_OK', 'OK');
define('_KUNENA_CSS_SAVE', 'Saving CSS file should be here: file="%file%"');
define('_KUNENA_UP_ATT_10', 'The attachment table was successfully upgraded to the latest 1.0.x series structure.');
define('_KUNENA_UP_ATT_10_MSG', 'The attachments in the message table were successfully upgraded to the latest 1.0.x series structure.');
define('_KUNENA_TOPIC_MOVED', '---');
define('_KUNENA_TOPIC_MOVED_LONG', '------------');
define('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
define('_KUNENA_POST_DEL_ERR_MSG', 'Could not delete the post(s). Nothing else deleted.');
define('_KUNENA_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
define('_KUNENA_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats.');
define('_KUNENA_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum.");
define('_KUNENA_UNIST_SUCCESS', "The Kunena Forum component was successfully uninstalled.");
define('_KUNENA_PDF_VERSION', 'Kunena Forum Component version: %version%');
define('_KUNENA_PDF_DATE', 'Generated: %date%');
define('_KUNENA_SEARCH_NOFORUM', 'No forums to search in.');

define('_KUNENA_ERRORADDUSERS', 'Error adding users:');
define('_KUNENA_USERSSYNCDELETED', 'Users syncronized. Deleted:');
define('_KUNENA_USERSSYNCADD', ', add:');
define('_KUNENA_SYNCUSERPROFILES', 'user profiles.');
define('_KUNENA_NOPROFILESFORSYNC', 'No eligible profiles found to synchronize.');
define('_KUNENA_SYNC_USERS', 'Synchronize Users');
define('_KUNENA_SYNC_USERS_DESC', 'Synchronize the Kunena user table with the Joomla user table.');
define('_KUNENA_A_MAIL_ADMIN', 'E-mail Administrators');
define('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want e-mail notifications on each new post sent to the enabled system administrator(s).');
define('_KUNENA_RANKS_EDIT', 'Edit Rank');
define('_KUNENA_USER_HIDEEMAIL', 'Hide E-mail');
define('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
define('_KUNENA_DT_TIME_FMT','%H:%M');
define('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
define('_KUNENA_DT_LDAY_SUN', 'Sunday');
define('_KUNENA_DT_LDAY_MON', 'Monday');
define('_KUNENA_DT_LDAY_TUE', 'Tuesday');
define('_KUNENA_DT_LDAY_WED', 'Wednesday');
define('_KUNENA_DT_LDAY_THU', 'Thursday');
define('_KUNENA_DT_LDAY_FRI', 'Friday');
define('_KUNENA_DT_LDAY_SAT', 'Saturday');
define('_KUNENA_DT_DAY_SUN', 'Sun');
define('_KUNENA_DT_DAY_MON', 'Mon');
define('_KUNENA_DT_DAY_TUE', 'Tue');
define('_KUNENA_DT_DAY_WED', 'Wed');
define('_KUNENA_DT_DAY_THU', 'Thu');
define('_KUNENA_DT_DAY_FRI', 'Fri');
define('_KUNENA_DT_DAY_SAT', 'Sat');
define('_KUNENA_DT_LMON_JAN', 'January');
define('_KUNENA_DT_LMON_FEB', 'February');
define('_KUNENA_DT_LMON_MAR', 'March');
define('_KUNENA_DT_LMON_APR', 'April');
define('_KUNENA_DT_LMON_MAY', 'May');
define('_KUNENA_DT_LMON_JUN', 'June');
define('_KUNENA_DT_LMON_JUL', 'July');
define('_KUNENA_DT_LMON_AUG', 'August');
define('_KUNENA_DT_LMON_SEP', 'September');
define('_KUNENA_DT_LMON_OCT', 'October');
define('_KUNENA_DT_LMON_NOV', 'November');
define('_KUNENA_DT_MON_JAN', 'Jan');
define('_KUNENA_DT_MON_FEB', 'Feb');
define('_KUNENA_DT_MON_MAR', 'Mar');
define('_KUNENA_DT_MON_APR', 'Apr');
define('_KUNENA_DT_MON_MAY', 'May');
define('_KUNENA_DT_MON_JUN', 'Jun');
define('_KUNENA_DT_MON_JUL', 'Jul');
define('_KUNENA_DT_MON_AUG', 'Aug');
define('_KUNENA_DT_MON_SEP', 'Sep');
define('_KUNENA_DT_MON_OCT', 'Oct');
define('_KUNENA_DT_MON_NOV', 'Nov');
define('_KUNENA_CHILD_BOARD', 'Child Board');
define('_WHO_ONLINE_GUEST', 'Guest');
define('_WHO_ONLINE_MEMBER', 'Member');
define('_KUNENA_IMAGE_PROCESSOR_NONE', 'none');
define('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
define('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Click here to continue...');
define('_KUNENA_INSTALL_APPLY', 'Apply!');
define('_KUNENA_NO_ACCESS', 'You do not have access to this forum!');
define('_KUNENA_TIME_SINCE', '%time% ago');
define('_KUNENA_DATE_YEARS', 'Years');
define('_KUNENA_DATE_MONTHS', 'Months');
define('_KUNENA_DATE_WEEKS','Weeks');
define('_KUNENA_DATE_DAYS', 'Days');
define('_KUNENA_DATE_HOURS', 'Hours');
define('_KUNENA_DATE_MINUTES', 'Minutes');
// 1.0.2
define('_KUNENA_HEADERADD', 'Forum header:');
define('_KUNENA_ADVANCEDDISPINFO', "Forum display");
define('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
define('_KUNENA_CLASS_SFXDESC', "CSS suffixes applied to index, showcat, view, and allow for different designs per forum.");
define('_COM_A_USER_EDIT_TIME', 'User Edit Time');
define('_COM_A_USER_EDIT_TIME_DESC', 'Set to 0 for unlimited time, else window
in seconds from post or last modification to allow edit.');
define('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
define('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], allows
storing a modification up to 600 seconds after edit link disappears');
define('_KUNENA_HELPPAGE','Enable Help Page');
define('_KUNENA_HELPPAGE_DESC','If set to &quot;Yes,&quot; a link to your help page will be shown in the header menu.');
define('_KUNENA_HELPPAGE_IN_FB','Show help in Kunena');
define('_KUNENA_HELPPAGE_IN_KUNENA_DESC','If set to &quot;Yes,&quot; help content will be included in Kunena and the external Help page link will be disabled. <b>Note:</b> you should add a Help Content ID.');
define('_KUNENA_HELPPAGE_CID','Help Content ID');
define('_KUNENA_HELPPAGE_CID_DESC','You should set <b>&quot;YES&quot;</b> &quot;Show help in Kunena&quot; setting.');
define('_KUNENA_HELPPAGE_LINK',' Help external page link');
define('_KUNENA_HELPPAGE_LINK_DESC','If you show help external link, please set <b>&quot;NO&quot;</b> &quot;Show help in Kunena&quot; setting.');
define('_KUNENA_RULESPAGE','Enable Rules Page');
define('_KUNENA_RULESPAGE_DESC','If set to &quot;Yes,&quot; a link to your rules page will be shown in the header menu.');
define('_KUNENA_RULESPAGE_IN_FB','Show rules in Kunena');
define('_KUNENA_RULESPAGE_IN_KUNENA_DESC','If set to &quot;Yes,&quot; rules content text will be included in Kunena and the external rules page link will be disabled. <b>Note:</b> you should add a Rules Content ID.');
define('_KUNENA_RULESPAGE_CID','Rules Content ID');
define('_KUNENA_RULESPAGE_CID_DESC','You should set <b>&quot;YES&quot;</b> &quot;Show rules in Kunena&quot; setting.');
define('_KUNENA_RULESPAGE_LINK',' Rules external page link');
define('_KUNENA_RULESPAGE_LINK_DESC','If you show rules external link, please set <b>&quot;NO&quot;</b> &quot;Show rules in Kunena&quot; setting.');
define('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library not found');
define('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Library not found');
define('_KUNENA_GD_INSTALLED','GD is available, version&#32;');
define('_KUNENA_GD_NO_VERSION','Can not detect GD version');
define('_KUNENA_GD_NOT_INSTALLED','GD is not installed. You can get more info&#32;');
define('_KUNENA_AVATAR_SMALL_HEIGHT','Small Image Height :');
define('_KUNENA_AVATAR_SMALL_WIDTH','Small Image Width :');
define('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium Image Height :');
define('_KUNENA_AVATAR_MEDIUM_WIDTH','Medium Image Width :');
define('_KUNENA_AVATAR_LARGE_HEIGHT','Large Image Height :');
define('_KUNENA_AVATAR_LARGE_WIDTH','Large Image Width :');
define('_KUNENA_AVATAR_QUALITY','Avatar Quality');
define('_KUNENA_WELCOME','Welcome to Kunena!');
define('_KUNENA_WELCOME_DESC','Thank you for choosing Kunena as your forum solution. This screen will give you a quick overview of your board statistics. The links on the left-hand side of this screen allow you to control every aspect of your board setup. Each page has instructions on how to use the tools.');
define('_KUNENA_STATISTIC','Statistic');
define('_KUNENA_VALUE','Value');
define('_GEN_CATEGORY','Category');
define('_GEN_STARTEDBY','Started by:&#32;');
define('_GEN_STATS','stats');
define('_STATS_TITLE',' forum - stats');
define('_STATS_GEN_STATS','General stats');
define('_STATS_TOTAL_MEMBERS','Members:');
define('_STATS_TOTAL_REPLIES','Replies:');
define('_STATS_TOTAL_TOPICS','Topics:');
define('_STATS_TODAY_TOPICS','Topics today:');
define('_STATS_TODAY_REPLIES','Replies today:');
define('_STATS_TOTAL_CATEGORIES','Categories:');
define('_STATS_TOTAL_SECTIONS','Sections:');
define('_STATS_LATEST_MEMBER','Latest member:');
define('_STATS_YESTERDAY_TOPICS','Topics yesterday:');
define('_STATS_YESTERDAY_REPLIES','Replies yesterday:');
define('_STATS_POPULAR_PROFILE','Popular 10 Members (Based on profile hits)');
define('_STATS_TOP_POSTERS','Top posters');
define('_STATS_POPULAR_TOPICS','Top popular topics');
define('_COM_A_STATSPAGE','Enable Stats Page');
define('_COM_A_STATSPAGE_DESC','If set to &quot;Yes,&quot; a public link to your stats page will be shown in the header menu. This page displays various statistics about your forum. <em>The stats page is always visible to admins.</em>');
define('_COM_C_JBSTATS','Forum Stats');
define('_COM_C_JBSTATS_DESC','Forum Statistics');
define('_GEN_GENERAL','General');
define('_PERM_NO_READ','You do not have sufficient permissions to access this forum.');
define ('_KUNENA_SMILEY_SAVED','Smiley saved.');
define ('_KUNENA_SMILEY_DELETED','Smiley deleted.');
define ('_KUNENA_CODE_ALLREADY_EXITS','Code already exists.');
define ('_KUNENA_MISSING_PARAMETER','Missing Parameter.');
define ('_KUNENA_RANK_ALLREADY_EXITS','Rank already exists.');
define ('_KUNENA_RANK_DELETED','Rank Deleted.');
define ('_KUNENA_RANK_SAVED','Rank saved.');
define ('_KUNENA_DELETE_SELECTED','Delete selected');
define ('_KUNENA_MOVE_SELECTED','Move selected');
define ('_KUNENA_REPORT_LOGGED','Logged');
define ('_KUNENA_GO','Go');
define('_KUNENA_MAILFULL','Include complete post content in the e-mail sent to subscribers.');
define('_KUNENA_MAILFULL_DESC','If &quot;No,&quot; subscribers will receive only titles of new messages.');
define('_KUNENA_HIDETEXT','Please log in to view this content.');
define('_BBCODE_HIDE','Hidden text: [hide]any hidden text[/hide] to hide part of a message from Guests');// Deprecated in 1.0.10
define('_KUNENA_FILEATTACH','File Attachment:&#32;');
define('_KUNENA_FILENAME','File Name:&#32;');
define('_KUNENA_FILESIZE','File Size:&#32;');
define('_KUNENA_MSG_CODE','Code:&#32;');
define('_KUNENA_CAPTCHA_ON','Spam protection system');
define('_KUNENA_CAPTCHA_DESC','Antispam and antibot CAPTCHA system On/Off');
define('_KUNENA_CAPDESC','Enter code here');
define('_KUNENA_CAPERR','Code not correct!');
define('_KUNENA_COM_A_REPORT', 'Message Reporting');
define('_KUNENA_COM_A_REPORT_DESC', 'If you want to allow users to report any message, select &quot;Yes.&quot;');
define('_KUNENA_REPORT_MSG', 'Message Reported');
define('_KUNENA_REPORT_REASON', 'Reason');
define('_KUNENA_REPORT_MESSAGE', 'Your Message');
define('_KUNENA_REPORT_SEND', 'Send Report');
define('_KUNENA_REPORT', 'Report to moderator');
define('_KUNENA_REPORT_RSENDER', 'Report Sender:&#32;');
define('_KUNENA_REPORT_RREASON', 'Report Reason:&#32;');
define('_KUNENA_REPORT_RMESSAGE', 'Report Message:&#32;');
define('_KUNENA_REPORT_POST_POSTER', 'Message Poster:&#32;');
define('_KUNENA_REPORT_POST_SUBJECT', 'Message Subject:&#32;');
define('_KUNENA_REPORT_POST_MESSAGE', 'Message:&#32;');
define('_KUNENA_REPORT_POST_LINK', 'Message Link:&#32;');
define('_KUNENA_REPORT_INTRO', 'was sent you a message because of');
define('_KUNENA_REPORT_SUCCESS', 'Report succesfully sent!');
define('_KUNENA_EMOTICONS', 'Emoticons');
define('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
define('_KUNENA_EMOTICONS_CODE', 'Code');
define('_KUNENA_EMOTICONS_URL', 'URL');
define('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Edit Smiley');
define('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Edit Smilies');
define('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
define('_KUNENA_EMOTICONS_NEW_SMILEY', 'New Smiley');
define('_KUNENA_EMOTICONS_MORE_SMILIES', 'More Smilies');
define('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Close Window');
define('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Additional Emoticons');
define('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Pick a smiley');
define('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
define('_KUNENA_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
define('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
define('_KUNENA_USERNAMECANCHANGE', 'Allow username change');
define('_KUNENA_USERNAMECANCHANGE_DESC', 'Allow username change on my profile plugin page');
define ('_KUNENA_RECOUNTFORUMS','Recount Category Stats');
define ('_KUNENA_RECOUNTFORUMS_DONE','All category statistics have been recounted.');
define ('_KUNENA_EDITING_REASON','Reason for Editing');
define ('_KUNENA_EDITING_LASTEDIT','Last Edit');
define ('_KUNENA_BY','By');
define ('_KUNENA_REASON','Reason');
define('_GEN_GOTOBOTTOM', 'Go to bottom');
define('_GEN_GOTOTOP', 'Go to top');
define('_STAT_USER_INFO', 'User Info');
define('_USER_SHOWEMAIL', 'Show E-mail'); // <=FB 1.0.3
define('_USER_SHOWONLINE', 'Show Online');
define('_KUNENA_HIDDEN_USERS', 'Hidden Users');
define('_KUNENA_SAVE', 'Save');
define('_KUNENA_RESET', 'Reset');
define('_KUNENA_DEFAULT_GALLERY', 'Default Gallery');
define('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Personal Info');
define('_KUNENA_MYPROFILE_SUMMARY', 'Summary');
define('_KUNENA_MYPROFILE_MYAVATAR', 'My Avatar');
define('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum Settings');
define('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Look and Layout');
define('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'My Profile Info');
define('_KUNENA_MYPROFILE_MY_POSTS', 'My Posts');
define('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'My Subscribes');
define('_KUNENA_MYPROFILE_MY_FAVORITES', 'My Favorites');
define('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Private Messaging');
define('_KUNENA_MYPROFILE_INBOX', 'Inbox');
define('_KUNENA_MYPROFILE_NEW_MESSAGE', 'New Message');
define('_KUNENA_MYPROFILE_OUTBOX', 'Outbox');
define('_KUNENA_MYPROFILE_TRASH', 'Trash');
define('_KUNENA_MYPROFILE_SETTINGS', 'Settings');
define('_KUNENA_MYPROFILE_CONTACTS', 'Contacts');
define('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Blocked List');
define('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Additional Info');
define('_KUNENA_MYPROFILE_NAME', 'Name');
define('_KUNENA_MYPROFILE_USERNAME', 'Username');
define('_KUNENA_MYPROFILE_EMAIL', 'E-mail');
define('_KUNENA_MYPROFILE_USERTYPE', 'User Type');
define('_KUNENA_MYPROFILE_REGISTERDATE', 'Register Date');
define('_KUNENA_MYPROFILE_LASTVISITDATE', 'Last Visit Date');
define('_KUNENA_MYPROFILE_POSTS', 'Posts');
define('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profile View');
define('_KUNENA_MYPROFILE_PERSONALTEXT', 'Personal Text');
define('_KUNENA_MYPROFILE_GENDER', 'Gender');
define('_KUNENA_MYPROFILE_BIRTHDATE', 'Birthdate');
define('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Year (YYYY) - Month (MM) - Day (DD)');
define('_KUNENA_MYPROFILE_LOCATION', 'Location');
define('_KUNENA_MYPROFILE_ICQ', 'ICQ');
define('_KUNENA_MYPROFILE_ICQ_DESC', 'This is your ICQ number.');
define('_KUNENA_MYPROFILE_AIM', 'AIM');
define('_KUNENA_MYPROFILE_AIM_DESC', 'This is your AOL Instant Messenger nickname.');
define('_KUNENA_MYPROFILE_YIM', 'YIM');
define('_KUNENA_MYPROFILE_YIM_DESC', 'This is your Yahoo! Instant Messenger nickname.');
define('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
define('_KUNENA_MYPROFILE_SKYPE_DESC', 'This is your Skype handle.');
define('_KUNENA_MYPROFILE_GTALK', 'GTALK');
define('_KUNENA_MYPROFILE_GTALK_DESC', 'This is your Gtalk nickname.');
define('_KUNENA_MYPROFILE_WEBSITE', 'Web site');
define('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Web site Name');
define('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Example: Kunena');
define('_KUNENA_MYPROFILE_WEBSITE_URL', 'Web site URL');
define('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Example: www.Kunena.com');
define('_KUNENA_MYPROFILE_MSN', 'MSN');
define('_KUNENA_MYPROFILE_MSN_DESC', 'Your MSN messenger e-mail address.');
define('_KUNENA_MYPROFILE_SIGNATURE', 'Signature');
define('_KUNENA_MYPROFILE_MALE', 'Male');
define('_KUNENA_MYPROFILE_FEMALE', 'Female');
define('_KUNENA_BULKMSG_DELETED', 'Posts were deleted successfully');
define('_KUNENA_DATE_YEAR', 'Year');
define('_KUNENA_DATE_MONTH', 'Month');
define('_KUNENA_DATE_WEEK','Week');
define('_KUNENA_DATE_DAY', 'Day');
define('_KUNENA_DATE_HOUR', 'Hour');
define('_KUNENA_DATE_MINUTE', 'Minute');
define('_KUNENA_IN_FORUM', '&#32;in Forum:&#32;');
define('_KUNENA_FORUM_AT', '&#32;Forum at:&#32;');
define('_KUNENA_QMESSAGE_NOTE', 'Please note: although no board code and smiley buttons are shown, they are still usable.');

// 1.0.1
define ('_KUNENA_FORUMTOOLS','Forum Tools');

//userlist
define ('_KUNENA_USRL_USERLIST','Userlist');
define ('_KUNENA_USRL_REGISTERED_USERS','%s has <b>%d</b> registered users');
define ('_KUNENA_USRL_SEARCH_ALERT','Please enter a value to search!');
define ('_KUNENA_USRL_SEARCH','Find user');
define ('_KUNENA_USRL_SEARCH_BUTTON','Search');
define ('_KUNENA_USRL_LIST_ALL','List all');
define ('_KUNENA_USRL_NAME','Name');
define ('_KUNENA_USRL_USERNAME','Username');
define ('_KUNENA_USRL_GROUP','Group');
define ('_KUNENA_USRL_POSTS','Posts');
define ('_KUNENA_USRL_KARMA','Karma');
define ('_KUNENA_USRL_HITS','Hits');
define ('_KUNENA_USRL_EMAIL','E-mail');
define ('_KUNENA_USRL_USERTYPE','Usertype');
define ('_KUNENA_USRL_JOIN_DATE','Join date');
define ('_KUNENA_USRL_LAST_LOGIN','Last login');
define ('_KUNENA_USRL_NEVER','Never');
define ('_KUNENA_USRL_ONLINE','Status');
define ('_KUNENA_USRL_AVATAR','Picture');
define ('_KUNENA_USRL_ASC','Ascending');
define ('_KUNENA_USRL_DESC','Descending');
define ('_KUNENA_USRL_DISPLAY_NR','Display');
define ('_KUNENA_USRL_DATE_FORMAT','%m/%d/%Y');

define('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
define('_KUNENA_ADMIN_CONFIG_USERLIST','Userlist');
define('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Number of userlist rows');
define('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Number of userlist rows');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online Status');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Show users online status');

define('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Display Avatar');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Show Real Name');
define('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Show Username');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Show Number of Posts');
define('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Show Karma');
define('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Show E-mail');
define('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Show User Type');
define('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Show Join Date');
define('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Show Last Visit Date');
define('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
define('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Show Profile Hits');
define('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
define('_KUNENA_DBWIZ', 'Database Wizard');
define('_KUNENA_DBMETHOD', 'Choose a preferred installation method:');
define('_KUNENA_DBCLEAN', 'Clean installation');
define('_KUNENA_DBUPGRADE', 'Upgrade From Joomlaboard');
define('_KUNENA_TOPLEVEL', 'Top Level Category');
define('_KUNENA_REGISTERED', 'Registered');
define('_KUNENA_PUBLICBACKEND', 'Public Backend');
define('_KUNENA_SELECTANITEMTO', 'Select an item to');
define('_KUNENA_ERRORSUBS', 'Something went wrong deleting the messages and subscriptions.');
define('_KUNENA_WARNING', 'Warning...');
define('_KUNENA_CHMOD1', 'You need to CHMOD this to 766 in order for the file to be updated.');
define('_KUNENA_YOURCONFIGFILEIS', 'Your config file is');
define('_KUNENA_KUNENA', 'Kunena');
define('_KUNENA_CB', 'Community Builder');
define('_KUNENA_MYPMS', 'myPMS II Open Source');
define('_KUNENA_UDDEIM', 'Uddeim');
define('_KUNENA_JIM', 'JIM');
define('_KUNENA_MISSUS', 'Missus');
define('_KUNENA_SELECTTEMPLATE', 'Select Template');
define('_KUNENA_CONFIGSAVED', 'Configuration saved.');
define('_KUNENA_CONFIGNOTSAVED', 'FATAL ERROR. Configuration could not be saved.');
define('_KUNENA_TFINW', 'The file is not writable.');
define('_KUNENA_FBCFS', 'Kunena CSS file saved.');
define('_KUNENA_SELECTMODTO', 'Select an moderator to');
define('_KUNENA_CHOOSEFORUMTOPRUNE', 'You must choose a forum to prune!');
define('_KUNENA_DELMSGERROR', 'Deleting messages failed:');
define('_KUNENA_DELMSGERROR1', 'Deleting messages texts failed:');
define('_KUNENA_CLEARSUBSFAIL', 'Clearing subscriptions failed:');
define('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned for');
define('_KUNENA_PRUNEDAYS', 'days');
define('_KUNENA_PRUNEDELETED', 'Deleted:');
define('_KUNENA_PRUNETHREADS', 'threads');
define('_KUNENA_ERRORPRUNEUSERS', 'Error pruning users:');
define('_KUNENA_USERSPRUNEDDELETED', 'Users pruned. Deleted:'); // <=FB 1.0.3
define('_KUNENA_PRUNEUSERPROFILES', 'user profiles'); // <=FB 1.0.3
define('_KUNENA_NOPROFILESFORPRUNNING', 'No profiles found eligible for pruning.'); // <=FB 1.0.3
define('_KUNENA_TABLESUPGRADED', 'Kunena tables are upgraded to version');
define('_KUNENA_FORUMCATEGORY', 'Forum Category');
define('_KUNENA_IMGDELETED', 'Image deleted');
define('_KUNENA_FILEDELETED', 'File deleted');
define('_KUNENA_NOPARENT', 'No Parent');
define('_KUNENA_DIRCOPERR', 'Error: File');
define('_KUNENA_DIRCOPERR1', 'could not be copied!\n');
define('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> component <em>for Joomla </em> <br />&copy; 2008 - 2009 by www.Kunena.com<br />All rights reserved.');
define('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
define('_KUNENA_FORUMPRF_TITLE', 'Profile Settings');
define('_KUNENA_FORUMPRF', 'Profile');
define('_KUNENA_FORUMPRRDESC', 'If you have Community Builder or JomSocial installed, you can configure Kunena to use their user profiles.');
define('_KUNENA_USERPROFILE_PROFILE', 'Profile');
define('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profile View</b>');
define('_KUNENA_USERPROFILE_MESSAGES', 'All Forum Messages');
define('_KUNENA_USERPROFILE_TOPICS', 'Topics');
define('_KUNENA_USERPROFILE_STARTBY', 'Started by');
define('_KUNENA_USERPROFILE_CATEGORIES', 'Categories');
define('_KUNENA_USERPROFILE_DATE', 'Date');
define('_KUNENA_USERPROFILE_HITS', 'Hits');
define('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'No Forum Post');
define('_KUNENA_TOTALFAVORITE', 'Favoured: &#32;');
define('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Number of child board columns &#32;');
define('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Number of child board column formating under main category&#32;');
define('_KUNENA_SUBSCRIPTIONSCHECKED', 'Post-subscription checked by default?');
define('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Set to &quot;Yes&quot; if you want to post subscription box always checked.');
// Errors (Re-integration from Joomlaboard 1.2)
define('_KUNENA_ERROR1', 'Category / Forum must have a name');
// Forum Configuration (New in Kunena)
define('_KUNENA_SHOWSTATS', 'Show Stats');
define('_KUNENA_SHOWSTATSDESC', 'If you want to show the Stats, select &quot;Yes.&quot;');
define('_KUNENA_SHOWWHOIS', 'Show Who is Online');
define('_KUNENA_SHOWWHOISDESC', 'If you want to show Whois Online, select &quot;Yes.&quot;');
define('_KUNENA_STATSGENERAL', 'Show General Stats');
define('_KUNENA_STATSGENERALDESC', 'If you want to show the General Stats, select &quot;Yes.&quot;');
define('_KUNENA_USERSTATS', 'Show Popular User Stats');
define('_KUNENA_USERSTATSDESC', 'If you want to show the Popular Stats, select &quot;Yes.&quot;');
define('_KUNENA_USERNUM', 'Number of Popular User');
define('_KUNENA_USERPOPULAR', 'Show Popular Subject Stats');
define('_KUNENA_USERPOPULARDESC', 'If you want to show the Popular Subject, select &quot;Yes.&quot;');
define('_KUNENA_NUMPOP', 'Number of Popular Subject');
define('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.8. It is a powerful and stylish forum component for a well-deserved content management system, Joomla. It is initially based on the hard work of Joomlaboard and Fireboard and our praise goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for third-party developers.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as Uddeim</li><li>Basic plugin system (practical rather than perfect)</li><li>Language defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add Joomla modules inside the forum template itself. Want to have a banner inside your forum?</li><li>Favorite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category-specific image system</li><li>Enhanced pathway</li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community Builder and JomSocial profile options</li><li>Avatar management : Community Builder and JomSocial options<br /></li></ul><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena Team<br /></td></tr></table>');
define('_KUNENA_INSTRUCTIONS', 'Instructions');
define('_KUNENA_FINFO', 'Kunena Forum Information');
define('_KUNENA_CSSEDITOR', 'Kunena Template CSS Editor');
define('_KUNENA_PATH', 'Path:');
define('_KUNENA_CSSERROR', 'Please note: The CSS template file must be writable to save changes.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena User Profile Manager');
DEFINE('_KUNENA_SORTID', 'Sort By Userid');
DEFINE('_KUNENA_SORTMOD', 'Sort By Moderators');
DEFINE('_KUNENA_SORTNAME', 'Sort By Names');
DEFINE('_KUNENA_SORTREALNAME', 'Sort By Real Names');
DEFINE('_KUNENA_VIEW', 'View');
DEFINE('_KUNENA_NOUSERSFOUND', 'No user profiles found.');
DEFINE('_KUNENA_ADDMOD', 'Add Moderator to');
DEFINE('_KUNENA_NOMODSAV', 'There are no possible moderators found. Read the note below.');
DEFINE('_KUNENA_NOTEUS',
    'NOTE: Only users which have the moderator flag set in their Kunena profile are shown here. In order to be able to add a moderator, give a user a moderator flag and then go to <a href="index2.php?option=com_kunena&task=profiles">User Administration</a> and search for the user you want to make a moderator. Then select their profile and update it. The moderator flag can only be set by a site administrator.');
DEFINE('_KUNENA_PROFFOR', 'Profile for');
DEFINE('_KUNENA_GENPROF', 'General Profile Options');
//DEFINE('_KUNENA_PREFVIEW', 'Prefered Viewtype:');
DEFINE('_KUNENA_PREFOR', 'Prefered Message Ordering:');
DEFINE('_KUNENA_ISMOD', 'Is Moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Yes</strong> (not changeable, this user is an site (super)administrator)');
DEFINE('_KUNENA_COLOR', 'Color');
DEFINE('_KUNENA_UAVATAR', 'User avatar:');
DEFINE('_KUNENA_NS', 'None selected');
DEFINE('_KUNENA_DELSIG', '&#32;check this box to delete this signature');
DEFINE('_KUNENA_DELAV', '&#32;check this box to delete this avatar');
DEFINE('_KUNENA_SUBFOR', 'Subscriptions for');
DEFINE('_KUNENA_NOSUBS', 'No subscriptions found for this user');
// Forum Administration (Re-integration from Joomlaboard 1.2)
define('_KUNENA_BASICS', 'Basics');
define('_KUNENA_BASICSFORUM', 'Basic Forum Information');
define('_KUNENA_PARENT', 'Parent:');
define('_KUNENA_PARENTDESC',
    'Please note: To create a category, choose \'Top Level Category\' as a parent. A category serves as a container for forums.<br />A forum can <strong>only</strong> be created within a category by selecting a previously created category as the parent for the forum.<br /> Messages can be <strong>ONLY</strong> posted to forums, not categories.');
define('_KUNENA_BASICSFORUMINFO', 'Forum name and description');
define('_KUNENA_NAMEADD', 'Name:');
define('_KUNENA_DESCRIPTIONADD', 'Description:');
define('_KUNENA_ADVANCEDDESC', 'Forum advanced configuration');
define('_KUNENA_ADVANCEDDESCINFO', 'Forum security and access');
define('_KUNENA_LOCKEDDESC', 'Set to &quot;Yes&quot; if you want to lock this forum. Nobody but Moderators and Admins can create new topics or replies in a locked forum (or move posts to it).');
define('_KUNENA_LOCKED1', 'Locked:');
define('_KUNENA_PUBACC', 'Public Access Level:');
define('_KUNENA_PUBACCDESC',
    'To create a non-public Forum, you can specify the minimum user level that can see/enter the forum here. By default, the minumum user level is set to &quot;Everybody&quot;.<br /><b>Please note:</b> If you restrict the access of a whole category to one or more certain groups, you will hide all forums it contains to anybody not having proper privileges for the category <b>even</b> if one or more of these Forums has a lower access level set! This is also true for moderators. You will need to add a moderator to the category moderator list if they do not have the proper group level to see the category.<br /> Categories cannot be moderated, but moderators can still be added to the moderator list.');
define('_KUNENA_CGROUPS', 'Include Child Groups:');
define('_KUNENA_CGROUPSDESC', 'Should child groups also be allowed access? If set to &quot;No,&quot; access to this forum is restricted to the selected group <strong>only</strong>.');
define('_KUNENA_ADMINLEVEL', 'Admin Access Level:');
define('_KUNENA_ADMINLEVELDESC',
    'If you create a forum with Public Access restrictions, you can specify here an additional Administration Access Level.<br /> If you restrict the access to the Forum to a special Public Frontend user group and don\'t specify a Public Backend Group here, administrators will not be able to enter/view the forum.');
define('_KUNENA_ADVANCED', 'Advanced');
define('_KUNENA_CGROUPS1', 'Include Child Groups:');
define('_KUNENA_CGROUPS1DESC', 'Should child groups be allowed access as well? If set to &quot;No &quot;, access to this forum is restricted to the selected group <strong>only</strong>.');
define('_KUNENA_REV', 'Review posts:');
define('_KUNENA_REVDESC',
    'Set to &quot;Yes&quot; if you want posts to be reviewed by moderators prior to publishing them in this forum. This is useful in a moderated forum only!<br />If you set this without any moderators specified, the site admin is solely responsible for approving/deleting submitted posts since these will be kept \'on hold\'!');
define('_KUNENA_MOD_NEW', 'Moderation');
define('_KUNENA_MODNEWDESC', 'Moderation of the Forum and Forum moderators');
define('_KUNENA_MOD', 'Moderated:');
define('_KUNENA_MODDESC',
    'Set to &quot;Yes&quot; if you want to be able to assign Moderators to this forum.<br /><strong>Note:</strong> This doesn\'t mean that new posts must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>Please note:</strong> after setting moderation to &quot;Yes,&quot; you must save the forum configuration first before you will be able to use the new button to add moderators.');
define('_KUNENA_MODHEADER', 'Moderation settings for this forum');
define('_KUNENA_MODSASSIGNED', 'Moderators assigned to this forum:');
define('_KUNENA_NOMODS', 'There are no Moderators assigned to this forum');
// Some General Strings (Improvement in Kunena)
define('_KUNENA_EDIT', 'Edit');
define('_KUNENA_ADD', 'Add');
// Reorder (Re-integration from Joomlaboard 1.2)
define('_KUNENA_MOVEUP', 'Move Up');
define('_KUNENA_MOVEDOWN', 'Move Down');
// Groups - Integration in Kunena
define('_KUNENA_ALLREGISTERED', 'All Registered');
define('_KUNENA_EVERYBODY', 'Everybody');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
define('_KUNENA_REORDER', 'Reorder');
define('_KUNENA_CHECKEDOUT', 'Check Out');
define('_KUNENA_ADMINACCESS', 'Admin Access');
define('_KUNENA_PUBLICACCESS', 'Public Access');
define('_KUNENA_PUBLISHED', 'Published');
define('_KUNENA_REVIEW', 'Review');
define('_KUNENA_MODERATED', 'Moderated');
define('_KUNENA_LOCKED', 'Locked');
define('_KUNENA_CATFOR', 'Category / Forum');
define('_KUNENA_ADMIN', 'Forum Administration');
define('_KUNENA_CP', 'Kunena Control Panel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
define('_COM_A_AVATAR_INTEGRATION', 'Avatar Integration');
define('_COM_A_RANKS_SETTINGS', 'Ranks');
define('_COM_A_RANKING_SETTINGS', 'Ranking Settings');
define('_COM_A_AVATAR_SETTINGS', 'Avatar Settings');
define('_COM_A_SECURITY_SETTINGS', 'Security Settings');
define('_COM_A_BASIC_SETTINGS', 'Basic Settings');
// Kunena 1.0.0
//
define('_COM_A_FAVORITES', 'Allow Favorites');
define('_COM_A_FAVORITES_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to favorite a topic&#32;');
define('_USER_UNFAVORITE_ALL', 'Check this box to <b><u>unfavorite</u></b> from all topics (including invisible ones for troubleshooting purposes).');
define('_VIEW_FAVORITETXT', 'Favorite this topic&#32;');
define('_USER_UNFAVORITE_YES', 'You have unfavorited the topic.');
define('_POST_FAVORITED_TOPIC', 'This thread has been added to your favorites.');
define('_VIEW_UNFAVORITETXT', 'Unfavorite');
define('_VIEW_UNSUBSCRIBETXT', 'Unsubscribe');
define('_USER_NOFAVORITES', 'No Favorites');
define('_POST_SUCCESS_FAVORITE', 'Your request to add to favorites has been processed.');
define('_COM_A_MESSAGES_SEARCH', 'Search Results');
define('_COM_A_MESSAGES_DESC_SEARCH', 'Messages per page for search results');
define('_KUNENA_USE_JOOMLA_STYLE', 'Use Joomla Style?');
define('_KUNENA_USE_JOOMLA_STYLE_DESC', 'If you want to use the Joomla style set to &quot;Yes.&quot; (CSS classes: sectionheader, sectionentry1, etc.)&#32;');
define('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Show Child Category Image');
define('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'If you want to show child category small icon on your forum list, set to &quot;Yes.&quot;&#32;');
define('_KUNENA_SHOW_ANNOUNCEMENT', 'Show Announcement');
define('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Set to &quot;Yes&quot; if you want to show the announcement box on your Forum home page.');
define('_KUNENA_RECENT_POSTS', 'Recent Post Settings');
define('_KUNENA_SHOW_LATEST_MESSAGES', 'Show Recent Posts');
define('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Set to &quot;Yes&quot; if you want to show recent post plugin on your forum.');
define('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Number of Recent Posts');
define('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Number of Recent Posts');
define('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Count Per Tab&#32;');
define('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Number of Posts per tab');
define('_KUNENA_LATEST_CATEGORY', 'Show Category');
define('_KUNENA_LATEST_CATEGORY_DESC', 'Specific category you can show on recent posts. For example: 2, 3, 7&#32;');
define('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Show Single Subject');
define('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Show Single Subject');
define('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Show Reply Subject');
define('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Show Reply Subject (Re:)');
define('_KUNENA_LATEST_SUBJECT_LENGTH', 'Subject Length');
define('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Subject Length');
define('_KUNENA_SHOW_LATEST_DATE', 'Show Date');
define('_KUNENA_SHOW_LATEST_DATE_DESC', 'Show Date');
define('_KUNENA_SHOW_LATEST_HITS', 'Show Hits');
define('_KUNENA_SHOW_LATEST_HITS_DESC', 'Show Hits');
define('_KUNENA_SHOW_AUTHOR', 'Show Author');
define('_KUNENA_SHOW_AUTHOR_DESC', '1=username, 2=realname, 0=none');
define('_KUNENA_STATS', 'Stats Plugin Settings&#32;');
define('_KUNENA_CATIMAGEPATH', 'Category Image Path&#32;');
define('_KUNENA_CATIMAGEPATH_DESC', 'Category Image path. If you set the path as &quot;category_images,&quot; the full path will be "your_html_rootfolder/images/fbfiles/category_images/');
define('_KUNENA_ANN_MODID', 'Announcement Moderator IDs&#32;');
define('_KUNENA_ANN_MODID_DESC', 'Add user IDs for announcement moderators (e.g. 62,63,73). Announcement moderators can add, edit, and delete the announcements.');
//
define('_KUNENA_FORUM_TOP', 'Board Categories&#32;');
define('_KUNENA_CHILD_BOARDS', 'Child Boards&#32;');
define('_KUNENA_QUICKMSG', 'Quick Reply&#32;');
define('_KUNENA_THREADS_IN_FORUM', 'Threads in Forum&#32;');
define('_KUNENA_FORUM', 'Forum&#32;');
define('_KUNENA_SPOTS', 'Spotlights');
define('_KUNENA_CANCEL', 'cancel');
define('_KUNENA_TOPIC', 'TOPIC:&#32;');
define('_KUNENA_POWEREDBY', 'Powered by&#32;');
// Time Format
define('_TIME_TODAY', '<b>Today</b>&#32;');
define('_TIME_YESTERDAY', '<b>Yesterday</b>&#32;');
//  STARTS HERE!
define('_KUNENA_WHO_LATEST_POSTS', 'Latest Posts');
define('_KUNENA_WHO_WHOISONLINE', 'Who is Online');
define('_KUNENA_WHO_MAINPAGE', 'Forum Main');
define('_KUNENA_GUEST', 'Guest');
define('_KUNENA_PATHWAY_VIEWING', 'viewing');
define('_KUNENA_ATTACH', 'Attachment');
// Favorite
define('_KUNENA_FAVORITE', 'Favorite');
define('_USER_FAVORITES', 'My Favorites');
define('_THREAD_UNFAVORITE', 'Remove from Favorites');
// profilebox
define('_PROFILEBOX_WELCOME', 'Welcome');
define('_PROFILEBOX_SHOW_LATEST_POSTS', 'Show Latest Posts');
define('_PROFILEBOX_SET_MYAVATAR', 'Set My Avatar');
define('_PROFILEBOX_MYPROFILE', 'My Profile');
define('_PROFILEBOX_SHOW_MYPOSTS', 'Show My Posts');
define('_PROFILEBOX_GUEST', 'Guest');
define('_PROFILEBOX_LOGIN', 'Login');
define('_PROFILEBOX_REGISTER', 'Register');
define('_PROFILEBOX_LOGOUT', 'Logout');
define('_PROFILEBOX_LOST_PASSWORD', 'Lost Password?');
define('_PROFILEBOX_PLEASE', 'Please');
define('_PROFILEBOX_OR', 'or');
// recentposts
define('_RECENT_RECENT_POSTS', 'Recent Posts');
define('_RECENT_TOPICS', 'Topic');
define('_RECENT_AUTHOR', 'Author');
define('_RECENT_CATEGORIES', 'Categories');
define('_RECENT_DATE', 'Date');
define('_RECENT_HITS', 'Hits');
// announcement

define('_ANN_ANNOUNCEMENTS', 'Announcements');
define('_ANN_ID', 'ID');
define('_ANN_DATE', 'Date');
define('_ANN_TITLE', 'Title');
define('_ANN_SORTTEXT', 'Short Text');
define('_ANN_LONGTEXT', 'Long Text');
define('_ANN_ORDER', 'Order');
define('_ANN_PUBLISH', 'Publish');
define('_ANN_PUBLISHED', 'Published');
define('_ANN_UNPUBLISHED', 'Unpublished');
define('_ANN_EDIT', 'Edit');
define('_ANN_DELETE', 'Delete');
define('_ANN_SUCCESS', 'Success');
define('_ANN_SAVE', 'Save');
define('_ANN_YES', 'Yes');
define('_ANN_NO', 'No');
define('_ANN_ADD', 'Add New');
define('_ANN_SUCCESS_EDIT', 'Success Edit');
define('_ANN_SUCCESS_ADD', 'Success Added');
define('_ANN_DELETED', 'Success Deleted');
define('_ANN_ERROR', 'ERROR');
define('_ANN_READMORE', 'Read More...');
define('_ANN_CPANEL', 'Announcement Control Panel');
define('_ANN_SHOWDATE', 'Show Date');
// Stats
define('_STAT_FORUMSTATS', 'Forum Stats');
define('_STAT_GENERAL_STATS', 'General Stats');
define('_STAT_TOTAL_USERS', 'Total Users');
define('_STAT_LATEST_MEMBERS', 'Latest Member');
define('_STAT_PROFILE_INFO', 'See Profile Info');
define('_STAT_TOTAL_MESSAGES', 'Total Messages');
define('_STAT_TOTAL_SUBJECTS', 'Total Subjects');
define('_STAT_TOTAL_CATEGORIES', 'Total Categories');
define('_STAT_TOTAL_SECTIONS', 'Total Sections');
define('_STAT_TODAY_OPEN_THREAD', 'Today Open');
define('_STAT_YESTERDAY_OPEN_THREAD', 'Yesterday Open');
define('_STAT_TODAY_TOTAL_ANSWER', 'Today Total Answer');
define('_STAT_YESTERDAY_TOTAL_ANSWER', 'Yesterday Total Answer');
define('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'View Recent Posts');
define('_STAT_MORE_ABOUT_STATS', 'More About Stats');
define('_STAT_USERLIST', 'User List');
define('_STAT_TEAMLIST', 'Board Team');
define('_STATS_FORUM_STATS', 'Forum Stats');
define('_STAT_POPULAR', 'Popular');
define('_STAT_POPULAR_USER_TMSG', 'Users ( Total Messages)&#32;');
define('_STAT_POPULAR_USER_KGSG', 'Threads&#32;');
define('_STAT_POPULAR_USER_GSG', 'Users ( Total Profile Views)&#32;');
//Team List
define('_MODLIST_ONLINE', 'User Online Now');
define('_MODLIST_OFFLINE', 'User Offline');
// Whoisonline
define('_WHO_WHOIS_ONLINE', 'Who is online');
define('_WHO_ONLINE_NOW', 'Online');
define('_WHO_ONLINE_MEMBERS', 'Members');
define('_WHO_AND', 'and');
define('_WHO_ONLINE_GUESTS', 'Guests');
define('_WHO_ONLINE_USER', 'User');
define('_WHO_ONLINE_TIME', 'Time');
define('_WHO_ONLINE_FUNC', 'Action');
// Userlist
define('_USRL_USERLIST', 'Userlist');
define('_USRL_REGISTERED_USERS', '%s has <strong>%d</strong> registered users');
define('_USRL_SEARCH_ALERT', 'Please enter a value to search!');
define('_USRL_SEARCH', 'Find user');
define('_USRL_SEARCH_BUTTON', 'Search');
define('_USRL_LIST_ALL', 'List all');
define('_USRL_NAME', 'Name');
define('_USRL_USERNAME', 'Username');
define('_USRL_EMAIL', 'E-mail');
define('_USRL_USERTYPE', 'Usertype');
define('_USRL_JOIN_DATE', 'Join date');
define('_USRL_LAST_LOGIN', 'Last login');
define('_USRL_NEVER', 'Never');
define('_USRL_BLOCK', 'Status');
define('_USRL_MYPMS2', 'MyPMS');
define('_USRL_ASC', 'Ascending');
define('_USRL_DESC', 'Descending');
define('_USRL_DATE_FORMAT', '%m/%d/%Y');
define('_USRL_TIME_FORMAT', '%H:%M');
define('_USRL_USEREXTENDED', 'Details');
define('_USRL_COMPROFILER', 'Profile');
define('_USRL_THUMBNAIL', 'Pic');
define('_USRL_READON', 'show');
define('_USRL_MYPMSPRO_SENDPM', 'Send PM');
define('_USRL_JIM', 'PM');
define('_USRL_UDDEIM', 'PM');
define('_USRL_SEARCHRESULT', 'Search result for');
define('_USRL_STATUS', 'Status');
define('_USRL_LISTSETTINGS', 'Userlist Settings');
define('_USRL_ERROR', 'Error');

//changed in 1.1.4 stable
define('_COM_A_PMS_TITLE', 'Private messaging component');
define('_COM_A_COMBUILDER_TITLE', 'Community Builder');
define('_FORUM_SEARCH', 'Searched for: %s');
define('_MODERATION_DELETE_MESSAGE', 'Are you sure you want to delete this message? \n\n NOTE: There is NO way to retrieve deleted messages!');
define('_MODERATION_DELETE_SUCCESS', 'The post(s) have been deleted');
define('_COM_A_RANKING', 'Ranking');
define('_COM_A_BOT_REFERENCE', 'Show Bot Reference Chart');
define('_COM_A_MOSBOT', 'Enable the Discuss Bot');
define('_PREVIEW', 'Preview');
define('_COM_A_MOSBOT_TITLE', 'Discussbot');
define('_COM_A_MOSBOT_DESC', 'The discuss bot enables your users to discuss articles in the forums. The article title is used as the topic subject.'
           . '<br />If a topic does not exist, a new one is created. If the topic already exists, the user is shown the thread and where to reply.' . '<br /><strong>You will need to download and install the bot separately.</strong>'
           . '<br />check the <a href="http://www.Kunena.com">Kunena Web Site</a> for more information.' . '<br />When installed, you will need to add the following bot lines to your articles:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the article can be discussed. To find the proper catid, look into the forums ' . 'and check the category ID from the URL in your browser.'
           . '<br />Example: if you want the article discussed in forum with the category ID 26, the bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each article to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
define('_FORUM_SEARCHTITLE', 'Search');
define('_FORUM_SEARCHRESULTS', 'Displaying %s out of %s results.');
// Help, FAQ
define('_COM_FORUM_HELP', 'FAQ');
// rules.php
define('_COM_FORUM_RULES', 'Rules');
define('_COM_FORUM_RULES_DESC', '<ul><li>Edit this file to insert your rules joomlaroot/administrator/components/com_kunena/language/kunena.english.php</li><li>Rule 2</li><li>Rule 3</li><li>Rule 4</li><li>...</li></ul>');
//smile.class.php
define('_COM_BOARDCODE', 'Boardcode');
// moderate_messages.php
define('_MODERATION_APPROVE_SUCCESS', 'The post(s) have been approved.');
define('_MODERATION_DELETE_ERROR', 'ERROR: The post(s) could not be deleted.');
define('_MODERATION_APPROVE_ERROR', 'ERROR: The post(s) could not be approved.');
// listcat.php
define('_GEN_NOFORUMS', 'There are no forums in this category!');
//new in 1.1.3 stable
define('_POST_GHOST_FAILED', 'Failed to create ghost topic in old forum!');
define('_POST_MOVE_GHOST', 'Leave ghost message in old forum');
//new in 1.1 Stable
define('_GEN_FORUM_JUMP', 'Forum Jump');
define('_COM_A_FORUM_JUMP', 'Enable Forum Jump');
define('_COM_A_FORUM_JUMP_DESC', 'If set to &quot;Yes,&quot; a selector will be shown on the forum pages that allows for a quick jump to another forum or category.');
//new in 1.1 RC1
define('_GEN_RULES', 'Rules');
define('_COM_A_RULESPAGE', 'Enable Rules Page');
define('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes,&quot; a link to your rules [page will be shown in the header menu. This page can be used for things like forum rules, etc. You can alter the contents of this file by opening rules.php in /joomla_root/components/com_kunena. <em>Make sure to always save a backup of this file. It will be overwritten when upgrading!</em>');
define('_MOVED_TOPIC', 'MOVED:');
define('_COM_A_PDF', 'Enable PDF creation');
define('_COM_A_PDF_DESC',
    'Set to &quot;Yes&quot; if you would like to enable users to download a simple PDF document with the contents of a thread.<br />It is a <u>simple</u> PDF document with no mark-up or fancy layout, but it contains all the thread text.');
define('_GEN_PDFA', 'Click this button to create a PDF document from this thread (opens in a new window).');
define('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
define('_VIEW_PROFILE', 'Click here to see the profile of this user');
define('_VIEW_ADDBUDDY', 'Click here to add this user to your buddy list');
define('_POST_SUCCESS_POSTED', 'Your message has been successfully posted');
define('_POST_SUCCESS_VIEW', '[ Return to the topic ]');
define('_POST_SUCCESS_FORUM', '[ Return to the forum ]');
define('_RANK_ADMINISTRATOR', 'Admin');
define('_RANK_MODERATOR', 'Moderator');
define('_SHOW_LASTVISIT', 'Since last visit');
define('_COM_A_BADWORDS_TITLE', 'Bad Words filtering');
define('_COM_A_BADWORDS', 'Use bad words filtering');
define('_COM_A_BADWORDS_DESC', 'Set to &quot;Yes&quot; if you want to filter posts containing the words you defined in the Badword Component configuration. To use this function you must have the Badword Component installed!');
define('_COM_A_BADWORDS_NOTICE', '* This message has been censored because it contained one or more words flagged by the administrator.*');
define('_COM_A_AVATAR_SRC', 'Use avatar picture from');
define('_COM_A_AVATAR_SRC_DESC',
    'If you have JomSocial or the Community Builder component installed, you can configure Kunena to use the user avatar picture from those user profiles. Note: For Community Builder you need to have the thumbnail option enabled because the forum uses the user thumbnail images instead of originals.');
define('_COM_A_KARMA', 'Show Karma indicator');
define('_COM_A_KARMA_DESC', 'Set to &quot;Yes&quot; to show user karma and related buttons (increase / decrease) if the user stats are activated.');
define('_COM_A_DISEMOTICONS', 'Disable emoticons');
define('_COM_A_DISEMOTICONS_DESC', 'Set to &quot;Yes&quot; to completely disable graphic emoticons (smileys).');
define('_COM_C_FBCONFIG', 'Kunena Configuration');
define('_COM_C_FBCONFIGDESC', 'Configure all Kunena\'s functionality');
define('_COM_C_FORUM', 'Forum Administration');
define('_COM_C_FORUMDESC', 'Add categories/forums and configure them');
define('_COM_C_USER', 'User Administration');
define('_COM_C_USERDESC', 'Basic user and user profile administration');
define('_COM_C_FILES', 'Uploaded Files Browser');
define('_COM_C_FILESDESC', 'Browse and administer uploaded files');
define('_COM_C_IMAGES', 'Uploaded Images Browser');
define('_COM_C_IMAGESDESC', 'Browse and administer uploaded images');
define('_COM_C_CSS', 'Edit CSS File');
define('_COM_C_CSSDESC', 'Tweak Kunena\'s look and feel');
define('_COM_C_SUPPORT', 'Support Web Site');
define('_COM_C_SUPPORTDESC', 'Connect to the Kunena Web site (new window)');
define('_COM_C_PRUNETAB', 'Prune Forums');
define('_COM_C_PRUNETABDESC', 'Remove old threads (configurable)');
define('_COM_C_PRUNEUSERS', 'Prune Users'); // <=FB 1.0.3
define('_COM_C_PRUNEUSERSDESC', 'Sync Kunena user table with Joomla! user table'); // <=FB 1.0.3
define('_COM_C_LOADMODPOS', 'Load Module Positions');
define('_COM_C_LOADMODPOSDESC', 'Load Module Positions for Kunena Template');
define('_COM_C_UPGRADEDESC', 'Get your database up to the latest version after an upgrade');
define('_COM_C_BACK', 'Back to Kunena Control Panel');
define('_SHOW_LAST_SINCE', 'Active topics since last visit on:');
define('_POST_SUCCESS_REQUEST2', 'Your request has been processed');
define('_POST_NO_PUBACCESS3', 'Click here to register.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
define('_POST_SUCCESS_DELETE', 'The message has been successfully deleted.');
define('_POST_SUCCESS_EDIT', 'The message has been successfully edited.');
define('_POST_SUCCESS_MOVE', 'The Topic has been succesfully moved.');
define('_POST_SUCCESS_POST', 'Your message has been successfully posted.');
define('_POST_SUCCESS_SUBSCRIBE', 'Your subscription has been processed.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
define('_KARMA', 'Karma');
define('_KARMA_SMITE', 'Smite');
define('_KARMA_APPLAUD', 'Applaud');
define('_KARMA_BACK', 'To get back to the topic,');
define('_KARMA_WAIT', 'You can modify only one person\'s karma every 6 hours. <br/>Please wait until this timeout period has passed before modifying any person\'s karma again.');
define('_KARMA_SELF_DECREASE', 'Please do not attempt to decrease your own karma!');
define('_KARMA_SELF_INCREASE', 'Your karma has been decreased for attempting to increase it yourself!');
define('_KARMA_DECREASED', 'User\'s karma decreased. If you are not taken back to the topic in a few moments,');
define('_KARMA_INCREASED', 'User\'s karma increased. If you are not taken back to the topic in a few moments,');
define('_COM_A_TEMPLATE', 'Template');
define('_COM_A_TEMPLATE_DESC', 'Choose the template to use.');
define('_COM_A_TEMPLATE_IMAGE_PATH', 'Image Sets');
define('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Choose the images set template to use.');
//==========================================
//new in 1.0 Stable
define('_COM_A_POSTSTATSBAR', 'Use Posts Statistics Bar');
define('_COM_A_POSTSTATSBAR_DESC', 'Set to &quot;Yes&quot; if you want the number of posts a user has made to be depicted graphically by a Statistics Bar.');
define('_COM_A_POSTSTATSCOLOR', 'Color number for Statistics Bar');
define('_COM_A_POSTSTATSCOLOR_DESC', 'Give the number of the color you want to use for the Post Statistics Bar');
define('_LATEST_REDIRECT',
    'Kunena needs to (re)establish your access privileges before it can create a list of the latest posts for you.\nDo not worry. This is quite normal after more than 30 minutes of inactivity or after logging back in.\nPlease submit your search request again.');
define('_SMILE_COLOUR', 'Color');
define('_SMILE_SIZE', 'Size');
define('_COLOUR_DEFAULT', 'Standard');
define('_COLOUR_RED', 'Red');
define('_COLOUR_PURPLE', 'Purple');
define('_COLOUR_BLUE', 'Blue');
define('_COLOUR_GREEN', 'Green');
define('_COLOUR_YELLOW', 'Yellow');
define('_COLOUR_ORANGE', 'Orange');
define('_COLOUR_DARKBLUE', 'Darkblue');
define('_COLOUR_BROWN', 'Brown');
define('_COLOUR_GOLD', 'Gold');
define('_COLOUR_SILVER', 'Silver');
define('_SIZE_NORMAL', 'Normal');
define('_SIZE_SMALL', 'Small');
define('_SIZE_VSMALL', 'Very Small');
define('_SIZE_BIG', 'Big');
define('_SIZE_VBIG', 'Very Big');
define('_IMAGE_SELECT_FILE', 'Select image file to attach');
define('_FILE_SELECT_FILE', 'Select file to attach');
define('_FILE_NOT_UPLOADED', 'Your file has not been uploaded. Try posting again or editing the post.');
define('_IMAGE_NOT_UPLOADED', 'Your image has not been uploaded. Try posting again or editing the post.');
define('_BBCODE_IMGPH', 'Insert [img] placeholder in the post for attached image'); // Deprecated in 1.0.10
define('_BBCODE_FILEPH', 'Insert [file] placeholder in the post for attached file'); // Deprecated in 1.0.10
define('_POST_ATTACH_IMAGE', '[img]');
define('_POST_ATTACH_FILE', '[file]');
define('_USER_UNSUBSCRIBE_ALL', 'Check this box to <strong><u>unsubscribe</u></strong> from all topics (including invisible ones for troubleshooting purposes)');
define('_LINK_JS_REMOVED', '<em>Active link containing JavaScript has been removed automatically.</em>');
//==========================================
//new in 1.0 RC4
define('_COM_A_LOOKS', 'Look and Feel');
define('_COM_A_USERS', 'User Related');
define('_COM_A_LENGTHS', 'Various length settings');
define('_COM_A_SUBJECTLENGTH', 'Max. Subject length');
define('_COM_A_SUBJECTLENGTH_DESC',
    'Maximum Subject line length. The maximum number supported by the database is 255 characters. If your site is configured to use multi-byte character sets like Unicode, UTF-8 or non-ISO-8599-x, make the maximum smaller using this forumula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Example: for UTF-8, for which the max. character bite syze per character is 4 bytes: 255/4=63.');
define('_LATEST_THREADFORUM', 'Topic/Forum');
define('_LATEST_NUMBER', 'New posts');
define('_COM_A_SHOWNEW', 'Show New posts');
define('_COM_A_SHOWNEW_DESC', 'If set to &quot;Yes,&quot; Kunena will show the user an indicator for forums that contain new posts and which posts are new since their last visit.');
define('_COM_A_NEWCHAR', '&quot;New&quot; indicator');
define('_COM_A_NEWCHAR_DESC', 'define here what should be used to indicate new posts (like an &quot;!&quot; or &quot;New!&quot;)');
define('_LATEST_AUTHOR', 'Latest post author');
define('_GEN_FORUM_NEWPOST', 'New Posts');
define('_GEN_FORUM_NOTNEW', 'No New Posts');
define('_GEN_UNREAD', 'Unread Topic');
define('_GEN_NOUNREAD', 'Read Topic');
define('_GEN_MARK_ALL_FORUMS_READ', 'Mark all forums read');
define('_GEN_MARK_THIS_FORUM_READ', 'Mark this forum read');
define('_GEN_FORUM_MARKED', 'All posts in this forum have been marked as read');
define('_GEN_ALL_MARKED', 'All posts have been marked as read');
define('_IMAGE_UPLOAD', 'Image Upload');
define('_IMAGE_DIMENSIONS', 'Your image file can be a maximum (width x height - size)');
define('_IMAGE_ERROR_TYPE', 'Please use only JPEG, GIF, or PNG images');
define('_IMAGE_ERROR_EMPTY', 'Please select a file before uploading');
define('_IMAGE_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
define('_IMAGE_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
define('_IMAGE_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
define('_IMAGE_UPLOADED', 'Your image has been uploaded.');
define('_COM_A_IMAGE', 'Images');
define('_COM_A_IMGHEIGHT', 'Max. Image Height');
define('_COM_A_IMGWIDTH', 'Max. Image Width');
define('_COM_A_IMGSIZE', 'Max. Image Filesize<br/><em>in Kilobytes</em>');
define('_COM_A_IMAGEUPLOAD', 'Allow Public Upload for Images');
define('_COM_A_IMAGEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload an image.');
define('_COM_A_IMAGEREGUPLOAD', 'Allow Registered Upload for Images');
define('_COM_A_IMAGEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged-in users to be able to upload images.<br/> Note: (Super)administrators and moderators are always able to upload images.');
//New since preRC4-II:
define('_COM_A_UPLOADS', 'Uploads');
define('_FILE_TYPES', 'Your file can be of type - max. size');
define('_FILE_ERROR_TYPE', 'You are only allowed to upload files of type:\n');
define('_FILE_ERROR_EMPTY', 'Please select a file before uploading');
define('_FILE_ERROR_SIZE', 'The file size exceeds the maximum set by the Administrator.');
define('_COM_A_FILE', 'Files');
define('_COM_A_FILEALLOWEDTYPES', 'File types allowed');
define('_COM_A_FILEALLOWEDTYPES_DESC', 'Specify here which file types are allowed for upload. Use comma-separated, <strong>lowercase</strong> lists without spaces.<br />Example: zip,txt,exe,htm,html');
define('_COM_A_FILESIZE', 'Max. File size<br/><em>in Kilobytes</em>');
define('_COM_A_FILEUPLOAD', 'Allow File Upload for Public');
define('_COM_A_FILEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload a file.');
define('_COM_A_FILEREGUPLOAD', 'Allow File Upload for Registered');
define('_COM_A_FILEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged-in users to be able to upload a file.<br/> Note: (Super)administrators and moderators are always able to upload files.');
define('_SUBMIT_CANCEL', 'Your post submission has been cancelled.');
define('_HELP_SUBMIT', 'Click here to submit your message'); // Deprecated in 1.0.10
define('_HELP_PREVIEW', 'Click here to see what your message will look like when submitted'); // Deprecated in 1.0.10
define('_HELP_CANCEL', 'Click here to cancel your message'); // Deprecated in 1.0.10
define('_POST_DELETE_ATT', 'If this box is checked, all image and file attachments of posts you are going to delete will be deleted as well (recommended).');
//new since preRC4-III
define('_COM_A_USER_MARKUP', 'Show Edited Mark Up');
define('_COM_A_USER_MARKUP_DESC', 'Set to &quot;Yes&quot; if you want an edited post be marked up with text showing that the post was edited by a user and when.');
define('_EDIT_BY', 'Post edited by:');
define('_EDIT_AT', 'at:');
define('_UPLOAD_ERROR_GENERAL', 'An error occured when uploading your avatar. Please try again or notify your system administrator.');
define('_COM_A_IMGB_IMG_BROWSE', 'Uploaded Images Browser');
define('_COM_A_IMGB_FILE_BROWSE', 'Uploaded Files Browser');
define('_COM_A_IMGB_TOTAL_IMG', 'Number of uploaded images');
define('_COM_A_IMGB_TOTAL_FILES', 'Number of uploaded files');
define('_COM_A_IMGB_ENLARGE', 'Click the image to see its full size');
define('_COM_A_IMGB_DOWNLOAD', 'Click the file image to download it');
define('_COM_A_IMGB_DUMMY_DESC',
    'The option &quot;Replace with dummy&quot; will replace the selected image with a dummy image.<br /> This allows you to remove the actual file without breaking the post.<br /><small><em>Please note that sometimes an explicit browser refresh is needed to see the dummy replacement.</em></small>');
define('_COM_A_IMGB_DUMMY', 'Current dummy image');
define('_COM_A_IMGB_REPLACE', 'Replace with dummy');
define('_COM_A_IMGB_REMOVE', 'Remove entirely');
define('_COM_A_IMGB_NAME', 'Name');
define('_COM_A_IMGB_SIZE', 'Size');
define('_COM_A_IMGB_DIMS', 'Dimensions');
define('_COM_A_IMGB_CONFIRM', 'Are you absolutely sure you want to delete this file? \n Deleting a file will create a broken referencing post...');
define('_COM_A_IMGB_VIEW', 'Open post (to edit)');
define('_COM_A_IMGB_NO_POST', 'No referencing post!');
define('_USER_CHANGE_VIEW', 'Changes in these settings will become active the next time you visit the forums.');
define('_MOSBOT_DISCUSS_A', 'Discuss this article on the forums. (');
define('_MOSBOT_DISCUSS_B', '&#32;posts)');
define('_POST_DISCUSS', 'This thread discusses the content article');
define('_COM_A_RSS', 'Enable RSS feed');
define('_COM_A_RSS_DESC', 'The RSS feed enables users to download the latest posts to their desktop or RSS reader aplication. See <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.');
define('_LISTCAT_RSS', 'get the latest posts directly to your desktop');
define('_SEARCH_REDIRECT', 'Kunena needs to (re)establish your access privileges before it can perform your search request.\nDo not worry, this is quite normal after more than 30 minutes of inactivity.\nPlease submit your search request again.');
//====================
//admin.forum.html.php
define('_COM_A_CONFIG', 'Kunena Configuration');
define('_COM_A_DISPLAY', 'Display #');
define('_COM_A_CURRENT_SETTINGS', 'Current Setting');
define('_COM_A_EXPLANATION', 'Explanation');
define('_COM_A_BOARD_TITLE', 'Board Title');
define('_COM_A_BOARD_TITLE_DESC', 'The name of your board');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//define('_COM_A_VIEW_TYPE', 'Default View type');
//define('_COM_A_VIEW_TYPE_DESC', 'Choose between a threaded or flat view as default');
define('_COM_A_THREADS', 'Threads Per Page');
define('_COM_A_THREADS_DESC', 'Number of threads per page to show');
define('_COM_A_REGISTERED_ONLY', 'Registered Users Only');
define('_COM_A_REG_ONLY_DESC', 'Set to &quot;Yes&quot; to allow only registered users to use the Forum (view & post). Set to &quot;No&quot; to allow any visitor to use the Forum.');
define('_COM_A_PUBWRITE', 'Public Read/Write');
define('_COM_A_PUBWRITE_DESC', 'Set to &quot;Yes&quot; to allow for public write privileges. Set to &quot;No&quot; to allow any visitor to see posts, but only registered users to write posts.');
define('_COM_A_USER_EDIT', 'User Edits');
define('_COM_A_USER_EDIT_DESC', 'Set to &quot;Yes&quot; to allow registered users to edit their own posts.');
define('_COM_A_MESSAGE', 'In order to save changes, press the &quot;Save&quot; button at the top.');
define('_COM_A_HISTORY', 'Show History');
define('_COM_A_HISTORY_DESC', 'Set to &quot;Yes&quot; if you want the topic history shown when a reply/quote is made');
define('_COM_A_SUBSCRIPTIONS', 'Allow Subscriptions');
define('_COM_A_SUBSCRIPTIONS_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to subscribe to a topic and receive e-mail notifications on new posts');
define('_COM_A_HISTLIM', 'History Limit');
define('_COM_A_HISTLIM_DESC', 'Number of posts to show in the history');
define('_COM_A_FLOOD', 'Flood Protection');
define('_COM_A_FLOOD_DESC', 'The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection <em>can</em> cause degradation of performance.');
define('_COM_A_MODERATION', 'E-mail Moderators');
define('_COM_A_MODERATION_DESC',
    'Set to &quot;Yes&quot; if you want e-mail notifications on new posts sent to the forum moderator(s). Note: Although every (super)administrator has automatically all Moderator privileges, assign them explicitly as moderators on
 the forum to recieve e-mails too!');
define('_COM_A_SHOWMAIL', 'Show E-mail');
define('_COM_A_SHOWMAIL_DESC', 'Set to &quot;No&quot; if you never want to display the users e-mail address even to registered users.');
define('_COM_A_AVATAR', 'Allow Avatars');
define('_COM_A_AVATAR_DESC', 'Set to &quot;Yes&quot; if you want registered users to have an avatar (manageable through their profile).');
define('_COM_A_AVHEIGHT', 'Max. Avatar Height');
define('_COM_A_AVWIDTH', 'Max. Avatar Width');
define('_COM_A_AVSIZE', 'Max. Avatar Filesize<br/><em>in Kilobytes</em>');
define('_COM_A_USERSTATS', 'Show User Stats');
define('_COM_A_USERSTATS_DESC', 'Set to &quot;Yes&quot; to show User Statistics like number of user posts and user type (Admin, Moderator, User, etc.).');
define('_COM_A_AVATARUPLOAD', 'Allow Avatar Upload');
define('_COM_A_AVATARUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to upload an avatar.');
define('_COM_A_AVATARGALLERY', 'Use Avatars Gallery');
define('_COM_A_AVATARGALLERY_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to choose an avatar from a gallery you provide (components/com_kunena/avatars/gallery).');
define('_COM_A_RANKING_DESC', 'Set to &quot;Yes&quot; if you want to show the rank registered users have based on the number of posts they made.<br/><strong>Note: You must also enable User Stats in the Advanced tab to show it.</strong>');
define('_COM_A_RANKINGIMAGES', 'Use Rank Images');
define('_COM_A_RANKINGIMAGES_DESC',
    'Set to &quot;Yes&quot; if you want to show the rank registered users have using an image (from components/com_kunena/ranks). Turning this of will show the text for that rank. Check the documentation on www.kunena.com for more information on ranking images.');

//email and stuff
define('_COM_A_NO', 'No');
define('_COM_A_YES', 'Yes');
define('_COM_A_FLAT', 'Flat');
define('_COM_A_THREADED', 'Threaded');
define('_COM_A_MESSAGES', 'Messages per page');
define('_COM_A_MESSAGES_DESC', 'Number of messages per page to show');
//admin; changes from 0.9 to 0.9.1
define('_COM_A_USERNAME', 'Username');
define('_COM_A_USERNAME_DESC', 'Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the user\'s real name');
define('_COM_A_CHANGENAME', 'Allow Name Change');
define('_COM_A_CHANGENAME_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to change their name when posting. If set to &quot;No,&quot; registered users will not be able to edit their names.');
//admin; changes 0.9.1 to 0.9.2
define('_COM_A_BOARD_OFFLINE', 'Forum Offline');
define('_COM_A_BOARD_OFFLINE_DESC', 'Set to &quot;Yes&quot; if you want to take the Forum section offline. The Forum will still remain browseable by site (super)admins.');
define('_COM_A_BOARD_OFFLINE_MES', 'Forum Offline Message');
define('_COM_A_PRUNE', 'Prune Forums');
define('_COM_A_PRUNE_NAME', 'Forum to prune:');
define('_COM_A_PRUNE_DESC',
    'The Prune Forums function allows you to prune threads where there have not been any new posts during the specified number of days. This does not remove stickies or locked topics and these must be removed manually. Threads in locked forums can not be pruned.');
define('_COM_A_PRUNE_NOPOSTS', 'Prune threads with no posts for the past&#32;');
define('_COM_A_PRUNE_DAYS', 'days');
define('_COM_A_PRUNE_USERS', 'Prune Users'); // <=FB 1.0.3
define('_COM_A_PRUNE_USERS_DESC',
    'This function allows you to prune your Kunena user list against the Joomla site user list. It will delete all profiles for Kunena users that have been deleted from your Joomla Framework.<br/>If you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.'); // <=FB 1.0.3
//general
define('_GEN_ACTION', 'Action');
define('_GEN_AUTHOR', 'Author');
define('_GEN_BY', 'by');
define('_GEN_CANCEL', 'Cancel');
define('_GEN_CONTINUE', 'Submit');
define('_GEN_DATE', 'Date');
define('_GEN_DELETE', 'Delete');
define('_GEN_EDIT', 'Edit');
define('_GEN_EMAIL', 'E-mail');
define('_GEN_EMOTICONS', 'Emoticons');
define('_GEN_FLAT', 'Flat');
define('_GEN_FLAT_VIEW', 'Flat');
define('_GEN_FORUMLIST', 'Forum List');
define('_GEN_FORUM', 'Forum');
define('_GEN_HELP', 'Help');
define('_GEN_HITS', 'Views');
define('_GEN_LAST_POST', 'Last Post');
define('_GEN_LATEST_POSTS', 'Show latest posts');
define('_GEN_LOCK', 'Lock');
define('_GEN_UNLOCK', 'Unlock');
define('_GEN_LOCKED_FORUM', 'Forum is locked');
define('_GEN_LOCKED_TOPIC', 'Topic is locked');
define('_GEN_MESSAGE', 'Message');
define('_GEN_MODERATED', 'Forum is moderated. Reviewed prior to publishing.');
define('_GEN_MODERATORS', 'Moderators');
define('_GEN_MOVE', 'Move');
define('_GEN_NAME', 'Name');
define('_GEN_POST_NEW_TOPIC', 'Post New Topic');
define('_GEN_POST_REPLY', 'Post Reply');
define('_GEN_MYPROFILE', 'My Profile');
define('_GEN_QUOTE', 'Quote');
define('_GEN_REPLY', 'Reply');
define('_GEN_REPLIES', 'Replies');
define('_GEN_THREADED', 'Threaded');
define('_GEN_THREADED_VIEW', 'Threaded');
define('_GEN_SIGNATURE', 'Signature');
define('_GEN_ISSTICKY', 'Topic is sticky.');
define('_GEN_STICKY', 'Sticky');
define('_GEN_UNSTICKY', 'Unsticky');
define('_GEN_SUBJECT', 'Subject');
define('_GEN_SUBMIT', 'Submit');
define('_GEN_TOPIC', 'Topic');
define('_GEN_TOPICS', 'Topics');
define('_GEN_TOPIC_ICON', 'topic icon');
define('_GEN_SEARCH_BOX', 'Search Forum');
$_GEN_THREADED_VIEW = "Threaded";
$_GEN_FLAT_VIEW = "Flat";
//avatar_upload.php
define('_UPLOAD_UPLOAD', 'Upload');
define('_UPLOAD_DIMENSIONS', 'Your image file can be maximum (width x height - size)');
define('_UPLOAD_SUBMIT', 'Submit a new avatar for upload');
define('_UPLOAD_SELECT_FILE', 'Select file');
define('_UPLOAD_ERROR_TYPE', 'Please use only jpeg, gif or png images');
define('_UPLOAD_ERROR_EMPTY', 'Please select a file before uploading');
define('_UPLOAD_ERROR_NAME', 'The image file must contain only alphanumeric characters and no spaces please.');
define('_UPLOAD_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
define('_UPLOAD_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
define('_UPLOAD_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
define('_UPLOAD_ERROR_CHOOSE', "You didn't choose an avatar from the gallery...");
define('_UPLOAD_UPLOADED', 'Your avatar has been uploaded.');
define('_UPLOAD_GALLERY', 'Choose one from the avatar gallery:');
define('_UPLOAD_CHOOSE', 'Confirm Choice.');
// listcat.php
define('_LISTCAT_ADMIN', 'An administrator should create them first from the&#32;');
define('_LISTCAT_DO', 'They will know what to do&#32;');
define('_LISTCAT_INFORM', 'Inform them and tell them to hurry up!');
define('_LISTCAT_NO_CATS', 'There are no categories in the forum defined yet.');
define('_LISTCAT_PANEL', 'Administration Panel of the Joomla OS CMS.');
define('_LISTCAT_PENDING', 'pending message(s)');
// moderation.php
define('_MODERATION_MESSAGES', 'There are no pending messages in this forum.');
// post.php
define('_POST_ABOUT_TO_DELETE', 'You are about to delete the message');
define('_POST_ABOUT_DELETE', '<strong>NOTES:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Consider blanking the post and poster\'s name if only the contents should be removed..
<br/>
- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.');
define('_POST_CLICK', 'click here');
define('_POST_ERROR', 'Could not find username/e-mail. Severe database error not listed');
define('_POST_ERROR_MESSAGE', 'An unknown SQL error occured and your message was not posted. If the problem persists, please contact the administrator.');
define('_POST_ERROR_MESSAGE_OCCURED', 'An error has occured and the message was not updated. Please try again. If this error persists, please contact the administrator.');
define('_POST_ERROR_TOPIC', 'An error occured during the delete(s). Please check the error below:');
define('_POST_FORGOT_NAME', 'You forgot to include your name. Click your browser&#146s back button to go back and try again.');
define('_POST_FORGOT_SUBJECT', 'You forgot to include a subject. Click your browser&#146s back button to go back and try again.');
define('_POST_FORGOT_MESSAGE', 'You forgot to include a message. Click your browser&#146s back button to go back and try again.');
define('_POST_INVALID', 'An invalid post ID was requested.');
define('_POST_LOCK_SET', 'The topic has been locked.');
define('_POST_LOCK_NOT_SET', 'The topic could not be locked.');
define('_POST_LOCK_UNSET', 'The topic has been unlocked.');
define('_POST_LOCK_NOT_UNSET', 'The topic could not be unlocked.');
define('_POST_MESSAGE', 'Post a new message in&#32;');
define('_POST_MOVE_TOPIC', 'Move this topic to forum&#32;');
define('_POST_NEW', 'Post a new message in:&#32;');
define('_POST_NO_SUBSCRIBED_TOPIC', 'Your subscription to this topic could not be processed.');
define('_POST_NOTIFIED', 'Check this box to have yourself notified about replies to this topic.');
define('_POST_STICKY_SET', 'The sticky bit has been set for this topic.');
define('_POST_STICKY_NOT_SET', 'The sticky bit could not be set for this topic.');
define('_POST_STICKY_UNSET', 'The sticky bit has been unset for this topic.');
define('_POST_STICKY_NOT_UNSET', 'The sticky bit could not be unset for this topic.');
define('_POST_SUBSCRIBE', 'subscribe');
define('_POST_SUBSCRIBED_TOPIC', 'You have been subscribed to this topic.');
define('_POST_SUCCESS', 'Your message has been successfully');
define('_POST_SUCCES_REVIEW', 'Your message has been successfully posted. It will be reviewed by a moderator before it will be published on the forum.');
define('_POST_SUCCESS_REQUEST', 'Your request has been processed. If you are not taken back to the topic in a few moments,');
define('_POST_TOPIC_HISTORY', 'Topic History of');
define('_POST_TOPIC_HISTORY_MAX', 'Max. showing the last');
define('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(Last post first)</i>');
define('_POST_TOPIC_NOT_MOVED', 'Your topic could not be moved. To get back to the topic:');
define('_POST_TOPIC_FLOOD1', 'The administrator of this forum has enabled flood protection. You must wait&#32;');
define('_POST_TOPIC_FLOOD2', '&#32;seconds before you can make another post.');
define('_POST_TOPIC_FLOOD3', 'Please click your browser&#146s back button to get back to the forum.');
define('_POST_EMAIL_NEVER', 'your e-mail address will never be displayed on the site.');
define('_POST_EMAIL_REGISTERED', 'your e-mail address will only be available to registered users.');
define('_POST_LOCKED', 'locked by the administrator.');
define('_POST_NO_NEW', 'New replies are not allowed.');
define('_POST_NO_PUBACCESS1', 'The administrator has disabled public write access.');
define('_POST_NO_PUBACCESS2', 'Only logged-in/registered users<br /> are allowed to contribute to the forum.');
// showcat.php
define('_SHOWCAT_NO_TOPICS', '>> There are no topics in this forum yet <<');
define('_SHOWCAT_PENDING', 'pending message(s)');
// userprofile.php
define('_USER_DELETE', '&#32;check this box to delete your signature');
define('_USER_ERROR_A', 'You came to this page in error. Please inform the administrator on which links&#32;');
define('_USER_ERROR_B', 'you clicked that got you here. They can then file a bug report.');
define('_USER_ERROR_C', 'Thank you!');
define('_USER_ERROR_D', 'Error number to include in your report:&#32;');
define('_USER_GENERAL', 'General Profile Options');
define('_USER_MODERATOR', 'You are assigned as a moderator to forums');
define('_USER_MODERATOR_NONE', 'No forums found assigned to you');
define('_USER_MODERATOR_ADMIN', 'Admins are moderator on all forums.');
define('_USER_NOSUBSCRIPTIONS', 'No subscriptions found for you');
//define('_USER_PREFERED', 'Prefered Viewtype');
define('_USER_PROFILE', 'Profile for&#32;');
define('_USER_PROFILE_NOT_A', 'Your profile could&#32;');
define('_USER_PROFILE_NOT_B', 'not');
define('_USER_PROFILE_NOT_C', '&#32;be updated.');
define('_USER_PROFILE_UPDATED', 'Your profile is updated.');
define('_USER_RETURN_A', 'If you are not taken back to your profile in a few moments&#32;');
define('_USER_RETURN_B', 'click here');
define('_USER_SUBSCRIPTIONS', 'Your Subscriptions');
define('_USER_UNSUBSCRIBE', 'Unsubscribe');
define('_USER_UNSUBSCRIBE_A', 'You could&#32;');
define('_USER_UNSUBSCRIBE_B', 'not');
define('_USER_UNSUBSCRIBE_C', '&#32;be unsubscribed from the topic.');
define('_USER_UNSUBSCRIBE_YES', 'You have been unsubscribed from the topic.');
define('_USER_DELETEAV', '&#32;check this box to delete your Avatar');
//New 0.9 to 1.0
define('_USER_ORDER', 'Preferred Message Ordering');
define('_USER_ORDER_DESC', 'Last post first');
define('_USER_ORDER_ASC', 'First post first');
// view.php
define('_VIEW_DISABLED', 'The administrator has disabled public write access.');
define('_VIEW_POSTED', 'Posted by');
define('_VIEW_SUBSCRIBE', ':: Subscribe to this topic ::');
define('_MODERATION_INVALID_ID', 'An invalid post ID was requested.');
define('_VIEW_NO_POSTS', 'There are no posts in this forum.');
define('_VIEW_VISITOR', 'Visitor');
define('_VIEW_ADMIN', 'Admin');
define('_VIEW_USER', 'User');
define('_VIEW_MODERATOR', 'Moderator');
define('_VIEW_REPLY', 'Reply to this message');
define('_VIEW_EDIT', 'Edit this message');
define('_VIEW_QUOTE', 'Quote this message in a new post');
define('_VIEW_DELETE', 'Delete this message');
define('_VIEW_STICKY', 'Set this topic sticky');
define('_VIEW_UNSTICKY', 'Unset this topic sticky');
define('_VIEW_LOCK', 'Lock this topic');
define('_VIEW_UNLOCK', 'Unlock this topic');
define('_VIEW_MOVE', 'Move this topic to another forum');
define('_VIEW_SUBSCRIBETXT', 'Subscribe to this topic and get notified by mail about new posts');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
define('_HOME', 'Forum');
define('_POSTS', 'Posts:');
define('_TOPIC_NOT_ALLOWED', 'Post');
define('_FORUM_NOT_ALLOWED', 'Forum');
define('_FORUM_IS_OFFLINE', 'Forum is OFFLINE!');
define('_PAGE', 'Page:&#32;');
define('_NO_POSTS', 'No Posts');
define('_CHARS', 'characters max.');
define('_HTML_YES', 'HTML is disabled');
define('_YOUR_AVATAR', '<b>Your Avatar</b>');
define('_NON_SELECTED', 'Not yet selected <br />');
define('_SET_NEW_AVATAR', 'Select new avatar');
define('_THREAD_UNSUBSCRIBE', 'Unsubscribe');
define('_SHOW_LAST_POSTS', 'Active topics in last');
define('_SHOW_HOURS', 'hours');
define('_SHOW_POSTS', 'Total:&#32;');
define('_DESCRIPTION_POSTS', 'The newest posts for the active topics are shown');
define('_SHOW_4_HOURS', '4 Hours');
define('_SHOW_8_HOURS', '8 Hours');
define('_SHOW_12_HOURS', '12 Hours');
define('_SHOW_24_HOURS', '24 Hours');
define('_SHOW_48_HOURS', '48 Hours');
define('_SHOW_WEEK', 'Week');
define('_POSTED_AT', 'Posted at');
define('_DATETIME', 'Y/m/d H:i');
define('_NO_TIMEFRAME_POSTS', 'There are no new posts in the timeframe you selected.');
define('_MESSAGE', 'Message');
define('_NO_SMILIE', 'no');
define('_FORUM_UNAUTHORIZIED', 'This forum is open only to registered and logged-in users.');
define('_FORUM_UNAUTHORIZIED2', 'If you are already registered, please log in first.');
define('_MESSAGE_ADMINISTRATION', 'Moderation');
define('_MOD_APPROVE', 'Approve');
define('_MOD_DELETE', 'Delete');
//NEW in RC1
define('_SHOW_LAST', 'Show most recent message');
define('_POST_WROTE', 'wrote');
define('_COM_A_EMAIL', 'Board E-mail Address');
define('_COM_A_EMAIL_DESC', 'This is the board\'s e-mail address. Make this a valid e-mail address');
define('_COM_A_WRAP', 'Wrap Words Longer Than');
define('_COM_A_WRAP_DESC',
    'Enter the maximum number of characters a single word may have. This feature allows you to tune the output of Kunena Posts to your template.<br/> 70 characters is probably the maximum for fixed width templates but you might need to experiment a bit.<br/>URLs, no matter how long, are not affected by the word wrap.');
define('_COM_A_SIGNATURE', 'Max. Signature Length');
define('_COM_A_SIGNATURE_DESC', 'Maximum number of characters allowed for the user signature.');
define('_SHOWCAT_NOPENDING', 'No pending message(s)');
define('_COM_A_BOARD_OFSET', 'Board Time Offset');
define('_COM_A_BOARD_OFSET_DESC', 'Some boards are located on servers in a different time zone than the users. Set the time offset for the post time in hours. Both positive and negative numbers can be used.');
//New in RC2
define('_COM_A_BASICS', 'Basics');
define('_COM_A_FRONTEND', 'Frontend');
define('_COM_A_SECURITY', 'Security');
define('_COM_A_AVATARS', 'Avatars');
define('_COM_A_INTEGRATION', 'Integration');
define('_COM_A_PMS', 'Enable private messaging');
define('_COM_A_PMS_DESC',
    'Select the appropriate private messaging component if you have one installed.');
define('_VIEW_PMS', 'Click here to send a Private Message to this user');
//new in RC3
define('_POST_RE', 'Re:');
define('_BBCODE_BOLD', 'Bold text: [b]text[/b]&#32;'); // Deprecated in 1.0.10
define('_BBCODE_ITALIC', 'Italic text: [i]text[/i]'); // Deprecated in 1.0.10
define('_BBCODE_UNDERL', 'Underline text: [u]text[/u]'); // Deprecated in 1.0.10
define('_BBCODE_QUOTE', 'Quote text: [quote]text[/quote]'); // Deprecated in 1.0.10
define('_BBCODE_CODE', 'Code display: [code]code[/code]'); // Deprecated in 1.0.10
define('_BBCODE_ULIST', 'Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items'); // Deprecated in 1.0.10
define('_BBCODE_OLIST', 'Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items'); // Deprecated in 1.0.10
define('_BBCODE_IMAGE', 'Image: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]'); // Deprecated in 1.0.10
define('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]This is a link[/url]'); // Deprecated in 1.0.10
define('_BBCODE_CLOSA', 'Close all tags'); // Deprecated in 1.0.10
define('_BBCODE_CLOSE', 'Close all open bbCode tags'); // Deprecated in 1.0.10
define('_BBCODE_COLOR', 'Color: [color=#FF6600]text[/color]'); // Deprecated in 1.0.10
define('_BBCODE_SIZE', 'Size: [size=1]text size[/size] - Tip: sizes range from 1 to 5'); // Deprecated in 1.0.10
define('_BBCODE_LITEM', 'List Item: [li] list item [/li]'); // Deprecated in 1.0.10
define('_BBCODE_HINT', 'bbCode Help - Tip: bbCode can be used on selected text!'); // Deprecated in 1.0.10
define('_COM_A_TAWIDTH', 'Textarea Width');
define('_COM_A_TAWIDTH_DESC', 'Adjust the width of the reply/post text entry area to match your template. <br/>The Topic Emoticon Toolbar will be wrapped accross two lines if width <= 420 pixels');
define('_COM_A_TAHEIGHT', 'Textarea Height');
define('_COM_A_TAHEIGHT_DESC', 'Adjust the height of the reply/post text entry area to match your template');
define('_COM_A_ASK_EMAIL', 'Require E-mail');
define('_COM_A_ASK_EMAIL_DESC', 'Require an e-mail address when users or visitors make a post. Set to &quot;No&quot; if you want this feature to be skipped on the front end. Posters will not be asked for their e-mail address.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Rank Management');
define('_KUNENA_SORTRANKS', 'Sort by Ranks');

define('_KUNENA_RANKSIMAGE', 'Rank Image');
define('_KUNENA_RANKS', 'Rank Title');
define('_KUNENA_RANKS_SPECIAL', 'Special');
define('_KUNENA_RANKSMIN', 'Minimum Post Count');
define('_KUNENA_RANKS_ACTION', 'Actions');
define('_KUNENA_NEW_RANK', 'New Rank');
?>