<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Date\KunenaDate;

/**
 * Config Model for Kunena
 *
 * @since 2.0
 */
class ConfigModel extends AdminModel
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
	 * @var    string  Latest category
	 * @since  Kunena 1.0.0
	 */
	public $latestCategory = '';

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
	 * @var    boolean Allow to auto-embedded soundcloud item when you put just the URL in a message
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
	 * @var    boolean  utm source
	 * @since  Kunena 1.0.5
	 */
	public $utmSource = 0;

	/**
	 * @var    boolean  profiler
	 * @since  Kunena 6.0.0
	 */
	public $profiler = 0;

	/**
	 * @inheritDoc
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getConfigLists(): array
	{
		$lists  = [];
		$config = KunenaConfig::getInstance();

		// RSS
		{
			// Options to be used later
			$rssYesNo    = [];
			$rssYesNo [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_NO'));
			$rssYesNo [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_YES'));

			// ------

			$rssType    = [];
			$rssType [] = HTMLHelper::_('select.option', 'post', Text::_('COM_KUNENA_A_RSS_TYPE_POST'));
			$rssType [] = HTMLHelper::_('select.option', 'topic', Text::_('COM_KUNENA_A_RSS_TYPE_TOPIC'));
			$rssType [] = HTMLHelper::_('select.option', 'recent', Text::_('COM_KUNENA_A_RSS_TYPE_RECENT'));

			// Build the html select list
			$lists ['rssType'] = HTMLHelper::_('select.genericlist', $rssType, 'cfg_rssType', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssType);

			// ------

			$rssTimeLimit    = [];
			$rssTimeLimit [] = HTMLHelper::_('select.option', 'week', Text::_('COM_KUNENA_A_RSS_TIMELIMIT_WEEK'));
			$rssTimeLimit [] = HTMLHelper::_('select.option', 'month', Text::_('COM_KUNENA_A_RSS_TIMELIMIT_MONTH'));
			$rssTimeLimit [] = HTMLHelper::_('select.option', 'year', Text::_('COM_KUNENA_A_RSS_TIMELIMIT_YEAR'));

			// Build the html select list
			$lists ['rssTimeLimit'] = HTMLHelper::_('select.genericlist', $rssTimeLimit, 'cfg_rssTimeLimit', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssTimeLimit);

			// ------

			$rssSpecification = [];

			$rssSpecification [] = HTMLHelper::_('select.option', 'rss0.91', 'RSS 0.91');
			$rssSpecification [] = HTMLHelper::_('select.option', 'rss1.0', 'RSS 1.0');
			$rssSpecification [] = HTMLHelper::_('select.option', 'rss2.0', 'RSS 2.0');
			$rssSpecification [] = HTMLHelper::_('select.option', 'atom1.0', 'Atom 1.0');

			// Build the html select list
			$lists ['rssSpecification'] = HTMLHelper::_('select.genericlist', $rssSpecification, 'cfg_rssSpecification', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssSpecification);

			// ------

			$rssAuthorFormat    = [];
			$rssAuthorFormat [] = HTMLHelper::_('select.option', 'name', Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_NAME'));
			$rssAuthorFormat [] = HTMLHelper::_('select.option', 'email', Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_EMAIL'));
			$rssAuthorFormat [] = HTMLHelper::_('select.option', 'both', Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_BOTH'));

			// Build the html select list
			$lists ['rssAuthorFormat'] = HTMLHelper::_('select.genericlist', $rssAuthorFormat, 'cfg_rssAuthorFormat', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssAuthorFormat);

			// ------

			// Build the html select list
			$lists ['rssAuthorInTitle'] = HTMLHelper::_('select.genericlist', $rssYesNo, 'cfg_rssAuthorInTitle', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssAuthorInTitle);

			// ------

			$rssWordCount    = [];
			$rssWordCount [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_RSS_WORD_COUNT_ALL'));
			$rssWordCount [] = HTMLHelper::_('select.option', '-1', Text::_('JNONE'));
			$rssWordCount [] = HTMLHelper::_('select.option', '50', '50');
			$rssWordCount [] = HTMLHelper::_('select.option', '100', '100');
			$rssWordCount [] = HTMLHelper::_('select.option', '250', '250');
			$rssWordCount [] = HTMLHelper::_('select.option', '500', '500');
			$rssWordCount [] = HTMLHelper::_('select.option', '750', '750');
			$rssWordCount [] = HTMLHelper::_('select.option', '1000', '1000');

			// Build the html select list
			$lists ['rssWordCount'] = HTMLHelper::_('select.genericlist', $rssWordCount, 'cfg_rssWordCount', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssWordCount);

			// ------

			// Build the html select list
			$lists ['rssAllowHtml'] = HTMLHelper::_('select.genericlist', $rssYesNo, 'cfg_rssAllowHtml', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssAllowHtml);

			// ------

			// Build the html select list
			$lists ['rssOldTitles'] = HTMLHelper::_('select.genericlist', $rssYesNo, 'cfg_rssOldTitles', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssOldTitles);

			// ------

			$rssCache = [];

			$rssCache [] = HTMLHelper::_('select.option', '0', '0');        // Disable
			$rssCache [] = HTMLHelper::_('select.option', '60', '1');
			$rssCache [] = HTMLHelper::_('select.option', '300', '5');
			$rssCache [] = HTMLHelper::_('select.option', '900', '15');
			$rssCache [] = HTMLHelper::_('select.option', '1800', '30');
			$rssCache [] = HTMLHelper::_('select.option', '3600', '60');

			$lists ['rssCache'] = HTMLHelper::_('select.genericlist', $rssCache, 'cfg_rssCache', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rssCache);

			// ------

			// Build the html select list - (moved enableRss here, to keep all rss-related features together)
			$lists ['enableRss'] = HTMLHelper::_('select.genericlist', $rssYesNo, 'cfg_enableRss', 'class="inputbox form-control"size="1"', 'value', 'text', $config->enableRss);
		}

		// Build the html select list
		// make a standard yes/no list
		$yesno    = [];
		$yesno [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_NO'));
		$yesno [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_YES'));

		$lists ['disableEmoticons']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_disableEmoticons', 'class="inputbox form-control"size="1"', 'value', 'text', $config->disableEmoticons);
		$lists ['regOnly']               = HTMLHelper::_('select.genericlist', $yesno, 'cfg_regOnly', 'class="inputbox form-control"size="1"', 'value', 'text', $config->regOnly);
		$lists ['boardOffline']          = HTMLHelper::_('select.genericlist', $yesno, 'cfg_boardOffline', 'class="inputbox form-control"size="1"', 'value', 'text', $config->boardOffline);
		$lists ['pubWrite']              = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pubWrite', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pubWrite);
		$lists ['showHistory']           = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showHistory', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showHistory);
		$lists ['showAnnouncement']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showAnnouncement', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showAnnouncement);
		$lists ['avatarOnCategory']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_avatarOnCategory', 'class="inputbox form-control"size="1"', 'value', 'text', $config->avatarOnCategory);
		$lists ['showChildCatIcon']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showChildCatIcon', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showChildCatIcon);
		$lists ['showUserStats']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showUserStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showUserStats);
		$lists ['showWhoIsOnline']       = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showWhoIsOnline', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showWhoIsOnline);
		$lists ['showPopSubjectStats']   = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showPopSubjectStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showPopSubjectStats);
		$lists ['showGenStats']          = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showGenStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showGenStats);
		$lists ['showPopUserStats']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showPopUserStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showPopUserStats);
		$lists ['allowSubscriptions']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowSubscriptions', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowSubscriptions);
		$lists ['subscriptionsChecked']  = HTMLHelper::_('select.genericlist', $yesno, 'cfg_subscriptionsChecked', 'class="inputbox form-control"size="1"', 'value', 'text', $config->subscriptionsChecked);
		$lists ['allowFavorites']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowFavorites', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowFavorites);
		$lists ['showEmail']             = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showEmail', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showEmail);
		$lists ['askEmail']              = HTMLHelper::_('select.genericlist', $yesno, 'cfg_askEmail', 'class="inputbox form-control"size="1"', 'value', 'text', $config->askEmail);
		$lists ['allowAvatarUpload']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowAvatarUpload', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowAvatarUpload);
		$lists ['allowAvatarGallery']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowAvatarGallery', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowAvatarGallery);
		$lists ['showStats']             = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showStats);
		$lists ['showRanking']           = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showRanking', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showRanking);
		$lists ['username']              = HTMLHelper::_('select.genericlist', $yesno, 'cfg_username', 'class="inputbox form-control"size="1"', 'value', 'text', $config->username);
		$lists ['showNew']               = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showNew', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showNew);
		$lists ['editMarkup']            = HTMLHelper::_('select.genericlist', $yesno, 'cfg_editMarkup', 'class="inputbox form-control"size="1"', 'value', 'text', $config->editMarkup);
		$lists ['showKarma']             = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showKarma', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showKarma);
		$lists ['enableForumJump']       = HTMLHelper::_('select.genericlist', $yesno, 'cfg_enableForumJump', 'class="inputbox form-control"size="1"', 'value', 'text', $config->enableForumJump);
		$lists ['userlistOnline']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistOnline', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistOnline);
		$lists ['userlistAvatar']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistAvatar', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistAvatar);
		$lists ['userlistPosts']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistPosts', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistPosts);
		$lists ['userlistKarma']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistKarma', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistKarma);
		$lists ['userlistEmail']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistEmail', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistEmail);
		$lists ['userlistJoinDate']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistJoinDate', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistJoinDate);
		$lists ['userlistLastVisitDate'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistLastVisitDate', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistLastVisitDate);
		$lists ['userlistUserHits']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userlistUserHits', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistUserHits);
		$lists ['reportMsg']             = HTMLHelper::_('select.genericlist', $yesno, 'cfg_reportMsg', 'class="inputbox form-control"size="1"', 'value', 'text', $config->reportMsg);

		$captcha   = [];
		$captcha[] = HTMLHelper::_('select.option', '-1', Text::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_NOBODY'));
		$captcha[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_REGISTERED_USERS'));
		$captcha[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_GUESTS_REGISTERED_USERS'));

		$lists ['captcha']  = HTMLHelper::_('select.genericlist', $captcha, 'cfg_captcha', 'class="inputbox form-control"size="1"', 'value', 'text', $config->captcha);
		$lists ['mailFull'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_mailFull', 'class="inputbox form-control"size="1"', 'value', 'text', $config->mailFull);

		// New for 1.0.5
		$lists ['showSpoilerTag']   = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showSpoilerTag', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showSpoilerTag);
		$lists ['showVideoTag']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showVideoTag', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showVideoTag);
		$lists ['showEbayTag']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showEbayTag', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showEbayTag);
		$lists ['trimLongUrls']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_trimLongUrls', 'class="inputbox form-control"size="1"', 'value', 'text', $config->trimLongUrls);
		$lists ['autoEmbedYoutube'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_autoEmbedYoutube', 'class="inputbox form-control"size="1"', 'value', 'text', $config->autoEmbedYoutube);
		$lists ['autoEmbedEbay']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_autoEmbedEbay', 'class="inputbox form-control"size="1"', 'value', 'text', $config->autoEmbedEbay);
		$lists ['highlightCode']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_highlightCode', 'class="inputbox form-control"size="1"', 'value', 'text', $config->highlightCode);

		// New for 1.5.8 -> SEF
		$lists ['sef'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_sef', 'class="inputbox form-control"size="1"', 'value', 'text', $config->sef);

		// New for 1.6 -> Hide images and files for guests
		$lists['showImgForGuest']  = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showImgForGuest', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showImgForGuest);
		$lists['showFileForGuest'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showFileForGuest', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showFileForGuest);

		// New for 1.6 -> Check Image MIME types
		$lists['checkMimeTypes'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_checkMimeTypes', 'class="inputbox form-control"size="1"', 'value', 'text', $config->checkMimeTypes);

		// New for 1.6 -> Poll
		$lists['pollAllowVoteOne']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pollAllowVoteOne', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pollAllowVoteOne);
		$lists['pollEnabled']          = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pollEnabled', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pollEnabled);
		$lists['showPopPollStats']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showPopPollStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showPopPollStats);
		$lists['pollResultsUserslist'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pollResultsUserslist', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pollResultsUserslist);

		// New for 1.6 -> Choose ordering system
		$orderingSystem_list     = [];
		$orderingSystem_list[]   = HTMLHelper::_('select.option', 'mesid', Text::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_NEW'));
		$orderingSystem_list[]   = HTMLHelper::_('select.option', 'replyid', Text::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_OLD'));
		$lists['orderingSystem'] = HTMLHelper::_('select.genericlist', $orderingSystem_list, 'cfg_orderingSystem', 'class="inputbox form-control"size="1"', 'value', 'text', $config->orderingSystem);

		// New for 1.6: datetime
		$dateformatlist               = [];
		$time                         = KunenaDate::getInstance(time() - 80000);
		$dateformatlist[]             = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
		$dateformatlist[]             = HTMLHelper::_('select.option', 'ago', $time->toKunena('ago'));
		$dateformatlist[]             = HTMLHelper::_('select.option', 'datetime_today', $time->toKunena('datetime_today'));
		$dateformatlist[]             = HTMLHelper::_('select.option', 'datetime', $time->toKunena('datetime'));
		$lists['postDateFormat']      = HTMLHelper::_('select.genericlist', $dateformatlist, 'cfg_postDateFormat', 'class="inputbox form-control"size="1"', 'value', 'text', $config->postDateFormat);
		$lists['postDateFormatHover'] = HTMLHelper::_('select.genericlist', $dateformatlist, 'cfg_postDateFormatHover', 'class="inputbox form-control"size="1"', 'value', 'text', $config->postDateFormatHover);

		// New for 1.6: hide ip
		$lists['hideIp'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_hideIp', 'class="inputbox form-control"size="1"', 'value', 'text', $config->hideIp);

		// New for 1.6: choose if you want that ghost message box checked by default
		$lists['boxGhostMessage'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_boxGhostMessage', 'class="inputbox form-control"size="1"', 'value', 'text', $config->boxGhostMessage);

		// New for 1.6 -> Thank you button
		$lists ['showThankYou'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showThankYou', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showThankYou);

		$listUserDeleteMessage      = [];
		$listUserDeleteMessage[]    = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_DELETEMESSAGE_NOT_ALLOWED'));
		$listUserDeleteMessage[]    = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_DELETEMESSAGE_ALLOWED_IF_REPLIES'));
		$listUserDeleteMessage[]    = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_A_DELETEMESSAGE_ALWAYS_ALLOWED'));
		$listUserDeleteMessage[]    = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_CONFIG_DELETEMESSAGE_NOT_FIRST_MESSAGE'));
		$listUserDeleteMessage[]    = HTMLHelper::_('select.option', '4', Text::_('COM_KUNENA_CONFIG_DELETEMESSAGE_ONLY_LAST_MESSAGE'));
		$lists['userDeleteMessage'] = HTMLHelper::_('select.genericlist', $listUserDeleteMessage, 'cfg_userDeleteMessage', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userDeleteMessage);

		$latestCategoryIn          = [];
		$latestCategoryIn[]        = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_LATESTCATEGORY_IN_HIDE'));
		$latestCategoryIn[]        = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_LATESTCATEGORY_IN_SHOW'));
		$lists['latestCategoryIn'] = HTMLHelper::_('select.genericlist', $latestCategoryIn, 'cfg_latestCategoryIn', 'class="inputbox form-control"size="1"', 'value', 'text', $config->latestCategoryIn);

		$optionsShowHide = [HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_COM_A_LATESTCATEGORY_SHOWALL'))];

		$lists['latestCategory'] = HTMLHelper::_('select.genericlist', $optionsShowHide, 'cfg_latestCategory', 'class="inputbox form-control"multiple="multiple"', 'value', 'text', explode(',', $config->latestCategory), 'latestCategory');

		$lists['topicIcons'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_topicIcons', 'class="inputbox form-control"size="1"', 'value', 'text', $config->topicIcons);

		$lists['debug'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_debug', 'class="inputbox form-control"size="1"', 'value', 'text', $config->debug);

		$lists['showBannedReason'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showBannedReason', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showBannedReason);

		$lists['timeToCreatePage'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_timeToCreatePage', 'class="inputbox form-control"size="1"', 'value', 'text', $config->timeToCreatePage);

		$lists['showPopThankYouStats'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showPopThankYouStats', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showPopThankYouStats);

		$seeRestoreDeleted       = [];
		$seeRestoreDeleted[]     = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_A_SEE_RESTORE_DELETED_NOBODY'));
		$seeRestoreDeleted[]     = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINSMODS'));
		$seeRestoreDeleted[]     = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINS'));
		$lists ['modSeeDeleted'] = HTMLHelper::_('select.genericlist', $seeRestoreDeleted, 'cfg_modSeeDeleted', 'class="inputbox form-control"size="1"', 'value', 'text', $config->modSeeDeleted);

		$listBbcodeImgSecure             = [];
		$listBbcodeImgSecure[]           = HTMLHelper::_('select.option', 'text', Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_TEXT'));
		$listBbcodeImgSecure[]           = HTMLHelper::_('select.option', 'link', Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_LINK'));
		$listBbcodeImgSecure[]           = HTMLHelper::_('select.option', 'image', Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_IMAGE'));
		$lists ['bbcodeImgSecure']       = HTMLHelper::_('select.genericlist', $listBbcodeImgSecure, 'cfg_bbcodeImgSecure', 'class="inputbox form-control"size="1"', 'value', 'text', $config->bbcodeImgSecure);
		$lists ['listCatShowModerators'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_listCatShowModerators', 'class="inputbox form-control"size="1"', 'value', 'text', $config->listCatShowModerators);
		$showlightbox                    = $yesno;
		$showlightbox[]                  = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_A_LIGHTBOX_NO_JS'));
		$lists ['lightbox']              = HTMLHelper::_('select.genericlist', $showlightbox, 'cfg_lightbox', 'class="inputbox form-control"size="1"', 'value', 'text', $config->lightbox);

		$timesel[] = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_SHOW_SELECT_ALL'));
		$timesel[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = HTMLHelper::_('select.option', 4, Text::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = HTMLHelper::_('select.option', 8, Text::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = HTMLHelper::_('select.option', 12, Text::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = HTMLHelper::_('select.option', 24, Text::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = HTMLHelper::_('select.option', 48, Text::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = HTMLHelper::_('select.option', 168, Text::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = HTMLHelper::_('select.option', 720, Text::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = HTMLHelper::_('select.option', 8760, Text::_('COM_KUNENA_SHOW_YEAR'));

		// Build the html select list
		$lists ['showListTime'] = HTMLHelper::_('select.genericlist', $timesel, 'cfg_showListTime', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showListTime);

		$sessionTimeType[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_SESSION_TYPE_ALL'));
		$sessionTimeType[] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_SHOW_SESSION_TYPE_VALID'));
		$sessionTimeType[] = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_SHOW_SESSION_TYPE_TIME'));

		$lists ['showSessionType'] = HTMLHelper::_('select.genericlist', $sessionTimeType, 'cfg_showSessionType', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showSessionType);

		$userlistAllowed           = [];
		$userlistAllowed []        = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_NO'));
		$userlistAllowed []        = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_YES'));
		$lists ['userlistAllowed'] = HTMLHelper::_('select.genericlist', $userlistAllowed, 'cfg_userlistAllowed', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistAllowed);
		$lists ['pubProfile']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pubProfile', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pubProfile);

		$userlistCountUsers[]         = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ALL'));
		$userlistCountUsers[]         = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVATED_ACCOUNT'));
		$userlistCountUsers[]         = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVE'));
		$userlistCountUsers[]         = HTMLHelper::_('select.option', 3, Text::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_NON_BLOCKED_USERS'));
		$lists ['userlistCountUsers'] = HTMLHelper::_('select.genericlist', $userlistCountUsers, 'cfg_userlistCountUsers', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userlistCountUsers);

		// Added new options into K1.6.4
		$lists ['allowSubscriptions'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowSubscriptions', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowSubscriptions);

		$categorySubscriptions           = [];
		$categorySubscriptions[]         = HTMLHelper::_('select.option', 'disabled', Text::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_DISABLED'));
		$categorySubscriptions[]         = HTMLHelper::_('select.option', 'topic', Text::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_TOPIC'));
		$categorySubscriptions[]         = HTMLHelper::_('select.option', 'post', Text::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_POST'));
		$lists ['categorySubscriptions'] = HTMLHelper::_('select.genericlist', $categorySubscriptions, 'cfg_categorySubscriptions', 'class="inputbox form-control"size="1"', 'value', 'text', $config->categorySubscriptions);

		$topicSubscriptions           = [];
		$topicSubscriptions[]         = HTMLHelper::_('select.option', 'disabled', Text::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_DISABLED'));
		$topicSubscriptions[]         = HTMLHelper::_('select.option', 'first', Text::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_FIRST'));
		$topicSubscriptions[]         = HTMLHelper::_('select.option', 'every', Text::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_EVERY'));
		$lists ['topicSubscriptions'] = HTMLHelper::_('select.genericlist', $topicSubscriptions, 'cfg_topicSubscriptions', 'class="inputbox form-control"size="1"', 'value', 'text', $config->topicSubscriptions);

		// Added new options into K1.6.6
		$emailRecipientPrivacy           = [];
		$emailRecipientPrivacy[]         = HTMLHelper::_('select.option', 'to', Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_TO'));
		$emailRecipientPrivacy[]         = HTMLHelper::_('select.option', 'cc', Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_CC'));
		$emailRecipientPrivacy[]         = HTMLHelper::_('select.option', 'bcc', Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_BCC'));
		$lists ['emailRecipientPrivacy'] = HTMLHelper::_('select.genericlist', $emailRecipientPrivacy, 'cfg_emailRecipientPrivacy', 'class="inputbox form-control"size="1"', 'value', 'text', $config->emailRecipientPrivacy);

		$uploads               = [];
		$uploads[]             = HTMLHelper::_('select.option', 'everybody', Text::_('COM_KUNENA_EVERYBODY'));
		$uploads[]             = HTMLHelper::_('select.option', 'registered', Text::_('COM_KUNENA_REGISTERED_USERS'));
		$uploads[]             = HTMLHelper::_('select.option', 'moderator', Text::_('COM_KUNENA_MODERATORS'));
		$uploads[]             = HTMLHelper::_('select.option', 'admin', Text::_('COM_KUNENA_ADMINS'));
		$uploads[]             = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_NOBODY'));
		$lists ['imageUpload'] = HTMLHelper::_('select.genericlist', $uploads, 'cfg_imageUpload', 'class="inputbox form-control"size="1"', 'value', 'text', $config->imageUpload);
		$lists ['fileUpload']  = HTMLHelper::_('select.genericlist', $uploads, 'cfg_fileUpload', 'class="inputbox form-control"size="1"', 'value', 'text', $config->fileUpload);

		$topicLayout[]         = HTMLHelper::_('select.option', 'flat', Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_FLAT'));
		$topicLayout[]         = HTMLHelper::_('select.option', 'threaded', Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_THREADED'));
		$topicLayout[]         = HTMLHelper::_('select.option', 'indented', Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_INDENTED'));
		$lists ['topicLayout'] = HTMLHelper::_('select.genericlist', $topicLayout, 'cfg_topicLayout', 'class="inputbox form-control"size="1"', 'value', 'text', $config->topicLayout);

		$lists ['showImgFilesManageProfile'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_showImgFilesManageProfile', 'class="inputbox form-control"size="1"', 'value', 'text', $config->showImgFilesManageProfile);

		$lists ['holdGuestPosts'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_holdGuestPosts', 'class="inputbox form-control"size="1"', 'value', 'text', $config->holdGuestPosts);

		$lists ['pickupCategory'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_pickupCategory', 'class="inputbox form-control"size="1"', 'value', 'text', $config->pickupCategory);

		$articleDisplay[]         = HTMLHelper::_('select.option', 'full', Text::_('COM_KUNENA_COM_A_FULL_ARTICLE'));
		$articleDisplay[]         = HTMLHelper::_('select.option', 'intro', Text::_('COM_KUNENA_COM_A_INTRO_ARTICLE'));
		$articleDisplay[]         = HTMLHelper::_('select.option', 'link', Text::_('COM_KUNENA_COM_A_ARTICLE_LINK'));
		$lists ['articleDisplay'] = HTMLHelper::_('select.genericlist', $articleDisplay, 'cfg_articleDisplay', 'class="inputbox form-control"size="1"', 'value', 'text', $config->articleDisplay);

		$lists ['sendEmails']            = HTMLHelper::_('select.genericlist', $yesno, 'cfg_sendEmails', 'class="inputbox form-control"size="1"', 'value', 'text', $config->sendEmails);
		$lists ['enableThreadedLayouts'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_enableThreadedLayouts', 'class="inputbox form-control"size="1"', 'value', 'text', $config->enableThreadedLayouts);

		$defaultSort           = [];
		$defaultSort[]         = HTMLHelper::_('select.option', 'asc', Text::_('COM_KUNENA_OPTION_DEFAULT_SORT_FIRST'));
		$defaultSort[]         = HTMLHelper::_('select.option', 'desc', Text::_('COM_KUNENA_OPTION_DEFAULT_SORT_LAST'));
		$lists ['defaultSort'] = HTMLHelper::_('select.genericlist', $defaultSort, 'cfg_defaultSort', 'class="inputbox form-control"size="1"', 'value', 'text', $config->defaultSort);

		$lists ['fallbackEnglish'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_fallbackEnglish', 'class="inputbox form-control"size="1"', 'value', 'text', $config->fallbackEnglish);

		$cacheTime           = [];
		$cacheTime[]         = HTMLHelper::_('select.option', '60', Text::_('COM_KUNENA_CFG_OPTION_1_MINUTE'));
		$cacheTime[]         = HTMLHelper::_('select.option', '120', Text::_('COM_KUNENA_CFG_OPTION_2_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '180', Text::_('COM_KUNENA_CFG_OPTION_3_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '300', Text::_('COM_KUNENA_CFG_OPTION_5_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '600', Text::_('COM_KUNENA_CFG_OPTION_10_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '900', Text::_('COM_KUNENA_CFG_OPTION_15_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '1800', Text::_('COM_KUNENA_CFG_OPTION_30_MINUTES'));
		$cacheTime[]         = HTMLHelper::_('select.option', '3600', Text::_('COM_KUNENA_CFG_OPTION_60_MINUTES'));
		$lists ['cache']     = HTMLHelper::_('select.genericlist', $yesno, 'cfg_cache', 'class="inputbox form-control"size="1"', 'value', 'text', $config->cache);
		$lists ['cacheTime'] = HTMLHelper::_('select.genericlist', $cacheTime, 'cfg_cacheTime', 'class="inputbox form-control"size="1"', 'value', 'text', $config->cacheTime);

		// Added new options into Kunena 2.0.1
		$mailOptions   = [];
		$mailOptions[] = HTMLHelper::_('select.option', '-1', Text::_('COM_KUNENA_NO'));
		$mailOptions[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CFG_OPTION_UNAPPROVED_POSTS'));
		$mailOptions[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CFG_OPTION_ALL_NEW_POSTS'));

		$lists ['mailModerators']     = HTMLHelper::_('select.genericlist', $mailOptions, 'cfg_mailModerators', 'class="inputbox form-control"size="1"', 'value', 'text', $config->mailModerators);
		$lists ['mailAdministrators'] = HTMLHelper::_('select.genericlist', $mailOptions, 'cfg_mailAdministrators', 'class="inputbox form-control"size="1"', 'value', 'text', $config->mailAdministrators);

		$lists ['ipTracking'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_ipTracking', 'class="inputbox form-control"size="1"', 'value', 'text', $config->ipTracking);

		// Added new options into Kunena 3.0.0
		$lists ['autoLink']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_autoLink', 'class="inputbox form-control"size="1"', 'value', 'text', $config->autoLink);
		$lists ['accessComponent'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_accessComponent', 'class="inputbox form-control"size="1"', 'value', 'text', $config->accessComponent);
		$lists ['componentUrl']    = preg_replace('|/+|', '/', Uri::root() . ($config->get('sef_rewrite') ? '' : 'index.php') . ($config->get('sef') ? '/component/kunena' : '?option=com_kunena'));

		$options                       = [];
		$options[]                     = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));
		$options[]                     = HTMLHelper::_('select.option', '1', 'Kunena 1.x');
		$lists['attachmentProtection'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_attachmentProtection', 'class="inputbox form-control"size="1"', 'value', 'text', $config->attachmentProtection);

		// Option to select if the stats link need to be showed for all users or only for registered users
		$lists ['statsLinkAllowed']   = HTMLHelper::_('select.genericlist', $yesno, 'cfg_statsLinkAllowed', 'class="inputbox form-control"size="1"', 'value', 'text', $config->statsLinkAllowed);
		$lists ['superAdminUserlist'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_superAdminUserlist', 'class="inputbox form-control"size="1"', 'value', 'text', $config->superAdminUserlist);
		$lists ['avatarCrop']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_avatarCrop', 'class="inputbox form-control"size="1"', 'value', 'text', $config->avatarCrop);
		$lists ['userReport']         = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userReport', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userReport);

		$searchTime           = [];
		$searchTime[]         = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_CFG_SEARCH_DATE_YESTERDAY'));
		$searchTime[]         = HTMLHelper::_('select.option', 7, Text::_('COM_KUNENA_CFG_SEARCH_DATE_WEEK'));
		$searchTime[]         = HTMLHelper::_('select.option', 14, Text::_('COM_KUNENA_CFG_SEARCH_DATE_2WEEKS'));
		$searchTime[]         = HTMLHelper::_('select.option', 30, Text::_('COM_KUNENA_CFG_SEARCH_DATE_MONTH'));
		$searchTime[]         = HTMLHelper::_('select.option', 90, Text::_('COM_KUNENA_CFG_SEARCH_DATE_3MONTHS'));
		$searchTime[]         = HTMLHelper::_('select.option', 180, Text::_('COM_KUNENA_CFG_SEARCH_DATE_6MONTHS'));
		$searchTime[]         = HTMLHelper::_('select.option', 365, Text::_('COM_KUNENA_CFG_SEARCH_DATE_YEAR'));
		$searchTime[]         = HTMLHelper::_('select.option', 'all', Text::_('COM_KUNENA_CFG_SEARCH_DATE_ANY'));
		$lists ['searchTime'] = HTMLHelper::_('select.genericlist', $searchTime, 'cfg_searchTime', 'class="inputbox form-control"size="1"', 'value', 'text', $config->searchTime);

		$lists ['teaser'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_teaser', 'class="inputbox form-control"size="1"', 'value', 'text', $config->teaser);

		// List of eBay language code
		$ebayLanguage   = [];
		$ebayLanguage[] = HTMLHelper::_('select.option', '0', 'en-US');
		$ebayLanguage[] = HTMLHelper::_('select.option', '2', 'en-CA');
		$ebayLanguage[] = HTMLHelper::_('select.option', '3', 'en-GB');
		$ebayLanguage[] = HTMLHelper::_('select.option', '15', 'en-AU');
		$ebayLanguage[] = HTMLHelper::_('select.option', '16', 'de-AT');
		$ebayLanguage[] = HTMLHelper::_('select.option', '23', 'fr-BE');
		$ebayLanguage[] = HTMLHelper::_('select.option', '71', 'fr-FR');
		$ebayLanguage[] = HTMLHelper::_('select.option', '77', 'de-DE');
		$ebayLanguage[] = HTMLHelper::_('select.option', '101', 'it-IT');
		$ebayLanguage[] = HTMLHelper::_('select.option', '123', 'nl-BE');
		$ebayLanguage[] = HTMLHelper::_('select.option', '146', 'nl-NL');
		$ebayLanguage[] = HTMLHelper::_('select.option', '186', 'es-ES');
		$ebayLanguage[] = HTMLHelper::_('select.option', '193', 'ch-CH');
		$ebayLanguage[] = HTMLHelper::_('select.option', '201', 'hk-HK');
		$ebayLanguage[] = HTMLHelper::_('select.option', '203', 'in-IN');
		$ebayLanguage[] = HTMLHelper::_('select.option', '205', 'ie-IE');
		$ebayLanguage[] = HTMLHelper::_('select.option', '207', 'my-MY');
		$ebayLanguage[] = HTMLHelper::_('select.option', '210', 'fr-CA');
		$ebayLanguage[] = HTMLHelper::_('select.option', '211', 'ph-PH');
		$ebayLanguage[] = HTMLHelper::_('select.option', '212', 'pl-PL');
		$ebayLanguage[] = HTMLHelper::_('select.option', '216', 'sg-SG');

		$lists['ebayLanguage'] = HTMLHelper::_('select.genericlist', $ebayLanguage, 'cfg_ebayLanguage', 'class="inputbox form-control"size="1"', 'value', 'text', $config->ebayLanguage);

		$userEdit          = [];
		$userEdit[]        = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_EDIT_ALLOWED_NEVER'));
		$userEdit[]        = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_EDIT_ALLOWED_ALWAYS'));
		$userEdit[]        = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_A_EDIT_ALLOWED_IF_REPLIES'));
		$userEdit[]        = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_EDIT_ALLOWED_ONLY_LAST_MESSAGE'));
		$userEdit[]        = HTMLHelper::_('select.option', '4', Text::_('COM_KUNENA_EDIT_ALLOWED_ONLY_FIRST_MESSAGE'));
		$lists['userEdit'] = HTMLHelper::_('select.genericlist', $userEdit, 'cfg_userEdit', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userEdit);

		$lists ['allowChangeSubject'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_allowChangeSubject', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowChangeSubject);

		// K5.0
		$lists ['readOnly'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_readOnly', 'class="inputbox form-control"size="1"', 'value', 'text', $config->readOnly);

		$lists['ratingEnabled'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_ratingEnabled', 'class="inputbox form-control"size="1"', 'value', 'text', $config->ratingEnabled);

		$lists ['urlSubjectTopic'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_urlSubjectTopic', 'class="inputbox form-control"size="1"', 'value', 'text', $config->urlSubjectTopic);

		$lists ['logModeration'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_logModeration', 'class="inputbox form-control"size="1"', 'value', 'text', $config->logModeration);

		$lists ['attachmentUtf8'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_attachmentUtf8', 'class="inputbox form-control"size="1"', 'value', 'text', $config->attachmentUtf8);

		$lists ['autoEmbedSoundcloud'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_autoEmbedSoundcloud', 'class="inputbox form-control"size="1"', 'value', 'text', $config->autoEmbedSoundcloud);

		$lists ['userStatus'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_userStatus', 'class="inputbox form-control"size="1"', 'value', 'text', $config->userStatus);

		// K5.1
		$lists ['signature'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_signature', 'class="inputbox form-control"size="1"', 'value', 'text', $config->signature);
		$lists ['personal']  = HTMLHelper::_('select.genericlist', $yesno, 'cfg_personal', 'class="inputbox form-control"size="1"', 'value', 'text', $config->personal);
		$lists ['social']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_social', 'class="inputbox form-control"size="1"', 'value', 'text', $config->social);

		$lists ['plainEmail']   = HTMLHelper::_('select.genericlist', $yesno, 'cfg_plainEmail', 'class="inputbox form-control"size="1"', 'value', 'text', $config->plainEmail);
		$lists ['smartLinking'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_smartLinking', 'class="inputbox form-control"size="1"', 'value', 'text', $config->smartLinking);

		$rankImages           = [];
		$rankImages[]         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_RANK_TEXT'));
		$rankImages[]         = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_RANK_IMAGE'));
		$rankImages[]         = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_RANK_USERGROUP'));
		$rankImages[]         = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_RANK_BOTH'));
		$rankImages[]         = HTMLHelper::_('select.option', '4', Text::_('COM_KUNENA_RANK_CSS'));
		$lists ['rankImages'] = HTMLHelper::_('select.genericlist', $rankImages, 'cfg_rankImages', 'class="inputbox form-control"size="1"', 'value', 'text', $config->rankImages);

		$lists['defaultAvatar']      = HTMLHelper::_('select.genericlist', $yesno, 'cfg_defaultAvatar', 'class="inputbox form-control"size="1"', 'value', 'text', $config->defaultAvatar);
		$lists['defaultAvatarSmall'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_defaultAvatarSmall', 'class="inputbox form-control"size="1"', 'value', 'text', $config->defaultAvatarSmall);
		$lists ['quickReply']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_quickReply', 'class="inputbox form-control"size="1"', 'value', 'text', $config->quickReply);
		$lists ['avatarEdit']        = HTMLHelper::_('select.genericlist', $yesno, 'cfg_avatarEdit', 'class="inputbox form-control"size="1"', 'value', 'text', $config->avatarEdit);

		$lists ['moderatorPermDelete'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_moderatorPermDelete', 'class="inputbox form-control"size="1"', 'value', 'text', $config->moderatorPermDelete);

		$avatarType           = [];
		$avatarType[]         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_AVATAR_IMAGE'));
		$avatarType[]         = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_AVATAR_ICONTYPE'));
		$lists ['avatarType'] = HTMLHelper::_('select.genericlist', $avatarType, 'cfg_avatarType', 'class="inputbox form-control"size="1"', 'value', 'text', $config->avatarType);

		$lists ['sefRedirect'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_sefRedirect', 'class="inputbox form-control"size="1"', 'value', 'text', $config->sefRedirect);

		$userEditPoll   = [];
		$userEditPoll[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CONFIG_POLL_ALLOW_USER_EDIT_POLL_ALLOW'));
		$userEditPoll[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CONFIG_POLL_ALLOW_USER_EDIT_POLL_DISALLOW'));

		$lists ['allowUserEditPoll'] = HTMLHelper::_('select.genericlist', $userEditPoll, 'cfg_allowEditPoll', 'class="inputbox form-control"size="1"', 'value', 'text', $config->allowEditPoll);

		// K 5.1.2
		$lists ['useSystemEmails']    = HTMLHelper::_('select.genericlist', $yesno, 'cfg_useSystemEmails', 'class="inputbox form-control"size="1"', 'value', 'text', $config->useSystemEmails);
		$lists ['autoEmbedInstagram'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_autoEmbedInstagram', 'class="inputbox form-control"size="1"', 'value', 'text', $config->autoEmbedInstagram);

		// K 5.1.19
		$lists ['display_filename_attachment'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_display_filename_attachment', 'class="inputbox" size="1"', 'value', 'text', $config->display_filename_attachment);

		// K5.2.0
		$lists ['new_users_prevent_post_url_images'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_new_users_prevent_post_url_images', 'class="inputbox" size="1"', 'value', 'text', $config->new_users_prevent_post_url_images);

		// K6.0
		$lists ['utmSource'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_utmSource', 'class="inputbox form-control" size="1"', 'value', 'text', $config->utmSource);

		$lists ['disableRe'] = HTMLHelper::_('select.genericlist', $yesno, 'cfg_disableRe', 'class="inputbox form-control" size="1"', 'value', 'text', $config->disableRe);
		$lists ['profiler']  = HTMLHelper::_('select.genericlist', $yesno, 'cfg_profiler', 'class="inputbox form-control" size="1"', 'value', 'text', $config->profiler);

		return $lists;
	}
}
