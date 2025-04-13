<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Clean in the configuration the leftovers of previous parameters which has been deleted since
 * 
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2025_02_11_clean_configuration($parent) {
	$config = KunenaFactory::getConfig();
	 
	$listNewConfigParams = ['boardTitle', 'boardOffline', 'offlineMessage', 'enableRss', 'threadsPerPage', 'messagesPerPage', 'messagesPerPageSearch', 'showHistory', 'historyLimit', 'showNew', 'disableEmoticons', 'showAnnouncement',
	    'avatarOnCategory', 'showChildCatIcon', 'rteWidth', 'rteHeight', 'enableForumJump', 'reportMsg', 'askEmail', 'showEmail', 'showUserStats', 'showKarma', 'userEdit', 'userEditTime', 'userEditTimeGrace', 'editMarkup', 'allowSubscriptions',
	    'subscriptionsChecked', 'allowFavorites', 'maxSig', 'regOnly', 'pubWrite', 'floodProtection', 'mailModerators', 'mailAdministrators', 'mailFull', 'allowAvatarUpload', 'allowAvatarGallery', 'avatarQuality', 'userlistJoinDate',
	    'avatarSize', 'imageHeight', 'imageWidth', 'imageSize', 'fileTypes', 'fileSize', 'showRanking', 'rankImages', 'userlistRows', 'userlistOnline', 'userlistAvatar', 'userlistPosts', 'userlistKarma', 'userlistEmail', 'showSpoilerTag',
	    'userlistLastVisitDate', 'userlistUserHits', 'latestCategory', 'showStats', 'showWhoIsOnline', 'showGenStats', 'showPopUserStats', 'popUserCount', 'showPopSubjectStats', 'popSubjectCount', 'showVideoTag', 'showEbayTag', 'trimLongUrls',
	    'trimLongUrlsFront', 'trimLongUrlsBack', 'autoEmbedYoutube', 'autoEmbedEbay', 'ebayLanguageCode', 'sessionTimeOut', 'highlightCode', 'rssType', 'rssTimeLimit', 'rssLimit', 'rssIncludedCategories', 'rssExcludedCategories', 'rssSpecification',
	    'rssAllowHtml', 'rssAuthorFormat', 'rssAuthorInTitle', 'rssWordCount', 'rssOldTitles', 'rssCache', 'defaultPage', 'defaultSort', 'showImgForGuest', 'showFileForGuest', 'pollNbOptions', 'pollAllowVoteOne', 'pollEnabled',
	    'popPollsCount', 'showPopPollStats', 'pollTimeBtVotes', 'pollNbVotesByUser', 'pollResultsUserslist', 'allowUserEditPoll', 'maxPersonalText', 'orderingSystem', 'postDateFormat', 'postDateFormatHover', 'hideIp', 'imageTypes', 'checkMimeTypes',
	    'imageMimeTypes', 'imageQuality', 'thumbHeight', 'thumbWidth', 'hideUserProfileInfo', 'boxGhostMessage', 'userDeleteMessage', 'latestCategoryIn', 'topicIcons', 'catsAutoSubscribed', 'showBannedReason', 'showThankYou', 'showPopThankYouStats',
	    'popThanksCount', 'modSeeDeleted', 'bbcodeImgSecure', 'listCatShowModerators', 'showListTime', 'showSessionType', 'showSessionStartTime', 'userlistAllowed', 'userlistCountUsers', 'enableThreadedLayouts',
	    'categorySubscriptions', 'topicSubscriptions', 'pubProfile', 'thankYouMax', 'emailRecipientCount', 'emailRecipientPrivacy', 'emailVisibleAddress', 'captchaPostLimit', 'imageUpload', 'fileUpload', 'topicLayout', 'timeToCreatePage',
	    'showImgFilesManageProfile', 'holdNewUsersPosts', 'holdGuestPosts', 'attachmentLimit', 'pickupCategory', 'articleDisplay', 'sendEmails', 'fallbackEnglish', 'cacheTime', 'ebayAffiliateId', 'ipTracking', 'rssFeedBurnerUrl',
	    'autoLink', 'accessComponent', 'statsLinkAllowed', 'superAdminUserlist', 'attachmentProtection', 'categoryIcons', 'avatarCrop', 'userReport', 'searchTime', 'ebayLanguage', 'ebayApiKey', 'ebayCertId', 'XConsumerKey',
	    'XConsumerSecret', 'allowChangeSubject', 'maxLinks', 'readOnly', 'ratingEnabled', 'urlSubjectTopic', 'logModeration', 'attachStart', 'attachEnd', 'googleMapApiKey', 'attachmentUtf8',
	    'autoEmbedSoundcloud', 'emailHeader', 'userStatus', 'plainEmail', 'moderatorPermDelete', 'avatarTypes', 'smartLinking', 'defaultAvatar', 'defaultAvatarSmall', 'stopForumSpamKey', 'quickReply', 'avatarEdit',
	    'activeMenuItem', 'mainMenuId', 'homeId', 'indexId', 'moderatorsId', 'topicListId', 'miscId', 'profileId', 'searchId', 'avatarType', 'sefRedirect', 'allowEditPoll', 'useSystemEmails', 'autoEmbedInstagram', 'disableRe'
	];
	
	$listOldConfigParams = ['board_title', 'board_offline', 'offline_message', 'enablerss', 'threads_per_page', 'messages_per_page', 'messages_per_page_search', 'showhistory', 'historylimit', 'shownew', 'disemoticons', 'showannouncement',
	    'avataroncat', 'showchildcaticon' ,'rtewidth', 'rteheight', 'enableforumjump', 'reportmsg', 'askemail', 'showemail', 'showuserstats', 'showkarma', 'useredit', 'useredittime', 'useredittimegrace', 'editmarkup', 'allowsubscriptions',
	    'subscriptionschecked', 'allowfavorites', 'maxsig', 'regonly', 'pubwrite', 'floodprotection', 'mailmod', 'mailadmin', 'mailfull', 'allowavatarupload', 'allowavatargallery', 'avatarquality', 'userlist_joindate',
	    'avatarsize', 'imageheight', 'imagewidth', 'imagesize', 'filetypes', 'filesize', 'showranking', 'rankimages', 'userlist_rows', 'userlist_online', 'userlist_avatar', 'userlist_posts', 'userlist_karma', 'userlist_email', 'showspoilertag',
	    'userlist_lastvisitdate', 'userlist_userhits', 'latestcategory', 'showstats', 'showwhoisonline', 'showgenstats', 'showpopuserstats', 'popusercount', 'showpopsubjectstats', 'popsubjectcount', 'showvideotag','showebaytag', 'trimlongurls',
	    'trimlongurlsfront', 'trimlongurlsback', 'autoembedyoutube', 'autoembedebay', 'ebaylanguagecode', 'sessiontimeout', 'highlightcode', 'rss_type', 'rss_timelimit', 'rss_limit', 'rss_included_categories', 'rss_excluded_categories', 'rss_specification',
	    'rss_allow_html', 'rss_author_format', 'rss_author_in_title', 'rss_word_count', 'rss_old_titles', 'rss_cache', 'defaultpage', 'default_sort', 'showimgforguest', 'showfileforguest', 'pollnboptions', 'pollallowvoteone', 'pollenabled',
	    'poppollscount', 'showpoppollstats', 'polltimebtvotes', 'pollnbvotesbyuser', 'pollresultsuserslist', 'allow_user_edit_poll', 'maxpersotext', 'ordering_system', 'post_dateformat', 'post_dateformat_hover', 'hide_ip', 'imagetypes', 'checkmimetypes',
	    'imagemimetypes', 'imagequality', 'thumbheight', 'thumbwidth', 'hideuserprofileinfo', 'boxghostmessage', 'userdeletetmessage', 'latestcategory_in', 'topicicons', 'catsautosubscribed', 'showbannedreason', 'showthankyou', 'showpopthankyoustats',
	    'popthankscount', 'mod_see_deleted', 'bbcode_img_secure', 'listcat_show_moderators', 'show_list_time', 'show_session_type', 'show_session_starttime', 'userlist_allowed', 'userlist_count_users', 'enable_threaded_layouts',
	    'category_subscriptions', 'topic_subscriptions', 'pubprofile', 'thankyou_max', 'email_recipient_count', 'email_recipient_privacy', 'email_visible_address', 'captcha_post_limit', 'image_upload', 'file_upload', 'topic_layout', 'time_to_create_page',
	    'show_imgfiles_manage_profile', 'hold_newusers_posts', 'hold_guest_posts', 'attachment_limit', 'pickup_category', 'article_display', 'send_emails', 'fallback_english', 'cache_time', 'ebay_affiliate_id', 'iptracking', 'rss_feedburner_url',
	    'autolink', 'access_component', 'statslink_allowed', 'superadmin_userlist', 'attachment_protection', 'categoryicons', 'avatarcrop', 'user_report', 'searchtime', 'ebay_language', 'ebay_api_key', 'ebay_cert_id', 'twitter_consumer_key',
	    'twitter_consumer_secret', 'allow_change_subject', 'max_links', 'read_only', 'ratingenabled', 'url_subject_topic', 'log_moderation', 'attach_start', 'attach_end', 'google_map_api_key' ,'attachment_utf8',
	    'autoembedsoundcloud', 'emailheader', 'user_status', 'plain_email', 'moderator_permdelete', 'avatartypes', 'smartlinking', 'defaultavatar', 'defaultavatarsmall', 'stopforumspam_key', 'quickreply', 'avataredit',
	    'activemenuitem', 'mainmenu_id' ,'home_id', 'index_id', 'moderators_id', 'topiclist_id', 'misc_id', 'profile_id', 'search_id', 'avatar_type', 'sef_redirect', 'allow_edit_poll', 'use_system_emails', 'autoembedinstagram', 'disable_re'
	];
	
	foreach($config as $param => $val) {
	    if ($param != 'id') {
	        $keyFound = array_search($param, $listOldConfigParams);
	        if ($keyFound !== false) {
	            $configParam = $listNewConfigParams[$keyFound];
	            $config->$configParam = $val;
	            unset($config->$param);
	        }
	    }
	}
	
	// Save configuration
	$config->save();	
	
	$listConfigParams = ['boardTitle', 'email', 'boardOffline', 'offlineMessage', 'enableRss', 'threadsPerPage', 'messagesPerPage', 'messagesPerPageSearch', 'showHistory', 'historyLimit', 'showNew', 'disableEmoticons', 'template', 'showAnnouncement',
	    'avatarOnCategory', 'showChildCatIcon', 'rteWidth', 'rteHeight', 'enableForumJump', 'reportMsg', 'username', 'askEmail', 'showEmail', 'showUserStats', 'showKarma', 'userEdit', 'userEditTime', 'userEditTimeGrace', 'editMarkup', 'allowSubscriptions',
	    'subscriptionsChecked', 'allowFavorites', 'maxSig', 'regOnly', 'pubWrite', 'floodProtection', 'mailModerators', 'mailAdministrators', 'captcha', 'mailFull', 'allowAvatarUpload', 'allowAvatarGallery', 'avatarQuality', 'userlistJoinDate',
	    'avatarSize', 'imageHeight', 'imageWidth', 'imageSize', 'fileTypes', 'fileSize', 'showRanking', 'rankImages', 'userlistRows', 'userlistOnline', 'userlistAvatar', 'userlistPosts', 'userlistKarma', 'userlistEmail', 'showSpoilerTag',
	    'userlistLastVisitDate', 'userlistUserHits', 'latestCategory', 'showStats', 'showWhoIsOnline', 'showGenStats', 'showPopUserStats', 'popUserCount', 'showPopSubjectStats', 'popSubjectCount', 'showVideoTag', 'showEbayTag', 'trimLongUrls',
	    'trimLongUrlsFront', 'trimLongUrlsBack', 'autoEmbedYoutube', 'autoEmbedEbay', 'ebayLanguageCode', 'sessionTimeOut', 'highlightCode', 'rssType', 'rssTimeLimit', 'rssLimit', 'rssIncludedCategories', 'rssExcludedCategories', 'rssSpecification',
	    'rssAllowHtml', 'rssAuthorFormat', 'rssAuthorInTitle', 'rssWordCount', 'rssOldTitles', 'rssCache', 'defaultPage', 'defaultSort', 'sef', 'showImgForGuest', 'showFileForGuest', 'pollNbOptions', 'pollAllowVoteOne', 'pollEnabled',
	    'popPollsCount', 'showPopPollStats', 'pollTimeBtVotes', 'pollNbVotesByUser', 'pollResultsUserslist', 'allowUserEditPoll', 'maxPersonalText', 'orderingSystem', 'postDateFormat', 'postDateFormatHover', 'hideIp', 'imageTypes', 'checkMimeTypes',
	    'imageMimeTypes', 'imageQuality', 'thumbHeight', 'thumbWidth', 'hideUserProfileInfo', 'boxGhostMessage', 'userDeleteMessage', 'latestCategoryIn', 'topicIcons', 'debug', 'catsAutoSubscribed', 'showBannedReason', 'showThankYou', 'showPopThankYouStats',
	    'popThanksCount', 'modSeeDeleted', 'bbcodeImgSecure', 'listCatShowModerators', 'lightbox', 'showListTime', 'showSessionType', 'showSessionStartTime', 'userlistAllowed', 'userlistCountUsers', 'enableThreadedLayouts',
	    'categorySubscriptions', 'topicSubscriptions', 'pubProfile', 'thankYouMax', 'emailRecipientCount', 'emailRecipientPrivacy', 'emailVisibleAddress', 'captchaPostLimit', 'imageUpload', 'fileUpload', 'topicLayout', 'timeToCreatePage',
	    'showImgFilesManageProfile', 'holdNewUsersPosts', 'holdGuestPosts', 'attachmentLimit', 'pickupCategory', 'articleDisplay', 'sendEmails', 'fallbackEnglish', 'cache', 'cacheTime', 'ebayAffiliateId', 'ipTracking', 'rssFeedBurnerUrl',
	    'autoLink', 'accessComponent', 'statsLinkAllowed', 'superAdminUserlist', 'attachmentProtection', 'categoryIcons', 'avatarCrop', 'userReport', 'searchTime', 'teaser', 'ebayLanguage', 'ebayApiKey', 'ebayCertId', 'blueskyappHandleOfApp',
	    'blueskyappPasswordOfApp', 'XConsumerKey', 'XConsumerSecret', 'allowChangeSubject', 'maxLinks', 'readOnly', 'ratingEnabled', 'urlSubjectTopic', 'logModeration', 'attachStart', 'attachEnd', 'googleMapApiKey', 'attachmentUtf8',
	    'autoEmbedSoundcloud', 'emailHeader', 'userStatus', 'signature', 'personal', 'plainEmail', 'moderatorPermDelete', 'avatarTypes', 'smartLinking', 'defaultAvatar', 'defaultAvatarSmall', 'stopForumSpamKey', 'quickReply', 'avatarEdit',
	    'activeMenuItem', 'mainMenuId', 'homeId', 'indexId', 'moderatorsId', 'topicListId', 'miscId', 'profileId', 'searchId', 'custom_id', 'avatarType', 'sefRedirect', 'allowEditPoll', 'useSystemEmails', 'autoEmbedInstagram', 'disableRe',
	    'email_sender_name', 'display_filename_attachment', 'new_users_prevent_post_url_images', 'minimal_user_posts_add_url_image', 'utmSource', 'plugins', 'emailHeaderSizeY', 'emailHeaderSizeX', 'profiler', 'privateMessage', 'datePickerFormat',
	    'sendMailUserBanned', 'mailBodyUserBanned', 'mailBodyUserUnBanned'
	];
	
	// We are deleting the remaining config params which have been dropped in previous versions
	foreach($config as $param => $val) {
	    if ($param != 'id') {
	        if (array_search($param, $listConfigParams) === false) {
	            unset($config->$param);
	        }
	    }
	}
	
	// Save configuration
	$config->save();	

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_CLEAN_CONFIGURATION'), 'success' => true);
}
