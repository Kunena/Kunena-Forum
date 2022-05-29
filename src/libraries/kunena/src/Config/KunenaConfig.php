<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 *
 * Based on FireBoard Component
 * @link           http://www.bestofjoomla.com
 **/

namespace Kunena\Forum\Libraries\Config;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Error\KunenaError;

/**
 * Class KunenaConfig
 *
 * @property int     $id
 * @property string  $boardTitle
 * @property string  $email
 * @property boolean $boardOffline
 * @property string  $offlineMessage
 * @property boolean $enableRss
 * @property integer $threadsPerPage
 * @property integer $messagesPerPage
 * @property integer $messagesPerPageSearch
 * @property boolean $showHistory
 * @property integer $historyLimit
 * @property boolean $showNew
 * @property boolean $disableEmoticons
 * @property string  $template
 * @property boolean $showAnnouncement
 * @property boolean $avatarOnCategory
 * @property boolean $showChildCatIcon
 * @property integer $rteWidth
 * @property integer $rteHeight
 * @property boolean $enableForumJump
 * @property boolean $reportMsg
 * @property boolean $username
 * @property boolean $askEmail
 * @property boolean $showEmail
 * @property boolean $showUserStats
 * @property boolean $showKarma
 * @property boolean $userEdit
 * @property integer $userEditTime
 * @property integer $userEditTimeGrace
 * @property boolean $editMarkup
 * @property boolean $allowSubscriptions
 * @property boolean $subscriptionsChecked
 * @property boolean $allowFavorites
 * @property integer $maxSig
 * @property boolean $regOnly
 * @property boolean $pubWrite
 * @property boolean $floodProtection
 * @property boolean $mailModerators
 * @property boolean $mailAdministrators
 * @property boolean $captcha
 * @property boolean $mailFull
 * @property boolean $allowAvatarUpload
 * @property boolean $allowAvatarGallery
 * @property integer $avatarQuality
 * @property integer $avatarSize
 * @property integer $imageHeight
 * @property integer $imageWidth
 * @property integer $imageSize
 * @property string  $fileTypes
 * @property integer $fileSize
 * @property boolean $showRanking
 * @property boolean $rankImages
 * @property integer $userlistRows
 * @property boolean $userlistOnline
 * @property boolean $userlistAvatar
 * @property boolean $userlistPosts
 * @property boolean $userlistKarma
 * @property boolean $userlistEmail
 * @property boolean $userlistJoinDate
 * @property boolean $userlistLastVisitDate
 * @property boolean $userlistUserHits
 * @property boolean $latestCategory
 * @property boolean $showStats
 * @property boolean $showWhoIsOnline
 * @property boolean $showGenStats
 * @property boolean $showPopUserStats
 * @property integer $popUserCount
 * @property boolean $showPopSubjectStats
 * @property boolean $popSubjectCount
 * @property boolean $showSpoilerTag
 * @property boolean $showVideoTag
 * @property boolean $showEbayTag
 * @property boolean $trimLongUrls
 * @property integer $trimLongUrlsFront
 * @property integer $trimLongUrlsBack
 * @property boolean $autoEmbedYoutube
 * @property boolean $autoEmbedEbay
 * @property boolean $ebayLanguageCode
 * @property integer $sessionTimeOut
 * @property boolean $highlightCode
 * @property string  $rssType
 * @property string  $rssTimeLimit
 * @property integer $rssLimit
 * @property string  $rssIncludedCategories
 * @property string  $rssExcludedCategories
 * @property string  $rssSpecification
 * @property boolean $rssAllowHtml
 * @property string  $rssAuthorFormat
 * @property boolean $rssAuthorInTitle
 * @property integer $rssWordCount
 * @property boolean $rssOldTitles
 * @property boolean $rssCache
 * @property string  $defaultPage
 * @property string  $defaultSort
 * @property boolean $sef
 * @property boolean $showImgForGuest
 * @property boolean $showFileForGuest
 * @property integer $pollNbOptions
 * @property boolean $pollAllowVoteOne
 * @property boolean $pollEnabled
 * @property integer $popPollsCount
 * @property boolean $showPopPollStats
 * @property integer $pollTimeBtVotes
 * @property integer $pollNbVotesByUser
 * @property boolean $pollResultsUserslist
 * @property boolean $allowUserEditPoll
 * @property integer $maxPersonalText
 * @property string  $orderingSystem
 * @property string  $postDateFormat
 * @property string  $postDateFormatHover
 * @property boolean $hideIp
 * @property string  $imageTypes
 * @property boolean $checkMimeTypes
 * @property string  $imageMimeTypes
 * @property integer $imageQuality
 * @property integer $thumbHeight
 * @property integer $thumbWidth
 * @property string  $hideUserProfileInfo
 * @property boolean $boxGhostMessage
 * @property integer $userDeleteMessage
 * @property integer $latestCategoryIn
 * @property boolean $topicIcons
 * @property boolean $debug
 * @property boolean $catsAutoSubscribed
 * @property boolean $showBannedReason
 * @property boolean $showThankYou
 * @property boolean $showPopThankYouStats
 * @property integer $popThanksCount
 * @property boolean $modSeeDeleted
 * @property string  $bbcodeImgSecure
 * @property boolean $listCatShowModerators
 * @property boolean $lightbox
 * @property integer $showListTime
 * @property integer $showSessionType
 * @property integer $showSessionStartTime
 * @property boolean $userlistAllowed
 * @property integer $userlistCountUsers
 * @property boolean $enableThreadedLayouts
 * @property string  $categorySubscriptions
 * @property string  $topicSubscriptions
 * @property boolean $pubProfile
 * @property integer $thankYouMax
 * @property integer $emailRecipientCount
 * @property string  $emailRecipientPrivacy
 * @property string  $emailVisibleAddress
 * @property integer $captchaPostLimit
 * @property string  $imageUpload
 * @property string  $fileUpload
 * @property string  $topicLayout
 * @property boolean $timeToCreatePage
 * @property boolean $showImgFilesManageProfile
 * @property boolean $holdNewUsersPosts
 * @property boolean $holdGuestPosts
 * @property integer $attachmentLimit
 * @property boolean $pickupCategory
 * @property string  $articleDisplay
 * @property boolean $sendEmails
 * @property boolean $fallbackEnglish
 * @property boolean $cache
 * @property integer $cacheTime
 * @property integer $ebayAffiliateId
 * @property boolean $ipTracking
 * @property string  $rssFeedBurnerUrl
 * @property boolean $autoLink
 * @property boolean $accessComponent
 * @property boolean $statsLinkAllowed
 * @property boolean $superAdminUserlist
 * @property boolean $attachmentProtection
 * @property boolean $categoryIcons
 * @property boolean $avatarCrop
 * @property boolean $userReport
 * @property integer $searchTime
 * @property boolean $teaser
 * @property boolean $ebayLanguage
 * @property string  $ebayApiKey
 * @property string  $ebayCertId
 * @property string  $twitterConsumerKey
 * @property string  $twitterConsumerSecret
 * @property boolean $allowChangeSubject
 * @property integer $maxLinks
 * @property boolean $readOnly
 * @property boolean $ratingEnabled
 * @property boolean $urlSubjectTopic
 * @property boolean $logModeration
 * @property integer $attachStart
 * @property integer $attachEnd
 * @property string  $googleMapApiKey
 * @property boolean $attachmentUtf8
 * @property boolean $autoEmbedSoundcloud
 * @property string  $emailHeader
 * @property integer $emailHeadersizex
 * @property integer $emailHeadersizey
 * @property boolean $userStatus
 * @property boolean $signature
 * @property boolean $personal
 * @property boolean $social
 * @property boolean $plainEmail
 * @property boolean $moderatorPermDelete
 * @property string  $avatarTypes
 * @property boolean $smartLinking
 * @property string  $defaultAvatar
 * @property string  $defaultAvatarSmall
 * @property string  $stopForumSpamKey
 * @property boolean $quickReply
 * @property boolean $avatarEdit
 * @property string  $activeMenuItem
 * @property integer $mainMenuId
 * @property integer $homeId
 * @property integer $indexId
 * @property integer $moderatorsId
 * @property integer $topicListId
 * @property integer $miscId
 * @property integer $profileId
 * @property integer $searchId
 * @property integer $custom_id
 * @property integer $avatarType
 * @property boolean $sefRedirect
 * @property boolean $allowEditPoll
 * @property boolean $useSystemEmails
 * @property boolean $autoEmbedInstagram
 * @property boolean $disableRe
 * @property boolean $utmSource
 * @property boolean $profiler
 *
 * @since   Kunena 6.0
 */
class KunenaConfig extends CMSObject
{
	/**
	 * @var    integer  ID
	 * @since  Kunena 1.5.2
	 */
	public $id = 0;

	/**
	 * @var    string  Board Title
	 * @since  Kunena 1.0.0
	 */
	public $boardTitle = 'Kunena';

	/**
	 * @var    string  Email
	 * @since  Kunena 1.0.0
	 */
	public $email = '';

	/**
	 * @var    boolean  Board offline
	 * @since  Kunena 1.0.0
	 */
	public $boardOffline = 0;

	/**
	 * @var    string  Offline message
	 * @since  Kunena 1.0.0
	 */
	public $offlineMessage = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>";

	/**
	 * @var    boolean Enable RSS
	 * @since  Kunena 1.0.0
	 */
	public $enableRss = 1;

	/**
	 * @var    integer    Threads per page
	 * @since  Kunena 1.0.0
	 */
	public $threadsPerPage = 20;

	/**
	 * @var    integer  Messages per page
	 * @since  Kunena 1.0.0
	 */
	public $messagesPerPage = 6;

	/**
	 * @var    integer  Messages per page search
	 * @since  Kunena 1.0.0
	 */
	public $messagesPerPageSearch = 15;

	/**
	 * @var    boolean  Show history
	 * @since  Kunena 1.0.0
	 */
	public $showHistory = 1;

	/**
	 * @var    integer  History limit
	 * @since  Kunena 1.0.0
	 */
	public $historyLimit = 6;

	/**
	 * @var    boolean  Show new
	 * @since  Kunena 1.0.0
	 */
	public $showNew = 1;

	/**
	 * @var    boolean  Disable emoticons
	 * @since  Kunena 1.0.0
	 */
	public $disableEmoticons = 0;

	/**
	 * @var    string  Template
	 * @since  Kunena 1.0.0
	 */
	public $template = 'aurelia';

	/**
	 * @var    boolean  Show announcement
	 * @since  Kunena 1.0.0
	 */
	public $showAnnouncement = 1;

	/**
	 * @var    boolean  Avatar on category
	 * @since  Kunena 1.0.0
	 */
	public $avatarOnCategory = 0;

	/**
	 * @var    boolean  Show child category icon
	 * @since  Kunena 1.0.0
	 */
	public $showChildCatIcon = 1;

	/**
	 * @var    integer  Text area width
	 * @since  Kunena 1.0.0
	 */
	public $rteWidth = 450;

	/**
	 * @var    integer  Text area height
	 * @since  Kunena 1.0.0
	 */
	public $rteHeight = 300;

	/**
	 * @var    boolean  Enable forum jump
	 * @since  Kunena 1.0.0
	 */
	public $enableForumJump = 1;

	/**
	 * @var    boolean  Report message
	 * @since  Kunena 1.0.0
	 */
	public $reportMsg = 1;

	/**
	 * @var    boolean  Username
	 * @since  Kunena 1.0.0
	 */
	public $username = 1;

	/**
	 * @var    boolean  Ask email
	 * @since  Kunena 1.0.0
	 */
	public $askEmail = 0;

	/**
	 * @var    boolean  Show email
	 * @since  Kunena 1.0.0
	 */
	public $showEmail = 0;

	/**
	 * @var    boolean  Show user statistics
	 * @since  Kunena 1.0.0
	 */
	public $showUserStats = 1;

	/**
	 * @var    boolean  Show karma
	 * @since  Kunena 1.0.0
	 */
	public $showKarma = 1;

	/**
	 * @var    boolean  User edit
	 * @since  Kunena 1.0.0
	 */
	public $userEdit = 1;

	/**
	 * @var    integer  User edit time
	 * @since  Kunena 1.0.0
	 */
	public $userEditTime = 0;

	/**
	 * @var    integer  User edit time Grace
	 * @since  Kunena 1.0.0
	 */
	public $userEditTimeGrace = 600;

	/**
	 * @var    boolean  Edit markup
	 * @since  Kunena 1.0.0
	 */
	public $editMarkup = 1;

	/**
	 * @var    boolean  Allow subscriptions
	 * @since  Kunena 1.0.0
	 */
	public $allowSubscriptions = 1;

	/**
	 * @var    boolean  Subscriptions Checked
	 * @since  Kunena 1.0.0
	 */
	public $subscriptionsChecked = 1;

	/**
	 * @var    boolean  Allow favorites
	 * @since  Kunena 1.0.0
	 */
	public $allowFavorites = 1;

	/**
	 * @var    integer  Max signature length
	 * @since  Kunena 1.0.0
	 */
	public $maxSig = 300;

	/**
	 * @var    boolean  Registered users only
	 * @since  Kunena 1.0.0
	 */
	public $regOnly = 0;

	/**
	 * @var    boolean  Public write
	 * @since  Kunena 1.0.0
	 */
	public $pubWrite = 0;

	/**
	 * @var    boolean  Flood projection
	 * @since  Kunena 1.0.0
	 */
	public $floodProtection = 0;

	/**
	 * @var    boolean  Mail moderators
	 * @since  Kunena 1.0.0
	 */
	public $mailModerators = 0;

	/**
	 * @var    boolean  Mail admin
	 * @since  Kunena 1.0.0
	 */
	public $mailAdministrators = 0;

	/**
	 * @var    boolean  CAPTCHA
	 * @since  Kunena 1.0.0
	 */
	public $captcha = 0;

	/**
	 * @var    boolean  Mail full
	 * @since  Kunena 1.0.0
	 */
	public $mailFull = 1;

	/**
	 * @var    boolean  Allow avatar upload
	 * @since  Kunena 1.0.0
	 */
	public $allowAvatarUpload = 1;

	/**
	 * @var    boolean  Allow avatar gallery
	 * @since  Kunena 1.0.0
	 */
	public $allowAvatarGallery = 1;

	/**
	 * @var    integer  Avatar quality
	 * @since  Kunena 1.0.0
	 */
	public $avatarQuality = 75;

	/**
	 * @var    integer  Avatar size
	 * @since  Kunena 1.0.0
	 */
	public $avatarSize = 2048;

	/**
	 * @var    integer  Image height
	 * @since  Kunena 1.0.0
	 */
	public $imageHeight = 800;

	/**
	 * @var    integer  Image width
	 * @since  Kunena 1.0.0
	 */
	public $imageWidth = 800;

	/**
	 * @var    integer  Image size
	 * @since  Kunena 1.0.0
	 */
	public $imageSize = 150;

	/**
	 * @var    string  File types
	 * @since  Kunena 1.0.0
	 */
	public $fileTypes = 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2';

	/**
	 * @var    integer  File size
	 * @since  Kunena 1.0.0
	 */
	public $fileSize = 120;

	/**
	 * @var    boolean  Show ranking
	 * @since  Kunena 1.0.0
	 */
	public $showRanking = 1;

	/**
	 * @var    boolean  Rank images
	 * @since  Kunena 1.0.0
	 */
	public $rankImages = 1;

	/**
	 * @var    integer  User list rows
	 * @since  Kunena 1.0.0
	 */
	public $userlistRows = 30;

	/**
	 * @var    boolean  User list online
	 * @since  Kunena 1.0.0
	 */
	public $userlistOnline = 1;

	/**
	 * @var    boolean  user list avatar
	 * @since  Kunena 1.0.0
	 */
	public $userlistAvatar = 1;

	/**
	 * @var    boolean  User list posts
	 * @since  Kunena 1.0.0
	 */
	public $userlistPosts = 1;

	/**
	 * @var    boolean  User list karma
	 * @since  Kunena 1.0.0
	 */
	public $userlistKarma = 1;

	/**
	 * @var    boolean  User list email
	 * @since  Kunena 1.0.0
	 */
	public $userlistEmail = 0;

	/**
	 * @var    boolean  User list join date
	 * @since  Kunena 1.0.0
	 */
	public $userlistJoinDate = 1;

	/**
	 * @var    boolean  User list lst visit date
	 * @since  Kunena 1.0.0
	 */
	public $userlistLastVisitDate = 1;

	/**
	 * @var    boolean  User list user hits
	 * @since  Kunena 1.0.0
	 */
	public $userlistUserHits = 1;

	/**
	 * @var    integer  Latest category; select, integer multiple
	 * @since  Kunena 1.0.0
	 */
	public $latestCategory = 0;

	/**
	 * @var    boolean  Show stats
	 * @since  Kunena 1.0.0
	 */
	public $showStats = 1;

	/**
	 * @var    boolean  Show who is online
	 * @since  Kunena 1.0.0
	 */
	public $showWhoIsOnline = 1;

	/**
	 * @var    boolean  Show general statistics
	 * @since  Kunena 1.0.0
	 */
	public $showGenStats = 1;

	/**
	 * @var    boolean  Show population user statistics
	 * @since  Kunena 1.0.0
	 */
	public $showPopUserStats = 1;

	/**
	 * @var    integer  Population user count
	 * @since  Kunena 1.0.0
	 */
	public $popUserCount = 5;

	/**
	 * @var    boolean  Show population subject statistics
	 * @since  Kunena 1.0.0
	 */
	public $showPopSubjectStats = 1;

	/**
	 * @var    integer  Population subject count
	 * @since  Kunena 1.0.0
	 */
	public $popSubjectCount = 5;

	/**
	 * @var    boolean  Show spoiler tag
	 * @since  Kunena 1.0.5
	 */
	public $showSpoilerTag = 1;

	/**
	 * @var    boolean  Show video tag
	 * @since  Kunena 1.0.5
	 */
	public $showVideoTag = 1;

	/**
	 * @var    boolean  Show ebay tag
	 * @since  Kunena 1.0.5
	 */
	public $showEbayTag = 1;

	/**
	 * @var    boolean  Trim long URLs
	 * @since  Kunena 1.0.5
	 */
	public $trimLongUrls = 1;

	/**
	 * @var    integer  Trim long URLs in front
	 * @since  Kunena 1.0.5
	 */
	public $trimLongUrlsFront = 40;

	/**
	 * @var    integer  Trim long URLs in back
	 * @since  Kunena 1.0.5
	 */
	public $trimLongUrlsBack = 20;

	/**
	 * @var    boolean  Auto embed youtube
	 * @since  Kunena 1.0.5
	 */
	public $autoEmbedYoutube = 1;

	/**
	 * @var    boolean  Auto embed ebay
	 * @since  Kunena 1.0.5
	 */
	public $autoEmbedEbay = 1;

	/**
	 * @var    string  Ebay language code
	 * @since  Kunena 1.0.5
	 */
	public $ebayLanguageCode = 'en-us';

	/**
	 * @var    integer  Session time out. In seconds
	 * @since  Kunena 1.0.5
	 */
	public $sessionTimeOut = 1800;

	/**
	 * @var    boolean  Highlight code
	 * @since  Kunena 1.0.5RC2
	 */
	public $highlightCode = 0;

	/**
	 * @var    string  RSS type
	 * @since  Kunena 1.0.6
	 */
	public $rssType = 'topic';

	/**
	 * @var    string  RSS time limit
	 * @since  Kunena 1.0.6
	 */
	public $rssTimeLimit = 'month';

	/**
	 * @var    integer  RSS limit
	 * @since  Kunena 1.0.6
	 */
	public $rssLimit = 100;

	/**
	 * @var    string  RSS included categories
	 * @since  Kunena 1.0.6
	 */
	public $rssIncludedCategories = '';

	/**
	 * @var    string  RSS excluded categories
	 * @since  Kunena 1.0.6
	 */
	public $rssExcludedCategories = '';

	/**
	 * @var    string  RSS specification
	 * @since  Kunena 1.0.6
	 */
	public $rssSpecification = 'rss2.0';

	/**
	 * @var    boolean  RSS allow HTML
	 * @since  Kunena 1.0.6
	 */
	public $rssAllowHtml = 1;

	/**
	 * @var    string  RSS author format
	 * @since  Kunena 1.0.6
	 */
	public $rssAuthorFormat = 'name';

	/**
	 * @var    boolean  RSS author in title
	 * @since  Kunena 1.0.6
	 */
	public $rssAuthorInTitle = 1;

	/**
	 * @var    integer  RSS word count
	 * @since  Kunena 1.0.6
	 */
	public $rssWordCount = '0';

	/**
	 * @var    boolean  RSS old titles
	 * @since  Kunena 1.0.6
	 */
	public $rssOldTitles = 1;

	/**
	 * @var    integer  RSS cache
	 * @since  Kunena 1.0.6
	 */
	public $rssCache = 900;

	/**
	 * @var    string  Default page
	 * @since  Kunena 1.0.6
	 */
	public $defaultPage = 'recent';

	/**
	 * @var    string  Default sort.  Description for the latest post first
	 * @since  Kunena 1.0.8
	 */
	public $defaultSort = 'asc';

	/**
	 * @var    boolean  Search engine friendly URLs
	 * @since  Kunena 1.5.8
	 */
	public $sef = 1;

	/**
	 * @var    boolean  Showing For Guest
	 * @since  Kunena 1.6.0
	 */
	public $showImgForGuest = 1;

	/**
	 * @var    boolean  Show file for guest
	 * @since  Kunena 1.6.0
	 */
	public $showFileForGuest = 1;

	/**
	 * @var    integer  Major version number
	 * @since  Kunena 1.6.0
	 */
	public $pollNbOptions = 4;

	/**
	 * @var    boolean  Pool allow one ore more time
	 * @since  Kunena 1.6.0
	 */
	public $pollAllowVoteOne = 1;

	/**
	 * @var    boolean  Poll enabled.  For poll integration
	 * @since  Kunena 1.6.0
	 */
	public $pollEnabled = 1;

	/**
	 * @var    integer  Population poll count
	 * @since  Kunena 1.6.0
	 */
	public $popPollsCount = 5;

	/**
	 * @var    boolean  Show population poll statistics
	 * @since  Kunena 1.6.0
	 */
	public $showPopPollStats = 1;

	/**
	 * @var    integer  Poll time by votes
	 * @since  Kunena 1.6.0
	 */
	public $pollTimeBtVotes = '00:15:00';

	/**
	 * @var    integer  Poll and votes by user
	 * @since  Kunena 1.6.0
	 */
	public $pollNbVotesByUser = 100;

	/**
	 * @var    boolean  Poll result user list
	 * @since  Kunena 1.6.0
	 */
	public $pollResultsUserslist = 1;

	/**
	 * @var    boolean  Poll result user list
	 * @since  Kunena 1.6.0
	 */
	public $allowUserEditPoll = 0;

	/**
	 * @var    integer  Max person text
	 * @since  Kunena 1.6.0
	 */
	public $maxPersonalText = 50;

	/**
	 * @var    string  Ordering system
	 * @since  Kunena 1.6.0
	 */
	public $orderingSystem = 'mesid';

	/**
	 * @var    string  Post date format
	 * @since  Kunena 1.6.0
	 */
	public $postDateFormat = 'ago';

	/**
	 * @var    string  Post date format hover
	 * @since  Kunena 1.6.0
	 */
	public $postDateFormatHover = 'datetime';

	/**
	 * @var    boolean  Hide IP
	 * @since  Kunena 1.6.0
	 */
	public $hideIp = 1;

	/**
	 * @var    string  Image types
	 * @since  Kunena 1.6.0
	 */
	public $imageTypes = 'jpg,jpeg,gif,png';

	/**
	 * @var    boolean  Check MIM types
	 * @since  Kunena 1.6.0
	 */
	public $checkMimeTypes = 1;

	/**
	 * @var    string  Image MIME types
	 * @since  Kunena 1.6.0
	 */
	public $imageMimeTypes = 'image/jpeg,image/jpg,image/gif,image/png';

	/**
	 * @var    integer  Image quality
	 * @since  Kunena 1.6.0
	 */
	public $imageQuality = 50;

	/**
	 * @var    integer  Thumbnail height
	 * @since  Kunena 1.6.0
	 */
	public $thumbHeight = 32;

	/**
	 * @var    integer  Thumbnail width
	 * @since  Kunena 1.6.0
	 */
	public $thumbWidth = 32;

	/**
	 * @var    string  Hide user profile info
	 * @since  1.6.0
	 */
	public $hideUserProfileInfo = 'put_empty';

	/**
	 * @var    boolean  Box ghost message
	 * @since  Kunena 1.6.0
	 */
	public $boxGhostMessage = 0;

	/**
	 * @var    integer  User delete message
	 * @since  Kunena 1.6.0
	 */
	public $userDeleteMessage = 0;

	/**
	 * @var    integer  Latest category in
	 * @since  Kunena 1.6.0
	 */
	public $latestCategoryIn = 1;

	/**
	 * @var    boolean  Topic icons
	 * @since  Kunena 1.6.0
	 */
	public $topicIcons = 1;

	/**
	 * @var    boolean  Debug
	 * @since  Kunena 1.6.0
	 */
	public $debug = 0;

	/**
	 * @var    boolean  Category auto subscribe
	 * @since  Kunena 1.6.0
	 */
	public $catsAutoSubscribed = 0;

	/**
	 * @var    boolean  SHow ban reason
	 * @since  Kunena 1.6.0
	 */
	public $showBannedReason = 0;

	/**
	 * @var    boolean  Show thank you
	 * @since  Kunena 1.6.0
	 */
	public $showThankYou = 1;

	/**
	 * @var    boolean  Show population thank you statistics
	 * @since  Kunena 1.6.0
	 */
	public $showPopThankYouStats = 1;

	/**
	 * @var    integer  Population thank you count
	 * @since  Kunena 1.6.0
	 */
	public $popThanksCount = 5;

	/**
	 * @var    boolean  Moderators see deleted topics
	 * @since  Kunena 1.6.0
	 */
	public $modSeeDeleted = 0;

	/**
	 * @var    string  BBCode image secure.  Allow only secure image extensions (jpg/gif/png)
	 * @since  Kunena 1.6.0
	 */
	public $bbcodeImgSecure = 'text';

	/**
	 * @var    boolean  List category show moderators
	 * @since  Kunena 1.6.0
	 */
	public $listCatShowModerators = 1;

	/**
	 * @var    boolean  Major version number
	 * @since  Kunena 1.6.1
	 */
	public $lightbox = 1;

	/**
	 * @var    integer  Show list time
	 * @since  Kunena 1.6.1
	 */
	public $showListTime = 720;

	/**
	 * @var    integer  Show session type
	 * @since  Kunena 1.6.1
	 */
	public $showSessionType = 2;

	/**
	 * @var    integer  Show session start time
	 * @since  Kunena 1.6.1
	 */
	public $showSessionStartTime = 1800;

	/**
	 * @var    boolean  User list allowed
	 * @since  Kunena 1.6.2
	 */
	public $userlistAllowed = 1;

	/**
	 * @var    integer  User list count users
	 * @since  Kunena 1.6.4
	 */
	public $userlistCountUsers = 1;

	/**
	 * @var    boolean  Enable threaded layouts
	 * @since  Kunena 1.6.4
	 */
	public $enableThreadedLayouts = 0;

	/**
	 * @var    string  Category subscriptions
	 * @since  Kunena 1.6.4
	 */
	public $categorySubscriptions = 'post';

	/**
	 * @var    string  Topic subscriptions
	 * @since  Kunena 1.6.4
	 */
	public $topicSubscriptions = 'every';

	/**
	 * @var    boolean  Public profile
	 * @since  Kunena 1.6.4
	 */
	public $pubProfile = 1;

	/**
	 * @var    integer  Thank you max
	 * @since  Kunena 1.6.5
	 */
	public $thankYouMax = 10;

	/**
	 * @var    integer  Email recipient count
	 * @since  Kunena 1.6.6
	 */
	public $emailRecipientCount = 0;

	/**
	 * @var    string  Email recipient pricing
	 * @since  Kunena 1.6.6
	 */
	public $emailRecipientPrivacy = 'bcc';

	/**
	 * @var    string  Email visible address
	 * @since  Kunena 1.6.6
	 */
	public $emailVisibleAddress = '';

	/**
	 * @var    integer  CAPTCHA post limit
	 * @since  Kunena 1.6.6
	 */
	public $captchaPostLimit = 0;

	/**
	 * @var    string  Image upload
	 * @since  Kunena 2.0.0
	 */
	public $imageUpload = 'registered';

	/**
	 * @var    string  File upload
	 * @since  Kunena 2.0.0
	 */
	public $fileUpload = 'registered';

	/**
	 * @var    string  Topic layout
	 * @since  Kunena 2.0.0
	 */
	public $topicLayout = 'flat';

	/**
	 * @var    boolean  Time to create page
	 * @since  Kunena 2.0.0
	 */
	public $timeToCreatePage = 1;

	/**
	 * @var    boolean  Show image files in mange profile
	 * @since  Kunena 2.0.0
	 */
	public $showImgFilesManageProfile = 1;

	/**
	 * @var    boolean  Hold new users posts
	 * @since  Kunena 2.0.0
	 */
	public $holdNewUsersPosts = 0;

	/**
	 * @var    boolean  Hold guest posts
	 * @since  Kunena 2.0.0
	 */
	public $holdGuestPosts = 0;

	/**
	 * @var    integer  Attachment limit
	 * @since  Kunena 2.0.0
	 */
	public $attachmentLimit = 8;

	/**
	 * @var    boolean  Pickup category
	 * @since  Kunena 2.0.0
	 */
	public $pickupCategory = 0;

	/**
	 * @var    string  Article display
	 * @since  Kunena 2.0.0
	 */
	public $articleDisplay = 'intro';

	/**
	 * @var    boolean  Send emails
	 * @since  Kunena 2.0.0
	 */
	public $sendEmails = 1;

	/**
	 * @var    boolean  Fallback english
	 * @since  Kunena 2.0.0
	 */
	public $fallbackEnglish = 1;

	/**
	 * @var    boolean  Cache
	 * @since  Kunena 2.0.0
	 */
	public $cache = 1;

	/**
	 * @var    integer  Cache time
	 * @since  Kunena 2.0.0
	 */
	public $cacheTime = 60;

	/**
	 * @var    integer  Ebay affiliate ID
	 * @since  Kunena 2.0.0
	 */
	public $ebayAffiliateId = 5337089937;

	/**
	 * @var    boolean  IP tracking
	 * @since  Kunena 2.0.0
	 */
	public $ipTracking = 1;

	/**
	 * @var    string  RSS feedburner URL
	 * @since  Kunena 2.0.3
	 */
	public $rssFeedBurnerUrl = '';

	/**
	 * @var    boolean  Auto link
	 * @since  Kunena 3.0.0
	 */
	public $autoLink = 1;

	/**
	 * @var    boolean  Access component
	 * @since  Kunena 3.0.0
	 */
	public $accessComponent = 1;

	/**
	 * @var    boolean  Statistic link allowed
	 * @since  Kunena 3.0.4
	 */
	public $statsLinkAllowed = 1;

	/**
	 * @var    boolean  Super admin user list
	 * @since  Kunena 3.0.6
	 */
	public $superAdminUserlist = 0;

	/**
	 * @var     boolean  Attachment protection
	 * @since   Kunena 4.0.0
	 */
	public $attachmentProtection = 0;

	/**
	 * @var     boolean  Category icons
	 * @since   Kunena 4.0.0
	 */
	public $categoryIcons = 1;

	/**
	 * @var     boolean  Avatar crop
	 * @since   Kunena 4.0.0
	 */
	public $avatarCrop = 0;

	/**
	 * @var     boolean  User can report himself
	 * @since   Kunena 4.0.0
	 */
	public $userReport = 1;

	/**
	 * @var     integer  Search time
	 * @since   Kunena 4.0.0
	 */
	public $searchTime = 365;

	/**
	 * @var     boolean  Teaser
	 * @since   Kunena 4.0.0
	 */
	public $teaser = 0;

	/**
	 * @var    boolean  Define ebay widget language
	 * @since  Kunena 3.0.7
	 */
	public $ebayLanguage = 0;

	/**
	 * @var    string  Define ebay Api key to be allowed to display ebay widget
	 * @since  Kunena 3.0.7
	 */
	public $ebayApiKey = '';

	/**
	 * @var    string  Define ebay cert Id key to be allowed to display ebay widget; select, boolean
	 * @since  5.2.0
	 */
	public $ebayCertId = '';

	/**
	 * @var     string  Define twitter API consumer key
	 * @since   Kunena 4.0.0
	 */
	public $twitterConsumerKey = '';

	/**
	 * @var     string  Define twitter API consumer secret
	 * @since   Kunena 4.0.0
	 */
	public $twitterConsumerSecret = '';

	/**
	 * @var     boolean  Allow to define if the user can change the subject of topic on replies
	 * @since   Kunena 4.0.0
	 */
	public $allowChangeSubject = 1;

	/**
	 * @var     integer  Max Links limit
	 * @since   Kunena 4.0.0
	 */
	public $maxLinks = 6;

	/**
	 * @var    boolean  Read Only State
	 * @since  Kunena 5.0.0
	 */
	public $readOnly = 0;

	/**
	 * @var    boolean  Rating integration
	 * @since  Kunena 5.0.0
	 */
	public $ratingEnabled = 0;

	/**
	 * @var    boolean  Allow to prevent posting if the subject of topic contains URL
	 * @since  Kunena 5.0.0
	 */
	public $urlSubjectTopic = 0;

	/**
	 * @var    boolean Allow to enable log to save moderation actions
	 * @since  Kunena 5.0.0
	 */
	public $logModeration = 0;

	/**
	 * @var    integer Define the number of characters from start when shorter then attachments filename
	 * @since  Kunena 5.0.0
	 */
	public $attachStart = 0;

	/**
	 * @var    integer Define the number of characters from end when shorten then attachments filename
	 * @since  Kunena 5.0.0
	 */
	public $attachEnd = 14;

	/**
	 * @var    string Define the google maps API key
	 * @since  Kunena 5.0.0
	 */
	public $googleMapApiKey = '';

	/**
	 * @var    boolean Allow to remove utf8 characters from filename of attachments
	 * @since  Kunena 5.0.0
	 */
	public $attachmentUtf8 = 1;

	/**
	 * @var    boolean Allow to auto-embded soundcloud item when you put just the URL in a message
	 * @since  Kunena 5.0.0
	 */
	public $autoEmbedSoundcloud = 1;

	/**
	 * @var    string to define the image location
	 * @since  Kunena 5.0.2
	 */
	public $emailHeader = 'media/kunena/email/hero-wide.png';

	/**
	 * @var    boolean show user status
	 * @since  Kunena 5.0.3
	 */
	public $userStatus = 1;

	/**
	 * @var    boolean Allow user signatures
	 * @since  Kunena 5.1.0
	 */
	public $signature = 1;

	/**
	 * @var    boolean Allow user personal
	 * @since  Kunena 5.1.0
	 */
	public $personal = 1;

	/**
	 * @var    boolean Allow user social
	 * @since  Kunena 5.1.0
	 */
	public $social = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.0.4
	 */
	public $plainEmail = 0;

	/**
	 * @var    boolean
	 * @since  Kunena 5.0.13
	 */
	public $moderatorPermDelete = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.0.4
	 */
	public $avatarTypes = 'gif, jpeg, jpg, png';

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $smartLinking = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $defaultAvatar = 'nophoto.png';

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $defaultAvatarSmall = 's_nophoto.png';

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $stopForumSpamKey = '';

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $quickReply = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $avatarEdit = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $activeMenuItem = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $mainMenuId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $homeId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $indexId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $moderatorsId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $topicListId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $miscId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $profileId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $searchId = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $custom_id = '';

	/**
	 * @var   integer
	 * @since  Kunena 5.1.0
	 */
	public $avatarType = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.1
	 */
	public $sefRedirect = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.1
	 */
	public $allowEditPoll = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.2
	 */
	public $useSystemEmails = 0;

	/**
	 * @var    boolean  Auto embed instagram
	 * @since  Kunena 5.1.5
	 */
	public $autoEmbedInstagram = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.14
	 */
	public $disableRe = 0;

	/**
	 * @var string
	 * @since  K5.1.18
	 */
	public $email_sender_name = '';

	/**
	 * @var integer
	 * @since  K5.1.19
	 */
	public $display_filename_attachment = 0;

	/**
	 * @var integer
	 * @since  K5.2.0
	 */
	public $new_users_prevent_post_url_images = 0;

	/**
	 * @var integer
	 * @since  K5.2.0
	 */
	public $minimal_user_posts_add_url_image = 10;

	/**
	 * @var    boolean  utm source
	 * @since  Kunena 1.0.5
	 */
	public $utmSource = 0;

	/**
	 * @var    Registry
	 * @since  Kunena 6.0
	 */
	public $plugins;

	/**
	 * @var    string to define the header image size
	 * @since  Kunena 6.0
	 */
	public $emailHeadersizey = 560;

	/**
	 * @var    string to define the header image size
	 * @since  Kunena 6.0
	 */
	public $emailHeadersizex = 560;

	public $moderator_id;

	/**
	 * @var    boolean  enabling profiler into Kunena
	 * @since  Kunena 6.0.0
	 */
	public $profiler = 0;

	public $pickup_category;

	/**
	 * @since   Kunena 6.0
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return  KunenaConfig|mixed
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance(): ?KunenaConfig
	{
		static $instance = null;

		if (!$instance)
		{
			$cache    = Factory::getCache('com_kunena', 'output');
			$instance = $cache->get('configuration', 'com_kunena');

			if (!$instance)
			{
				$instance = new KunenaConfig;
				$instance->load();
			}

			$cache->store($instance, 'configuration', 'com_kunena');
		}

		return $instance;
	}

	/**
	 * Load config settings from database table.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function load(): void
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_configuration'))
			->where($db->quoteName('id') . ' = 1');
		$db->setQuery($query);

		try
		{
			$config = $db->loadAssoc();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		if ($config)
		{
			$params = json_decode($config['params']);
			$this->bind($params);
		}

		// Perform custom validation of config data before we let anybody access it.
		$this->check();

		PluginHelper::importPlugin('kunena');
		$plugins = [];
		Factory::getApplication()->triggerEvent('onKunenaGetConfiguration', ['kunena.configuration', &$plugins]);
		$this->plugins = [];

		foreach ($plugins as $name => $registry)
		{
			if ($name == '38432UR24T5bBO6')
			{
				$this->bind($registry->toArray());
			}
			elseif ($name && $registry instanceof Registry)
			{
				$this->plugins[$name] = $registry;
			}
		}
	}

	/**
	 * @param   mixed  $properties  properties
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function bind($properties): void
	{
		$this->setProperties($properties);
	}

	/**
	 * Messages per page
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): void
	{
		// Add anything that requires validation

		// Need to have at least one per page of these
		$this->messagesPerPage       = max($this->messagesPerPage, 1);
		$this->messagesPerPageSearch = max($this->messagesPerPageSearch, 1);
		$this->threadsPerPage        = max($this->threadsPerPage, 1);
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function save(): void
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Perform custom validation of config data before we write it.
		$this->check();

		// Get current configuration
		$params = $this->getProperties();
		unset($params['id']);

		$db->setQuery("REPLACE INTO #__kunena_configuration SET id=1, params={$db->quote(json_encode($params))}");

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// Clear cache.
		KunenaCacheHelper::clear();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function reset(): void
	{
		$instance = new KunenaConfig;
		$this->bind($instance->getProperties());
	}

	/**
	 * @param   string  $name  Name of the plugin
	 *
	 * @return  Registry
	 *
	 * @internal
	 *
	 * @since   Kunena 6.0
	 */
	public function getPlugin(string $name): Registry
	{
		return isset($this->plugins[$name]) ? $this->plugins[$name] : new Registry;
	}

	/**
	 * Email set for the configuration
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getEmail(): string
	{
		$email = $this->get('email');

		return !empty($email) ? $email : Factory::getApplication()->get('mailfrom', '');
	}
}
