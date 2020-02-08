<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @copyright      Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 *
 * Based on FireBoard Component
 * @link           http://www.bestofjoomla.com
 **/

namespace Kunena\Forum\Libraries\Config;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Cache\CacheHelper;
use Kunena\Forum\Libraries\Error\KunenaError;
use function defined;

/**
 * Class KunenaConfig
 *
 * @since   Kunena 6.0
 * @property int     $id
 * @property string  $board_title
 * @property string  $email
 * @property boolean $board_offline
 * @property string  $offline_message
 * @property boolean $enablerss
 * @property integer $threads_per_page
 * @property integer $messages_per_page
 * @property integer $messages_per_page_search
 * @property boolean $showhistory
 * @property integer $historylimit
 * @property boolean $shownew
 * @property boolean $disemoticons
 * @property string  $template
 * @property boolean $showannouncement
 * @property boolean $avataroncat
 * @property boolean $showchildcaticon
 * @property integer $rtewidth
 * @property integer $rteheight
 * @property boolean $enableforumjump
 * @property boolean $reportmsg
 * @property boolean $username
 * @property boolean $askemail
 * @property boolean $showemail
 * @property boolean $showuserstats
 * @property boolean $showkarma
 * @property boolean $useredit
 * @property integer $useredittime
 * @property integer $useredittimegrace
 * @property boolean $editmarkup
 * @property boolean $allowsubscriptions
 * @property boolean $subscriptionschecked
 * @property boolean $allowfavorites
 * @property integer $maxsig
 * @property boolean $regonly
 * @property boolean $pubwrite
 * @property boolean $floodprotection
 * @property boolean $mailmod
 * @property boolean $mailadmin
 * @property boolean $captcha
 * @property boolean $mailfull
 * @property boolean $allowavatarupload
 * @property boolean $allowavatargallery
 * @property integer $avatarquality
 * @property integer $avatarsize
 * @property integer $imageheight
 * @property integer $imagewidth
 * @property integer $imagesize
 * @property string  $filetypes
 * @property integer $filesize
 * @property boolean $showranking
 * @property boolean $rankimages
 * @property integer $userlist_rows
 * @property boolean $userlist_online
 * @property boolean $userlist_avatar
 * @property boolean $userlist_posts
 * @property boolean $userlist_karma
 * @property boolean $userlist_email
 * @property boolean $userlist_joindate
 * @property boolean $userlist_lastvisitdate
 * @property boolean $userlist_userhits
 * @property boolean $latestcategory
 * @property boolean $showstats
 * @property boolean $showwhoisonline
 * @property boolean $showgenstats
 * @property boolean $showpopuserstats
 * @property integer $popusercount
 * @property boolean $showpopsubjectstats
 * @property boolean $popsubjectcount
 * @property boolean $showspoilertag
 * @property boolean $showvideotag
 * @property boolean $showebaytag
 * @property boolean $trimlongurls
 * @property integer $trimlongurlsfront
 * @property integer $trimlongurlsback
 * @property boolean $autoembedyoutube
 * @property boolean $autoembedebay
 * @property boolean $ebaylanguagecode
 * @property integer $sessiontimeout
 * @property boolean $highlightcode
 * @property string  $rss_type
 * @property string  $rss_timelimit
 * @property integer $rss_limit
 * @property string  $rss_included_categories
 * @property string  $rss_excluded_categories
 * @property string  $rss_specification
 * @property boolean $rss_allow_html
 * @property string  $rss_author_format
 * @property boolean $rss_author_in_title
 * @property integer $rss_word_count
 * @property boolean $rss_old_titles
 * @property boolean $rss_cache
 * @property string  $defaultpage
 * @property string  $default_sort
 * @property boolean $sef
 * @property boolean $showimgforguest
 * @property boolean $showfileforguest
 * @property integer $pollnboptions
 * @property boolean $pollallowvoteone
 * @property boolean $pollenabled
 * @property integer $poppollscount
 * @property boolean $showpoppollstats
 * @property integer $polltimebtvotes
 * @property integer $pollnbvotesbyuser
 * @property boolean $pollresultsuserslist
 * @property boolean $allow_user_edit_poll
 * @property integer $maxpersotext
 * @property string  $ordering_system
 * @property string  $post_dateformat
 * @property string  $post_dateformat_hover
 * @property boolean $hide_ip
 * @property string  $imagetypes
 * @property boolean $checkmimetypes
 * @property string  $imagemimetypes
 * @property integer $imagequality
 * @property integer $thumbheight
 * @property integer $thumbwidth
 * @property string  $hideuserprofileinfo
 * @property boolean $boxghostmessage
 * @property integer $userdeletetmessage
 * @property integer $latestcategory_in
 * @property boolean $topicicons
 * @property boolean $debug
 * @property boolean $catsautosubscribed
 * @property boolean $showbannedreason
 * @property boolean $showthankyou
 * @property boolean $showpopthankyoustats
 * @property integer $popthankscount
 * @property boolean $mod_see_deleted
 * @property string  $bbcode_img_secure
 * @property boolean $listcat_show_moderators
 * @property boolean $lightbox
 * @property integer $show_list_time
 * @property integer $show_session_type
 * @property integer $show_session_starttime
 * @property boolean $userlist_allowed
 * @property integer $userlist_count_users
 * @property boolean $enable_threaded_layouts
 * @property string  $category_subscriptions
 * @property string  $topic_subscriptions
 * @property boolean $pubprofile
 * @property integer $thankyou_max
 * @property integer $email_recipient_count
 * @property string  $email_recipient_privacy
 * @property string  $email_visible_address
 * @property integer $captcha_post_limit
 * @property string  $image_upload
 * @property string  $file_upload
 * @property string  $topic_layout
 * @property boolean $time_to_create_page
 * @property boolean $show_imgfiles_manage_profile
 * @property boolean $hold_newusers_posts
 * @property boolean $hold_guest_posts
 * @property integer $attachment_limit
 * @property boolean $pickup_category
 * @property string  $article_display
 * @property boolean $send_emails
 * @property boolean $fallback_english
 * @property boolean $cache
 * @property integer $cache_time
 * @property integer $ebay_affiliate_id
 * @property boolean $iptracking
 * @property string  $rss_feedburner_url
 * @property boolean $autolink
 * @property boolean $access_component
 * @property boolean $statslink_allowed
 * @property boolean $superadmin_userlist
 * @property boolean $legacy_urls
 * @property boolean $attachment_protection
 * @property boolean $categoryicons
 * @property boolean $avatarcrop
 * @property boolean $user_report
 * @property integer $searchtime
 * @property boolean $teaser
 * @property boolean $ebay_language
 * @property string  $ebay_api_key
 * @property string  $twitter_consumer_key
 * @property string  $twitter_consumer_secret
 * @property boolean $allow_change_subject
 * @property integer $max_links
 * @property boolean $read_only
 * @property boolean $ratingenabled
 * @property boolean $url_subject_topic
 * @property boolean $log_moderation
 * @property integer $attach_start
 * @property integer $attach_end
 * @property string  $google_map_api_key
 * @property boolean $attachment_utf8
 * @property boolean $autoembedsoundcloud
 * @property string  $emailheader
 * @property boolean $user_status
 * @property boolean $signature
 * @property boolean $personal
 * @property boolean $social
 * @property boolean $plain_email
 * @property boolean $moderator_permdelete
 * @property string  $avatartypes
 * @property boolean $smartlinking
 * @property string  $defaultavatar
 * @property string  $defaultavatarsmall
 * @property string  $stopforumspam_key
 * @property boolean $quickreply
 * @property boolean $avataredit
 * @property string  $activemenuitem
 * @property integer $mainmenu_id
 * @property integer $home_id
 * @property integer $index_id
 * @property integer $moderators_id
 * @property integer $topiclist_id
 * @property integer $misc_id
 * @property integer $profile_id
 * @property integer $search_id
 * @property integer $custom_id
 * @property integer $avatar_type
 * @property boolean $sef_redirect
 * @property boolean $allow_edit_poll
 * @property boolean $use_system_emails
 * @property boolean $autoembedinstagram
 * @property boolean $disable_re
 * @property boolean $utm_source
 *
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
	public $board_title = 'Kunena';

	/**
	 * @var    string  Email
	 * @since  Kunena 1.0.0
	 */
	public $email = '';

	/**
	 * @var    boolean  Board offline
	 * @since  Kunena 1.0.0
	 */
	public $board_offline = 0;

	/**
	 * @var    string  Offline message
	 * @since  Kunena 1.0.0
	 */
	public $offline_message = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>";

	/**
	 * @var    boolean Enable RSS
	 * @since  Kunena 1.0.0
	 */
	public $enablerss = 1;

	/**
	 * @var    integer    Threads per page
	 * @since  Kunena 1.0.0
	 */
	public $threads_per_page = 20;

	/**
	 * @var    integer  Messages per page
	 * @since  Kunena 1.0.0
	 */
	public $messages_per_page = 6;

	/**
	 * @var    integer  Messages per page search
	 * @since  Kunena 1.0.0
	 */
	public $messages_per_page_search = 15;

	/**
	 * @var    boolean  Show history
	 * @since  Kunena 1.0.0
	 */
	public $showhistory = 1;

	/**
	 * @var    integer  History limit
	 * @since  Kunena 1.0.0
	 */
	public $historylimit = 6;

	/**
	 * @var    boolean  Show new
	 * @since  Kunena 1.0.0
	 */
	public $shownew = 1;

	/**
	 * @var    boolean  Disable emoticons
	 * @since  Kunena 1.0.0
	 */
	public $disemoticons = 0;

	/**
	 * @var    string  Template
	 * @since  Kunena 1.0.0
	 */
	public $template = 'aurelia';

	/**
	 * @var    boolean  Show announcement
	 * @since  Kunena 1.0.0
	 */
	public $showannouncement = 1;

	/**
	 * @var    boolean  Avatar on category
	 * @since  Kunena 1.0.0
	 */
	public $avataroncat = 0;

	/**
	 * @var    boolean  Show child category icon
	 * @since  Kunena 1.0.0
	 */
	public $showchildcaticon = 1;

	/**
	 * @var    integer  Text area width
	 * @since  Kunena 1.0.0
	 */
	public $rtewidth = 450;

	/**
	 * @var    integer  Text area height
	 * @since  Kunena 1.0.0
	 */
	public $rteheight = 300;

	/**
	 * @var    boolean  Enable forum jump
	 * @since  Kunena 1.0.0
	 */
	public $enableforumjump = 1;

	/**
	 * @var    boolean  Report message
	 * @since  Kunena 1.0.0
	 */
	public $reportmsg = 1;

	/**
	 * @var    boolean  Username
	 * @since  Kunena 1.0.0
	 */
	public $username = 1;

	/**
	 * @var    boolean  Ask email
	 * @since  Kunena 1.0.0
	 */
	public $askemail = 0;

	/**
	 * @var    boolean  Show email
	 * @since  Kunena 1.0.0
	 */
	public $showemail = 0;

	/**
	 * @var    boolean  Show user statistics
	 * @since  Kunena 1.0.0
	 */
	public $showuserstats = 1;

	/**
	 * @var    boolean  Show karma
	 * @since  Kunena 1.0.0
	 */
	public $showkarma = 1;

	/**
	 * @var    boolean  User edit
	 * @since  Kunena 1.0.0
	 */
	public $useredit = 1;

	/**
	 * @var    integer  User edit time
	 * @since  Kunena 1.0.0
	 */
	public $useredittime = 0;

	/**
	 * @var    integer  User edit time Grace
	 * @since  Kunena 1.0.0
	 */
	public $useredittimegrace = 600;

	/**
	 * @var    boolean  Edit markup
	 * @since  Kunena 1.0.0
	 */
	public $editmarkup = 1;

	/**
	 * @var    boolean  Allow subscriptions
	 * @since  Kunena 1.0.0
	 */
	public $allowsubscriptions = 1;

	/**
	 * @var    boolean  Subscriptions Checked
	 * @since  Kunena 1.0.0
	 */
	public $subscriptionschecked = 1;

	/**
	 * @var    boolean  Allow favorites
	 * @since  Kunena 1.0.0
	 */
	public $allowfavorites = 1;

	/**
	 * @var    integer  Max signature length
	 * @since  Kunena 1.0.0
	 */
	public $maxsig = 300;

	/**
	 * @var    boolean  Registered users only
	 * @since  Kunena 1.0.0
	 */
	public $regonly = 0;

	/**
	 * @var    boolean  Public write
	 * @since  Kunena 1.0.0
	 */
	public $pubwrite = 0;

	/**
	 * @var    boolean  Flood projection
	 * @since  Kunena 1.0.0
	 */
	public $floodprotection = 0;

	/**
	 * @var    boolean  Mail moderators
	 * @since  Kunena 1.0.0
	 */
	public $mailmod = 0;

	/**
	 * @var    boolean  Mail admin
	 * @since  Kunena 1.0.0
	 */
	public $mailadmin = 0;

	/**
	 * @var    boolean  CAPTCHA
	 * @since  Kunena 1.0.0
	 */
	public $captcha = 0;

	/**
	 * @var    boolean  Mail full
	 * @since  Kunena 1.0.0
	 */
	public $mailfull = 1;

	/**
	 * @var    boolean  Allow avatar upload
	 * @since  Kunena 1.0.0
	 */
	public $allowavatarupload = 1;

	/**
	 * @var    boolean  Allow avatar gallery
	 * @since  Kunena 1.0.0
	 */
	public $allowavatargallery = 1;

	/**
	 * @var    integer  Avatar quality
	 * @since  Kunena 1.0.0
	 */
	public $avatarquality = 75;

	/**
	 * @var    integer  Avatar size
	 * @since  Kunena 1.0.0
	 */
	public $avatarsize = 2048;

	/**
	 * @var    integer  Image height
	 * @since  Kunena 1.0.0
	 */
	public $imageheight = 800;

	/**
	 * @var    integer  Image width
	 * @since  Kunena 1.0.0
	 */
	public $imagewidth = 800;

	/**
	 * @var    integer  Image size
	 * @since  Kunena 1.0.0
	 */
	public $imagesize = 150;

	/**
	 * @var    string  File types
	 * @since  Kunena 1.0.0
	 */
	public $filetypes = 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2';

	/**
	 * @var    integer  File size
	 * @since  Kunena 1.0.0
	 */
	public $filesize = 120;

	/**
	 * @var    boolean  Show ranking
	 * @since  Kunena 1.0.0
	 */
	public $showranking = 1;

	/**
	 * @var    boolean  Rank images
	 * @since  Kunena 1.0.0
	 */
	public $rankimages = 1;

	/**
	 * @var    integer  User list rows
	 * @since  Kunena 1.0.0
	 */
	public $userlist_rows = 30;

	/**
	 * @var    boolean  User list online
	 * @since  Kunena 1.0.0
	 */
	public $userlist_online = 1;

	/**
	 * @var    boolean  user list avatar
	 * @since  Kunena 1.0.0
	 */
	public $userlist_avatar = 1;

	/**
	 * @var    boolean  User list posts
	 * @since  Kunena 1.0.0
	 */
	public $userlist_posts = 1;

	/**
	 * @var    boolean  User list karma
	 * @since  Kunena 1.0.0
	 */
	public $userlist_karma = 1;

	/**
	 * @var    boolean  User list email
	 * @since  Kunena 1.0.0
	 */
	public $userlist_email = 0;

	/**
	 * @var    boolean  User list join date
	 * @since  Kunena 1.0.0
	 */
	public $userlist_joindate = 1;

	/**
	 * @var    boolean  User list lst visit date
	 * @since  Kunena 1.0.0
	 */
	public $userlist_lastvisitdate = 1;

	/**
	 * @var    boolean  User list user hits
	 * @since  Kunena 1.0.0
	 */
	public $userlist_userhits = 1;

	/**
	 * @var    string  Latest category
	 * @since  Kunena 1.0.0
	 */
	public $latestcategory = '';

	/**
	 * @var    boolean  Show stats
	 * @since  Kunena 1.0.0
	 */
	public $showstats = 1;

	/**
	 * @var    boolean  Show who is online
	 * @since  Kunena 1.0.0
	 */
	public $showwhoisonline = 1;

	/**
	 * @var    boolean  Show general statistics
	 * @since  Kunena 1.0.0
	 */
	public $showgenstats = 1;

	/**
	 * @var    boolean  Show population user statistics
	 * @since  Kunena 1.0.0
	 */
	public $showpopuserstats = 1;

	/**
	 * @var    integer  Population user count
	 * @since  Kunena 1.0.0
	 */
	public $popusercount = 5;

	/**
	 * @var    boolean  Show population subject statistics
	 * @since  Kunena 1.0.0
	 */
	public $showpopsubjectstats = 1;

	/**
	 * @var    integer  Population subject count
	 * @since  Kunena 1.0.0
	 */
	public $popsubjectcount = 5;

	/**
	 * @var    boolean  Show spoiler tag
	 * @since  Kunena 1.0.5
	 */
	public $showspoilertag = 1;

	/**
	 * @var    boolean  Show video tag
	 * @since  Kunena 1.0.5
	 */
	public $showvideotag = 1;

	/**
	 * @var    boolean  Show ebay tag
	 * @since  Kunena 1.0.5
	 */
	public $showebaytag = 1;

	/**
	 * @var    boolean  Trim long URLs
	 * @since  Kunena 1.0.5
	 */
	public $trimlongurls = 1;

	/**
	 * @var    integer  Trim long URLs in front
	 * @since  Kunena 1.0.5
	 */
	public $trimlongurlsfront = 40;

	/**
	 * @var    integer  Trim long URLs in back
	 * @since  Kunena 1.0.5
	 */
	public $trimlongurlsback = 20;

	/**
	 * @var    boolean  Auto embed youtube
	 * @since  Kunena 1.0.5
	 */
	public $autoembedyoutube = 1;

	/**
	 * @var    boolean  Auto embed ebay
	 * @since  Kunena 1.0.5
	 */
	public $autoembedebay = 1;

	/**
	 * @var    string  Ebay language code
	 * @since  Kunena 1.0.5
	 */
	public $ebaylanguagecode = 'en-us';

	/**
	 * @var    integer  Session time out. In seconds
	 * @since  Kunena 1.0.5
	 */
	public $sessiontimeout = 1800;

	/**
	 * @var    boolean  Highlight code
	 * @since  Kunena 1.0.5RC2
	 */
	public $highlightcode = 0;

	/**
	 * @var    string  RSS type
	 * @since  Kunena 1.0.6
	 */
	public $rss_type = 'topic';

	/**
	 * @var    string  RSS time limit
	 * @since  Kunena 1.0.6
	 */
	public $rss_timelimit = 'month';

	/**
	 * @var    integer  RSS limit
	 * @since  Kunena 1.0.6
	 */
	public $rss_limit = 100;

	/**
	 * @var    string  RSS included categories
	 * @since  Kunena 1.0.6
	 */
	public $rss_included_categories = '';

	/**
	 * @var    string  RSS excluded categories
	 * @since  Kunena 1.0.6
	 */
	public $rss_excluded_categories = '';

	/**
	 * @var    string  RSS specification
	 * @since  Kunena 1.0.6
	 */
	public $rss_specification = 'rss2.0';

	/**
	 * @var    boolean  RSS allow HTML
	 * @since  Kunena 1.0.6
	 */
	public $rss_allow_html = 1;

	/**
	 * @var    string  RSS author format
	 * @since  Kunena 1.0.6
	 */
	public $rss_author_format = 'name';

	/**
	 * @var    boolean  RSS author in title
	 * @since  Kunena 1.0.6
	 */
	public $rss_author_in_title = 1;

	/**
	 * @var    integer  RSS word count
	 * @since  Kunena 1.0.6
	 */
	public $rss_word_count = '0';

	/**
	 * @var    boolean  RSS old titles
	 * @since  Kunena 1.0.6
	 */
	public $rss_old_titles = 1;

	/**
	 * @var    integer  RSS cache
	 * @since  Kunena 1.0.6
	 */
	public $rss_cache = 900;

	/**
	 * @var    string  Default page
	 * @since  Kunena 1.0.6
	 */
	public $defaultpage = 'recent';

	/**
	 * @var    string  Default sort.  Description for the latest post first
	 * @since  Kunena 1.0.8
	 */
	public $default_sort = 'asc';

	/**
	 * @var    boolean  Search engine friendly URLs
	 * @since  Kunena 1.5.8
	 */
	public $sef = 1;

	/**
	 * @var    boolean  Showing For Guest
	 * @since  Kunena 1.6.0
	 */
	public $showimgforguest = 1;

	/**
	 * @var    boolean  Show file for guest
	 * @since  Kunena 1.6.0
	 */
	public $showfileforguest = 1;

	/**
	 * @var    integer  Major version number
	 * @since  Kunena 1.6.0
	 */
	public $pollnboptions = 4;

	/**
	 * @var    boolean  Pool allow one ore more time
	 * @since  Kunena 1.6.0
	 */
	public $pollallowvoteone = 1;

	/**
	 * @var    boolean  Poll enabled.  For poll integration
	 * @since  Kunena 1.6.0
	 */
	public $pollenabled = 1;

	/**
	 * @var    integer  Population poll count
	 * @since  Kunena 1.6.0
	 */
	public $poppollscount = 5;

	/**
	 * @var    boolean  Show population poll statistics
	 * @since  Kunena 1.6.0
	 */
	public $showpoppollstats = 1;

	/**
	 * @var    integer  Poll time by votes
	 * @since  Kunena 1.6.0
	 */
	public $polltimebtvotes = '00:15:00';

	/**
	 * @var    integer  Poll and votes by user
	 * @since  Kunena 1.6.0
	 */
	public $pollnbvotesbyuser = 100;

	/**
	 * @var    boolean  Poll result user list
	 * @since  Kunena 1.6.0
	 */
	public $pollresultsuserslist = 1;

	/**
	 * @var    boolean  Poll result user list
	 * @since  Kunena 1.6.0
	 */
	public $allow_user_edit_poll = 0;

	/**
	 * @var    integer  Max person text
	 * @since  Kunena 1.6.0
	 */
	public $maxpersotext = 50;

	/**
	 * @var    string  Ordering system
	 * @since  Kunena 1.6.0
	 */
	public $ordering_system = 'mesid';

	/**
	 * @var    string  Post date format
	 * @since  Kunena 1.6.0
	 */
	public $post_dateformat = 'ago';

	/**
	 * @var    string  Post date format hover
	 * @since  Kunena 1.6.0
	 */
	public $post_dateformat_hover = 'datetime';

	/**
	 * @var    boolean  Hide IP
	 * @since  Kunena 1.6.0
	 */
	public $hide_ip = 1;

	/**
	 * @var    string  Image types
	 * @since  Kunena 1.6.0
	 */
	public $imagetypes = 'jpg,jpeg,gif,png';

	/**
	 * @var    boolean  Check MIM types
	 * @since  Kunena 1.6.0
	 */
	public $checkmimetypes = 1;

	/**
	 * @var    string  Image MIME types
	 * @since  Kunena 1.6.0
	 */
	public $imagemimetypes = 'image/jpeg,image/jpg,image/gif,image/png';

	/**
	 * @var    integer  Image quality
	 * @since  Kunena 1.6.0
	 */
	public $imagequality = 50;

	/**
	 * @var    integer  Thumbnail height
	 * @since  Kunena 1.6.0
	 */
	public $thumbheight = 32;

	/**
	 * @var    integer  Thumbnail width
	 * @since  Kunena 1.6.0
	 */
	public $thumbwidth = 32;

	/**
	 * @var    string  Hide user profile info
	 * @since  1.6.0
	 */
	public $hideuserprofileinfo = 'put_empty';

	/**
	 * @var    boolean  Box ghost message
	 * @since  Kunena 1.6.0
	 */
	public $boxghostmessage = 0;

	/**
	 * @var    integer  User delete message
	 * @since  Kunena 1.6.0
	 */
	public $userdeletetmessage = 0;

	/**
	 * @var    integer  Latest category in
	 * @since  Kunena 1.6.0
	 */
	public $latestcategory_in = 1;

	/**
	 * @var    boolean  Topic icons
	 * @since  Kunena 1.6.0
	 */
	public $topicicons = 1;

	/**
	 * @var    boolean  Debug
	 * @since  Kunena 1.6.0
	 */
	public $debug = 0;

	/**
	 * @var    boolean  Category auto subscribe
	 * @since  Kunena 1.6.0
	 */
	public $catsautosubscribed = 0;

	/**
	 * @var    boolean  SHow ban reason
	 * @since  Kunena 1.6.0
	 */
	public $showbannedreason = 0;

	/**
	 * @var    boolean  Show thank you
	 * @since  Kunena 1.6.0
	 */
	public $showthankyou = 1;

	/**
	 * @var    boolean  Show population thank you statistics
	 * @since  Kunena 1.6.0
	 */
	public $showpopthankyoustats = 1;

	/**
	 * @var    integer  Population thank you count
	 * @since  Kunena 1.6.0
	 */
	public $popthankscount = 5;

	/**
	 * @var    boolean  Moderators see deleted topics
	 * @since  Kunena 1.6.0
	 */
	public $mod_see_deleted = 0;

	/**
	 * @var    string  BBCode image secure.  Allow only secure image extensions (jpg/gif/png)
	 * @since  Kunena 1.6.0
	 */
	public $bbcode_img_secure = 'text';

	/**
	 * @var    boolean  List category show moderators
	 * @since  Kunena 1.6.0
	 */
	public $listcat_show_moderators = 1;

	/**
	 * @var    boolean  Major version number
	 * @since  Kunena 1.6.1
	 */
	public $lightbox = 1;

	/**
	 * @var    integer  Show list time
	 * @since  Kunena 1.6.1
	 */
	public $show_list_time = 720;

	/**
	 * @var    integer  Show session type
	 * @since  Kunena 1.6.1
	 */
	public $show_session_type = 2;

	/**
	 * @var    integer  Show session start time
	 * @since  Kunena 1.6.1
	 */
	public $show_session_starttime = 1800;

	/**
	 * @var    boolean  User list allowed
	 * @since  Kunena 1.6.2
	 */
	public $userlist_allowed = 1;

	/**
	 * @var    integer  User list count users
	 * @since  Kunena 1.6.4
	 */
	public $userlist_count_users = 1;

	/**
	 * @var    boolean  Enable threaded layouts
	 * @since  Kunena 1.6.4
	 */
	public $enable_threaded_layouts = 0;

	/**
	 * @var    string  Category subscriptions
	 * @since  Kunena 1.6.4
	 */
	public $category_subscriptions = 'post';

	/**
	 * @var    string  Topic subscriptions
	 * @since  Kunena 1.6.4
	 */
	public $topic_subscriptions = 'every';

	/**
	 * @var    boolean  Public profile
	 * @since  Kunena 1.6.4
	 */
	public $pubprofile = 1;

	/**
	 * @var    integer  Thank you max
	 * @since  Kunena 1.6.5
	 */
	public $thankyou_max = 10;

	/**
	 * @var    integer  Email recipient count
	 * @since  Kunena 1.6.6
	 */
	public $email_recipient_count = 0;

	/**
	 * @var    string  Email recipient pricing
	 * @since  Kunena 1.6.6
	 */
	public $email_recipient_privacy = 'bcc';

	/**
	 * @var    string  Email visible address
	 * @since  Kunena 1.6.6
	 */
	public $email_visible_address = '';

	/**
	 * @var    integer  CAPTCHA post limit
	 * @since  Kunena 1.6.6
	 */
	public $captcha_post_limit = 0;

	/**
	 * @var    string  Image upload
	 * @since  Kunena 2.0.0
	 */
	public $image_upload = 'registered';

	/**
	 * @var    string  File upload
	 * @since  Kunena 2.0.0
	 */
	public $file_upload = 'registered';

	/**
	 * @var    string  Topic layout
	 * @since  Kunena 2.0.0
	 */
	public $topic_layout = 'flat';

	/**
	 * @var    boolean  Time to create page
	 * @since  Kunena 2.0.0
	 */
	public $time_to_create_page = 1;

	/**
	 * @var    boolean  Show image files in mange profile
	 * @since  Kunena 2.0.0
	 */
	public $show_imgfiles_manage_profile = 1;

	/**
	 * @var    boolean  Hold new users posts
	 * @since  Kunena 2.0.0
	 */
	public $hold_newusers_posts = 0;

	/**
	 * @var    boolean  Hold guest posts
	 * @since  Kunena 2.0.0
	 */
	public $hold_guest_posts = 0;

	/**
	 * @var    integer  Attachment limit
	 * @since  Kunena 2.0.0
	 */
	public $attachment_limit = 8;

	/**
	 * @var    boolean  Pickup category
	 * @since  Kunena 2.0.0
	 */
	public $pickup_category = 0;

	/**
	 * @var    string  Article display
	 * @since  Kunena 2.0.0
	 */
	public $article_display = 'intro';

	/**
	 * @var    boolean  Send emails
	 * @since  Kunena 2.0.0
	 */
	public $send_emails = 1;

	/**
	 * @var    boolean  Fallback english
	 * @since  Kunena 2.0.0
	 */
	public $fallback_english = 1;

	/**
	 * @var    boolean  Cache
	 * @since  Kunena 2.0.0
	 */
	public $cache = 1;

	/**
	 * @var    integer  Cache time
	 * @since  Kunena 2.0.0
	 */
	public $cache_time = 60;

	/**
	 * @var    integer  Ebay affiliate ID
	 * @since  Kunena 2.0.0
	 */
	public $ebay_affiliate_id = 5337089937;

	/**
	 * @var    boolean  IP tracking
	 * @since  Kunena 2.0.0
	 */
	public $iptracking = 1;

	/**
	 * @var    string  RSS feedburner URL
	 * @since  Kunena 2.0.3
	 */
	public $rss_feedburner_url = '';

	/**
	 * @var    boolean  Auto link
	 * @since  Kunena 3.0.0
	 */
	public $autolink = 1;

	/**
	 * @var    boolean  Access component
	 * @since  Kunena 3.0.0
	 */
	public $access_component = 1;

	/**
	 * @var    boolean  Statistic link allowed
	 * @since  Kunena 3.0.4
	 */
	public $statslink_allowed = 1;

	/**
	 * @var    boolean  Super admin user list
	 * @since  Kunena 3.0.6
	 */
	public $superadmin_userlist = 0;

	/**
	 * @var     boolean  Legacy URLs
	 * @since   Kunena 4.0.0
	 */
	public $legacy_urls = 1;

	/**
	 * @var     boolean  Attachment protection
	 * @since   Kunena 4.0.0
	 */
	public $attachment_protection = 0;

	/**
	 * @var     boolean  Category icons
	 * @since   Kunena 4.0.0
	 */
	public $categoryicons = 1;

	/**
	 * @var     boolean  Avatar crop
	 * @since   Kunena 4.0.0
	 */
	public $avatarcrop = 0;

	/**
	 * @var     boolean  User can report himself
	 * @since   Kunena 4.0.0
	 */
	public $user_report = 1;

	/**
	 * @var     integer  Search time
	 * @since   Kunena 4.0.0
	 */
	public $searchtime = 365;

	/**
	 * @var     boolean  Teaser
	 * @since   Kunena 4.0.0
	 */
	public $teaser = 0;

	/**
	 * @var    boolean  Define ebay widget language
	 * @since  Kunena 3.0.7
	 */
	public $ebay_language = 0;

	/**
	 * @var    string  Define ebay Api key to be allowed to display ebay widget
	 * @since  Kunena 3.0.7
	 */
	public $ebay_api_key = '';

	/**
	 * @var     string  Define twitter API consumer key
	 * @since   Kunena 4.0.0
	 */
	public $twitter_consumer_key = '';

	/**
	 * @var     string  Define twitter API consumer secret
	 * @since   Kunena 4.0.0
	 */
	public $twitter_consumer_secret = '';

	/**
	 * @var     boolean  Allow to define if the user can change the subject of topic on replies
	 * @since   Kunena 4.0.0
	 */
	public $allow_change_subject = 1;

	/**
	 * @var     integer  Max Links limit
	 * @since   Kunena 4.0.0
	 */
	public $max_links = 6;

	/**
	 * @var    boolean  Read Only State
	 * @since  Kunena 5.0.0
	 */
	public $read_only = 0;

	/**
	 * @var    boolean  Rating integration
	 * @since  Kunena 5.0.0
	 */
	public $ratingenabled = 0;

	/**
	 * @var    boolean  Allow to prevent posting if the subject of topic contains URL
	 * @since  Kunena 5.0.0
	 */
	public $url_subject_topic = 0;

	/**
	 * @var    boolean Allow to enable log to save moderation actions
	 * @since  Kunena 5.0.0
	 */
	public $log_moderation = 0;

	/**
	 * @var    integer Define the number of characters from start when shorter then attachments filename
	 * @since  Kunena 5.0.0
	 */
	public $attach_start = 0;

	/**
	 * @var    integer Define the number of characters from end when shorten then attachments filename
	 * @since  Kunena 5.0.0
	 */
	public $attach_end = 14;

	/**
	 * @var    string Define the google maps API key
	 * @since  Kunena 5.0.0
	 */
	public $google_map_api_key = '';

	/**
	 * @var    boolean Allow to remove utf8 characters from filename of attachments
	 * @since  Kunena 5.0.0
	 */
	public $attachment_utf8 = 1;

	/**
	 * @var    boolean Allow to auto-embded soundcloud item when you put just the URL in a message
	 * @since  Kunena 5.0.0
	 */
	public $autoembedsoundcloud = 1;

	/**
	 * @var    string to define the image location
	 * @since  Kunena 5.0.2
	 */
	public $emailheader = 'media/kunena/email/hero-wide.png';

	/**
	 * @var    boolean show user status
	 * @since  Kunena 5.0.3
	 */
	public $user_status = 1;

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
	public $plain_email = 0;

	/**
	 * @var    boolean
	 * @since  Kunena 5.0.13
	 */
	public $moderator_permdelete = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.0.4
	 */
	public $avatartypes = 'gif, jpeg, jpg, png';

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $smartlinking = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $defaultavatar = 'nophoto.png';

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $defaultavatarsmall = 's_nophoto.png';

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $stopforumspam_key = '';

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $quickreply = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.0
	 */
	public $avataredit = 0;

	/**
	 * @var    string
	 * @since  Kunena 5.1.0
	 */
	public $activemenuitem = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $mainmenu_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $home_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $index_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $moderators_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $topiclist_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $misc_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $profile_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $search_id = '';

	/**
	 * @var    integer
	 * @since  Kunena 5.1.0
	 */
	public $custom_id = '';

	/**
	 * @var   integer
	 * @since  Kunena 5.1.0
	 */
	public $avatar_type = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.1
	 */
	public $sef_redirect = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.1
	 */
	public $allow_edit_poll = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.2
	 */
	public $use_system_emails = 0;

	/**
	 * @var    boolean  Auto embed instagram
	 * @since  Kunena 5.1.5
	 */
	public $autoembedinstagram = 1;

	/**
	 * @var    boolean
	 * @since  Kunena 5.1.14
	 */
	public $disable_re = 0;

	/**
	 * @var    boolean  utm source
	 * @since  Kunena 1.0.5
	 */
	public $utm_source = 0;

	/**
	 * @var    Registry
	 * @since  Kunena 6.0
	 */
	public $plugins;

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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance()
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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load()
	{
		$db    = Factory::getDBO();
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
	public function bind($properties)
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
	public function check()
	{
		// Add anything that requires validation

		// Need to have at least one per page of these
		$this->messages_per_page        = max($this->messages_per_page, 1);
		$this->messages_per_page_search = max($this->messages_per_page_search, 1);
		$this->threads_per_page         = max($this->threads_per_page, 1);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function save()
	{
		$db = Factory::getDBO();

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
		CacheHelper::clear();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function reset()
	{
		$instance = new KunenaConfig;
		$this->bind($instance->getProperties());
	}

	/**
	 * @internal
	 *
	 * @param   string  $name  Name of the plugin
	 *
	 * @return  Registry
	 *
	 * @since   Kunena 6.0
	 */
	public function getPlugin($name)
	{
		return isset($this->plugins[$name]) ? $this->plugins[$name] : new Registry;
	}

	/**
	 * Email set for the configuration
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getEmail()
	{
		$email = $this->get('email');

		return !empty($email) ? $email : Factory::getApplication()->get('mailfrom', '');
	}
}
