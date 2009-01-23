<?php
/**
* @version $Id: english.php 1075 2008-10-18 13:50:46Z fxstein $
* FireBoard Component
* @package FireBoard
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
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.5RC2
DEFINE('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the FireBoard code tag highlighting java script. If your members post php and similar code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from getting malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Default RSS type');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Choose between RSS feeds by thread or post. By thread means that only one entry per thread will be listed in the RSS feed, independet of how many posts have been made within that thread. By thread creates a shorter more compact RSS feed but will not list every reply made.');
DEFINE('_COM_A_RSS_BY_THREAD', 'By Thread');
DEFINE('_COM_A_RSS_BY_POST', 'By Post');
DEFINE('_COM_A_RSS_HISTORY', 'RSS History');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Select how much history should be included in the RSS feed. Default is 1 month but you might want to limit it to 1 week on high volume sites.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Week');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Month');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Year');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Default FireBoard Page');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default FireBoard page that gets displayed when a forum link is clicked or the forum is entered initially. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Recent Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'My Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categories');

// 1.0.5
DEFINE('_FB_BBCODE_HIDE', 'The following is hidden from non registered users:');
DEFINE('_FB_BBCODE_SPOILER', 'Warning Spoiler!');
DEFINE('_FB_FORUM_SAME_ERR', 'Parent Forum must not be the same.');
DEFINE('_FB_FORUM_OWNCHILD_ERR', 'Parent Forum is one of its own childs.');
DEFINE('_FB_FORUM_UNKNOWN_ERR', 'Forum ID does not exist.');
DEFINE('_FB_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'You forgot to enter your name.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'You forgot to enter your email.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'You forgot to enter a subject.');
DEFINE('_FB_EDIT_TITLE', 'Edit Your Details');
DEFINE('_FB_YOUR_NAME', 'Your Name:');
DEFINE('_FB_EMAIL', 'e-mail:');
DEFINE('_FB_UNAME', 'User Name:');
DEFINE('_FB_PASS', 'Password:');
DEFINE('_FB_VPASS', 'Verify Password:');
DEFINE('_FB_USER_DETAILS_SAVE', 'User details have been saved.');
DEFINE('_FB_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trimm long URLs');
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
DEFINE('_COM_A_FB_SESSION_TIMEOUT', 'Session Lifetime');
DEFINE('_COM_A_FB_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Merge');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Merge this thread with');
DEFINE('_POST_MERGE_GHOST', 'Leave ghost copy of thread');
DEFINE('_POST_SUCCESS_MERGE', 'Thread merged successfully.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Merge failed.');
DEFINE('_GEN_SPLIT', 'Split');
DEFINE('_GEN_DOSPLIT', 'Go');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Thread split successfully.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Topic changed successfully.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Topic change failed.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Split failed.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicate, identical message has been ignored.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
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
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'This thread has <b>NOT</b> removed from your favorites');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Your request to remove from favorites has been processed.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'This thread has been removed from your subscriptions.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'This thread has <b>NOT</b> removed from your subscriptions');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Your request to remove from subscriptions has been processed.');
DEFINE('_POST_NO_DEST_CATEGORY', 'No destination category was selected. Nothing was moved.');
// Default_EX template
DEFINE('_FB_ALL_DISCUSSIONS', 'Recent Discussions');
DEFINE('_FB_MY_DISCUSSIONS', 'My Discussions');
DEFINE('_FB_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_FB_CATEGORY', 'Category:');
DEFINE('_FB_CATEGORIES', 'Categories');
DEFINE('_FB_POSTED_AT', 'Posted');
DEFINE('_FB_AGO', 'ago');
DEFINE('_FB_DISCUSSIONS', 'Discussions');
DEFINE('_FB_TOTAL_THREADS', 'Total Threads:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'Month');
DEFINE('_SHOW_YEAR', 'Year');

// 1.0.4
DEFINE('_FB_COPY_FILE', 'Copying "%src%" to "%dst%"...');
DEFINE('_FB_COPY_OK', 'OK');
DEFINE('_FB_CSS_SAVE', 'Saving css file should be here...file="%file%"');
DEFINE('_FB_UP_ATT_10', 'Attachment table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_FB_UP_ATT_10_MSG', 'Attachments in messages table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_FB_TOPIC_MOVED', '---');
DEFINE('_FB_TOPIC_MOVED_LONG', '------------');
DEFINE('_FB_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_FB_POST_DEL_ERR_MSG', 'Could not delete the post(s) - nothing else deleted');
DEFINE('_FB_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
DEFINE('_FB_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats!');
DEFINE('_FB_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum as well");
DEFINE('_FB_UNIST_SUCCESS', "FireBoard component was successfully uninstalled!");
DEFINE('_FB_PDF_VERSION', 'FireBoard Forum Component version: %version%');
DEFINE('_FB_PDF_DATE', 'Generated: %date%');
DEFINE('_FB_SEARCH_NOFORUM', 'No forums to search in.');

DEFINE('_FB_ERRORADDUSERS', 'Error adding users:');
DEFINE('_FB_USERSSYNCDELETED', 'Users syncronized; Deleted:');
DEFINE('_FB_USERSSYNCADD', ', add:');
DEFINE('_FB_SYNCUSERPROFILES', 'user profiles.');
DEFINE('_FB_NOPROFILESFORSYNC', 'No profiles found eligible for syncronizing.');
DEFINE('_FB_SYNC_USERS', 'Syncronize Users');
DEFINE('_FB_SYNC_USERS_DESC', 'Sync FireBoard user table with Joomla! user table');
DEFINE('_FB_A_MAIL_ADMIN', 'Email Administrators');
DEFINE('_FB_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_FB_RANKS_EDIT', 'Edit Rank');
DEFINE('_FB_USER_HIDEEMAIL', 'Hide Email');
DEFINE('_FB_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_FB_DT_TIME_FMT','%H:%M');
DEFINE('_FB_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_FB_DT_LDAY_SUN', 'Sunday');
DEFINE('_FB_DT_LDAY_MON', 'Monday');
DEFINE('_FB_DT_LDAY_TUE', 'Tuesday');
DEFINE('_FB_DT_LDAY_WED', 'Wednesday');
DEFINE('_FB_DT_LDAY_THU', 'Thursday');
DEFINE('_FB_DT_LDAY_FRI', 'Friday');
DEFINE('_FB_DT_LDAY_SAT', 'Saturday');
DEFINE('_FB_DT_DAY_SUN', 'Sun');
DEFINE('_FB_DT_DAY_MON', 'Mon');
DEFINE('_FB_DT_DAY_TUE', 'Tue');
DEFINE('_FB_DT_DAY_WED', 'Wed');
DEFINE('_FB_DT_DAY_THU', 'Thu');
DEFINE('_FB_DT_DAY_FRI', 'Fri');
DEFINE('_FB_DT_DAY_SAT', 'Sat');
DEFINE('_FB_DT_LMON_JAN', 'January');
DEFINE('_FB_DT_LMON_FEB', 'February');
DEFINE('_FB_DT_LMON_MAR', 'March');
DEFINE('_FB_DT_LMON_APR', 'April');
DEFINE('_FB_DT_LMON_MAY', 'May');
DEFINE('_FB_DT_LMON_JUN', 'June');
DEFINE('_FB_DT_LMON_JUL', 'July');
DEFINE('_FB_DT_LMON_AUG', 'August');
DEFINE('_FB_DT_LMON_SEP', 'September');
DEFINE('_FB_DT_LMON_OCT', 'October');
DEFINE('_FB_DT_LMON_NOV', 'November');
DEFINE('_FB_DT_LMON_DEV', 'December');
DEFINE('_FB_DT_MON_JAN', 'Jan');
DEFINE('_FB_DT_MON_FEB', 'Feb');
DEFINE('_FB_DT_MON_MAR', 'Mar');
DEFINE('_FB_DT_MON_APR', 'Apr');
DEFINE('_FB_DT_MON_MAY', 'May');
DEFINE('_FB_DT_MON_JUN', 'Jun');
DEFINE('_FB_DT_MON_JUL', 'Jul');
DEFINE('_FB_DT_MON_AUG', 'Aug');
DEFINE('_FB_DT_MON_SEP', 'Sep');
DEFINE('_FB_DT_MON_OCT', 'Oct');
DEFINE('_FB_DT_MON_NOV', 'Nov');
DEFINE('_FB_DT_MON_DEV', 'Dec');
DEFINE('_FB_CHILD_BOARD', 'Child Board');
DEFINE('_WHO_ONLINE_GUEST', 'Guest');
DEFINE('_WHO_ONLINE_MEMBER', 'Member');
DEFINE('_FB_IMAGE_PROCESSOR_NONE', 'none');
DEFINE('_FB_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_FB_INSTALL_CLICK_TO_CONTINUE', 'Click here to continue...');
DEFINE('_FB_INSTALL_APPLY', 'Apply!');
DEFINE('_FB_NO_ACCESS', 'You do not have access to this Forum!');
DEFINE('_FB_TIME_SINCE', '%time% ago');
DEFINE('_FB_DATE_YEARS', 'Years');
DEFINE('_FB_DATE_MONTHS', 'Months');
DEFINE('_FB_DATE_WEEKS','Weeks');
DEFINE('_FB_DATE_DAYS', 'Days');
DEFINE('_FB_DATE_HOURS', 'Hours');
DEFINE('_FB_DATE_MINUTES', 'Minutes');
// 1.0.3
DEFINE('_FB_CONFIRM_REMOVESAMPLEDATA', 'Are you sure you want to remove the sample data? This action is irreversible.');
// 1.0.2
DEFINE('_FB_HEADERADD', 'Forumheader:');
DEFINE('_FB_ADVANCEDDISPINFO', "Forum display");
DEFINE('_FB_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_FB_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'User Edit Time');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Set to 0 for unlimited time, else window
in seconds from post or last modification to allow edit.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], allows
storing a modification up to 600 seconds after edit link disappears');
DEFINE('_FB_HELPPAGE','Enable Help Page');
DEFINE('_FB_HELPPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Help page.');
DEFINE('_FB_HELPPAGE_IN_FB','Show help in fireboard');
DEFINE('_FB_HELPPAGE_IN_FB_DESC','If set to &quot;Yes&quot; help content text will be include in fireboard and Help external page link will not work. <b>Note:</b> You should add "Help Content ID" .');
DEFINE('_FB_HELPPAGE_CID','Help Content ID');
DEFINE('_FB_HELPPAGE_CID_DESC','You should set <b>"YES"</b> "Show help in fireboard" setting.');
DEFINE('_FB_HELPPAGE_LINK',' Help external page link');
DEFINE('_FB_HELPPAGE_LINK_DESC','If you show help external link, please set <b>"NO"</b> "Show help in fireboard" setting.');
DEFINE('_FB_RULESPAGE','Enable Rules Page');
DEFINE('_FB_RULESPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page.');
DEFINE('_FB_RULESPAGE_IN_FB','Show rules in fireboard');
DEFINE('_FB_RULESPAGE_IN_FB_DESC','If set to &quot;Yes&quot; rules content text will be include in fireboard and Rules external page link will not work. <b>Note:</b> You should add "Rules Content ID" .');
DEFINE('_FB_RULESPAGE_CID','Rules Content ID');
DEFINE('_FB_RULESPAGE_CID_DESC','You should set <b>"YES"</b> "Show rules in fireboard" setting.');
DEFINE('_FB_RULESPAGE_LINK',' Rules external page link');
DEFINE('_FB_RULESPAGE_LINK_DESC','If you show rules external link, please set <b>"NO"</b> "Show rules in fireboard" setting.');
DEFINE('_FB_AVATAR_GDIMAGE_NOT','GD Library not found');
DEFINE('_FB_AVATAR_GD2IMAGE_NOT','GD2 Library not found');
DEFINE('_FB_GD_INSTALLED','GD is avabile version ');
DEFINE('_FB_GD_NO_VERSION','Can not detect GD version');
DEFINE('_FB_GD_NOT_INSTALLED','GD isnt installed, you can get more info ');
DEFINE('_FB_AVATAR_SMALL_HEIGHT','Small Image Height :');
DEFINE('_FB_AVATAR_SMALL_WIDTH','Small Image Width :');
DEFINE('_FB_AVATAR_MEDIUM_HEIGHT','Medium Image Height :');
DEFINE('_FB_AVATAR_MEDIUM_WIDTH','Medium Image Width :');
DEFINE('_FB_AVATAR_LARGE_HEIGHT','Large Image Height :');
DEFINE('_FB_AVATAR_LARGE_WIDTH','Large Image Width :');
DEFINE('_FB_AVATAR_QUALITY','Avatar Quality');
DEFINE('_FB_WELCOME','Welcome to FireBoard');
DEFINE('_FB_WELCOME_DESC','Thank you for choosing FireBoard as your board solution. This screen will give you a quick overview of all the various statistics of your board. The links on the left hand side of this screen allow you to control every aspect of your board experience. Each page will have instructions on how to use the tools.');
DEFINE('_FB_STATISTIC','Statistic');
DEFINE('_FB_VALUE','Value');
DEFINE('_GEN_CATEGORY','Category');
DEFINE('_GEN_STARTEDBY','Started by: ');
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
DEFINE('_STATS_POPULAR_PROFILE','Popular 10 Members (Profile hits)');
DEFINE('_STATS_TOP_POSTERS','Top posters');
DEFINE('_STATS_POPULAR_TOPICS','Top popular topics');
DEFINE('_COM_A_STATSPAGE','Enable Stats Page');
DEFINE('_COM_A_STATSPAGE_DESC','If set to &quot;Yes&quot; a public link in the header menu will be shown to your forum Stats page. This page will display various statistics about your forum. <em>Stats page will be always available to admin regardless of this setting!</em>');
DEFINE('_COM_C_JBSTATS','Forum Stats');
DEFINE('_COM_C_JBSTATS_DESC','Forum Statistics');
define('_GEN_GENERAL','General');
define('_PERM_NO_READ','You do not have sufficient permissions to access this forum.');
DEFINE ('_FB_SMILEY_SAVED','Smiley saved');
DEFINE ('_FB_SMILEY_DELETED','Smiley deleted');
DEFINE ('_FB_CODE_ALLREADY_EXITS','Code already exists');
DEFINE ('_FB_MISSING_PARAMETER','Missing Parameter');
DEFINE ('_FB_RANK_ALLREADY_EXITS','Rank already exists');
DEFINE ('_FB_RANK_DELETED','Rank Deleted');
DEFINE ('_FB_RANK_SAVED','Rank saved');
DEFINE ('_FB_DELETE_SELECTED','Delete selected');
DEFINE ('_FB_MOVE_SELECTED','Move selected');
DEFINE ('_FB_REPORT_LOGGED','Logged');
DEFINE ('_FB_GO','Go');
DEFINE('_FB_MAILFULL','Include complete post content in the email sent to subscribers');
DEFINE('_FB_MAILFULL_DESC','If No - subscribers will receive only titles of new messages');
DEFINE('_FB_HIDETEXT','Please login to view this content!');
DEFINE('_BBCODE_HIDE','Hidden text: [hide]any hidden text[/hide] - hide part of message from Guests');
DEFINE('_FB_FILEATTACH','File Attachment: ');
DEFINE('_FB_FILENAME','File Name: ');
DEFINE('_FB_FILESIZE','File Size: ');
DEFINE('_FB_MSG_CODE','Code: ');
DEFINE('_FB_CAPTCHA_ON','Spam protect system');
DEFINE('_FB_CAPTCHA_DESC','Antispam & antibot CAPTCHA system On/Off');
DEFINE('_FB_CAPDESC','Enter code here');
DEFINE('_FB_CAPERR','Code not correct!');
DEFINE('_FB_COM_A_REPORT', 'Message Reporting');
DEFINE('_FB_COM_A_REPORT_DESC', 'If you want to users report any message, choose yes.');
DEFINE('_FB_REPORT_MSG', 'Message Reported');
DEFINE('_FB_REPORT_REASON', 'Reason');
DEFINE('_FB_REPORT_MESSAGE', 'Your Message');
DEFINE('_FB_REPORT_SEND', 'Send Report');
DEFINE('_FB_REPORT', 'Report to moderator');
DEFINE('_FB_REPORT_RSENDER', 'Report Sender: ');
DEFINE('_FB_REPORT_RREASON', 'Report Reason: ');
DEFINE('_FB_REPORT_RMESSAGE', 'Report Message: ');
DEFINE('_FB_REPORT_POST_POSTER', 'Message Poster: ');
DEFINE('_FB_REPORT_POST_SUBJECT', 'Message Subject: ');
DEFINE('_FB_REPORT_POST_MESSAGE', 'Message: ');
DEFINE('_FB_REPORT_POST_LINK', 'Message Link: ');
DEFINE('_FB_REPORT_INTRO', 'was sent you a message because of');
DEFINE('_FB_REPORT_SUCCESS', 'Report succesfully sent!');
DEFINE('_FB_EMOTICONS', 'Emoticons');
DEFINE('_FB_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_FB_EMOTICONS_CODE', 'Code');
DEFINE('_FB_EMOTICONS_URL', 'URL');
DEFINE('_FB_EMOTICONS_EDIT_SMILEY', 'Edit Smiley');
DEFINE('_FB_EMOTICONS_EDIT_SMILIES', 'Edit Smilies');
DEFINE('_FB_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_FB_EMOTICONS_NEW_SMILEY', 'New Smiley');
DEFINE('_FB_EMOTICONS_MORE_SMILIES', 'More Smilies');
DEFINE('_FB_EMOTICONS_CLOSE_WINDOW', 'Close Window');
DEFINE('_FB_EMOTICONS_ADDITIONAL_EMOTICONS', 'Additional Emoticons');
DEFINE('_FB_EMOTICONS_PICK_A_SMILEY', 'Pick a smiley');
DEFINE('_FB_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_FB_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
DEFINE('_FB_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
DEFINE('_FB_USERNAMECANCHANGE', 'Allow username change');
DEFINE('_FB_USERNAMECANCHANGE_DESC', 'Allow username change on myprofile plugin page');
DEFINE ('_FB_RECOUNTFORUMS','Recount Categories Stats');
DEFINE ('_FB_RECOUNTFORUMS_DONE','All category statistics now are recounted.');
DEFINE ('_FB_EDITING_REASON','Reason for Editing');
DEFINE ('_FB_EDITING_LASTEDIT','Last Edit');
DEFINE ('_FB_BY','By');
DEFINE ('_FB_REASON','Reason');
DEFINE('_GEN_GOTOBOTTOM', 'Go to bottom');
DEFINE('_GEN_GOTOTOP', 'Go to top');
DEFINE('_STAT_USER_INFO', 'User Info');
DEFINE('_USER_SHOWEMAIL', 'Show Email'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Show Online');
DEFINE('_FB_HIDDEN_USERS', 'Hidden Users');
DEFINE('_FB_SAVE', 'Save');
DEFINE('_FB_RESET', 'Reset');
DEFINE('_FB_DEFAULT_GALLERY', 'Default Gallery');
DEFINE('_FB_MYPROFILE_PERSONAL_INFO', 'Personal Info');
DEFINE('_FB_MYPROFILE_SUMMARY', 'Summary');
DEFINE('_FB_MYPROFILE_MYAVATAR', 'My Avatar');
DEFINE('_FB_MYPROFILE_FORUM_SETTINGS', 'Forum Settings');
DEFINE('_FB_MYPROFILE_LOOK_AND_LAYOUT', 'Look and Layout');
DEFINE('_FB_MYPROFILE_MY_PROFILE_INFO', 'My Profile Info');
DEFINE('_FB_MYPROFILE_MY_POSTS', 'My Posts');
DEFINE('_FB_MYPROFILE_MY_SUBSCRIBES', 'My Subscribes');
DEFINE('_FB_MYPROFILE_MY_FAVORITES', 'My Favorites');
DEFINE('_FB_MYPROFILE_PRIVATE_MESSAGING', 'Private Messaging');
DEFINE('_FB_MYPROFILE_INBOX', 'Inbox');
DEFINE('_FB_MYPROFILE_NEW_MESSAGE', 'New Message');
DEFINE('_FB_MYPROFILE_OUTBOX', 'Outbox');
DEFINE('_FB_MYPROFILE_TRASH', 'Trash');
DEFINE('_FB_MYPROFILE_SETTINGS', 'Settings');
DEFINE('_FB_MYPROFILE_CONTACTS', 'Contacts');
DEFINE('_FB_MYPROFILE_BLOCKEDLIST', 'Blocked List');
DEFINE('_FB_MYPROFILE_ADDITIONAL_INFO', 'Additional Info');
DEFINE('_FB_MYPROFILE_NAME', 'Name');
DEFINE('_FB_MYPROFILE_USERNAME', 'Username');
DEFINE('_FB_MYPROFILE_EMAIL', 'Email');
DEFINE('_FB_MYPROFILE_USERTYPE', 'User Type');
DEFINE('_FB_MYPROFILE_REGISTERDATE', 'Register Date');
DEFINE('_FB_MYPROFILE_LASTVISITDATE', 'Last Visit Date');
DEFINE('_FB_MYPROFILE_POSTS', 'Posts');
DEFINE('_FB_MYPROFILE_PROFILEVIEW', 'Profile View');
DEFINE('_FB_MYPROFILE_PERSONALTEXT', 'Personal Text');
DEFINE('_FB_MYPROFILE_GENDER', 'Gender');
DEFINE('_FB_MYPROFILE_BIRTHDATE', 'Birthdate');
DEFINE('_FB_MYPROFILE_BIRTHDATE_DESC', 'Year (YYYY) - Month (MM) - Day (DD)');
DEFINE('_FB_MYPROFILE_LOCATION', 'Location');
DEFINE('_FB_MYPROFILE_ICQ', 'ICQ');
DEFINE('_FB_MYPROFILE_ICQ_DESC', 'This is your ICQ number.');
DEFINE('_FB_MYPROFILE_AIM', 'AIM');
DEFINE('_FB_MYPROFILE_AIM_DESC', 'This is your AOL Instant Messenger nickname.');
DEFINE('_FB_MYPROFILE_YIM', 'YIM');
DEFINE('_FB_MYPROFILE_YIM_DESC', 'This is your Yahoo! Instant Messenger nickname.');
DEFINE('_FB_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_FB_MYPROFILE_SKYPE_DESC', 'This is your Skype handle.');
DEFINE('_FB_MYPROFILE_GTALK', 'GTALK');
DEFINE('_FB_MYPROFILE_GTALK_DESC', 'This is your Gtalk nickname.');
DEFINE('_FB_MYPROFILE_WEBSITE', 'Website');
DEFINE('_FB_MYPROFILE_WEBSITE_NAME', 'Website Name');
DEFINE('_FB_MYPROFILE_WEBSITE_NAME_DESC', 'Example: Best of Joomla!');
DEFINE('_FB_MYPROFILE_WEBSITE_URL', 'Website URL');
DEFINE('_FB_MYPROFILE_WEBSITE_URL_DESC', 'Example: www.bestofjoomla.com');
DEFINE('_FB_MYPROFILE_MSN', 'MSN');
DEFINE('_FB_MYPROFILE_MSN_DESC', 'Your MSN messenger email address.');
DEFINE('_FB_MYPROFILE_SIGNATURE', 'Signature');
DEFINE('_FB_MYPROFILE_MALE', 'Male');
DEFINE('_FB_MYPROFILE_FEMALE', 'Female');
DEFINE('_FB_BULKMSG_DELETED', 'Posts were deleted successfully');
DEFINE('_FB_DATE_YEAR', 'Year');
DEFINE('_FB_DATE_MONTH', 'Month');
DEFINE('_FB_DATE_WEEK','Week');
DEFINE('_FB_DATE_DAY', 'Day');
DEFINE('_FB_DATE_HOUR', 'Hour');
DEFINE('_FB_DATE_MINUTE', 'Minute');
DEFINE('_FB_IN_FORUM', ' in Forum: ');
DEFINE('_FB_FORUM_AT', ' Forum at: ');
DEFINE('_FB_QMESSAGE_NOTE', 'Please note, although no boardcode and smiley buttons are shown, they are still useable');

// 1.0.1
DEFINE ('_FB_FORUMTOOLS','Forum Tools');

//userlist
DEFINE ('_FB_USRL_USERLIST','Userlist');
DEFINE ('_FB_USRL_REGISTERED_USERS','%s has <b>%d</b> registered users');
DEFINE ('_FB_USRL_SEARCH_ALERT','Please enter a value to search!');
DEFINE ('_FB_USRL_SEARCH','Find user');
DEFINE ('_FB_USRL_SEARCH_BUTTON','Search');
DEFINE ('_FB_USRL_LIST_ALL','List all');
DEFINE ('_FB_USRL_NAME','Name');
DEFINE ('_FB_USRL_USERNAME','Username');
DEFINE ('_FB_USRL_GROUP','Group');
DEFINE ('_FB_USRL_POSTS','Posts');
DEFINE ('_FB_USRL_KARMA','Karma');
DEFINE ('_FB_USRL_HITS','Hits');
DEFINE ('_FB_USRL_EMAIL','E-mail');
DEFINE ('_FB_USRL_USERTYPE','Usertype');
DEFINE ('_FB_USRL_JOIN_DATE','Join date');
DEFINE ('_FB_USRL_LAST_LOGIN','Last login');
DEFINE ('_FB_USRL_NEVER','Never');
DEFINE ('_FB_USRL_ONLINE','Status');
DEFINE ('_FB_USRL_AVATAR','Picture');
DEFINE ('_FB_USRL_ASC','Ascending');
DEFINE ('_FB_USRL_DESC','Descending');
DEFINE ('_FB_USRL_DISPLAY_NR','Display');
DEFINE ('_FB_USRL_DATE_FORMAT','%m/%d/%Y');

DEFINE('_FB_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_FB_ADMIN_CONFIG_USERLIST','Userlist');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_ROWS_DESC','Number of userlist rows');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_ROWS','Number of userlist rows');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERONLINE','Online Status');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Show users online status');

DEFINE('_FB_ADMIN_CONFIG_USERLIST_AVATAR','Display Avatar');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_NAME','Show Real Name');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERNAME','Show Username');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_GROUP','Show User Group');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_POSTS','Show Number of Posts');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_KARMA','Show Karma');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_EMAIL','Show Email');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERTYPE','Show User Type');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_JOINDATE','Show Join Date');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Show Last Visit Date');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_HITS','Show Profile Hits');
DEFINE('_FB_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_FB_DBWIZ', 'Database Wizard');
DEFINE('_FB_DBMETHOD', 'Please select which method you want to complete your installation:');
DEFINE('_FB_DBCLEAN', 'Clean installation');
DEFINE('_FB_DBUPGRADE', 'Upgrade From Joomlaboard');
DEFINE('_FB_TOPLEVEL', 'Top Level Category');
DEFINE('_FB_REGISTERED', 'Registered');
DEFINE('_FB_PUBLICBACKEND', 'Public Backend');
DEFINE('_FB_SELECTANITEMTO', 'Select an item to');
DEFINE('_FB_ERRORSUBS', 'Something went wrong deleting the messages and subscriptions');
DEFINE('_FB_WARNING', 'Warning...');
DEFINE('_FB_CHMOD1', 'You need to chmod this to 766 in order for the file to be updated.');
DEFINE('_FB_YOURCONFIGFILEIS', 'Your config file is');
DEFINE('_FB_FIREBOARD', 'FireBoard');
DEFINE('_FB_CLEXUS', 'Clexus PM');
DEFINE('_FB_CB', 'Community Builder');
DEFINE('_FB_MYPMS', 'myPMS II Open Source');
DEFINE('_FB_UDDEIM', 'Uddeim');
DEFINE('_FB_JIM', 'JIM');
DEFINE('_FB_MISSUS', 'Missus');
DEFINE('_FB_SELECTTEMPLATE', 'Select Template');
DEFINE('_FB_CONFIGSAVED', 'Configuration saved.');
DEFINE('_FB_CONFIGNOTSAVED', 'FATAL ERROR: Configuration could not be saved.');
DEFINE('_FB_TFINW', 'The file is not writable.');
DEFINE('_FB_FBCFS', 'FireBoard CSS file saved.');
DEFINE('_FB_SELECTMODTO', 'Select an moderator to');
DEFINE('_FB_CHOOSEFORUMTOPRUNE', 'You must choose a forum to prune!');
DEFINE('_FB_DELMSGERROR', 'Deleting messages failed:');
DEFINE('_FB_DELMSGERROR1', 'Deleting messages texts failed:');
DEFINE('_FB_CLEARSUBSFAIL', 'Clearing subscriptions failed:');
DEFINE('_FB_FORUMPRUNEDFOR', 'Forum pruned for');
DEFINE('_FB_PRUNEDAYS', 'days');
DEFINE('_FB_PRUNEDELETED', 'Deleted:');
DEFINE('_FB_PRUNETHREADS', 'threads');
DEFINE('_FB_ERRORPRUNEUSERS', 'Error pruning users:');
DEFINE('_FB_USERSPRUNEDDELETED', 'Users pruned; Deleted:'); // <=FB 1.0.3
DEFINE('_FB_PRUNEUSERPROFILES', 'user profiles'); // <=FB 1.0.3
DEFINE('_FB_NOPROFILESFORPRUNNING', 'No profiles found eligible for pruning.'); // <=FB 1.0.3
DEFINE('_FB_TABLESUPGRADED', 'FireBoard Tables are upgraded to version');
DEFINE('_FB_FORUMCATEGORY', 'Forum Category');
DEFINE('_FB_SAMPLWARN1', '-- Make absolutely sure that you load the sample data on completely empty fireboard tables. If anything is in them, it will not work!');
DEFINE('_FB_FORUM1', 'Forum 1');
DEFINE('_FB_SAMPLEPOST1', 'Sample Post 1');
DEFINE('_FB_SAMPLEFORUM11', 'Sample Forum 1\r\n');
DEFINE('_FB_SAMPLEPOST11', '[b][size=4][color=#FF6600]Sample Post[/color][/size][/b]\nCongratulations with your new Forum!\n\n[url=http://bestofjoomla.com]- Best of Joomla[/url]');
DEFINE('_FB_SAMPLESUCCESS', 'Sample data loaded');
DEFINE('_FB_SAMPLEREMOVED', 'Sample data removed');
DEFINE('_FB_CBADDED', 'Community Builder profile added');
DEFINE('_FB_IMGDELETED', 'Image deleted');
DEFINE('_FB_FILEDELETED', 'File deleted');
DEFINE('_FB_NOPARENT', 'No Parent');
DEFINE('_FB_DIRCOPERR', 'Error: File');
DEFINE('_FB_DIRCOPERR1', 'could not be copied!\n');
DEFINE('_FB_INSTALL1', '<strong>FireBoard Forum</strong> Component <em>for Joomla! CMS</em> <br />&copy; 2006 - 2008 by Best Of Joomla<br />All rights reserved.');
DEFINE('_FB_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
// added by aliyar
DEFINE('_FB_FORUMPRF_TITLE', 'Profile Settings');
DEFINE('_FB_FORUMPRF', 'Profile');
DEFINE('_FB_FORUMPRRDESC', 'If you have Clexus PM or Community Builder component installed, you can configure fireboard to use the user profile page.');
DEFINE('_FB_USERPROFILE_PROFILE', 'Profile');
DEFINE('_FB_USERPROFILE_PROFILEHITS', '<b>Profile View</b>');
DEFINE('_FB_USERPROFILE_MESSAGES', 'All Forum Messages');
DEFINE('_FB_USERPROFILE_TOPICS', 'Topics');
DEFINE('_FB_USERPROFILE_STARTBY', 'Started by');
DEFINE('_FB_USERPROFILE_CATEGORIES', 'Categories');
DEFINE('_FB_USERPROFILE_DATE', 'Date');
DEFINE('_FB_USERPROFILE_HITS', 'Hits');
DEFINE('_FB_USERPROFILE_NOFORUMPOSTS', 'No Forum Post');
DEFINE('_FB_TOTALFAVORITE', 'Favoured:  ');
DEFINE('_FB_SHOW_CHILD_CATEGORY_COLON', 'Number of child board columns  ');
DEFINE('_FB_SHOW_CHILD_CATEGORY_COLONDESC', 'Number of child board column formating under main category ');
DEFINE('_FB_SUBSCRIPTIONSCHECKED', 'Post-subscription checked by default?');
DEFINE('_FB_SUBSCRIPTIONSCHECKED_DESC', 'Set to "Yes" If you want to post subscription box always checked');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_FB_ERROR1', 'Category / Forum must have a name');
// Forum Configuration (New in FireBoard)
DEFINE('_FB_SHOWSTATS', 'Show Stats');
DEFINE('_FB_SHOWSTATSDESC', 'If you want to show Stats, select Yes');
DEFINE('_FB_SHOWWHOIS', 'Show Whois Online');
DEFINE('_FB_SHOWWHOISDESC', 'If you want to show  Whois Online, select Yes');
DEFINE('_FB_STATSGENERAL', 'Show General Stats');
DEFINE('_FB_STATSGENERALDESC', 'If you want to show General Stats, select Yes');
DEFINE('_FB_USERSTATS', 'Show Popular User Stats');
DEFINE('_FB_USERSTATSDESC', 'If you want to show Popular Stats, select Yes');
DEFINE('_FB_USERNUM', 'Number of Popular User');
DEFINE('_FB_USERPOPULAR', 'Show Popular Subject Stats');
DEFINE('_FB_USERPOPULARDESC', 'If you want to show Popular Subject, select Yes');
DEFINE('_FB_NUMPOP', 'Number of Popular Subject');
DEFINE('_FB_INFORMATION',
    'Best of Joomla team is proud to announce the release of FireBoard 1.0.0. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard team and most of our praises goes to their team.Some of the main features of FireBoard can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Best of Joomla! team<br /></td></tr></table>');
DEFINE('_FB_INSTRUCTIONS', 'Instructions');
DEFINE('_FB_FINFO', 'FireBoard Forum Information');
DEFINE('_FB_CSSEDITOR', 'FireBoard Template CSS Editor');
DEFINE('_FB_PATH', 'Path:');
DEFINE('_FB_CSSERROR', 'Please Note:CSS Template file must be Writable to Save Changes.');
// User Management
DEFINE('_FB_FUM', 'FireBoard User Profile Manager');
DEFINE('_FB_SORTID', 'sort by userid');
DEFINE('_FB_SORTMOD', 'sort by moderators');
DEFINE('_FB_SORTNAME', 'sort by names');
DEFINE('_FB_VIEW', 'View');
DEFINE('_FB_NOUSERSFOUND', 'No userprofiles found.');
DEFINE('_FB_ADDMOD', 'Add Moderator to');
DEFINE('_FB_NOMODSAV', 'There are no possible moderators found. Read the \'note\' below.');
DEFINE('_FB_NOTEUS',
    'NOTE: Only users which have the moderator flag set in their fireboard Profile are shown here. In order to be able to add a moderator give a user a moderator flag, go to <a href="index2.php?option=com_fireboard&task=profiles">User Administration</a> and search for the user you want to make a moderator. Then select his or her profile and update it. The moderator flag can only be set by an site administrator. ');
DEFINE('_FB_PROFFOR', 'Profile for');
DEFINE('_FB_GENPROF', 'General Profile Options');
DEFINE('_FB_PREFVIEW', 'Prefered Viewtype:');
DEFINE('_FB_PREFOR', 'Prefered Message Ordering:');
DEFINE('_FB_ISMOD', 'Is Moderator:');
DEFINE('_FB_ISADM', '<strong>Yes</strong> (not changeable, this user is an site (super)administrator)');
DEFINE('_FB_COLOR', 'Color');
DEFINE('_FB_UAVATAR', 'User avatar:');
DEFINE('_FB_NS', 'None selected');
DEFINE('_FB_DELSIG', ' check this box to delete this signature');
DEFINE('_FB_DELAV', ' check this box to delete this avatar');
DEFINE('_FB_SUBFOR', 'Subscriptions for');
DEFINE('_FB_NOSUBS', 'No subscriptions found for this user');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_FB_BASICS', 'Basics');
DEFINE('_FB_BASICSFORUM', 'Basic Forum Information');
DEFINE('_FB_PARENT', 'Parent:');
DEFINE('_FB_PARENTDESC',
    'Please note: To create a Category, choose \'Top Level Category\' as a Parent. A Category serves as a container for Forums.<br />A Forum can <strong>only</strong> be created within a Category by selecting a previously created Category as the Parent for the Forum.<br /> Messages can <strong>NOT</strong> be posted to a Category; only to Forums.');
DEFINE('_FB_BASICSFORUMINFO', 'Forum name and description');
DEFINE('_FB_NAMEADD', 'Name:');
DEFINE('_FB_DESCRIPTIONADD', 'Description:');
DEFINE('_FB_ADVANCEDDESC', 'Forum advanced configuration');
DEFINE('_FB_ADVANCEDDESCINFO', 'Forum security and access');
DEFINE('_FB_LOCKEDDESC', 'Set to &quot;Yes&quot; if you want to lock this forum Nobody, except Moderators and Admins can create new topics or replies in a locked forum (or move posts to it).');
DEFINE('_FB_LOCKED1', 'Locked:');
DEFINE('_FB_PUBACC', 'Public Access Level:');
DEFINE('_FB_PUBACCDESC',
    'To create a Non-Public Forum you can specify the minimum userlevel that can see/enter the forum here.By default the minumum userlevel is set to &quot;Everybody&quot;.<br /><b>Please note</b>: if you restrict access on a whole Category to one or more certain groups, it will hide all Forums it contains to anybody not having proper privileges on the Category <b>even</b> if one or more of these Forums have a lower access level set! This holds for Moderators too; you will have to add a Moderator to the moderator list of the Category if (s)he does not have the proper group level to see the Category.<br /> This is irrespective of the fact that Categories can not be Moderated; Moderators can still be added to the moderator list.');
DEFINE('_FB_CGROUPS', 'Include Child Groups:');
DEFINE('_FB_CGROUPSDESC', 'Should child groups be allowed access as well? If set to &quot;No&quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_FB_ADMINLEVEL', 'Admin Access Level:');
DEFINE('_FB_ADMINLEVELDESC',
    'If you create a forum with Public Access restrictions, you can specify here an additional Administration Access Level.<br /> If you restrict the access to the forum to a special Public Frontend user group and don\'t specify a Public Backend Group here, administrators will not be able to enter/view the Forum.');
DEFINE('_FB_ADVANCED', 'Advanced');
DEFINE('_FB_CGROUPS1', 'Include Child Groups:');
DEFINE('_FB_CGROUPS1DESC', 'Should child groups be allowed access as well? If set to &quot;No &quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_FB_REV', 'Review posts:');
DEFINE('_FB_REVDESC',
    'Set to &quot;Yes&quot; if you want posts to be reviewed by Moderators prior to publishing them in this forum. This is useful in a Moderated forum only!<br />If you set this without any Moderators specified, the Site Admin is solely responsible for approving/deleting submitted posts as these will be kept \'on hold\'!');
DEFINE('_FB_MOD_NEW', 'Moderation');
DEFINE('_FB_MODNEWDESC', 'Moderation of the forum and forum moderators');
DEFINE('_FB_MOD', 'Moderated:');
DEFINE('_FB_MODDESC',
    'Set to &quot;Yes&quot; if you want to be able to assign Moderators to this forum.<br /><strong>Note:</strong> This doesn\'t mean that new post must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>Please do note:</strong> After setting Moderation to &quot;Yes&quot; you must save the forum configuration first before you will be able to add Moderators using the new button.');
DEFINE('_FB_MODHEADER', 'Moderation settings for this forum');
DEFINE('_FB_MODSASSIGNED', 'Moderators assigned to this forum:');
DEFINE('_FB_NOMODS', 'There are no Moderators assigned to this forum');
// Some General Strings (Improvement in FireBoard)
DEFINE('_FB_EDIT', 'Edit');
DEFINE('_FB_ADD', 'Add');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_FB_MOVEUP', 'Move Up');
DEFINE('_FB_MOVEDOWN', 'Move Down');
// Groups - Integration in FireBoard
DEFINE('_FB_ALLREGISTERED', 'All Registered');
DEFINE('_FB_EVERYBODY', 'Everybody');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_FB_REORDER', 'Reorder');
DEFINE('_FB_CHECKEDOUT', 'Check Out');
DEFINE('_FB_ADMINACCESS', 'Admin Access');
DEFINE('_FB_PUBLICACCESS', 'Public Access');
DEFINE('_FB_PUBLISHED', 'Published');
DEFINE('_FB_REVIEW', 'Review');
DEFINE('_FB_MODERATED', 'Moderated');
DEFINE('_FB_LOCKED', 'Locked');
DEFINE('_FB_CATFOR', 'Category / Forum');
DEFINE('_FB_ADMIN', 'FireBoard Administration');
DEFINE('_FB_CP', 'FireBoard Control Panel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar Integration');
DEFINE('_COM_A_RANKS_SETTINGS', 'Ranks');
DEFINE('_COM_A_RANKING_SETTINGS', 'Ranking Settings');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Avatar Settings');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Security Settings');
DEFINE('_COM_A_BASIC_SETTINGS', 'Basic Settings');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Allow Favorites');
DEFINE('_COM_A_FAVORITES_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to favorite to a topic ');
DEFINE('_USER_UNFAVORITE_ALL', 'Check this box to <b><u>unfavorite</u></b> from all topics (including invisible ones for troubleshooting purposes)');
DEFINE('_VIEW_FAVORITETXT', 'Favorite to this topic ');
DEFINE('_USER_UNFAVORITE_YES', 'You have been unfavorited from the topic.');
DEFINE('_POST_FAVORITED_TOPIC', 'This thread has been added to your favorites.');
DEFINE('_VIEW_UNFAVORITETXT', 'Unfavorite');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Unsubscribe');
DEFINE('_USER_NOFAVORITES', 'No Favorites');
DEFINE('_POST_SUCCESS_FAVORITE', 'Your request to add to favorites has been processed.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Search Results');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Messages per page on Search Results');
DEFINE('_FB_USE_JOOMLA_STYLE', 'Use Joomla Style?');
DEFINE('_FB_USE_JOOMLA_STYLE_DESC', 'If you want to use joomla style set to YES. (class: like sectionheader, sectionentry1 ...) ');
DEFINE('_FB_SHOW_CHILD_CATEGORY_ON_LIST', 'Show Child Category Image');
DEFINE('_FB_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'If you want to show child category small icon  on your forum list, set to YES. ');
DEFINE('_FB_SHOW_ANNOUNCEMENT', 'Show Announcement');
DEFINE('_FB_SHOW_ANNOUNCEMENT_DESC', 'Set to "Yes" , if you want to show announcement box on forum homepage.');
DEFINE('_FB_SHOW_AVATAR_ON_CAT', 'Show Avartar on Categories list?');
DEFINE('_FB_SHOW_AVATAR_ON_CAT_DESC', 'Set to "Yes" , if you want to show user avatar on your forum category list.');
DEFINE('_FB_RECENT_POSTS', 'Recent Post Settings');
DEFINE('_FB_SHOW_LATEST_MESSAGES', 'Show Recent Posts');
DEFINE('_FB_SHOW_LATEST_MESSAGES_DESC', 'Set to "Yes" if you want to show recent post plugin on your forum');
DEFINE('_FB_NUMBER_OF_LATEST_MESSAGES', 'Number of Recent Posts');
DEFINE('_FB_NUMBER_OF_LATEST_MESSAGES_DESC', 'Number of Recent Posts');
DEFINE('_FB_COUNT_PER_PAGE_LATEST_MESSAGES', 'Count Per Tab ');
DEFINE('_FB_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Number of Post per one tab');
DEFINE('_FB_LATEST_CATEGORY', 'Show Category');
DEFINE('_FB_LATEST_CATEGORY_DESC', 'Specific category you can show on recent posts. For example:2,3,7 ');
DEFINE('_FB_SHOW_LATEST_SINGLE_SUBJECT', 'Show Single Subject');
DEFINE('_FB_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Show Single Subject');
DEFINE('_FB_SHOW_LATEST_REPLY_SUBJECT', 'Show Reply Subject');
DEFINE('_FB_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Show Reply Subject (Re:)');
DEFINE('_FB_LATEST_SUBJECT_LENGTH', 'Subject Length');
DEFINE('_FB_LATEST_SUBJECT_LENGTH_DESC', 'Subject Length');
DEFINE('_FB_SHOW_LATEST_DATE', 'Show Date');
DEFINE('_FB_SHOW_LATEST_DATE_DESC', 'Show Date');
DEFINE('_FB_SHOW_LATEST_HITS', 'Show Hits');
DEFINE('_FB_SHOW_LATEST_HITS_DESC', 'Show Hits');
DEFINE('_FB_SHOW_AUTHOR', 'Show Author');
DEFINE('_FB_SHOW_AUTHOR_DESC', '1=username, 2=realname, 0=none');
DEFINE('_FB_STATS', 'Stats Plugin Settings ');
DEFINE('_FB_CATIMAGEPATH', 'Category Image Path ');
DEFINE('_FB_CATIMAGEPATH_DESC', 'Category Image path. If you set "category_images/" path will be "your_html_rootfolder/images/fbfiles/category_images/');
DEFINE('_FB_ANN_MODID', 'Announcement Moderator IDs ');
DEFINE('_FB_ANN_MODID_DESC', 'Add user ids for announcements moderation. e.g. 62,63,73 . Announcement moderators can add, edit, delete the announcements.');
//
DEFINE('_FB_FORUM_TOP', 'Board Categories ');
DEFINE('_FB_CHILD_BOARDS', 'Child Boards ');
DEFINE('_FB_QUICKMSG', 'Quick Reply ');
DEFINE('_FB_THREADS_IN_FORUM', 'Threads in Forum ');
DEFINE('_FB_FORUM', 'Forum ');
DEFINE('_FB_SPOTS', 'Spotlights');
DEFINE('_FB_CANCEL', 'cancel');
DEFINE('_FB_TOPIC', 'TOPIC: ');
DEFINE('_FB_POWEREDBY', 'Powered by ');
// Time Format
DEFINE('_TIME_TODAY', '<b>Today</b> ');
DEFINE('_TIME_YESTERDAY', '<b>Yesterday</b> ');
//  STARTS HERE!
DEFINE('_FB_WHO_LATEST_POSTS', 'Latest Posts');
DEFINE('_FB_WHO_WHOISONLINE', 'Who is Online');
DEFINE('_FB_WHO_MAINPAGE', 'Forum Main');
DEFINE('_FB_GUEST', 'Guest');
DEFINE('_FB_PATHWAY_VIEWING', 'viewing');
DEFINE('_FB_ATTACH', 'Attachment');
// Favorite
DEFINE('_FB_FAVORITE', 'Favorite');
DEFINE('_USER_FAVORITES', 'My Favorites');
DEFINE('_THREAD_UNFAVORITE', 'Remove as Favorites');
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
DEFINE('_STAT_POPULAR_USER_TMSG', 'Users ( Total Messages) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Threads ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Users ( Total Profile Views) ');
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
DEFINE('_USRL_REGISTERED_USERS', '%s has <b>%d</b> registered users');
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
DEFINE('_USRL_SEARCHRESULT', 'Searchresult for');
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
DEFINE('_PREVIEW', 'preview');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'The Discuss Bot enables your users to discuss content items in the forums. The Content Title is used as the topic subject.'
           . '<br />If a topic does not exist yet a new one is created. If the topic already exists, the user is shown the thread and (s)he can reply.' . '<br /><strong>You will need to download and install the Bot separately.</strong>'
           . '<br />check the <a href="http://www.bestofjoomla.com">Best of Joomla Site</a> for more information.' . '<br />When Installed you will need to add the following bot lines to your Content:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the Content Item can be discussed. To find the proper catid, just look into the forums ' . 'and check the category id from the URLs from your browsers status bar.'
           . '<br />Example: if you want the article discussed in Forum with catid 26, the Bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each Content Item to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Search');
DEFINE('_FORUM_SEARCHRESULTS', 'displaying %s out of %s results.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Rules');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Edit this file to insert your rules joomlaroot/administrator/components/com_fireboard/language/english.php</li><li>Rule 2</li><li>Rule 3</li><li>Rule 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Boardcode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'The post(s) have been approved');
DEFINE('_MODERATION_DELETE_ERROR', 'ERROR: The post(s) could not be deleted');
DEFINE('_MODERATION_APPROVE_ERROR', 'ERROR: The post(s) could not be approved');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'There are no forums in this category!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Failed to create ghost topic in old forum!');
DEFINE('_POST_MOVE_GHOST', 'Leave ghost message in old forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Forum Jump');
DEFINE('_COM_A_FORUM_JUMP', 'Enable Forum Jump');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'If set to &quot;Yes&quot; a selector will be shown on the forum pages that allow for a quick jump to another forum or category.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Rules');
DEFINE('_COM_A_RULESPAGE', 'Enable Rules Page');
DEFINE('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page. This page can be used for things like your forum rules etcetera. You can alter the contents of this file to your liking by opening the rules.php in /joomla_root/components/com_fireboard. <em>Make sure you always have a backup of this file as it will be overwritten when upgrading!</em>');
DEFINE('_MOVED_TOPIC', 'MOVED:');
DEFINE('_COM_A_PDF', 'Enable PDF creation');
DEFINE('_COM_A_PDF_DESC',
    'Set to &quot;Yes&quot; if you would like to enable users to download a simple PDF document with the contents of a thread.<br />It is a <u>simple</u> PDF document; no mark up, no fancy layout and such but it does contain all the text from the thread.');
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
DEFINE('_COM_A_BADWORDS_DESC', 'Set to &quot;Yes&quot; if you want to filter posts containing the words you defined in the Badword Component config. To use this function you must have Badword Component installed!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* This message has been censored because it contained one or more words set by the administrator *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Create Community Builder forum profile');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Click the link to create necessary Forum fields in Community Builder user profile. After they are created you are free to move them whenever you want using the Community Builder admin, just do not rename their names or options. If you delete them from the Community Builder admin, you can create them again using this link, otherwise do not click on the link multiple times!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Click here <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder user profiles');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Setting to &quot;Yes&quot; will activate the integration with Community Builder component (www.joomlapolis.com). All FireBoard user profile functions will be handled by the Community Builder and the profile link in the forums will take you to the Community Builder user profile. This setting will override the Clexus PM profile setting if both are set to &quot;Yes&quot;. Also, make sure you apply the required changes in the Community Builder database by using the option below.');
DEFINE('_COM_A_AVATAR_SRC', 'Use avatar picture from');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'If you have Clexus PM or Community Builder component installed, you can configure FireBoard to use the user avatar picture from Clexus PM or Community Builder user profile. NOTE: For Community Builder you need to have thumbnail option turned on because forum uses thumbnail user pictures, not the originals.');
DEFINE('_COM_A_KARMA', 'Show Karma indicator');
DEFINE('_COM_A_KARMA_DESC', 'Set to &quot;Yes&quot; to show user Karma and related buttons (increase / decrease) if the User Stats are activated.');
DEFINE('_COM_A_DISEMOTICONS', 'Disable emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Set to &quot;Yes&quot; to completely disable graphic emoticons (smileys).');
DEFINE('_COM_C_FBCONFIG', 'FireBoard Configuration');
DEFINE('_COM_C_FBCONFIGDESC', 'Configure all FireBoard\'s functionality');
DEFINE('_COM_C_FORUM', 'Forum Administration');
DEFINE('_COM_C_FORUMDESC', 'Add categories/forums and configure them');
DEFINE('_COM_C_USER', 'User Administration');
DEFINE('_COM_C_USERDESC', 'Basic user and user profile administration');
DEFINE('_COM_C_FILES', 'Uploaded Files Browser');
DEFINE('_COM_C_FILESDESC', 'Browse and administer uploaded files');
DEFINE('_COM_C_IMAGES', 'Uploaded Images Browser');
DEFINE('_COM_C_IMAGESDESC', 'Browse and administer uploaded images');
DEFINE('_COM_C_CSS', 'Edit CSS File');
DEFINE('_COM_C_CSSDESC', 'Tweak FireBoard\'s look and feel');
DEFINE('_COM_C_SUPPORT', 'Support WebSite');
DEFINE('_COM_C_SUPPORTDESC', 'Connect to the Best of Joomla website (new window)');
DEFINE('_COM_C_PRUNETAB', 'Prune Forums');
DEFINE('_COM_C_PRUNETABDESC', 'Remove old threads (configurable)');
DEFINE('_COM_C_PRUNEUSERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sync FireBoard user table with Joomla! user table'); // <=FB 1.0.3
DEFINE('_COM_C_LOADSAMPLE', 'Load Sample Data');
DEFINE('_COM_C_LOADSAMPLEDESC', 'For an easy start: load the Sample Data on an empty FireBoard database');
DEFINE('_COM_C_REMOVESAMPLE', 'Remove Sample Data');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Remove Sample Data from your database');
DEFINE('_COM_C_LOADMODPOS', 'Load Module Positions');
DEFINE('_COM_C_LOADMODPOSDESC', 'Load Module Positions for FireBoard Template');
DEFINE('_COM_C_UPGRADEDESC', 'Get your database up to the latest version after an upgrade');
DEFINE('_COM_C_BACK', 'Back to FireBoard Control Panel');
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
DEFINE('_KARMA_WAIT', 'You can modify only one person\'s karma every 6 hours. <br/>Please wait untill this timeout period has passed before modifying any person\'s karma again.');
DEFINE('_KARMA_SELF_DECREASE', 'Please do not attempt to decrease your own karma!');
DEFINE('_KARMA_SELF_INCREASE', 'Your karma has been decreased for attempting to increase it yourself!');
DEFINE('_KARMA_DECREASED', 'User\'s karma decreased. If you are not taken back to the topic in a few moments,');
DEFINE('_KARMA_INCREASED', 'User\'s karma increased. If you are not taken back to the topic in a few moments,');
DEFINE('_COM_A_TEMPLATE', 'Template');
DEFINE('_COM_A_TEMPLATE_DESC', 'Choose the template to use.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Image Sets');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Choose the images set template to use.');
DEFINE('_PREVIEW_CLOSE', 'Close this window');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Use Posts Statistics Bar');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Set to &quot;Yes&quot; if you want the number of posts a user has made to be depicted graphically by a Statistics Bar.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Color number for Stats Bar');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Give the number of the color you want to use for the Post Stats Bar');
DEFINE('_LATEST_REDIRECT',
    'FireBoard needs to (re)establish your access privileges before it can create a list of the latest posts for you.\nDo not worry, this is quite normal after more than 30 minutes of inactivity or after logging back in.\nPlease submit your search request again.');
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
DEFINE('_IMAGE_SELECT_FILE', 'Select Image file to attach');
DEFINE('_FILE_SELECT_FILE', 'Select file to attach');
DEFINE('_FILE_NOT_UPLOADED', 'Your file has not been uploaded. Try posting again or editing the post');
DEFINE('_IMAGE_NOT_UPLOADED', 'Your image has not been uploaded. Try posting again or editing the post');
DEFINE('_BBCODE_IMGPH', 'Insert [img] placeholder in the post for attached image');
DEFINE('_BBCODE_FILEPH', 'Insert [file] placeholder in the post for attached file');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Check this box to <b><u>unsubscribe</u></b> from all topics (including invisible ones for troubleshooting purposes)');
DEFINE('_LINK_JS_REMOVED', '<em>Active link containing JavaScript has been removed automatically</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Look and Feel');
DEFINE('_COM_A_USERS', 'User Related');
DEFINE('_COM_A_LENGTHS', 'Various length settings');
DEFINE('_COM_A_SUBJECTLENGTH', 'Max. Subject length');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maximum Subject line length. The maximum number supported by the database is 255 characters. If your site is configured to use multi-byte character sets like Unicode, UTF-8 or non-ISO-8599-x make the maximum smaller using this forumula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Example for UTF-8, for which the max. character bite syze per character is 4 bytes: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Topic/Forum');
DEFINE('_LATEST_NUMBER', 'New posts');
DEFINE('_COM_A_SHOWNEW', 'Show New posts');
DEFINE('_COM_A_SHOWNEW_DESC', 'If set to &quot;Yes&quot; FireBoard will show the user an indicator for forums that contain new posts and which posts are new since his/her last visit.');
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
DEFINE('_IMAGE_DIMENSIONS', 'Your image file can be maximum (width x height - size)');
DEFINE('_IMAGE_ERROR_TYPE', 'Please use only jpeg, gif or png images');
DEFINE('_IMAGE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_IMAGE_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_UPLOADED', 'Your Image has been uploaded.');
DEFINE('_COM_A_IMAGE', 'Images');
DEFINE('_COM_A_IMGHEIGHT', 'Max. Image Height');
DEFINE('_COM_A_IMGWIDTH', 'Max. Image Width');
DEFINE('_COM_A_IMGSIZE', 'Max. Image Filesize<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Allow Public Upload for Images');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload an image.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Allow Registered Upload for Images');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload an image.<br/> Note: (Super)administrators and moderators are always able to upload images.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Uploads');
DEFINE('_FILE_TYPES', 'Your file can be of type - max. size');
DEFINE('_FILE_ERROR_TYPE', 'You are only allowed to upload files of type:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_FILE_ERROR_SIZE', 'The file size exceeds the maximum set by the Administrator.');
DEFINE('_COM_A_FILE', 'Files');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'File types allowed');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Specify here which file types are allowed for upload. Use comma separated <strong>lowercase</strong> lists without spaces.<br />Example: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Max. File size<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Allow File Upload for Public');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload a file.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Allow File Upload for Registered');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload a file.<br/> Note: (Super)administrators and moderators are always able to upload files.');
DEFINE('_SUBMIT_CANCEL', 'Your post submission has been cancelled');
DEFINE('_HELP_SUBMIT', 'Click here to submit your message');
DEFINE('_HELP_PREVIEW', 'Click here to see what your message will look like when submitted');
DEFINE('_HELP_CANCEL', 'Click here to cancel your message');
DEFINE('_POST_DELETE_ATT', 'If this box is checked, all image and file attachments of posts you are going to delete will be deleted as well (recommended).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Show Edited Mark Up');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Set to &quot;Yes&quot; if you want an edited post be marked up with text showing that the post is edited by a user and when it was edited.');
DEFINE('_EDIT_BY', 'Post edited by:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'An error occured when uploading your Avatar. Please try again or notify your system administrator');
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
DEFINE('_COM_A_IMGB_CONFIRM', 'Are you absolutely sure you want to delete this file? \n Deleting a file, will give a crippled referencing post...');
DEFINE('_COM_A_IMGB_VIEW', 'Open post (to edit)');
DEFINE('_COM_A_IMGB_NO_POST', 'No referencing post!');
DEFINE('_USER_CHANGE_VIEW', 'Changes in these settings will become effective the next time you visit the forums.<br /> If you want to change the view type &quot;Mid-Flight&quot; you can use the options from the forum menu bar.');
DEFINE('_MOSBOT_DISCUSS_A', 'Discuss this article on the forums. (');
DEFINE('_MOSBOT_DISCUSS_B', ' posts)');
DEFINE('_POST_DISCUSS', 'This thread discusses the Content article');
DEFINE('_COM_A_RSS', 'Enable RSS feed');
DEFINE('_COM_A_RSS_DESC', 'The RSS feed enables users to download the latest posts to their desktop/RSS Reader aplication (see <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.');
DEFINE('_LISTCAT_RSS', 'get the latest posts directly to your desktop');
DEFINE('_SEARCH_REDIRECT', 'FireBoard needs to (re)establish your access privileges before it can perform your search request.\nDo not worry, this is quite normal after more than 30 minutes of inactivity.\nPlease submit your search request again.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'FireBoard Configuration');
DEFINE('_COM_A_DISPLAY', 'Display #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Current Setting');
DEFINE('_COM_A_EXPLANATION', 'Explanation');
DEFINE('_COM_A_BOARD_TITLE', 'Board Title');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'The name of your board');
DEFINE('_COM_A_VIEW_TYPE', 'Default View type');
DEFINE('_COM_A_VIEW_TYPE_DESC', 'Choose between a threaded or flat view as default');
DEFINE('_COM_A_THREADS', 'Threads Per Page');
DEFINE('_COM_A_THREADS_DESC', 'Number of threads per page to show');
DEFINE('_COM_A_REGISTERED_ONLY', 'Registered Users Only');
DEFINE('_COM_A_REG_ONLY_DESC', 'Set to &quot;Yes&quot; to allow only registered users to use the Forum (view & post), Set to &quot;No&quot; to allow any visitor to use the Forum');
DEFINE('_COM_A_PUBWRITE', 'Public Read/Write');
DEFINE('_COM_A_PUBWRITE_DESC', 'Set to &quot;Yes&quot; to allow for public write privileges, Set to &quot;No&quot; to allow any visitor to see posts, but only registered users to write posts');
DEFINE('_COM_A_USER_EDIT', 'User Edits');
DEFINE('_COM_A_USER_EDIT_DESC', 'Set to &quot;Yes&quot; to allow registered Users to edit their own posts.');
DEFINE('_COM_A_MESSAGE', 'In order to save changes of the values above, press the &quot;Save&quot; button at the top.');
DEFINE('_COM_A_HISTORY', 'Show History');
DEFINE('_COM_A_HISTORY_DESC', 'Set to &quot;Yes&quot; if you want the topic history shown when a reply/quote is made');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Allow Subscriptions');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to subscribe to a topic and recieve email notifications on new posts');
DEFINE('_COM_A_HISTLIM', 'History Limit');
DEFINE('_COM_A_HISTLIM_DESC', 'Number of posts to show in the history');
DEFINE('_COM_A_FLOOD', 'Flood Protection');
DEFINE('_COM_A_FLOOD_DESC', 'The amount of seconds a user has to wait between two consecutive post. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection <em>can</em> cause degradation of performance..');
DEFINE('_COM_A_MODERATION', 'Email Moderators');
DEFINE('_COM_A_MODERATION_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on new posts sent to the forum moderator(s). Note: although every (super)administrator has automatically all Moderator privileges assign them explicitly as moderators on
 the forum to recieve emails too!');
DEFINE('_COM_A_SHOWMAIL', 'Show Email');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Set to &quot;No&quot; if you never want to display the users email address; not even to registered users.');
DEFINE('_COM_A_AVATAR', 'Allow Avatars');
DEFINE('_COM_A_AVATAR_DESC', 'Set to &quot;Yes&quot; if you want registered users to have an avatar (manageable trough their profile)');
DEFINE('_COM_A_AVHEIGHT', 'Max. Avatar Height');
DEFINE('_COM_A_AVWIDTH', 'Max. Avatar Width');
DEFINE('_COM_A_AVSIZE', 'Max. Avatar Filesize<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_USERSTATS', 'Show User Stats');
DEFINE('_COM_A_USERSTATS_DESC', 'Set to &quot;Yes&quot; to show User Statistics like number of posts user made, User type (Admin, Moderator, User, etc.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Allow Avatar Upload');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to upload an avatar.');
DEFINE('_COM_A_AVATARGALLERY', 'Use Avatars Gallery');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to choose an avatar from a Gallery you provide (components/com_fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Set to &quot;Yes&quot; if you want to show the rank registered users have based on the number of posts they made.<br/><strong>Note that you must enable User Stats in the Advanced tab too to show it.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Use Rank Images');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Set to &quot;Yes&quot; if you want to show the rank registered users have with an image (from components/com_fireboard/ranks). Turning this of will show the text for that rank. Check the documentation on www.bestofjoomla.com for more information on ranking images');

//email and stuff
$_COM_A_NOTIFICATION = "New post notification from ";
$_COM_A_NOTIFICATION1 = "A new post has been made to a topic to which you have subscribed on the ";
$_COM_A_NOTIFICATION2 = "You can administer your subscriptions by following the 'my profile' link on the forum home page after you have logged in on the site. From your profile you can also unsubscribe from the topic.";
$_COM_A_NOTIFICATION3 = "Do not answer to this email notification as it is a generated email.";
$_COM_A_NOT_MOD1 = "A new post has been made to a forum to which you have assigned as moderator on the ";
$_COM_A_NOT_MOD2 = "Please have a look at it after you have logged in on the site.";
DEFINE('_COM_A_NO', 'No');
DEFINE('_COM_A_YES', 'Yes');
DEFINE('_COM_A_FLAT', 'Flat');
DEFINE('_COM_A_THREADED', 'Threaded');
DEFINE('_COM_A_MESSAGES', 'Messages per page');
DEFINE('_COM_A_MESSAGES_DESC', 'Number of messages per page to show');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Username');
DEFINE('_COM_A_USERNAME_DESC', 'Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the users real name');
DEFINE('_COM_A_CHANGENAME', 'Allow Name Change');
DEFINE('_COM_A_CHANGENAME_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to change their name when posting. If set to &quot;No&quot; then registered users will not be able to edit his/her name');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum Offline');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Set to &quot;Yes&quot; if you want to take the Forum section offline. The forum will remain browsable by site (super)admins.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Forum Offline Message');
DEFINE('_COM_A_PRUNE', 'Prune Forums');
DEFINE('_COM_A_PRUNE_NAME', 'Forum to prune:');
DEFINE('_COM_A_PRUNE_DESC',
    'The Prune Forums function allows you to prune threads to which there have not been any new posts for the specified number of days. This does not remove topics with the sticky bit set or which are explicitly locked; these must be removed manually. Threads in locked forums can not be pruned.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Prune threads with no posts for the past ');
DEFINE('_COM_A_PRUNE_DAYS', 'days');
DEFINE('_COM_A_PRUNE_USERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'This function allows you to prune your FireBoard user list against the Joomla! Site user list. It will delete all profiles for FireBoard Users that have been deleted from your Joomla! Framework.<br/>When you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Action');
DEFINE('_GEN_AUTHOR', 'Author');
DEFINE('_GEN_BY', 'by');
DEFINE('_GEN_CANCEL', 'Cancel');
DEFINE('_GEN_CONTINUE', 'Submit');
DEFINE('_GEN_DATE', 'Date');
DEFINE('_GEN_DELETE', 'Delete');
DEFINE('_GEN_EDIT', 'Edit');
DEFINE('_GEN_EMAIL', 'Email');
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
DEFINE('_GEN_MODERATED', 'Forum is moderated; Reviewed prior to publishing.');
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
DEFINE('_UPLOAD_SUBMIT', 'Submit a new Avatar for Upload');
DEFINE('_UPLOAD_SELECT_FILE', 'Select file');
DEFINE('_UPLOAD_ERROR_TYPE', 'Please use only jpeg, gif or png images');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_UPLOAD_ERROR_NAME', 'The image file must contain only alphanumeric characters and no spaces please.');
DEFINE('_UPLOAD_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "You didn't choose an Avatar from the gallery...");
DEFINE('_UPLOAD_UPLOADED', 'Your avatar has been uploaded.');
DEFINE('_UPLOAD_GALLERY', 'Choose one from the Avatar Gallery:');
DEFINE('_UPLOAD_CHOOSE', 'Confirm Choice.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'An administrator should create them first from the ');
DEFINE('_LISTCAT_DO', 'They will know what to do ');
DEFINE('_LISTCAT_INFORM', 'Inform them and tell them to hurry up!');
DEFINE('_LISTCAT_NO_CATS', 'There are no categories in the forum defined yet.');
DEFINE('_LISTCAT_PANEL', 'Administration Panel of the Joomla! OS CMS.');
DEFINE('_LISTCAT_PENDING', 'pending message(s)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'There are no pending messages in this forum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'You are about to delete the message');
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTES:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Consider blanking the post and posters name if only the contents should be removed..
<br/>
- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.');
DEFINE('_POST_CLICK', 'click here');
DEFINE('_POST_ERROR', 'Could not find username/email. Severe database error not listed');
DEFINE('_POST_ERROR_MESSAGE', 'An unknown SQL Error occured and your message was not posted.  If the problem persists, please contact the administrator.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'An error has occured and the message was not updated.  Please try again.  If this error persists please contact the administrator.');
DEFINE('_POST_ERROR_TOPIC', 'An error occured during the delete(s). Please check the error below:');
DEFINE('_POST_FORGOT_NAME', 'You forgot to include your name.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_SUBJECT', 'You forgot to include a subject.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_MESSAGE', 'You forgot to include a message.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_INVALID', 'An invalid post id was requested.');
DEFINE('_POST_LOCK_SET', 'The topic has been locked.');
DEFINE('_POST_LOCK_NOT_SET', 'The topic could not be locked.');
DEFINE('_POST_LOCK_UNSET', 'The topic has been unlocked.');
DEFINE('_POST_LOCK_NOT_UNSET', 'The topic could not be unlocked.');
DEFINE('_POST_MESSAGE', 'Post a new message in ');
DEFINE('_POST_MOVE_TOPIC', 'Move this Topic to forum ');
DEFINE('_POST_NEW', 'Post a new message in: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Your subscription to this topic could not be processed.');
DEFINE('_POST_NOTIFIED', 'Check this box to have yourself notified about replies to this topic.');
DEFINE('_POST_STICKY_SET', 'The sticky bit has been set for this topic.');
DEFINE('_POST_STICKY_NOT_SET', 'The sticky bit could not be set for this topic.');
DEFINE('_POST_STICKY_UNSET', 'The sticky bit has been unset for this topic.');
DEFINE('_POST_STICKY_NOT_UNSET', 'The sticky bit could not be unset for this topic.');
DEFINE('_POST_SUBSCRIBE', 'subscribe');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'You have been subscribed to this topic.');
DEFINE('_POST_SUCCESS', 'Your message has been successfully');
DEFINE('_POST_SUCCES_REVIEW', 'Your message has been successfully posted.  It will be reviewed by a Moderator before it will be published on the forum.');
DEFINE('_POST_SUCCESS_REQUEST', 'Your request has been processed.  If you are not taken back to the topic in a few moments,');
DEFINE('_POST_TOPIC_HISTORY', 'Topic History of');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Max. showing the last');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(Last post first)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Your topic could not be moved. To get back to the topic:');
DEFINE('_POST_TOPIC_FLOOD1', 'The administrator of this forum has enabled Flood Protection and has decided that you must wait ');
DEFINE('_POST_TOPIC_FLOOD2', ' seconds before you can make another post.');
DEFINE('_POST_TOPIC_FLOOD3', 'Please click your browser&#146s back button to get back to the forum.');
DEFINE('_POST_EMAIL_NEVER', 'your email address will never be displayed on the site.');
DEFINE('_POST_EMAIL_REGISTERED', 'your email address will only be available to registered users.');
DEFINE('_POST_LOCKED', 'locked by the administrator.');
DEFINE('_POST_NO_NEW', 'New replies are not allowed.');
DEFINE('_POST_NO_PUBACCESS1', 'The administrator has disabled public write access.');
DEFINE('_POST_NO_PUBACCESS2', 'Only logged in / registered users<br /> are allowed to contribute to the forum.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> There are no topics in this forum yet <<');
DEFINE('_SHOWCAT_PENDING', 'pending message(s)');
// userprofile.php
DEFINE('_USER_DELETE', ' check this box to delete your signature');
DEFINE('_USER_ERROR_A', 'You came to this page in error. Please inform the administrator on which links ');
DEFINE('_USER_ERROR_B', 'you clicked that got you here. She or he can then file a bug report.');
DEFINE('_USER_ERROR_C', 'Thank you!');
DEFINE('_USER_ERROR_D', 'Error number to include in your report: ');
DEFINE('_USER_GENERAL', 'General Profile Options');
DEFINE('_USER_MODERATOR', 'You are assigned as a Moderator to forums');
DEFINE('_USER_MODERATOR_NONE', 'No forums found assigned to you');
DEFINE('_USER_MODERATOR_ADMIN', 'Admins are moderator on all forums.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'No subscriptions found for you');
DEFINE('_USER_PREFERED', 'Prefered Viewtype');
DEFINE('_USER_PROFILE', 'Profile for ');
DEFINE('_USER_PROFILE_NOT_A', 'Your profile could ');
DEFINE('_USER_PROFILE_NOT_B', 'not');
DEFINE('_USER_PROFILE_NOT_C', ' be updated.');
DEFINE('_USER_PROFILE_UPDATED', 'Your profile is updated.');
DEFINE('_USER_RETURN_A', 'If you are not taken back to your profile in a few moments ');
DEFINE('_USER_RETURN_B', 'click here');
DEFINE('_USER_SUBSCRIPTIONS', 'Your Subscriptions');
DEFINE('_USER_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_USER_UNSUBSCRIBE_A', 'You could ');
DEFINE('_USER_UNSUBSCRIBE_B', 'not');
DEFINE('_USER_UNSUBSCRIBE_C', ' be unsubscribed from the topic.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'You have been unsubscribed from the topic.');
DEFINE('_USER_DELETEAV', ' check this box to delete your Avatar');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Preferred Message Ordering');
DEFINE('_USER_ORDER_DESC', 'Last post first');
DEFINE('_USER_ORDER_ASC', 'First post first');
// view.php
DEFINE('_VIEW_DISABLED', 'The administrator has disabled public write access.');
DEFINE('_VIEW_POSTED', 'Posted by');
DEFINE('_VIEW_SUBSCRIBE', ':: Subscribe to this topic ::');
DEFINE('_MODERATION_INVALID_ID', 'An invalid post id was requested.');
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
DEFINE('_PAGE', 'Page: ');
DEFINE('_NO_POSTS', 'No Posts');
DEFINE('_CHARS', 'characters max.');
DEFINE('_HTML_YES', 'HTML is disabled');
DEFINE('_YOUR_AVATAR', '<b>Your Avatar</b>');
DEFINE('_NON_SELECTED', 'Not yet selected <br />');
DEFINE('_SET_NEW_AVATAR', 'Select new Avatar');
DEFINE('_THREAD_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_SHOW_LAST_POSTS', 'Active topics in last');
DEFINE('_SHOW_HOURS', 'hours');
DEFINE('_SHOW_POSTS', 'Total: ');
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
DEFINE('_FORUM_UNAUTHORIZIED', 'This forum is open only to registered and logged in users.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'If you are already registered, please log in first.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderation');
DEFINE('_MOD_APPROVE', 'Approve');
DEFINE('_MOD_DELETE', 'Delete');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Show most recent message');
DEFINE('_POST_WROTE', 'wrote');
DEFINE('_COM_A_EMAIL', 'Board Email Address');
DEFINE('_COM_A_EMAIL_DESC', 'This is the Boards email address. Make this a valid email address');
DEFINE('_COM_A_WRAP', 'Wrap Words Longer Than');
DEFINE('_COM_A_WRAP_DESC',
    'Enter the maximum number of characters a single word may have. This feature allows you to tune the output of FireBoard Posts to your template.<br/> 70 characters is probably the maximum for fixed width templates but you might need to experiment a bit.<br/>URLs, no matter how long, are not affected by the wordwrap');
DEFINE('_COM_A_SIGNATURE', 'Max. Signature Length');
DEFINE('_COM_A_SIGNATURE_DESC', 'Maximum number of characters allowed for the user signature.');
DEFINE('_SHOWCAT_NOPENDING', 'No pending message(s)');
DEFINE('_COM_A_BOARD_OFSET', 'Board Time Offset');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Some boards are located on servers in a different timezone than the users are. Set the Time Offset FireBoard must use in the post time in hours. Positive and negative numbers can be used');
//New in RC2
DEFINE('_COM_A_BASICS', 'Basics');
DEFINE('_COM_A_FRONTEND', 'Frontend');
DEFINE('_COM_A_SECURITY', 'Security');
DEFINE('_COM_A_AVATARS', 'Avatars');
DEFINE('_COM_A_INTEGRATION', 'Integration');
DEFINE('_COM_A_PMS', 'Enable private messaging');
DEFINE('_COM_A_PMS_DESC',
    'Select the appropriate private messaging component if you have installed any. Selecting Clexus PM will also enable ClexusPM user profile related options (like ICQ, AIM, Yahoo, MSN and profile links if supported by the FireBoard template used');
DEFINE('_VIEW_PMS', 'Click here to send a Private Message to this user');
//new in RC3
DEFINE('_POST_RE', 'Re:');
DEFINE('_BBCODE_BOLD', 'Bold text: [b]text[/b] ');
DEFINE('_BBCODE_ITALIC', 'Italic text: [i]text[/i]');
DEFINE('_BBCODE_UNDERL', 'Underline text: [u]text[/u]');
DEFINE('_BBCODE_QUOTE', 'Quote text: [quote]text[/quote]');
DEFINE('_BBCODE_CODE', 'Code display: [code]code[/code]');
DEFINE('_BBCODE_ULIST', 'Unordered List: [ul] [li]text[/li] [/ul] - Hint: a list must contain List Items');
DEFINE('_BBCODE_OLIST', 'Ordered List: [ol] [li]text[/li] [/ol] - Hint: a list must contain List Items');
DEFINE('_BBCODE_IMAGE', 'Image: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]This is a link[/url]');
DEFINE('_BBCODE_CLOSA', 'Close all tags');
DEFINE('_BBCODE_CLOSE', 'Close all open bbCode tags');
DEFINE('_BBCODE_COLOR', 'Color: [color=#FF6600]text[/color]');
DEFINE('_BBCODE_SIZE', 'Size: [size=1]text size[/size] - Hint: sizes range from 1 to 5');
DEFINE('_BBCODE_LITEM', 'List Item: [li] list item [/li]');
DEFINE('_BBCODE_HINT', 'bbCode Help - Hint: bbCode can be used on selected text!');
DEFINE('_COM_A_TAWIDTH', 'Textarea Width');
DEFINE('_COM_A_TAWIDTH_DESC', 'Adjust the width of the reply/post text entry area to match your template. <br/>The Topic Emoticon Toolbar will be wrapped accross two lines if width <= 420 pixels');
DEFINE('_COM_A_TAHEIGHT', 'Textarea Height');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Adjust the height of the reply/post text entry area to match your template');
DEFINE('_COM_A_ASK_EMAIL', 'Require Email');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Require an email address when users or visitors make a post. Set to &quot;No&quot; if you want this feature to be skipped on the frontend. Posters will not be asked for their email address.');

//Rank Administration - Dan Syme/IGD
define('_FB_RANKS_MANAGE', 'Ranks Management');
define('_FB_SORTRANKS', 'Sort by Ranks');

define('_FB_RANKSIMAGE', 'Rank Image');
define('_FB_RANKS', 'Rank Title');
define('_FB_RANKS_SPECIAL', 'Special');
define('_FB_RANKSMIN', 'Minimum Post Count');
define('_FB_RANKS_ACTION', 'Actions');
define('_FB_NEW_RANK', 'New Rank');

?>
