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
	 
	$listConfigParams = ['boardTitle', 'email', 'boardOffline', 'offlineMessage', 'enableRss', 'threadsPerPage', 'messagesPerPage', 'messagesPerPageSearch', 'showHistory', 'historyLimit', 'showNew', 'disableEmoticons', 'template', 'showAnnouncement', 
	    'avatarOnCategory', 'showChildCatIcon', 'rteWidth', 'rteHeight', 'enableForumJump', 'reportMsg', 'username', 'askEmail', 'showEmail', 'showUserStats', 'showKarma', 'userEdit', 'userEditTime', 'userEditTimeGrace', 'editMarkup', 'allowSubscriptions',
	    'subscriptionsChecked', 'allowFavorites', 'maxSig', 'regOnly', 'pubWrite', 'floodProtection', 'mailModerators', 'mailAdministrators', 'captcha', 'mailFull', 'allowAvatarUpload', 'allowAvatarGallery', 'avatarQuality', 'userlistJoinDate',
	    'avatarSize', 'imageHeight', 'imageWidth', 'imageSize', 'fileTypes', 'fileSize', 'showRanking', 'rankImages', 'userlistRows', 'userlistOnline', 'userlistAvatar', 'userlistPosts', 'userlistKarma', 'userlistEmail', 'showSpoilerTag',
	    'userlistLastVisitDate', 'userlistUserHits', 'latestCategory', 'showStats', 'showWhoIsOnline', 'showGenStats', 'showPopUserStats', 'popUserCount', 'showPopSubjectStats', 'popSubjectCount', 'showVideoTag', 'showEbayTag', 'trimLongUrls',
	    'trimLongUrlsFront', 'trimLongUrlsBack', 'autoEmbedYoutube', 'autoEmbedEbay', 'ebayLanguageCode', 'sessionTimeOut', 'highlightCode', 'rssType', 'rssTimeLimit', 'rssLimit', 'rssIncludedCategories', 'rssExcludedCategories', 'rssSpecification',
	    'rssAllowHtml', 'rssAuthorFormat', 'rssAuthorInTitle', 'rssWordCount', 'rssOldTitles', 'rssCache', 'defaultPage', 'defaultSort', 'sef', 'showImgForGuest', 'showFileForGuest', 'pollNbOptions', 'pollAllowVoteOne', 'pollEnabled', 'popPollsCount',
	    'showPopPollStats', 'pollTimeBtVotes', 'pollNbVotesByUser', 'pollResultsUserslist', 'allowUserEditPoll', 'maxPersonalText', 'orderingSystem', 'postDateFormat', 'postDateFormatHover', 'hideIp', 'imageTypes', 'checkMimeTypes', 'imageMimeTypes',
	    'imageQuality', 'thumbHeight', 'thumbWidth', 'hideUserProfileInfo', 'boxGhostMessage', 'userDeleteMessage', 'latestCategoryIn', 'topicIcons', 'debug', 'catsAutoSubscribed', 'showBannedReason', 'showThankYou', 'showPopThankYouStats',
	    'popThanksCount', 'modSeeDeleted', 'bbcodeImgSecure', 'listCatShowModerators', 'lightbox', 'showListTime', 'showSessionType', 'showSessionStartTime', 'userlistAllowed', 'userlistCountUsers', 'enableThreadedLayouts', 'categorySubscriptions',
	    'topicSubscriptions', 'pubProfile', 'thankYouMax', 'emailRecipientCount', 'emailRecipientPrivacy', 'emailVisibleAddress', 'captchaPostLimit', 'imageUpload', 'fileUpload', 'topicLayout', 'timeToCreatePage', 'showImgFilesManageProfile',
	    'holdNewUsersPosts', 'holdGuestPosts', 'attachmentLimit', 'pickupCategory', 'articleDisplay', 'sendEmails', 'fallbackEnglish', 'cache', 'cacheTime', 'ebayAffiliateId', 'ipTracking', 'rssFeedBurnerUrl', 'autoLink', 'accessComponent', 'statsLinkAllowed',
	    'superAdminUserlist', 'attachmentProtection', 'categoryIcons', 'avatarCrop', 'userReport', 'searchTime', 'teaser', 'ebayLanguage', 'ebayApiKey', 'ebayCertId', 'blueskyappHandleOfApp', 'blueskyappPasswordOfApp', 'XConsumerKey', 'XConsumerSecret',
	    'allowChangeSubject', 'maxLinks', 'readOnly', 'ratingEnabled', 'urlSubjectTopic', 'logModeration', 'attachStart', 'attachEnd', 'googleMapApiKey', 'attachmentUtf8', 'autoEmbedSoundcloud', 'emailHeader', 'userStatus', 'signature', 'personal',
	    'plainEmail', 'moderatorPermDelete', 'avatarTypes', 'smartLinking', 'defaultAvatar', 'defaultAvatarSmall', 'stopForumSpamKey', 'quickReply', 'avatarEdit', 'activeMenuItem', 'mainMenuId', 'homeId', 'indexId', 'moderatorsId', 'topicListId',
	    'miscId', 'profileId', 'searchId', 'custom_id', 'avatarType', 'sefRedirect', 'allowEditPoll', 'useSystemEmails', 'autoEmbedInstagram', 'disableRe', 'email_sender_name', 'display_filename_attachment', 'new_users_prevent_post_url_images',
	    'minimal_user_posts_add_url_image', 'utmSource', 'plugins', 'emailHeaderSizeY', 'emailHeaderSizeX', 'profiler', 'privateMessage', 'datePickerFormat', 'sendMailUserBanned', 'mailBodyUserBanned', 'mailBodyUserUnBanned'
    ];
	
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
