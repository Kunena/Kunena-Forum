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

// 1.5.11
define('_KUNENA_SECTION','Section');
define('_KUNENA_NOBODY','Nobody');
//Disable userlist
define('_USERLIST_DISABLED','The Kunena userlist has been disabled, you can not access to it');
define('_KUNENA_ADMIN_CONFIG_USERLIST_ENABLE','Enable the userlist');
define('_KUNENA_ADMIN_CONFIG_USERLIST_ENABLE_DESC','Set the userlist on enabled or disable it if you don\'t want use it');

// 1.5.10
DEFINE('_KUNENA_PARENTDESC', 'Note: To create a category, choose <em>Top Level Category</em> as the parent. A category serves as a container for forums.<br />A forum can only be created within a category by selecting an existing category as the parent for the forum.<br /> Messages can only be posted to forums, not categories.');
DEFINE('_KUNENA_ADMIN', 'Forum Administration');
DEFINE('_KUNENA_NOTEUS', 'Note: Only users which have the moderator flag set in their Kunena profile are shown here. In order to be able to add a moderator, set the moderator flag and then go to <a href="index.php?option=com_kunena&task=profiles">User Administration</a>. Search for the user to make a moderator and update their profile. The moderator flag can only be set by a Site Administrator.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Set to <em>Yes</em> if you want to show user avatars in Category view, Recent Discussions, and My Discussions.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Show avatars in Category view, Recent Discussions, and My Discussions?');
DEFINE('_KUNENA_SORTID', 'Sort by UserID');
DEFINE('_KUNENA_SORTMOD', 'Sort by Moderator');
DEFINE('_KUNENA_SORTNAME', 'Sort by Name');
DEFINE('_KUNENA_SORTREALNAME', 'Sort by Real Name');
define('_KUNENA_PDF_NOT_GENERATED_MESSAGE_DELETED', 'The PDF cannot be generated because the thread was deleted.');
//Hide IP
define('_KUNENA_COM_A_HIDE_IP', 'Hide the IP in messages from moderators.');
define('_KUNENA_COM_A_HIDE_IP_DESC', 'Hide the IP in messages from moderators and display IP¨only to administrators.');
//JomSocial Activity Stream Integration disable/enable
define('_COM_A_JS_ACTIVITYSTREAM_INTEGRATION', 'Enable the JomSocial Activity Stream Integration');
define('_COM_A_JS_ACTIVITYSTREAM_INTEGRATION_DESC', 'The activity stream on the JomSocial wall displays the latest messages or topics posted in the Kunena forum.');
// Email
define('_KUNENA_EMAIL_INVALID', 'Forum tried to send email from an invalid address. Please contact the site administrator!');
define('_KUNENA_MY_EMAIL_INVALID', 'Your email address is invalid. A valid email address is required to post in this forum!');

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
DEFINE('_KUNENA_MOVED', 'Moved');

// 1.0.11 and 1.5.3
DEFINE('_KUNENA_VERSION_SVN', 'SVN Revision');
DEFINE('_KUNENA_VERSION_DEV', 'Development Snapshot');
DEFINE('_KUNENA_VERSION_ALPHA', 'Alpha Release');
DEFINE('_KUNENA_VERSION_BETA', 'Beta Release');
DEFINE('_KUNENA_VERSION_RC', 'Release Candidate');
DEFINE('_KUNENA_VERSION_INSTALLED', 'You have installed Kunena %s (%s).');
DEFINE('_KUNENA_VERSION_SVN_WARNING', 'Never use an SVN revision for anything else other than software development!');
DEFINE('_KUNENA_VERSION_DEV_WARNING', 'This internal release should be used only by developers and testers!');
DEFINE('_KUNENA_VERSION_ALPHA_WARNING', 'This release should not be used on live production sites.');
DEFINE('_KUNENA_VERSION_BETA_WARNING', 'This release is not recommended to be used on live production sites.');
DEFINE('_KUNENA_VERSION_RC_WARNING', 'This release may contain bugs, which will be fixed in the final version.');
DEFINE('_KUNENA_ERROR_UPGRADE', 'Upgrading Kunena to version %s has failed!');
DEFINE('_KUNENA_ERROR_UPGRADE_WARN', 'Your forum may be missing some important fixes and some features may be broken.');
DEFINE('_KUNENA_ERROR_UPGRADE_AGAIN', 'Please try to upgrade again. If you cannot upgrade to Kunena %s, you can easily downgrade to the latest working version.');
DEFINE('_KUNENA_PAGE', 'Page');
DEFINE('_KUNENA_RANK_NO_ASSIGNED', 'No Rank Assigned');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_GENERAL', 'Problems detected in Community Builder integration:');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_INSTALL', 'Community Builder integration only works if you have Community Builder version %s or higher installed.');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_PUBLISH', 'Community Builder Profile integration only works if Community Builder User profile has been published.');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_UPDATE', 'Community Builder Profile integration only works if you are using Community Builder version %s or higher.');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_XHTML', 'Community Builder Profile integration only works if Community Builder is in W3C XHTML 1.0 Trans. compliance mode.');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_INTEGRATION', 'Community Builder Profile integration only works if the forum integration plugin has been enabled in Community Builder.');
DEFINE('_KUNENA_INTEGRATION_CB_WARN_HIDE', 'Saving the Kunena configuration will disable integration and hide this warning.');

// 1.0.10
DEFINE('_KUNENA_BACK', 'Back');
DEFINE('_KUNENA_SYNC', 'Sync');
DEFINE('_KUNENA_NEW_SMILIE', 'New Smilie');
DEFINE('_KUNENA_PRUNE', 'Prune');
// Editor
DEFINE('_KUNENA_EDITOR_HELPLINE_BOLD', 'Bold text: [b]text[/b]');
DEFINE('_KUNENA_EDITOR_HELPLINE_ITALIC', 'Italic text: [i]text[/i]');
DEFINE('_KUNENA_EDITOR_HELPLINE_UNDERL', 'Underline text: [u]text[/u]');
DEFINE('_KUNENA_EDITOR_HELPLINE_STRIKE', 'Strikethrough Text: [strike]Text[/strike]');
DEFINE('_KUNENA_EDITOR_HELPLINE_SUB', 'Subscript Text: [sub]Text[/sub]');
DEFINE('_KUNENA_EDITOR_HELPLINE_SUP', 'Superscript Text: [sup]Text[/sup]');
DEFINE('_KUNENA_EDITOR_HELPLINE_QUOTE', 'Quote text: [quote]text[/quote]');
DEFINE('_KUNENA_EDITOR_HELPLINE_CODE', 'Code display: [code]code[/code]');
DEFINE('_KUNENA_EDITOR_HELPLINE_UL', 'Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items');
DEFINE('_KUNENA_EDITOR_HELPLINE_OL', 'Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items');
DEFINE('_KUNENA_EDITOR_HELPLINE_LI', 'List Item: [li] list item [/li]');
DEFINE('_KUNENA_EDITOR_HELPLINE_ALIGN_LEFT', 'Align left: [left]Text[/left]');
DEFINE('_KUNENA_EDITOR_HELPLINE_ALIGN_CENTER', 'Align center: [center]Text[/center]');
DEFINE('_KUNENA_EDITOR_HELPLINE_ALIGN_RIGHT', 'Align right: [right]Text[/right]');
DEFINE('_KUNENA_EDITOR_HELPLINE_IMAGELINK', 'Image link: [img size=400]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE', 'Image link: Size');
DEFINE('_KUNENA_EDITOR_HELPLINE_IMAGELINKURL', 'Image link: URL of the image link');
DEFINE('_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY', 'Image link: Apply image link');
DEFINE('_KUNENA_EDITOR_HELPLINE_LINK', 'Link: [url=http://www.zzz.com/]This is a link[/url]');
DEFINE('_KUNENA_EDITOR_HELPLINE_LINKURL', 'Link: URL of the link');
DEFINE('_KUNENA_EDITOR_HELPLINE_LINKTEXT', 'Link: Text / Description of the link');
DEFINE('_KUNENA_EDITOR_HELPLINE_LINKAPPLY', 'Link: Apply link');
DEFINE('_KUNENA_EDITOR_HELPLINE_HIDE','Hidden text: [hide]any hidden text[/hide] - hide part of message from Guests');
DEFINE('_KUNENA_EDITOR_HELPLINE_SPOILER', 'Spoiler: Text is only shown after you click the spoiler');
DEFINE('_KUNENA_EDITOR_HELPLINE_COLOR', 'Color: [color=#FF6600]text[/color]');
DEFINE('_KUNENA_EDITOR_HELPLINE_FONTSIZE', 'Fontsize: [size=1]text size[/size] - Tip: sizes range from 1 to 5');
DEFINE('_KUNENA_EDITOR_HELPLINE_FONTSIZESELECTION', 'Fontsize: Select Fontsize, mark text and press the button left from here');
DEFINE('_KUNENA_EDITOR_HELPLINE_EBAY', 'eBay: [ebay]ItemId[/ebay]');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEO', 'Video: Select Provider or URL - modus');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOSIZE', 'Video: Size of the video');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOWIDTH', 'Video: Width of the video');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOHEIGHT', 'Video: Height of the video');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOPROVIDER', 'Video: Select video provider');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOID', 'Video: ID of the video - you can see it in the video URL');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY1', 'Video: [video size=100 width=480 height=360 provider=clipfish]3423432[/video]');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOURL', 'Video: URL of the video');
DEFINE('_KUNENA_EDITOR_HELPLINE_VIDEOAPPLY2', 'Video: [video size=100 width=480 height=360]http://myvideodomain.com/myvideo[/video]');
DEFINE('_KUNENA_EDITOR_HELPLINE_IMGPH', 'Insert [img] placeholder in the post for attached image');
DEFINE('_KUNENA_EDITOR_HELPLINE_FILEPH', 'Insert [file] placeholder in the post for attached file');
DEFINE('_KUNENA_EDITOR_HELPLINE_SUBMIT', 'Click here to submit your message');
DEFINE('_KUNENA_EDITOR_HELPLINE_PREVIEW', 'Click here to see what your message will look like when submitted');
DEFINE('_KUNENA_EDITOR_HELPLINE_CANCEL', 'Click here to cancel your message');
DEFINE('_KUNENA_EDITOR_HELPLINE_HINT', 'bbCode Help - Tip: bbCode can be used on selected text!');
DEFINE('_KUNENA_EDITOR_LINK_URL', ' URL: ');
DEFINE('_KUNENA_EDITOR_LINK_TEXT', ' Text: ');
DEFINE('_KUNENA_EDITOR_LINK_INSERT', 'Insert');
DEFINE('_KUNENA_EDITOR_IMAGE_SIZE', ' Size: ');
DEFINE('_KUNENA_EDITOR_IMAGE_URL', ' URL: ');
DEFINE('_KUNENA_EDITOR_IMAGE_INSERT', 'Insert');
DEFINE('_KUNENA_EDITOR_VIDEO_SIZE', 'Size: ');
DEFINE('_KUNENA_EDITOR_VIDEO_WIDTH', 'Width: ');
DEFINE('_KUNENA_EDITOR_VIDEO_HEIGHT', 'Height:');
DEFINE('_KUNENA_EDITOR_VIDEO_URL', 'URL: ');
DEFINE('_KUNENA_EDITOR_VIDEO_ID', 'ID: ');
DEFINE('_KUNENA_EDITOR_VIDEO_PROVIDER', 'Provider: ');
DEFINE('_KUNENA_BBCODE_HIDDENTEXT', '<span class="fb_quote">Something is hidden for guests. Please log in or register to see it.</span>');

DEFINE('_KUNENA_PROFILE_BIRTHDAY', 'Birthday');
DEFINE('_KUNENA_DT_MONTHDAY_FMT','%m/%d');
DEFINE('_KUNENA_CFC_FILENAME','CSS file to be modified');
DEFINE('_KUNENA_CFC_SAVED','CSS file saved.');
DEFINE('_KUNENA_CFC_NOTSAVED','CSS file not saved.');
DEFINE('_KUNENA_JS_WARN_NAME_MISSING','Your name is missing');
DEFINE('_KUNENA_JS_WARN_UNAME_MISSING','Your username is missing');
DEFINE('_KUNENA_JS_WARN_VALID_AZ09','Field contains forbidden letters');
DEFINE('_KUNENA_JS_WARN_MAIL_MISSING','E-mail address is missing');
DEFINE('_KUNENA_JS_WARN_PASSWORD2','Please enter valid password');
DEFINE('_KUNENA_JS_PROMPT_UNAME','Please retype your new username');
DEFINE('_KUNENA_JS_PROMPT_PASS','Please retype your new password');
DEFINE('_KUNENA_DT_LMON_DEC', 'December');
DEFINE('_KUNENA_DT_MON_DEC', 'Dec');
DEFINE('_KUNENA_NOGENDER', 'Unknown');
DEFINE('_KUNENA_ERROR_INCOMPLETE_ERROR', 'Your Kunena installation is incomplete!');
DEFINE('_KUNENA_ERROR_INCOMPLETE_OFFLINE', 'Because of the above errors your Forum is now Offline and Forum Administration has been disabled.');
DEFINE('_KUNENA_ERROR_INCOMPLETE_REASONS', 'Possible reasons for this error:');
DEFINE('_KUNENA_ERROR_INCOMPLETE_1', '1) Kunena installation process has failed or timed out (try to install it again)');
DEFINE('_KUNENA_ERROR_INCOMPLETE_2', '2) You have manually modified or removed some of the Kunena tables from your database');
DEFINE('_KUNENA_ERROR_INCOMPLETE_3', 'You can find solutions to the most common issues on our community documentation wiki: <a href="http://docs.kunena.com/index.php/Installation_Issues">Kunena Documentation Wiki</a>');
DEFINE('_KUNENA_ERROR_INCOMPLETE_SUPPORT', 'Our support forum can be found from:');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Installed version');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'License');
DEFINE('_KUNENA_PROFILE_NO_USER', 'User does not exist.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'User has not yet visited forum and has no profile.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Search Results');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Advanced Search');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Search by Keyword');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Keywords');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Search entire posts');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Search titles only');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Search by User Name');
DEFINE('_KUNENA_SEARCH_UNAME', 'User Name');
DEFINE('_KUNENA_SEARCH_EXACT', 'Exact Name');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Messages posted by');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Threads started by');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Activity in threads');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Search Options');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Find Threads with');
DEFINE('_KUNENA_SEARCH_LEAST', 'At least');
DEFINE('_KUNENA_SEARCH_MOST', 'At most');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Answers');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Find Posts from');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Any date');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Last visit');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Yesterday');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'A week ago');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 weeks ago');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'A month ago');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 months ago');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 months ago');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'A year ago');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'And newer');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'And older');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sort Results by');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Title');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Number of posts');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Number of views');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Thread start date');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Posting date');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'User name');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Increasing order');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Decreasing order');
DEFINE('_KUNENA_SEARCH_START', 'Jump to Result Number');
DEFINE('_KUNENA_SEARCH_LIMIT5', 'Show 5 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT10', 'Show 10 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT15', 'Show 15 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT20', 'Show 20 Search Results');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Search in Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'All Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Also search in child forums');
DEFINE('_KUNENA_SEARCH_SEND', 'Search');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Cancel');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'No messages containing all your search terms were found.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'At least one keyword should be over 3 characters long!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'You don\'t have moderator permissions!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'This thread has <b>NOT</b> been added to your favorites');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Sync the Kunena user table with the Joomla user table');
DEFINE('_POST_FORGOT_EMAIL', 'You forgot to include your e-mail address.  Click your browser&#146s back button to go back and try again.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Everything deleted. Some attachment files were missing!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Main Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'In order to provide additional information for you guests and members, the forum header can be leveraged to display text at the very top of a particular category.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Welcome Mat');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'We encourage new members to post a short introduction of themselves in this forum category. Get to know each other and share you common interests.
');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Welcome to the Kunena forum![/b]

Tell us and our members who you are, what you like and why you became a member of this site.
We welcome all new members and hope to see you around a lot!
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Suggestion Box');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Have some feedback and input to share?
Don\'t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'This is the optional Forum header for the Suggestion Box.
');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Welcome to Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Welcome to Kunena![/b][/size]

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
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the Kunena code tag highlighting Javascript. If your members post PHP or other code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from becoming malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Default RSS type');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Choose between RSS feeds &quot;By Thread &quot; or &quot;By Post.&quot; &quot;By Thread &quot; means that only one entry per thread will be listed in the RSS feed independent of how many posts have been made within that thread. &quot;By Thread&quot; creates a shorter, more compact RSS feed but will not list every reply.');
DEFINE('_COM_A_RSS_BY_THREAD', 'By Thread');
DEFINE('_COM_A_RSS_BY_POST', 'By Post');
DEFINE('_COM_A_RSS_HISTORY', 'RSS History');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Select how much history should be included in the RSS feed. The default is one month, but it is recommended to limit it to one week on high volume sites.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Week');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Month');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Year');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Default Kunena Page');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default Kunena page that is displayed when a forum link is clicked or the forum is initially entered. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Recent Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'My Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categories');
DEFINE('_KUNENA_BBCODE_HIDE', 'The following is hidden from unregistered users:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'The parent forum must not be the same.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'The parent forum is one of its own children.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID does not exist.');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'You forgot to enter your name.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'You forgot to enter your e-mail.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'You forgot to enter a subject.');
DEFINE('_KUNENA_EDIT_TITLE', 'Edit Your Details');
DEFINE('_KUNENA_YOUR_NAME', 'Your Name:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'User Name:');
DEFINE('_KUNENA_PASS', 'Password:');
DEFINE('_KUNENA_VPASS', 'Verify Password:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'User details have been saved.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trim long URLs');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Set to &quot;Yes&quot; if you want long URLs to be trimmed. See URL trim front and back settings.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Front portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Number of characters for front portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Back portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Auto embed YouTube videos');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Set to &quot;Yes&quot; if you want youtube video urls to get automatically embedded.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Auto embed eBay items');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay widget language code');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Session Lifetime');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and the NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Merge');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Merge this thread with');
DEFINE('_POST_MERGE_GHOST', 'Leave ghost copy of thread');
DEFINE('_POST_SUCCESS_MERGE', 'Thread successfully merged.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Merge failed.');
DEFINE('_GEN_SPLIT', 'Split');
DEFINE('_GEN_DOSPLIT', 'Go');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Thread split successfully.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Topic successfully changed.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Topic change failed.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Split failed.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicate. Identical message has been ignored.');
DEFINE('_POST_SPLIT_HINT', '<br />Tip: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'merge');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'This thread has been removed from your favorites.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'This thread has <b>NOT</b> been removed from your favorites.');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Your request to remove from favorites has been processed.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'This thread has been removed from your subscriptions.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'This thread has <b>NOT</b> been removed from your subscriptions.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Your request to remove from subscriptions has been processed.');
DEFINE('_POST_NO_DEST_CATEGORY', 'No destination category was selected. Nothing was moved.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Recent Discussions');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'My Discussions');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_KUNENA_CATEGORY', 'Category:');
DEFINE('_KUNENA_CATEGORIES', 'Categories');
DEFINE('_KUNENA_POSTED_AT', 'Posted');
DEFINE('_KUNENA_AGO', 'ago');
DEFINE('_KUNENA_DISCUSSIONS', 'Discussions');
DEFINE('_KUNENA_TOTAL_THREADS', 'Total Threads:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'Month');
DEFINE('_SHOW_YEAR', 'Year');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Copying "%src%" to "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Saving CSS file should be here: file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'The attachment table was successfully upgraded to the latest 1.0.x series structure.');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'The attachments in the message table were successfully upgraded to the latest 1.0.x series structure.');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Could not delete the post(s). Nothing else deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats.');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum.");
DEFINE('_KUNENA_UNIST_SUCCESS', "The Kunena Forum component was successfully uninstalled.");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena Forum Component version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generated: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'No forums to search in.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Error adding users:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Users syncronized. Deleted:');
DEFINE('_KUNENA_USERSSYNCADD', ', add:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'user profiles.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'No eligible profiles found to synchronize.');
DEFINE('_KUNENA_SYNC_USERS', 'Synchronize Users');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synchronize the Kunena user table with the Joomla user table.');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'E-mail Administrators');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want e-mail notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Edit Rank');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Hide E-mail');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Sunday');
DEFINE('_KUNENA_DT_LDAY_MON', 'Monday');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Tuesday');
DEFINE('_KUNENA_DT_LDAY_WED', 'Wednesday');
DEFINE('_KUNENA_DT_LDAY_THU', 'Thursday');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Friday');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Saturday');
DEFINE('_KUNENA_DT_DAY_SUN', 'Sun');
DEFINE('_KUNENA_DT_DAY_MON', 'Mon');
DEFINE('_KUNENA_DT_DAY_TUE', 'Tue');
DEFINE('_KUNENA_DT_DAY_WED', 'Wed');
DEFINE('_KUNENA_DT_DAY_THU', 'Thu');
DEFINE('_KUNENA_DT_DAY_FRI', 'Fri');
DEFINE('_KUNENA_DT_DAY_SAT', 'Sat');
DEFINE('_KUNENA_DT_LMON_JAN', 'January');
DEFINE('_KUNENA_DT_LMON_FEB', 'February');
DEFINE('_KUNENA_DT_LMON_MAR', 'March');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'May');
DEFINE('_KUNENA_DT_LMON_JUN', 'June');
DEFINE('_KUNENA_DT_LMON_JUL', 'July');
DEFINE('_KUNENA_DT_LMON_AUG', 'August');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'October');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'May');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Oct');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_CHILD_BOARD', 'Child Board');
DEFINE('_WHO_ONLINE_GUEST', 'Guest');
DEFINE('_WHO_ONLINE_MEMBER', 'Member');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'none');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Click here to continue...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Apply!');
DEFINE('_KUNENA_NO_ACCESS', 'You do not have access to this forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% ago');
DEFINE('_KUNENA_DATE_YEARS', 'Years');
DEFINE('_KUNENA_DATE_MONTHS', 'Months');
DEFINE('_KUNENA_DATE_WEEKS','Weeks');
DEFINE('_KUNENA_DATE_DAYS', 'Days');
DEFINE('_KUNENA_DATE_HOURS', 'Hours');
DEFINE('_KUNENA_DATE_MINUTES', 'Minutes');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forum header:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum display");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffixes applied to index, showcat, view, and allow for different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'User Edit Time');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Set to 0 for unlimited time, else window
in seconds from post or last modification to allow edit.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], allows
storing a modification up to 600 seconds after edit link disappears');
DEFINE('_KUNENA_HELPPAGE','Enable Help Page');
DEFINE('_KUNENA_HELPPAGE_DESC','If set to &quot;Yes,&quot; a link to your help page will be shown in the header menu.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Show help in Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','If set to &quot;Yes,&quot; help content will be included in Kunena and the external Help page link will be disabled. <b>Note:</b> you should add a Help Content ID.');
DEFINE('_KUNENA_HELPPAGE_CID','Help Content ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','You should set <b>&quot;YES&quot;</b> &quot;Show help in Kunena&quot; setting.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Help external page link');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','If you show help external link, please set <b>&quot;NO&quot;</b> &quot;Show help in Kunena&quot; setting.');
DEFINE('_KUNENA_RULESPAGE','Enable Rules Page');
DEFINE('_KUNENA_RULESPAGE_DESC','If set to &quot;Yes,&quot; a link to your rules page will be shown in the header menu.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Show rules in Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','If set to &quot;Yes,&quot; rules content text will be included in Kunena and the external rules page link will be disabled. <b>Note:</b> you should add a Rules Content ID.');
DEFINE('_KUNENA_RULESPAGE_CID','Rules Content ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','You should set <b>&quot;YES&quot;</b> &quot;Show rules in Kunena&quot; setting.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Rules external page link');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','If you show rules external link, please set <b>&quot;NO&quot;</b> &quot;Show rules in Kunena&quot; setting.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library not found');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Library not found');
DEFINE('_KUNENA_GD_INSTALLED','GD is available, version&#32;');
DEFINE('_KUNENA_GD_NO_VERSION','Can not detect GD version');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD is not installed. You can get more info&#32;');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Small Image Height :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Small Image Width :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium Image Height :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Medium Image Width :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Large Image Height :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Large Image Width :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar Quality');
DEFINE('_KUNENA_WELCOME','Welcome to Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','Thank you for choosing Kunena as your forum solution. This screen will give you a quick overview of your board statistics. The links on the left-hand side of this screen allow you to control every aspect of your board setup. Each page has instructions on how to use the tools.');
DEFINE('_KUNENA_STATISTIC','Statistic');
DEFINE('_KUNENA_VALUE','Value');
DEFINE('_GEN_CATEGORY','Category');
DEFINE('_GEN_STARTEDBY','Started by:&#32;');
DEFINE('_GEN_STATS','stats');
DEFINE('_STATS_TITLE',' forum - stats');
DEFINE('_STATS_GEN_STATS','General stats');
DEFINE('_STATS_TOTAL_MEMBERS','Members:');
DEFINE('_STATS_TOTAL_REPLIES','Replies:');
DEFINE('_STATS_TOTAL_TOPICS','Topics:');
DEFINE('_STATS_TODAY_TOPICS','Topics today:');
DEFINE('_STATS_TODAY_REPLIES','Replies today:');
DEFINE('_STATS_TOTAL_CATEGORIES','Categories:');
DEFINE('_STATS_TOTAL_SECTIONS','Sections:');
DEFINE('_STATS_LATEST_MEMBER','Latest member:');
DEFINE('_STATS_YESTERDAY_TOPICS','Topics yesterday:');
DEFINE('_STATS_YESTERDAY_REPLIES','Replies yesterday:');
DEFINE('_STATS_POPULAR_PROFILE','Popular 10 Members (Based on profile hits)');
DEFINE('_STATS_TOP_POSTERS','Top posters');
DEFINE('_STATS_POPULAR_TOPICS','Top popular topics');
DEFINE('_COM_A_STATSPAGE','Enable Stats Page');
DEFINE('_COM_A_STATSPAGE_DESC','If set to &quot;Yes,&quot; a public link to your stats page will be shown in the header menu. This page displays various statistics about your forum. <em>The stats page is always visible to admins.</em>');
DEFINE('_COM_C_JBSTATS','Forum Stats');
DEFINE('_COM_C_JBSTATS_DESC','Forum Statistics');
define('_GEN_GENERAL','General');
define('_PERM_NO_READ','You do not have sufficient permissions to access this forum.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smiley saved.');
DEFINE ('_KUNENA_SMILEY_DELETED','Smiley deleted.');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Code already exists.');
DEFINE ('_KUNENA_MISSING_PARAMETER','Missing Parameter.');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rank already exists.');
DEFINE ('_KUNENA_RANK_DELETED','Rank Deleted.');
DEFINE ('_KUNENA_RANK_SAVED','Rank saved.');
DEFINE ('_KUNENA_DELETE_SELECTED','Delete selected');
DEFINE ('_KUNENA_MOVE_SELECTED','Move selected');
DEFINE ('_KUNENA_REPORT_LOGGED','Logged');
DEFINE ('_KUNENA_GO','Go');
DEFINE('_KUNENA_MAILFULL','Include complete post content in the e-mail sent to subscribers.');
DEFINE('_KUNENA_MAILFULL_DESC','If &quot;No,&quot; subscribers will receive only titles of new messages.');
DEFINE('_KUNENA_HIDETEXT','Please log in to view this content.');
DEFINE('_BBCODE_HIDE','Hidden text: [hide]any hidden text[/hide] to hide part of a message from Guests');// Deprecated in 1.0.10
DEFINE('_KUNENA_FILEATTACH','File Attachment:&#32;');
DEFINE('_KUNENA_FILENAME','File Name:&#32;');
DEFINE('_KUNENA_FILESIZE','File Size:&#32;');
DEFINE('_KUNENA_MSG_CODE','Code:&#32;');
DEFINE('_KUNENA_CAPTCHA_ON','Spam protection system');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam and antibot CAPTCHA system On/Off');
DEFINE('_KUNENA_CAPDESC','Enter code here');
DEFINE('_KUNENA_CAPERR','Code not correct!');
DEFINE('_KUNENA_COM_A_REPORT', 'Message Reporting');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'If you want to allow users to report any message, select &quot;Yes.&quot;');
DEFINE('_KUNENA_REPORT_MSG', 'Message Reported');
DEFINE('_KUNENA_REPORT_REASON', 'Reason');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Your Message');
DEFINE('_KUNENA_REPORT_SEND', 'Send Report');
DEFINE('_KUNENA_REPORT', 'Report to moderator');
DEFINE('_KUNENA_REPORT_RSENDER', 'Report Sender:&#32;');
DEFINE('_KUNENA_REPORT_RREASON', 'Report Reason:&#32;');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Report Message:&#32;');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Message Poster:&#32;');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Message Subject:&#32;');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Message:&#32;');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Message Link:&#32;');
DEFINE('_KUNENA_REPORT_INTRO', 'was sent you a message because of');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Report succesfully sent!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Code');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Edit Smiley');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Edit Smilies');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'New Smiley');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'More Smilies');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Close Window');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Additional Emoticons');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Pick a smiley');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Allow username change');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Allow username change on my profile plugin page');
DEFINE ('_KUNENA_RECOUNTFORUMS','Recount Category Stats');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','All category statistics have been recounted.');
DEFINE ('_KUNENA_EDITING_REASON','Reason for Editing');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Last Edit');
DEFINE ('_KUNENA_BY','By');
DEFINE ('_KUNENA_REASON','Reason');
DEFINE('_GEN_GOTOBOTTOM', 'Go to bottom');
DEFINE('_GEN_GOTOTOP', 'Go to top');
DEFINE('_STAT_USER_INFO', 'User Info');
DEFINE('_USER_SHOWEMAIL', 'Show E-mail'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Show Online');
DEFINE('_KUNENA_HIDDEN_USERS', 'Hidden Users');
DEFINE('_KUNENA_SAVE', 'Save');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Default Gallery');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Personal Info');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Summary');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'My Avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum Settings');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Look and Layout');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'My Profile Info');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'My Posts');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'My Subscribes');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'My Favorites');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Private Messaging');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Inbox');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'New Message');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Outbox');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Trash');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Settings');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Contacts');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Blocked List');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Additional Info');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Name');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Username');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-mail');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'User Type');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Register Date');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Last Visit Date');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Posts');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profile View');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Personal Text');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Gender');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Birthdate');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Year (YYYY) - Month (MM) - Day (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Location');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'This is your ICQ number.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'This is your AOL Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'This is your Yahoo! Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'This is your Skype handle.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'This is your Gtalk nickname.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Web site');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Web site Name');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Example: Kunena');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Web site URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Example: www.Kunena.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Your MSN messenger e-mail address.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Signature');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Male');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Female');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Posts were deleted successfully');
DEFINE('_KUNENA_DATE_YEAR', 'Year');
DEFINE('_KUNENA_DATE_MONTH', 'Month');
DEFINE('_KUNENA_DATE_WEEK','Week');
DEFINE('_KUNENA_DATE_DAY', 'Day');
DEFINE('_KUNENA_DATE_HOUR', 'Hour');
DEFINE('_KUNENA_DATE_MINUTE', 'Minute');
DEFINE('_KUNENA_IN_FORUM', '&#32;in Forum:&#32;');
DEFINE('_KUNENA_FORUM_AT', '&#32;Forum at:&#32;');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Please note: although no board code and smiley buttons are shown, they are still usable.');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum Tools');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Userlist');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s has <b>%d</b> registered users');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Please enter a value to search!');
DEFINE ('_KUNENA_USRL_SEARCH','Find user');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Search');
DEFINE ('_KUNENA_USRL_LIST_ALL','List all');
DEFINE ('_KUNENA_USRL_NAME','Name');
DEFINE ('_KUNENA_USRL_USERNAME','Username');
DEFINE ('_KUNENA_USRL_GROUP','Group');
DEFINE ('_KUNENA_USRL_POSTS','Posts');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Hits');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Usertype');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Join date');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Last login');
DEFINE ('_KUNENA_USRL_NEVER','Never');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Picture');
DEFINE ('_KUNENA_USRL_ASC','Ascending');
DEFINE ('_KUNENA_USRL_DESC','Descending');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Display');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%m/%d/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Userlist');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Number of userlist rows');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Number of userlist rows');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online Status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Show users online status');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Display Avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Show Real Name');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Show Username');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Show Number of Posts');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Show Karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Show E-mail');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Show User Type');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Show Join Date');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Show Last Visit Date');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Show Profile Hits');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Database Wizard');
DEFINE('_KUNENA_DBMETHOD', 'Choose a preferred installation method:');
DEFINE('_KUNENA_DBCLEAN', 'Clean installation');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade From Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Top Level Category');
DEFINE('_KUNENA_REGISTERED', 'Registered');
DEFINE('_KUNENA_PUBLICBACKEND', 'Public Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Select an item to');
DEFINE('_KUNENA_ERRORSUBS', 'Something went wrong deleting the messages and subscriptions.');
DEFINE('_KUNENA_WARNING', 'Warning...');
DEFINE('_KUNENA_CHMOD1', 'You need to CHMOD this to 766 in order for the file to be updated.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Your config file is');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Select Template');
DEFINE('_KUNENA_CONFIGSAVED', 'Configuration saved.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'FATAL ERROR. Configuration could not be saved.');
DEFINE('_KUNENA_TFINW', 'The file is not writable.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS file saved.');
DEFINE('_KUNENA_SELECTMODTO', 'Select an moderator to');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'You must choose a forum to prune!');
DEFINE('_KUNENA_DELMSGERROR', 'Deleting messages failed:');
DEFINE('_KUNENA_DELMSGERROR1', 'Deleting messages texts failed:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Clearing subscriptions failed:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned for');
DEFINE('_KUNENA_PRUNEDAYS', 'days');
DEFINE('_KUNENA_PRUNEDELETED', 'Deleted:');
DEFINE('_KUNENA_PRUNETHREADS', 'threads');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Error pruning users:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Users pruned. Deleted:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'user profiles'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'No profiles found eligible for pruning.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena tables are upgraded to version');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forum Category');
DEFINE('_KUNENA_IMGDELETED', 'Image deleted');
DEFINE('_KUNENA_FILEDELETED', 'File deleted');
DEFINE('_KUNENA_NOPARENT', 'No Parent');
DEFINE('_KUNENA_DIRCOPERR', 'Error: File');
DEFINE('_KUNENA_DIRCOPERR1', 'could not be copied!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> component <em>for Joomla </em> <br />&copy; 2008 - 2009 by www.Kunena.com<br />All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profile Settings');
DEFINE('_KUNENA_FORUMPRF', 'Profile');
DEFINE('_KUNENA_FORUMPRRDESC', 'If you have Community Builder or JomSocial installed, you can configure Kunena to use their user profiles.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profile');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profile View</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'All Forum Messages');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Topics');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Started by');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Categories');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Date');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Hits');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'No Forum Post');
DEFINE('_KUNENA_TOTALFAVORITE', 'Favoured: &#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Number of child board columns &#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Number of child board column formating under main category&#32;');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Post-subscription checked by default?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Set to &quot;Yes&quot; if you want to post subscription box always checked.');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Category / Forum must have a name');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Show Stats');
DEFINE('_KUNENA_SHOWSTATSDESC', 'If you want to show the Stats, select &quot;Yes.&quot;');
DEFINE('_KUNENA_SHOWWHOIS', 'Show Who is Online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'If you want to show Whois Online, select &quot;Yes.&quot;');
DEFINE('_KUNENA_STATSGENERAL', 'Show General Stats');
DEFINE('_KUNENA_STATSGENERALDESC', 'If you want to show the General Stats, select &quot;Yes.&quot;');
DEFINE('_KUNENA_USERSTATS', 'Show Popular User Stats');
DEFINE('_KUNENA_USERSTATSDESC', 'If you want to show the Popular Stats, select &quot;Yes.&quot;');
DEFINE('_KUNENA_USERNUM', 'Number of Popular User');
DEFINE('_KUNENA_USERPOPULAR', 'Show Popular Subject Stats');
DEFINE('_KUNENA_USERPOPULARDESC', 'If you want to show the Popular Subject, select &quot;Yes.&quot;');
DEFINE('_KUNENA_NUMPOP', 'Number of Popular Subject');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.8. It is a powerful and stylish forum component for a well-deserved content management system, Joomla. It is initially based on the hard work of Joomlaboard and Fireboard and our praise goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for third-party developers.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfect)</li><li>Language defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add Joomla modules inside the forum template itself. Want to have a banner inside your forum?</li><li>Favorite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category-specific image system</li><li>Enhanced pathway</li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community Builder and JomSocial profile options</li><li>Avatar management : Community Builder and JomSocial options<br /></li></ul><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena Team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instructions');
DEFINE('_KUNENA_FINFO', 'Kunena Forum Information');
DEFINE('_KUNENA_CSSEDITOR', 'Kunena Template CSS Editor');
DEFINE('_KUNENA_PATH', 'Path:');
DEFINE('_KUNENA_CSSERROR', 'Please note: The CSS template file must be writable to save changes.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena User Profile Manager');
DEFINE('_KUNENA_VIEW', 'View');
DEFINE('_KUNENA_NOUSERSFOUND', 'No user profiles found.');
DEFINE('_KUNENA_ADDMOD', 'Add Moderator to');
DEFINE('_KUNENA_NOMODSAV', 'There are no possible moderators found. Read the note below.');
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
DEFINE('_KUNENA_BASICS', 'Basics');
DEFINE('_KUNENA_BASICSFORUM', 'Basic Forum Information');
DEFINE('_KUNENA_PARENT', 'Parent:');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Forum name and description');
DEFINE('_KUNENA_NAMEADD', 'Name:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Description:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Forum advanced configuration');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Forum security and access');
DEFINE('_KUNENA_LOCKEDDESC', 'Set to &quot;Yes&quot; if you want to lock this forum. Nobody but Moderators and Admins can create new topics or replies in a locked forum (or move posts to it).');
DEFINE('_KUNENA_LOCKED1', 'Locked:');
DEFINE('_KUNENA_PUBACC', 'Public Access Level:');
DEFINE('_KUNENA_PUBACCDESC',
    'To create a non-public Forum, you can specify the minimum user level that can see/enter the forum here. By default, the minumum user level is set to &quot;Everybody&quot;.<br /><b>Please note:</b> If you restrict the access of a whole category to one or more certain groups, you will hide all forums it contains to anybody not having proper privileges for the category <b>even</b> if one or more of these Forums has a lower access level set! This is also true for moderators. You will need to add a moderator to the category moderator list if they do not have the proper group level to see the category.<br /> Categories cannot be moderated, but moderators can still be added to the moderator list.');
DEFINE('_KUNENA_CGROUPS', 'Include Child Groups:');
DEFINE('_KUNENA_CGROUPSDESC', 'Should child groups also be allowed access? If set to &quot;No,&quot; access to this forum is restricted to the selected group <strong>only</strong>.');
DEFINE('_KUNENA_ADMINLEVEL', 'Admin Access Level:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'If you create a forum with Public Access restrictions, you can specify here an additional Administration Access Level.<br /> If you restrict the access to the Forum to a special Public Frontend user group and don\'t specify a Public Backend Group here, administrators will not be able to enter/view the forum.');
DEFINE('_KUNENA_ADVANCED', 'Advanced');
DEFINE('_KUNENA_CGROUPS1', 'Include Child Groups:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Should child groups be allowed access as well? If set to &quot;No &quot;, access to this forum is restricted to the selected group <strong>only</strong>.');
DEFINE('_KUNENA_REV', 'Review posts:');
DEFINE('_KUNENA_REVDESC',
    'Set to &quot;Yes&quot; if you want posts to be reviewed by moderators prior to publishing them in this forum. This is useful in a moderated forum only!<br />If you set this without any moderators specified, the site admin is solely responsible for approving/deleting submitted posts since these will be kept \'on hold\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderation');
DEFINE('_KUNENA_MODNEWDESC', 'Moderation of the Forum and Forum moderators');
DEFINE('_KUNENA_MOD', 'Moderated:');
DEFINE('_KUNENA_MODDESC',
    'Set to &quot;Yes&quot; if you want to be able to assign Moderators to this forum.<br /><strong>Note:</strong> This doesn\'t mean that new posts must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>Please note:</strong> after setting moderation to &quot;Yes,&quot; you must save the forum configuration first before you will be able to use the new button to add moderators.');
DEFINE('_KUNENA_MODHEADER', 'Moderation settings for this forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderators assigned to this forum:');
DEFINE('_KUNENA_NOMODS', 'There are no Moderators assigned to this forum');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Edit');
DEFINE('_KUNENA_ADD', 'Add');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Move Up');
DEFINE('_KUNENA_MOVEDOWN', 'Move Down');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'All Registered');
DEFINE('_KUNENA_EVERYBODY', 'Everybody');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Reorder');
DEFINE('_KUNENA_CHECKEDOUT', 'Check Out');
DEFINE('_KUNENA_ADMINACCESS', 'Admin Access');
DEFINE('_KUNENA_PUBLICACCESS', 'Public Access');
DEFINE('_KUNENA_PUBLISHED', 'Published');
DEFINE('_KUNENA_REVIEW', 'Review');
DEFINE('_KUNENA_MODERATED', 'Moderated');
DEFINE('_KUNENA_LOCKED', 'Locked');
DEFINE('_KUNENA_CATFOR', 'Category / Forum');
DEFINE('_KUNENA_CP', 'Kunena Control Panel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar Integration');
DEFINE('_COM_A_RANKS_SETTINGS', 'Ranks');
DEFINE('_COM_A_RANKING_SETTINGS', 'Ranking Settings');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Avatar Settings');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Security Settings');
DEFINE('_COM_A_BASIC_SETTINGS', 'Basic Settings');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Allow Favorites');
DEFINE('_COM_A_FAVORITES_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to favorite a topic&#32;');
DEFINE('_USER_UNFAVORITE_ALL', 'Check this box to <b><u>unfavorite</u></b> from all topics (including invisible ones for troubleshooting purposes).');
DEFINE('_VIEW_FAVORITETXT', 'Favorite this topic&#32;');
DEFINE('_USER_UNFAVORITE_YES', 'You have unfavorited the topic.');
DEFINE('_POST_FAVORITED_TOPIC', 'This thread has been added to your favorites.');
DEFINE('_VIEW_UNFAVORITETXT', 'Unfavorite');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Unsubscribe');
DEFINE('_USER_NOFAVORITES', 'No Favorites');
DEFINE('_POST_SUCCESS_FAVORITE', 'Your request to add to favorites has been processed.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Search Results');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Messages per page for search results');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Use Joomla Style?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'If you want to use the Joomla style set to &quot;Yes.&quot; (CSS classes: sectionheader, sectionentry1, etc.)&#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Show Child Category Image');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'If you want to show child category small icon on your forum list, set to &quot;Yes.&quot;&#32;');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Show Announcement');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Set to &quot;Yes&quot; if you want to show the announcement box on your Forum home page.');
DEFINE('_KUNENA_RECENT_POSTS', 'Recent Post Settings');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Show Recent Posts');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Set to &quot;Yes&quot; if you want to show recent post plugin on your forum.');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Number of Recent Posts');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Number of Recent Posts');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Count Per Tab&#32;');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Number of Posts per tab');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Show Category');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Specific category you can show on recent posts. For example: 2, 3, 7&#32;');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Show Single Subject');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Show Single Subject');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Show Reply Subject');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Show Reply Subject (Re:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Subject Length');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Subject Length');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Show Date');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Show Date');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Show Hits');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Show Hits');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Show Author');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=username, 2=realname, 0=none');
DEFINE('_KUNENA_STATS', 'Stats Plugin Settings&#32;');
DEFINE('_KUNENA_CATIMAGEPATH', 'Category Image Path&#32;');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Category Image path. If you set the path as &quot;category_images,&quot; the full path will be "your_html_rootfolder/images/fbfiles/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'Announcement Moderator IDs&#32;');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Add user IDs for announcement moderators (e.g. 62,63,73). Announcement moderators can add, edit, and delete the announcements.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Board Categories&#32;');
DEFINE('_KUNENA_CHILD_BOARDS', 'Child Boards&#32;');
DEFINE('_KUNENA_QUICKMSG', 'Quick Reply&#32;');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Threads in Forum&#32;');
DEFINE('_KUNENA_FORUM', 'Forum&#32;');
DEFINE('_KUNENA_SPOTS', 'Spotlights');
DEFINE('_KUNENA_CANCEL', 'cancel');
DEFINE('_KUNENA_TOPIC', 'TOPIC:&#32;');
DEFINE('_KUNENA_POWEREDBY', 'Powered by&#32;');
// Time Format
DEFINE('_TIME_TODAY', '<b>Today</b>&#32;');
DEFINE('_TIME_YESTERDAY', '<b>Yesterday</b>&#32;');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Latest Posts');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Who is Online');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Forum Main');
DEFINE('_KUNENA_GUEST', 'Guest');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'viewing');
DEFINE('_KUNENA_ATTACH', 'Attachment');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Favorite');
DEFINE('_USER_FAVORITES', 'My Favorites');
DEFINE('_THREAD_UNFAVORITE', 'Remove from Favorites');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Welcome');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Show Latest Posts');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Set My Avatar');
DEFINE('_PROFILEBOX_MYPROFILE', 'My Profile');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Show My Posts');
DEFINE('_PROFILEBOX_GUEST', 'Guest');
DEFINE('_PROFILEBOX_LOGIN', 'Login');
DEFINE('_PROFILEBOX_REGISTER', 'Register');
DEFINE('_PROFILEBOX_LOGOUT', 'Logout');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Lost Password?');
DEFINE('_PROFILEBOX_PLEASE', 'Please');
DEFINE('_PROFILEBOX_OR', 'or');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Recent Posts');
DEFINE('_RECENT_TOPICS', 'Topic');
DEFINE('_RECENT_AUTHOR', 'Author');
DEFINE('_RECENT_CATEGORIES', 'Categories');
DEFINE('_RECENT_DATE', 'Date');
DEFINE('_RECENT_HITS', 'Hits');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Announcements');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Date');
DEFINE('_ANN_TITLE', 'Title');
DEFINE('_ANN_SORTTEXT', 'Short Text');
DEFINE('_ANN_LONGTEXT', 'Long Text');
DEFINE('_ANN_ORDER', 'Order');
DEFINE('_ANN_PUBLISH', 'Publish');
DEFINE('_ANN_PUBLISHED', 'Published');
DEFINE('_ANN_UNPUBLISHED', 'Unpublished');
DEFINE('_ANN_EDIT', 'Edit');
DEFINE('_ANN_DELETE', 'Delete');
DEFINE('_ANN_SUCCESS', 'Success');
DEFINE('_ANN_SAVE', 'Save');
DEFINE('_ANN_YES', 'Yes');
DEFINE('_ANN_NO', 'No');
DEFINE('_ANN_ADD', 'Add New');
DEFINE('_ANN_SUCCESS_EDIT', 'Success Edit');
DEFINE('_ANN_SUCCESS_ADD', 'Success Added');
DEFINE('_ANN_DELETED', 'Success Deleted');
DEFINE('_ANN_ERROR', 'ERROR');
DEFINE('_ANN_READMORE', 'Read More...');
DEFINE('_ANN_CPANEL', 'Announcement Control Panel');
DEFINE('_ANN_SHOWDATE', 'Show Date');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Forum Stats');
DEFINE('_STAT_GENERAL_STATS', 'General Stats');
DEFINE('_STAT_TOTAL_USERS', 'Total Users');
DEFINE('_STAT_LATEST_MEMBERS', 'Latest Member');
DEFINE('_STAT_PROFILE_INFO', 'See Profile Info');
DEFINE('_STAT_TOTAL_MESSAGES', 'Total Messages');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Total Subjects');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Total Categories');
DEFINE('_STAT_TOTAL_SECTIONS', 'Total Sections');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Today Open');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Yesterday Open');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Today Total Answer');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Yesterday Total Answer');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'View Recent Posts');
DEFINE('_STAT_MORE_ABOUT_STATS', 'More About Stats');
DEFINE('_STAT_USERLIST', 'User List');
DEFINE('_STAT_TEAMLIST', 'Board Team');
DEFINE('_STATS_FORUM_STATS', 'Forum Stats');
DEFINE('_STAT_POPULAR', 'Popular');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Users ( Total Messages)&#32;');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Threads&#32;');
DEFINE('_STAT_POPULAR_USER_GSG', 'Users ( Total Profile Views)&#32;');
//Team List
DEFINE('_MODLIST_ONLINE', 'User Online Now');
DEFINE('_MODLIST_OFFLINE', 'User Offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Who is online');
DEFINE('_WHO_ONLINE_NOW', 'Online');
DEFINE('_WHO_ONLINE_MEMBERS', 'Members');
DEFINE('_WHO_AND', 'and');
DEFINE('_WHO_ONLINE_GUESTS', 'Guests');
DEFINE('_WHO_ONLINE_USER', 'User');
DEFINE('_WHO_ONLINE_TIME', 'Time');
DEFINE('_WHO_ONLINE_FUNC', 'Action');
// Userlist
DEFINE('_USRL_USERLIST', 'Userlist');
DEFINE('_USRL_REGISTERED_USERS', '%s has <strong>%d</strong> registered users');
DEFINE('_USRL_SEARCH_ALERT', 'Please enter a value to search!');
DEFINE('_USRL_SEARCH', 'Find user');
DEFINE('_USRL_SEARCH_BUTTON', 'Search');
DEFINE('_USRL_LIST_ALL', 'List all');
DEFINE('_USRL_NAME', 'Name');
DEFINE('_USRL_USERNAME', 'Username');
DEFINE('_USRL_EMAIL', 'E-mail');
DEFINE('_USRL_USERTYPE', 'Usertype');
DEFINE('_USRL_JOIN_DATE', 'Join date');
DEFINE('_USRL_LAST_LOGIN', 'Last login');
DEFINE('_USRL_NEVER', 'Never');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'Ascending');
DEFINE('_USRL_DESC', 'Descending');
DEFINE('_USRL_DATE_FORMAT', '%m/%d/%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Details');
DEFINE('_USRL_COMPROFILER', 'Profile');
DEFINE('_USRL_THUMBNAIL', 'Pic');
DEFINE('_USRL_READON', 'show');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Send PM');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Search result for');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'Userlist Settings');
DEFINE('_USRL_ERROR', 'Error');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Private messaging component');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Searched for: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Are you sure you want to delete this message? \n\n NOTE: There is NO way to retrieve deleted messages!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'The post(s) have been deleted');
DEFINE('_COM_A_RANKING', 'Ranking');
DEFINE('_COM_A_BOT_REFERENCE', 'Show Bot Reference Chart');
DEFINE('_COM_A_MOSBOT', 'Enable the Discuss Bot');
DEFINE('_PREVIEW', 'Preview');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'The discuss bot enables your users to discuss articles in the forums. The article title is used as the topic subject.'
           . '<br />If a topic does not exist, a new one is created. If the topic already exists, the user is shown the thread and where to reply.' . '<br /><strong>You will need to download and install the bot separately.</strong>'
           . '<br />check the <a href="http://www.Kunena.com">Kunena Web Site</a> for more information.' . '<br />When installed, you will need to add the following bot lines to your articles:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the article can be discussed. To find the proper catid, look into the forums ' . 'and check the category ID from the URL in your browser.'
           . '<br />Example: if you want the article discussed in forum with the category ID 26, the bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each article to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Search');
DEFINE('_FORUM_SEARCHRESULTS', 'Displaying %s out of %s results.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Rules');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Edit this file to insert your rules joomlaroot/administrator/components/com_kunena/language/kunena.english.php</li><li>Rule 2</li><li>Rule 3</li><li>Rule 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Boardcode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'The post(s) have been approved.');
DEFINE('_MODERATION_DELETE_ERROR', 'ERROR: The post(s) could not be deleted.');
DEFINE('_MODERATION_APPROVE_ERROR', 'ERROR: The post(s) could not be approved.');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'There are no forums in this category!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Failed to create ghost topic in old forum!');
DEFINE('_POST_MOVE_GHOST', 'Leave ghost message in old forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Forum Jump');
DEFINE('_COM_A_FORUM_JUMP', 'Enable Forum Jump');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'If set to &quot;Yes,&quot; a selector will be shown on the forum pages that allows for a quick jump to another forum or category.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Rules');
DEFINE('_COM_A_RULESPAGE', 'Enable Rules Page');
DEFINE('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes,&quot; a link to your rules [page will be shown in the header menu. This page can be used for things like forum rules, etc. You can alter the contents of this file by opening rules.php in /joomla_root/components/com_kunena. <em>Make sure to always save a backup of this file. It will be overwritten when upgrading!</em>');
DEFINE('_MOVED_TOPIC', 'MOVED:');
DEFINE('_COM_A_PDF', 'Enable PDF creation');
DEFINE('_COM_A_PDF_DESC',
    'Set to &quot;Yes&quot; if you would like to enable users to download a simple PDF document with the contents of a thread.<br />It is a <u>simple</u> PDF document with no mark-up or fancy layout, but it contains all the thread text.');
DEFINE('_GEN_PDFA', 'Click this button to create a PDF document from this thread (opens in a new window).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Click here to see the profile of this user');
DEFINE('_VIEW_ADDBUDDY', 'Click here to add this user to your buddy list');
DEFINE('_POST_SUCCESS_POSTED', 'Your message has been successfully posted');
DEFINE('_POST_SUCCESS_VIEW', '[ Return to the topic ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Return to the forum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Admin');
DEFINE('_RANK_MODERATOR', 'Moderator');
DEFINE('_SHOW_LASTVISIT', 'Since last visit');
DEFINE('_COM_A_BADWORDS_TITLE', 'Bad Words filtering');
DEFINE('_COM_A_BADWORDS', 'Use bad words filtering');
DEFINE('_COM_A_BADWORDS_DESC', 'Set to &quot;Yes&quot; if you want to filter posts containing the words you defined in the Badword Component configuration. To use this function you must have the Badword Component installed!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* This message has been censored because it contained one or more words flagged by the administrator.*');
DEFINE('_COM_A_AVATAR_SRC', 'Use avatar picture from');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'If you have JomSocial, Clexus PM or the Community Builder component installed, you can configure Kunena to use the user avatar picture from those user profiles. Note: For Community Builder you need to have the thumbnail option enabled because the forum uses the user thumbnail images instead of originals.');
DEFINE('_COM_A_KARMA', 'Show Karma indicator');
DEFINE('_COM_A_KARMA_DESC', 'Set to &quot;Yes&quot; to show user karma and related buttons (increase / decrease) if the user stats are activated.');
DEFINE('_COM_A_DISEMOTICONS', 'Disable emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Set to &quot;Yes&quot; to completely disable graphic emoticons (smileys).');
DEFINE('_COM_C_FBCONFIG', 'Kunena Configuration');
DEFINE('_COM_C_FBCONFIGDESC', 'Configure all Kunena\'s functionality');
DEFINE('_COM_C_FORUM', 'Forum Administration');
DEFINE('_COM_C_FORUMDESC', 'Add categories/forums and configure them');
DEFINE('_COM_C_USER', 'User Administration');
DEFINE('_COM_C_USERDESC', 'Basic user and user profile administration');
DEFINE('_COM_C_FILES', 'Uploaded Files Browser');
DEFINE('_COM_C_FILESDESC', 'Browse and administer uploaded files');
DEFINE('_COM_C_IMAGES', 'Uploaded Images Browser');
DEFINE('_COM_C_IMAGESDESC', 'Browse and administer uploaded images');
DEFINE('_COM_C_CSS', 'Edit CSS File');
DEFINE('_COM_C_CSSDESC', 'Tweak Kunena\'s look and feel');
DEFINE('_COM_C_SUPPORT', 'Support Web Site');
DEFINE('_COM_C_SUPPORTDESC', 'Connect to the Kunena Web site (new window)');
DEFINE('_COM_C_PRUNETAB', 'Prune Forums');
DEFINE('_COM_C_PRUNETABDESC', 'Remove old threads (configurable)');
DEFINE('_COM_C_PRUNEUSERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sync Kunena user table with Joomla! user table'); // <=FB 1.0.3
DEFINE('_COM_C_LOADMODPOS', 'Load Module Positions');
DEFINE('_COM_C_LOADMODPOSDESC', 'Load Module Positions for Kunena Template');
DEFINE('_COM_C_UPGRADEDESC', 'Get your database up to the latest version after an upgrade');
DEFINE('_COM_C_BACK', 'Back to Kunena Control Panel');
DEFINE('_SHOW_LAST_SINCE', 'Active topics since last visit on:');
DEFINE('_POST_SUCCESS_REQUEST2', 'Your request has been processed');
DEFINE('_POST_NO_PUBACCESS3', 'Click here to register.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'The message has been successfully deleted.');
DEFINE('_POST_SUCCESS_EDIT', 'The message has been successfully edited.');
DEFINE('_POST_SUCCESS_MOVE', 'The Topic has been succesfully moved.');
DEFINE('_POST_SUCCESS_POST', 'Your message has been successfully posted.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Your subscription has been processed.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Karma');
DEFINE('_KARMA_SMITE', 'Smite');
DEFINE('_KARMA_APPLAUD', 'Applaud');
DEFINE('_KARMA_BACK', 'To get back to the topic,');
DEFINE('_KARMA_WAIT', 'You can modify only one person\'s karma every 6 hours. <br/>Please wait until this timeout period has passed before modifying any person\'s karma again.');
DEFINE('_KARMA_SELF_DECREASE', 'Please do not attempt to decrease your own karma!');
DEFINE('_KARMA_SELF_INCREASE', 'Your karma has been decreased for attempting to increase it yourself!');
DEFINE('_KARMA_DECREASED', 'User\'s karma decreased. If you are not taken back to the topic in a few moments,');
DEFINE('_KARMA_INCREASED', 'User\'s karma increased. If you are not taken back to the topic in a few moments,');
DEFINE('_COM_A_TEMPLATE', 'Template');
DEFINE('_COM_A_TEMPLATE_DESC', 'Choose the template to use.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Image Sets');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Choose the images set template to use.');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Use Posts Statistics Bar');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Set to &quot;Yes&quot; if you want the number of posts a user has made to be depicted graphically by a Statistics Bar.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Color number for Statistics Bar');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Give the number of the color you want to use for the Post Statistics Bar');
DEFINE('_LATEST_REDIRECT',
    'Kunena needs to (re)establish your access privileges before it can create a list of the latest posts for you.\nDo not worry. This is quite normal after more than 30 minutes of inactivity or after logging back in.\nPlease submit your search request again.');
DEFINE('_SMILE_COLOUR', 'Color');
DEFINE('_SMILE_SIZE', 'Size');
DEFINE('_COLOUR_DEFAULT', 'Standard');
DEFINE('_COLOUR_RED', 'Red');
DEFINE('_COLOUR_PURPLE', 'Purple');
DEFINE('_COLOUR_BLUE', 'Blue');
DEFINE('_COLOUR_GREEN', 'Green');
DEFINE('_COLOUR_YELLOW', 'Yellow');
DEFINE('_COLOUR_ORANGE', 'Orange');
DEFINE('_COLOUR_DARKBLUE', 'Darkblue');
DEFINE('_COLOUR_BROWN', 'Brown');
DEFINE('_COLOUR_GOLD', 'Gold');
DEFINE('_COLOUR_SILVER', 'Silver');
DEFINE('_SIZE_NORMAL', 'Normal');
DEFINE('_SIZE_SMALL', 'Small');
DEFINE('_SIZE_VSMALL', 'Very Small');
DEFINE('_SIZE_BIG', 'Big');
DEFINE('_SIZE_VBIG', 'Very Big');
DEFINE('_IMAGE_SELECT_FILE', 'Select image file to attach');
DEFINE('_FILE_SELECT_FILE', 'Select file to attach');
DEFINE('_FILE_NOT_UPLOADED', 'Your file has not been uploaded. Try posting again or editing the post.');
DEFINE('_IMAGE_NOT_UPLOADED', 'Your image has not been uploaded. Try posting again or editing the post.');
DEFINE('_BBCODE_IMGPH', 'Insert [img] placeholder in the post for attached image'); // Deprecated in 1.0.10
DEFINE('_BBCODE_FILEPH', 'Insert [file] placeholder in the post for attached file'); // Deprecated in 1.0.10
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Check this box to <strong><u>unsubscribe</u></strong> from all topics (including invisible ones for troubleshooting purposes)');
DEFINE('_LINK_JS_REMOVED', '<em>Active link containing JavaScript has been removed automatically.</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Look and Feel');
DEFINE('_COM_A_USERS', 'User Related');
DEFINE('_COM_A_LENGTHS', 'Various length settings');
DEFINE('_COM_A_SUBJECTLENGTH', 'Max. Subject length');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maximum Subject line length. The maximum number supported by the database is 255 characters. If your site is configured to use multi-byte character sets like Unicode, UTF-8 or non-ISO-8599-x, make the maximum smaller using this forumula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Example: for UTF-8, for which the max. character bite syze per character is 4 bytes: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Topic/Forum');
DEFINE('_LATEST_NUMBER', 'New posts');
DEFINE('_COM_A_SHOWNEW', 'Show New posts');
DEFINE('_COM_A_SHOWNEW_DESC', 'If set to &quot;Yes,&quot; Kunena will show the user an indicator for forums that contain new posts and which posts are new since their last visit.');
DEFINE('_COM_A_NEWCHAR', '&quot;New&quot; indicator');
DEFINE('_COM_A_NEWCHAR_DESC', 'Define here what should be used to indicate new posts (like an &quot;!&quot; or &quot;New!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Latest post author');
DEFINE('_GEN_FORUM_NEWPOST', 'New Posts');
DEFINE('_GEN_FORUM_NOTNEW', 'No New Posts');
DEFINE('_GEN_UNREAD', 'Unread Topic');
DEFINE('_GEN_NOUNREAD', 'Read Topic');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Mark all forums read');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Mark this forum read');
DEFINE('_GEN_FORUM_MARKED', 'All posts in this forum have been marked as read');
DEFINE('_GEN_ALL_MARKED', 'All posts have been marked as read');
DEFINE('_IMAGE_UPLOAD', 'Image Upload');
DEFINE('_IMAGE_DIMENSIONS', 'Your image file can be a maximum (width x height - size)');
DEFINE('_IMAGE_ERROR_TYPE', 'Please use only JPEG, GIF, or PNG images');
DEFINE('_IMAGE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_IMAGE_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_UPLOADED', 'Your image has been uploaded.');
DEFINE('_COM_A_IMAGE', 'Images');
DEFINE('_COM_A_IMGHEIGHT', 'Max. Image Height');
DEFINE('_COM_A_IMGWIDTH', 'Max. Image Width');
DEFINE('_COM_A_IMGSIZE', 'Max. Image Filesize<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Allow Public Upload for Images');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload an image.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Allow Registered Upload for Images');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged-in users to be able to upload images.<br/> Note: (Super)administrators and moderators are always able to upload images.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Uploads');
DEFINE('_FILE_TYPES', 'Your file can be of type - max. size');
DEFINE('_FILE_ERROR_TYPE', 'You are only allowed to upload files of type:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_FILE_ERROR_SIZE', 'The file size exceeds the maximum set by the Administrator.');
DEFINE('_COM_A_FILE', 'Files');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'File types allowed');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Specify here which file types are allowed for upload. Use comma-separated, <strong>lowercase</strong> lists without spaces.<br />Example: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Max. File size<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Allow File Upload for Public');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload a file.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Allow File Upload for Registered');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged-in users to be able to upload a file.<br/> Note: (Super)administrators and moderators are always able to upload files.');
DEFINE('_SUBMIT_CANCEL', 'Your post submission has been cancelled.');
DEFINE('_HELP_SUBMIT', 'Click here to submit your message'); // Deprecated in 1.0.10
DEFINE('_HELP_PREVIEW', 'Click here to see what your message will look like when submitted'); // Deprecated in 1.0.10
DEFINE('_HELP_CANCEL', 'Click here to cancel your message'); // Deprecated in 1.0.10
DEFINE('_POST_DELETE_ATT', 'If this box is checked, all image and file attachments of posts you are going to delete will be deleted as well (recommended).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Show Edited Mark Up');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Set to &quot;Yes&quot; if you want an edited post be marked up with text showing that the post was edited by a user and when.');
DEFINE('_EDIT_BY', 'Post edited by:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'An error occured when uploading your avatar. Please try again or notify your system administrator.');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Uploaded Images Browser');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Uploaded Files Browser');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Number of uploaded images');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Number of uploaded files');
DEFINE('_COM_A_IMGB_ENLARGE', 'Click the image to see its full size');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Click the file image to download it');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'The option &quot;Replace with dummy&quot; will replace the selected image with a dummy image.<br /> This allows you to remove the actual file without breaking the post.<br /><small><em>Please note that sometimes an explicit browser refresh is needed to see the dummy replacement.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Current dummy image');
DEFINE('_COM_A_IMGB_REPLACE', 'Replace with dummy');
DEFINE('_COM_A_IMGB_REMOVE', 'Remove entirely');
DEFINE('_COM_A_IMGB_NAME', 'Name');
DEFINE('_COM_A_IMGB_SIZE', 'Size');
DEFINE('_COM_A_IMGB_DIMS', 'Dimensions');
DEFINE('_COM_A_IMGB_CONFIRM', 'Are you absolutely sure you want to delete this file? \n Deleting a file will create a broken referencing post...');
DEFINE('_COM_A_IMGB_VIEW', 'Open post (to edit)');
DEFINE('_COM_A_IMGB_NO_POST', 'No referencing post!');
DEFINE('_USER_CHANGE_VIEW', 'Changes in these settings will become active the next time you visit the forums.');
DEFINE('_MOSBOT_DISCUSS_A', 'Discuss this article on the forums. (');
DEFINE('_MOSBOT_DISCUSS_B', '&#32;posts)');
DEFINE('_POST_DISCUSS', 'This thread discusses the content article');
DEFINE('_COM_A_RSS', 'Enable RSS feed');
DEFINE('_COM_A_RSS_DESC', 'The RSS feed enables users to download the latest posts to their desktop or RSS reader aplication. See <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.');
DEFINE('_LISTCAT_RSS', 'get the latest posts directly to your desktop');
DEFINE('_SEARCH_REDIRECT', 'Kunena needs to (re)establish your access privileges before it can perform your search request.\nDo not worry, this is quite normal after more than 30 minutes of inactivity.\nPlease submit your search request again.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunena Configuration');
DEFINE('_COM_A_DISPLAY', 'Display #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Current Setting');
DEFINE('_COM_A_EXPLANATION', 'Explanation');
DEFINE('_COM_A_BOARD_TITLE', 'Board Title');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'The name of your board');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Default View type');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Choose between a threaded or flat view as default');
DEFINE('_COM_A_THREADS', 'Threads Per Page');
DEFINE('_COM_A_THREADS_DESC', 'Number of threads per page to show');
DEFINE('_COM_A_REGISTERED_ONLY', 'Registered Users Only');
DEFINE('_COM_A_REG_ONLY_DESC', 'Set to &quot;Yes&quot; to allow only registered users to use the Forum (view & post). Set to &quot;No&quot; to allow any visitor to use the Forum.');
DEFINE('_COM_A_PUBWRITE', 'Public Read/Write');
DEFINE('_COM_A_PUBWRITE_DESC', 'Set to &quot;Yes&quot; to allow for public write privileges. Set to &quot;No&quot; to allow any visitor to see posts, but only registered users to write posts.');
DEFINE('_COM_A_USER_EDIT', 'User Edits');
DEFINE('_COM_A_USER_EDIT_DESC', 'Set to &quot;Yes&quot; to allow registered users to edit their own posts.');
DEFINE('_COM_A_MESSAGE', 'In order to save changes, press the &quot;Save&quot; button at the top.');
DEFINE('_COM_A_HISTORY', 'Show History');
DEFINE('_COM_A_HISTORY_DESC', 'Set to &quot;Yes&quot; if you want the topic history shown when a reply/quote is made');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Allow Subscriptions');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to subscribe to a topic and receive e-mail notifications on new posts');
DEFINE('_COM_A_HISTLIM', 'History Limit');
DEFINE('_COM_A_HISTLIM_DESC', 'Number of posts to show in the history');
DEFINE('_COM_A_FLOOD', 'Flood Protection');
DEFINE('_COM_A_FLOOD_DESC', 'The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection <em>can</em> cause degradation of performance.');
DEFINE('_COM_A_MODERATION', 'E-mail Moderators');
DEFINE('_COM_A_MODERATION_DESC',
    'Set to &quot;Yes&quot; if you want e-mail notifications on new posts sent to the forum moderator(s). Note: Although every (super)administrator has automatically all Moderator privileges, assign them explicitly as moderators on
 the forum to recieve e-mails too!');
DEFINE('_COM_A_SHOWMAIL', 'Show E-mail');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Set to &quot;No&quot; if you never want to display the users e-mail address even to registered users.');
DEFINE('_COM_A_AVATAR', 'Allow Avatars');
DEFINE('_COM_A_AVATAR_DESC', 'Set to &quot;Yes&quot; if you want registered users to have an avatar (manageable through their profile).');
DEFINE('_COM_A_AVHEIGHT', 'Max. Avatar Height');
DEFINE('_COM_A_AVWIDTH', 'Max. Avatar Width');
DEFINE('_COM_A_AVSIZE', 'Max. Avatar Filesize<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_USERSTATS', 'Show User Stats');
DEFINE('_COM_A_USERSTATS_DESC', 'Set to &quot;Yes&quot; to show User Statistics like number of user posts and user type (Admin, Moderator, User, etc.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Allow Avatar Upload');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to upload an avatar.');
DEFINE('_COM_A_AVATARGALLERY', 'Use Avatars Gallery');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to choose an avatar from a gallery you provide (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Set to &quot;Yes&quot; if you want to show the rank registered users have based on the number of posts they made.<br/><strong>Note: You must also enable User Stats in the Advanced tab to show it.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Use Rank Images');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Set to &quot;Yes&quot; if you want to show the rank registered users have using an image (from components/com_kunena/ranks). Turning this of will show the text for that rank. Check the documentation on www.kunena.com for more information on ranking images.');

//email and stuff
$_COM_A_NOTIFICATION = "New post notification from";
$_COM_A_NOTIFICATION1 = "A new post has been made to a topic to which you have subscribed on the";
$_COM_A_NOTIFICATION2 = "You can administer your subscriptions by following the 'My Profile' link on the Forum home page after you have logged in on the site. From your profile you can also unsubscribe from the topic.";
$_COM_A_NOTIFICATION3 = "Do not answer to this e-mail notification as it is a generated e-mail.";
$_COM_A_NOT_MOD1 = "A new post has been made to a forum to which you have assigned as moderator on the";
$_COM_A_NOT_MOD2 = "Please have a look at it after you have logged in on the site.";
DEFINE('_COM_A_NO', 'No');
DEFINE('_COM_A_YES', 'Yes');
DEFINE('_COM_A_FLAT', 'Flat');
DEFINE('_COM_A_THREADED', 'Threaded');
DEFINE('_COM_A_MESSAGES', 'Messages per page');
DEFINE('_COM_A_MESSAGES_DESC', 'Number of messages per page to show');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Username');
DEFINE('_COM_A_USERNAME_DESC', 'Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the user\'s real name');
DEFINE('_COM_A_CHANGENAME', 'Allow Name Change');
DEFINE('_COM_A_CHANGENAME_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to change their name when posting. If set to &quot;No,&quot; registered users will not be able to edit their names.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum Offline');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Set to &quot;Yes&quot; if you want to take the Forum section offline. The Forum will still remain browseable by site (super)admins.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Forum Offline Message');
DEFINE('_COM_A_PRUNE', 'Prune Forums');
DEFINE('_COM_A_PRUNE_NAME', 'Forum to prune:');
DEFINE('_COM_A_PRUNE_DESC',
    'The Prune Forums function allows you to prune threads where there have not been any new posts during the specified number of days. This does not remove stickies or locked topics and these must be removed manually. Threads in locked forums can not be pruned.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Prune threads with no posts for the past&#32;');
DEFINE('_COM_A_PRUNE_DAYS', 'days');
DEFINE('_COM_A_PRUNE_USERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'This function allows you to prune your Kunena user list against the Joomla site user list. It will delete all profiles for Kunena users that have been deleted from your Joomla Framework.<br/>If you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Action');
DEFINE('_GEN_AUTHOR', 'Author');
DEFINE('_GEN_BY', 'by');
DEFINE('_GEN_CANCEL', 'Cancel');
DEFINE('_GEN_CONTINUE', 'Submit');
DEFINE('_GEN_DATE', 'Date');
DEFINE('_GEN_DELETE', 'Delete');
DEFINE('_GEN_EDIT', 'Edit');
DEFINE('_GEN_EMAIL', 'E-mail');
DEFINE('_GEN_EMOTICONS', 'Emoticons');
DEFINE('_GEN_FLAT', 'Flat');
DEFINE('_GEN_FLAT_VIEW', 'Flat');
DEFINE('_GEN_FORUMLIST', 'Forum List');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Help');
DEFINE('_GEN_HITS', 'Views');
DEFINE('_GEN_LAST_POST', 'Last Post');
DEFINE('_GEN_LATEST_POSTS', 'Show latest posts');
DEFINE('_GEN_LOCK', 'Lock');
DEFINE('_GEN_UNLOCK', 'Unlock');
DEFINE('_GEN_LOCKED_FORUM', 'Forum is locked');
DEFINE('_GEN_LOCKED_TOPIC', 'Topic is locked');
DEFINE('_GEN_MESSAGE', 'Message');
DEFINE('_GEN_MODERATED', 'Forum is moderated. Reviewed prior to publishing.');
DEFINE('_GEN_MODERATORS', 'Moderators');
DEFINE('_GEN_MOVE', 'Move');
DEFINE('_GEN_NAME', 'Name');
DEFINE('_GEN_POST_NEW_TOPIC', 'Post New Topic');
DEFINE('_GEN_POST_REPLY', 'Post Reply');
DEFINE('_GEN_MYPROFILE', 'My Profile');
DEFINE('_GEN_QUOTE', 'Quote');
DEFINE('_GEN_REPLY', 'Reply');
DEFINE('_GEN_REPLIES', 'Replies');
DEFINE('_GEN_THREADED', 'Threaded');
DEFINE('_GEN_THREADED_VIEW', 'Threaded');
DEFINE('_GEN_SIGNATURE', 'Signature');
DEFINE('_GEN_ISSTICKY', 'Topic is sticky.');
DEFINE('_GEN_STICKY', 'Sticky');
DEFINE('_GEN_UNSTICKY', 'Unsticky');
DEFINE('_GEN_SUBJECT', 'Subject');
DEFINE('_GEN_SUBMIT', 'Submit');
DEFINE('_GEN_TOPIC', 'Topic');
DEFINE('_GEN_TOPICS', 'Topics');
DEFINE('_GEN_TOPIC_ICON', 'topic icon');
DEFINE('_GEN_SEARCH_BOX', 'Search Forum');
$_GEN_THREADED_VIEW = "Threaded";
$_GEN_FLAT_VIEW = "Flat";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Upload');
DEFINE('_UPLOAD_DIMENSIONS', 'Your image file can be maximum (width x height - size)');
DEFINE('_UPLOAD_SUBMIT', 'Submit a new avatar for upload');
DEFINE('_UPLOAD_SELECT_FILE', 'Select file');
DEFINE('_UPLOAD_ERROR_TYPE', 'Please use only jpeg, gif or png images');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_UPLOAD_ERROR_NAME', 'The image file must contain only alphanumeric characters and no spaces please.');
DEFINE('_UPLOAD_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "You didn't choose an avatar from the gallery...");
DEFINE('_UPLOAD_UPLOADED', 'Your avatar has been uploaded.');
DEFINE('_UPLOAD_GALLERY', 'Choose one from the avatar gallery:');
DEFINE('_UPLOAD_CHOOSE', 'Confirm Choice.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'An administrator should create them first from the&#32;');
DEFINE('_LISTCAT_DO', 'They will know what to do&#32;');
DEFINE('_LISTCAT_INFORM', 'Inform them and tell them to hurry up!');
DEFINE('_LISTCAT_NO_CATS', 'There are no categories in the forum defined yet.');
DEFINE('_LISTCAT_PANEL', 'Administration Panel of the Joomla OS CMS.');
DEFINE('_LISTCAT_PENDING', 'pending message(s)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'There are no pending messages in this forum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'You are about to delete the message');
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTES:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Consider blanking the post and poster\'s name if only the contents should be removed..
<br/>
- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.');
DEFINE('_POST_CLICK', 'click here');
DEFINE('_POST_ERROR', 'Could not find username/e-mail. Severe database error not listed');
DEFINE('_POST_ERROR_MESSAGE', 'An unknown SQL error occured and your message was not posted. If the problem persists, please contact the administrator.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'An error has occured and the message was not updated. Please try again. If this error persists, please contact the administrator.');
DEFINE('_POST_ERROR_TOPIC', 'An error occured during the delete(s). Please check the error below:');
DEFINE('_POST_FORGOT_NAME', 'You forgot to include your name. Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_SUBJECT', 'You forgot to include a subject. Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_MESSAGE', 'You forgot to include a message. Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_INVALID', 'An invalid post ID was requested.');
DEFINE('_POST_LOCK_SET', 'The topic has been locked.');
DEFINE('_POST_LOCK_NOT_SET', 'The topic could not be locked.');
DEFINE('_POST_LOCK_UNSET', 'The topic has been unlocked.');
DEFINE('_POST_LOCK_NOT_UNSET', 'The topic could not be unlocked.');
DEFINE('_POST_MESSAGE', 'Post a new message in&#32;');
DEFINE('_POST_MOVE_TOPIC', 'Move this topic to forum&#32;');
DEFINE('_POST_NEW', 'Post a new message in:&#32;');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Your subscription to this topic could not be processed.');
DEFINE('_POST_NOTIFIED', 'Check this box to have yourself notified about replies to this topic.');
DEFINE('_POST_STICKY_SET', 'The sticky bit has been set for this topic.');
DEFINE('_POST_STICKY_NOT_SET', 'The sticky bit could not be set for this topic.');
DEFINE('_POST_STICKY_UNSET', 'The sticky bit has been unset for this topic.');
DEFINE('_POST_STICKY_NOT_UNSET', 'The sticky bit could not be unset for this topic.');
DEFINE('_POST_SUBSCRIBE', 'subscribe');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'You have been subscribed to this topic.');
DEFINE('_POST_SUCCESS', 'Your message has been successfully');
DEFINE('_POST_SUCCES_REVIEW', 'Your message has been successfully posted. It will be reviewed by a moderator before it will be published on the forum.');
DEFINE('_POST_SUCCESS_REQUEST', 'Your request has been processed. If you are not taken back to the topic in a few moments,');
DEFINE('_POST_TOPIC_HISTORY', 'Topic History of');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Max. showing the last');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(Last post first)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Your topic could not be moved. To get back to the topic:');
DEFINE('_POST_TOPIC_FLOOD1', 'The administrator of this forum has enabled flood protection. You must wait&#32;');
DEFINE('_POST_TOPIC_FLOOD2', '&#32;seconds before you can make another post.');
DEFINE('_POST_TOPIC_FLOOD3', 'Please click your browser&#146s back button to get back to the forum.');
DEFINE('_POST_EMAIL_NEVER', 'your e-mail address will never be displayed on the site.');
DEFINE('_POST_EMAIL_REGISTERED', 'your e-mail address will only be available to registered users.');
DEFINE('_POST_LOCKED', 'locked by the administrator.');
DEFINE('_POST_NO_NEW', 'New replies are not allowed.');
DEFINE('_POST_NO_PUBACCESS1', 'The administrator has disabled public write access.');
DEFINE('_POST_NO_PUBACCESS2', 'Only logged-in/registered users are allowed to contribute to the forum.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> There are no topics in this forum yet <<');
DEFINE('_SHOWCAT_PENDING', 'pending message(s)');
// userprofile.php
DEFINE('_USER_DELETE', '&#32;check this box to delete your signature');
DEFINE('_USER_ERROR_A', 'You came to this page in error. Please inform the administrator on which links&#32;');
DEFINE('_USER_ERROR_B', 'you clicked that got you here. They can then file a bug report.');
DEFINE('_USER_ERROR_C', 'Thank you!');
DEFINE('_USER_ERROR_D', 'Error number to include in your report:&#32;');
DEFINE('_USER_GENERAL', 'General Profile Options');
DEFINE('_USER_MODERATOR', 'You are assigned as a moderator to forums');
DEFINE('_USER_MODERATOR_NONE', 'No forums found assigned to you');
DEFINE('_USER_MODERATOR_ADMIN', 'Admins are moderator on all forums.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'No subscriptions found for you');
//DEFINE('_USER_PREFERED', 'Prefered Viewtype');
DEFINE('_USER_PROFILE', 'Profile for&#32;');
DEFINE('_USER_PROFILE_NOT_A', 'Your profile could&#32;');
DEFINE('_USER_PROFILE_NOT_B', 'not');
DEFINE('_USER_PROFILE_NOT_C', '&#32;be updated.');
DEFINE('_USER_PROFILE_UPDATED', 'Your profile is updated.');
DEFINE('_USER_RETURN_A', 'If you are not taken back to your profile in a few moments&#32;');
DEFINE('_USER_RETURN_B', 'click here');
DEFINE('_USER_SUBSCRIPTIONS', 'Your Subscriptions');
DEFINE('_USER_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_USER_UNSUBSCRIBE_A', 'You could&#32;');
DEFINE('_USER_UNSUBSCRIBE_B', 'not');
DEFINE('_USER_UNSUBSCRIBE_C', '&#32;be unsubscribed from the topic.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'You have been unsubscribed from the topic.');
DEFINE('_USER_DELETEAV', '&#32;check this box to delete your Avatar');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Preferred Message Ordering');
DEFINE('_USER_ORDER_DESC', 'Last post first');
DEFINE('_USER_ORDER_ASC', 'First post first');
// view.php
DEFINE('_VIEW_DISABLED', 'The administrator has disabled public write access.');
DEFINE('_VIEW_POSTED', 'Posted by');
DEFINE('_VIEW_SUBSCRIBE', ':: Subscribe to this topic ::');
DEFINE('_MODERATION_INVALID_ID', 'An invalid post ID was requested.');
DEFINE('_VIEW_NO_POSTS', 'There are no posts in this forum.');
DEFINE('_VIEW_VISITOR', 'Visitor');
DEFINE('_VIEW_ADMIN', 'Admin');
DEFINE('_VIEW_USER', 'User');
DEFINE('_VIEW_MODERATOR', 'Moderator');
DEFINE('_VIEW_REPLY', 'Reply to this message');
DEFINE('_VIEW_EDIT', 'Edit this message');
DEFINE('_VIEW_QUOTE', 'Quote this message in a new post');
DEFINE('_VIEW_DELETE', 'Delete this message');
DEFINE('_VIEW_STICKY', 'Set this topic sticky');
DEFINE('_VIEW_UNSTICKY', 'Unset this topic sticky');
DEFINE('_VIEW_LOCK', 'Lock this topic');
DEFINE('_VIEW_UNLOCK', 'Unlock this topic');
DEFINE('_VIEW_MOVE', 'Move this topic to another forum');
DEFINE('_VIEW_SUBSCRIBETXT', 'Subscribe to this topic and get notified by mail about new posts');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Forum');
DEFINE('_POSTS', 'Posts:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Post');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forum is OFFLINE!');
DEFINE('_PAGE', 'Page:&#32;');
DEFINE('_NO_POSTS', 'No Posts');
DEFINE('_CHARS', 'characters max.');
DEFINE('_HTML_YES', 'HTML is disabled');
DEFINE('_YOUR_AVATAR', '<b>Your Avatar</b>');
DEFINE('_NON_SELECTED', 'Not yet selected <br />');
DEFINE('_SET_NEW_AVATAR', 'Select new avatar');
DEFINE('_THREAD_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_SHOW_LAST_POSTS', 'Active topics in last');
DEFINE('_SHOW_HOURS', 'hours');
DEFINE('_SHOW_POSTS', 'Total:&#32;');
DEFINE('_DESCRIPTION_POSTS', 'The newest posts for the active topics are shown');
DEFINE('_SHOW_4_HOURS', '4 Hours');
DEFINE('_SHOW_8_HOURS', '8 Hours');
DEFINE('_SHOW_12_HOURS', '12 Hours');
DEFINE('_SHOW_24_HOURS', '24 Hours');
DEFINE('_SHOW_48_HOURS', '48 Hours');
DEFINE('_SHOW_WEEK', 'Week');
DEFINE('_POSTED_AT', 'Posted at');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'There are no new posts in the timeframe you selected.');
DEFINE('_MESSAGE', 'Message');
DEFINE('_NO_SMILIE', 'no');
DEFINE('_FORUM_UNAUTHORIZIED', 'This forum is open only to registered and logged-in users.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'If you are already registered, please log in first.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderation');
DEFINE('_MOD_APPROVE', 'Approve');
DEFINE('_MOD_DELETE', 'Delete');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Show most recent message');
DEFINE('_POST_WROTE', 'wrote');
DEFINE('_COM_A_EMAIL', 'Board E-mail Address');
DEFINE('_COM_A_EMAIL_DESC', 'This is the board\'s e-mail address. Make this a valid e-mail address');
DEFINE('_COM_A_WRAP', 'Wrap Words Longer Than');
DEFINE('_COM_A_WRAP_DESC',
    'Enter the maximum number of characters a single word may have. This feature allows you to tune the output of Kunena Posts to your template.<br/> 70 characters is probably the maximum for fixed width templates but you might need to experiment a bit.<br/>URLs, no matter how long, are not affected by the word wrap.');
DEFINE('_COM_A_SIGNATURE', 'Max. Signature Length');
DEFINE('_COM_A_SIGNATURE_DESC', 'Maximum number of characters allowed for the user signature.');
DEFINE('_SHOWCAT_NOPENDING', 'No pending message(s)');
DEFINE('_COM_A_BOARD_OFSET', 'Board Time Offset');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Some boards are located on servers in a different time zone than the users. Set the time offset for the post time in hours. Both positive and negative numbers can be used.');
//New in RC2
DEFINE('_COM_A_BASICS', 'Basics');
DEFINE('_COM_A_FRONTEND', 'Frontend');
DEFINE('_COM_A_SECURITY', 'Security');
DEFINE('_COM_A_AVATARS', 'Avatars');
DEFINE('_COM_A_INTEGRATION', 'Integration');
DEFINE('_COM_A_PMS', 'Enable private messaging');
DEFINE('_COM_A_PMS_DESC',
    'Select the appropriate private messaging component if you have one installed. Selecting Clexus PM will also enable ClexusPM user profile related options (like ICQ, AIM, Yahoo, MSN and profile links if supported by the Kunena template used.');
DEFINE('_VIEW_PMS', 'Click here to send a Private Message to this user');
//new in RC3
DEFINE('_POST_RE', 'Re:');
DEFINE('_BBCODE_BOLD', 'Bold text: [b]text[/b]&#32;'); // Deprecated in 1.0.10
DEFINE('_BBCODE_ITALIC', 'Italic text: [i]text[/i]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_UNDERL', 'Underline text: [u]text[/u]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_QUOTE', 'Quote text: [quote]text[/quote]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_CODE', 'Code display: [code]code[/code]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_ULIST', 'Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items'); // Deprecated in 1.0.10
DEFINE('_BBCODE_OLIST', 'Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items'); // Deprecated in 1.0.10
DEFINE('_BBCODE_IMAGE', 'Image: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]This is a link[/url]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_CLOSA', 'Close all tags'); // Deprecated in 1.0.10
DEFINE('_BBCODE_CLOSE', 'Close all open bbCode tags'); // Deprecated in 1.0.10
DEFINE('_BBCODE_COLOR', 'Color: [color=#FF6600]text[/color]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_SIZE', 'Size: [size=1]text size[/size] - Tip: sizes range from 1 to 5'); // Deprecated in 1.0.10
DEFINE('_BBCODE_LITEM', 'List Item: [li] list item [/li]'); // Deprecated in 1.0.10
DEFINE('_BBCODE_HINT', 'bbCode Help - Tip: bbCode can be used on selected text!'); // Deprecated in 1.0.10
DEFINE('_COM_A_TAWIDTH', 'Textarea Width');
DEFINE('_COM_A_TAWIDTH_DESC', 'Adjust the width of the reply/post text entry area to match your template. <br/>The Topic Emoticon Toolbar will be wrapped accross two lines if width <= 420 pixels');
DEFINE('_COM_A_TAHEIGHT', 'Textarea Height');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Adjust the height of the reply/post text entry area to match your template');
DEFINE('_COM_A_ASK_EMAIL', 'Require E-mail');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Require an e-mail address when users or visitors make a post. Set to &quot;No&quot; if you want this feature to be skipped on the front end. Posters will not be asked for their e-mail address.');

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
