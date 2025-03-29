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

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;

// Kunena 5.2.0: Convert all configuration options to the news ones in K6.0
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 5.2.4
 */
function kunena_5215_2021_07_24_update_configuration($parent) {	
    $db    = Factory::getContainer()->get('DatabaseDriver');
    
    $query = $db->createQuery();   
    
    $query->select('*') 
        ->from($db->quoteName('#__kunena_configuration'))
        ->where($db->quoteName('id') . ' = 1');    
    
    $db->setQuery($query);
    
    try {        
        $config = $db->loadAssoc(); 
    } catch (ExecutionFailureException $e) {
        KunenaError::displayDatabaseError($e);
    }
    
    $oldParams = json_decode($config['params']);
    
    $listConfigParams = ['boardTitle', 'email', 'boardOffline', 'offlineMessage', 'enableRss', 'threadsPerPage', 'messagesPerPage', 'messagesPerPageSearch', 'showHistory', 'historyLimit', 'showNew', 'disableEmoticons', 'template', 'showAnnouncement',
	    'avatarOnCategory', 'catimagepath', 'showChildCatIcon', 'rteWidth', 'rteHeight', 'enableForumJump', 'reportMsg', 'username', 'askEmail', 'showEmail', 'showUserStats', 'showKarma', 'userEdit', 'userEditTime', 'userEditTimeGrace', 'editMarkup', 'allowSubscriptions',
	    'subscriptionsChecked', 'allowFavorites', 'maxsubject', 'maxSig', 'regOnly', 'pubWrite', 'floodProtection', 'mailModerators', 'mailAdministrators', 'captcha', 'mailFull', 'allowAvatarUpload', 'allowAvatarGallery', 'avatarQuality',
	    'avatarSize', 'imageHeight', 'imageWidth', 'imageSize', 'fileTypes', 'fileSize', 'showRanking', 'rankImages', 'userlistRows', 'userlistOnline', 'userlistAvatar', 'userlistPosts', 'userlistKarma', 'userlistEmail', 'userlistJoinDate',
	    'userlistLastVisitDate', 'userlistUserHits', 'latestCategory', 'showStats', 'showWhoIsOnline', 'showGenStats', 'showPopUserStats', 'popUserCount', 'showPopSubjectStats', 'popSubjectCount', 'showSpoilerTag', 'showVideoTag', 'showEbayTag', 'trimLongUrls',
	    'trimLongUrlsFront', 'trimLongUrlsBack', 'autoEmbedYoutube', 'autoEmbedEbay', 'ebayLanguageCode', 'sessionTimeOut', 'highlightCode', 'rssType', 'rssTimeLimit', 'rssLimit', 'rssIncludedCategories', 'rssExcludedCategories', 'rssSpecification',
	    'rssAllowHtml', 'rssAuthorFormat', 'rssAuthorInTitle', 'rssWordCount', 'rssOldTitles', 'rssCache', 'defaultPage', 'defaultSort', 'sef', 'showImgForGuest', 'showFileForGuest', 'pollNbOptions', 'pollAllowVoteOne', 'pollEnabled', 'popPollsCount',
	    'showPopPollStats', 'pollTimeBtVotes', 'pollNbVotesByUser', 'pollResultsUserslist', 'allowUserEditPoll', 'maxPersonalText', 'orderingSystem', 'postDateFormat', 'postDateFormatHover', 'hideIp', 'imageTypes', 'checkMimeTypes', 'imageMimeTypes',
	    'imageQuality', 'thumbHeight', 'thumbWidth', 'hideUserProfileInfo', 'boxGhostMessage', 'userDeleteMessage', 'latestCategoryIn', 'topicIcons', 'debug', 'catsAutoSubscribed', 'showBannedReason', 'showThankYou', 'showPopThankYouStats',
	    'popThanksCount', 'modSeeDeleted', 'bbcodeImgSecure', 'listCatShowModerators', 'lightbox', 'showListTime', 'showSessionType', 'showSessionStartTime', 'userlistAllowed', 'userlistCountUsers', 'enableThreadedLayouts', 'categorySubscriptions',
	    'topicSubscriptions', 'pubProfile', 'thankYouMax', 'emailRecipientCount', 'emailRecipientPrivacy', 'emailVisibleAddress', 'captchaPostLimit', 'imageUpload', 'fileUpload', 'topicLayout', 'timeToCreatePage', 'showImgFilesManageProfile',
	    'holdNewUsersPosts', 'holdGuestPosts', 'attachmentLimit', 'pickupCategory', 'articleDisplay', 'sendEmails', 'fallbackEnglish', 'cache', 'cacheTime', 'ebayAffiliateId', 'ipTracking', 'rssFeedBurnerUrl', 'autoLink', 'accessComponent', 'statsLinkAllowed',
	    'superAdminUserlist', 'legacy_urls', 'attachmentProtection', 'categoryIcons', 'avatarresizemethod', 'avatarCrop', 'userReport', 'searchTime', 'teaser', 'ebayLanguage', 'ebayApiKey', 'ebayCertId', 'XConsumerKey', 'XConsumerSecret',
	    'allowChangeSubject', 'maxLinks', 'readOnly', 'ratingEnabled', 'urlSubjectTopic', 'logModeration', 'attachStart', 'attachEnd', 'googleMapApiKey', 'attachmentUtf8', 'autoEmbedSoundcloud', 'emailHeader', 'userStatus', 'signature', 'personal', 'social',
	    'plainEmail', 'moderatorPermDelete', 'avatarTypes', 'smartLinking', 'defaultAvatar', 'defaultAvatarSmall', 'stopForumSpamKey', 'quickReply', 'avatarEdit', 'activeMenuItem', 'mainMenuId', 'homeId', 'indexId', 'moderatorsId', 'topicListId',
	    'miscId', 'profileId', 'searchId', 'avatarType', 'sefRedirect', 'allowEditPoll', 'useSystemEmails', 'autoEmbedInstagram', 'disableRe', 'email_sender_name', 'display_filename_attachment', 'new_users_prevent_post_url_images',
	    'minimal_user_posts_add_url_image', 'plugins'
	];
	
	$i = 0;
	foreach ($oldParams as $p => $oldParam) {
	    
	    if ($p == 'useredittime') {
	        $p = 'userEdittime';
	    }
	    
	    if ($p == 'useredittimegrace') {
	        $p = 'userEditTimegrace';
	    }
	    
	    if ($p == 'trimlongurlsfront') {
	        $p = 'trimLongUrlsfront';
	    }
	    
	    if ($p == 'trimlongurlsback') {
	        $p = 'trimLongUrlsback';
	    }
	    
	    if ($p == 'latestcategory_in') {
	        $p = 'latestCategory_in';
	    }
	    
	    if ($p == 'defaultavatarsmall') {
	        $p = 'defaultAvatarsmall';
	    }
	    
	    if ($p == 'messages_per_page_search') {
	        $p = 'messagesPerPage_search';
	    }
	    
	    $query = $db->createQuery();
	    $query = "UPDATE `#__kunena_configuration` SET `params` = REPLACE(params, '{$p}', '{$listConfigParams[$i]}');";
	    
	    $db->setQuery($query);
	    $db->execute();
	    
	    $i++;
	}

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_5215_UPDATE_CONFIGURATION'), 'success' => true);
}
