<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaConfig
 */
class KunenaConfig extends JObject {

	// New in Kunena 1.5.2: $id for JoomFish support
	public $id = 0;
	public $board_title = 'Kunena';
	public $email = '';
	public $board_offline = 0;
	public $offline_message = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>";
	public $enablerss = 1;
	public $threads_per_page = 20;
	public $messages_per_page = 6;
	public $messages_per_page_search = 15;
	public $showhistory = 1;
	public $historylimit = 6;
	public $shownew = 1;
	public $disemoticons = 0;
	public $template = 'blue_eagle';
	public $showannouncement = 1;
	public $avataroncat = 0;
	public $catimagepath = 'category_images';
	public $showchildcaticon = 1;
	public $rtewidth = 450;
	public $rteheight = 300;
	public $enableforumjump = 1;
	public $reportmsg = 1;
	public $username = 1;
	public $askemail = 0;
	public $showemail = 0;
	public $showuserstats = 1;
	public $showkarma = 1;
	public $useredit = 1;
	public $useredittime = 0;
	public $useredittimegrace = 600;
	public $editmarkup = 1;
	public $allowsubscriptions = 1;
	public $subscriptionschecked = 1;
	public $allowfavorites = 1;
	public $maxsubject = 50;
	public $maxsig = 300;
	public $regonly = 0;
	public $pubwrite = 0;
	public $floodprotection = 0;
	public $mailmod = 0;
	public $mailadmin = 0;
	public $captcha = 0;
	public $mailfull = 1;
	public $allowavatarupload = 1;
	public $allowavatargallery = 1;
	public $avatarquality = 75;
	public $avatarsize = 2048;
	public $imageheight = 800;
	public $imagewidth = 800;
	public $imagesize = 150;
	public $filetypes = 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2';
	public $filesize = 120;
	public $showranking = 1;
	public $rankimages = 1;
	public $userlist_rows = 30;
	public $userlist_online = 1;
	public $userlist_avatar = 1;
	public $userlist_name = 1;
	public $userlist_posts = 1;
	public $userlist_karma = 1;
	public $userlist_email = 0;
	public $userlist_joindate = 1;
	public $userlist_lastvisitdate = 1;
	public $userlist_userhits = 1;
	public $latestcategory = '';
	public $showstats = 1;
	public $showwhoisonline = 1;
	public $showgenstats = 1;
	public $showpopuserstats = 1;
	public $popusercount = 5;
	public $showpopsubjectstats = 1;
	public $popsubjectcount = 5;
	public $usernamechange = 0;
	// New 1.0.5 config variables
	// bbcode options
	public $showspoilertag = 1;
	public $showvideotag = 1;
	public $showebaytag = 1;
	public $trimlongurls = 1;
	public $trimlongurlsfront = 40;
	public $trimlongurlsback = 20;
	public $autoembedyoutube = 1;
	public $autoembedebay = 1;
	public $ebaylanguagecode = 'en-us';
	public $sessiontimeout = 1800; // in seconds
	// New 1.0.5RC2 config variables
	public $highlightcode = 0;
	// New 1.6 rss config vars
	public $rss_type = 'topic';
	public $rss_timelimit = 'month';
	public $rss_limit = 100;
	public $rss_included_categories = '';
	public $rss_excluded_categories = '';
	public $rss_specification = 'rss2.0';
	public $rss_allow_html = 1;
	public $rss_author_format = 'name';
	public $rss_author_in_title = 1;
	public $rss_word_count = '0';
	public $rss_old_titles = 1;
	public $rss_cache = 900;
	public $defaultpage = 'recent';
	// New 1.0.8 config variables
	public $default_sort = 'asc'; // 'desc' for latest post first
	// New 1.5.8 config variables
	public $sef = 1;
	// New for 1.6 -> Hide images and files for guests
	public $showimgforguest = 1;
	public $showfileforguest = 1;
	//New for 1.6 -> Poll
	public $pollnboptions = 4; //For poll integration, set the number maximum of options
	public $pollallowvoteone = 1; //For poll integration, set if yes or no the user can vote one or more time for a poll
	public $pollenabled = 1; //For poll integration, for disable the poll
	public $poppollscount = 5;
	public $showpoppollstats = 1;
	public $polltimebtvotes = '00:15:00';
	public $pollnbvotesbyuser = 100;
	public $pollresultsuserslist = 1;
	// New for 1.6 -> Max length for personnal text
	public $maxpersotext = 50;
	// New for 1.6 -> Choose ordering system
	public $ordering_system = 'mesid';
	// New for 1.6 -> dateformat
	public $post_dateformat = 'ago'; // See KunenaDate
	public $post_dateformat_hover = 'datetime'; // See KunenaDate
	// New for 1.6 -> hide IP
	public $hide_ip = 1;
	// New for 1.6 -> image file types
	public $imagetypes = 'jpg,jpeg,gif,png';
	public $checkmimetypes = 1;
	public $imagemimetypes = 'image/jpeg,image/jpg,image/gif,image/png';
	public $imagequality = 50;
	public $thumbheight = 32;
	public $thumbwidth = 32;
	// New for 1.6: hide profile info when user is deleted from joomla!
	public $hideuserprofileinfo = 'put_empty';
	//New for 1.6: choose if you want that ghost message box checked by default
	public $boxghostmessage = 0;
	public $userdeletetmessage = 0;
	public $latestcategory_in = 1;
	public $topicicons = 1;
	public $debug = 0;
	public $catsautosubscribed = 0;
	public $showbannedreason = 0;
	public $version_check = 1;
	//New for 1.6: choose if you want a Thank you function
	public $showthankyou = 1;
	public $showpopthankyoustats = 1;
	public $popthankscount = 5;
	//New for 1.6: choose to allow moderators to see deleted messages
	public $mod_see_deleted = 0;
	//New for 1.6: allow only secure image extensions (jpg/gif/png) in IMG tag
	public $bbcode_img_secure = 'text';
	public $listcat_show_moderators = 1;
	//New for 1.6.1: allow the admin to disable lightbox
	public $lightbox = 1;
	//New for 1.6.2: choose the time since which to show the topics
	public $show_list_time = 720;
	//New for 1.6.2: configuration option to show online users by minutes or session time
	public $show_session_type = 0;
	public $show_session_starttime = 0;
	// New for 1.6.2: configuration option to set all users or only registred users to see userlist
	public $userlist_allowed = 0;
	// New for 1.6.4
	public $userlist_count_users = 1;
	public $enable_threaded_layouts = 0;
	public $category_subscriptions = 'post';
	public $topic_subscriptions = 'every';
	public $pubprofile = 1;
	// New for 1.6.5
	public $thankyou_max = 10;
	// New for 1.6.6
	public $email_recipient_count = 0;
	public $email_recipient_privacy = 'bcc';
	public $email_visible_address = '';
	public $captcha_post_limit = 0;
	public $recaptcha_publickey = '';
	public $recaptcha_privatekey = '';
	public $recaptcha_theme = 'white';
	// New for 2.0.0
	public $keywords = 1;
	public $userkeywords = 0;
	public $image_upload = 'registered';
	public $file_upload = 'registered';
	public $topic_layout = 'flat';
	public $time_to_create_page = 1;
	public $show_imgfiles_manage_profile = 1;
	public $hold_newusers_posts = 0;
	public $hold_guest_posts = 0;
	public $attachment_limit = 8;
	public $pickup_category = 0;
	public $article_display = 'intro';
	public $send_emails = 1;
	public $stopforumspam_key = '';
	public $fallback_english = 1;
	public $cache = 1;
	public $cache_time = 60; // 1 minute
	public $ebay_affiliate_id = 5337089937;
	public $iptracking = 1;
	// New for 2.0.3
	public $rss_feedburner_url = '';
	// New for 3.0.0
	public $autolink = 1;
	public $access_component = 1;
	// New for 3.0.4
	public $statslink_allowed = 1;
	// New for 3.0.6
	public $superadmin_userlist = 0;

	public function __construct() {
		parent::__construct ();
	}

	public static function getInstance() {
		static $instance = null;
		if (! $instance) {
			$instance = new KunenaConfig ();
			$instance->load ();
		}
		return $instance;
	}

	/**
	 * @param mixed $properties
	 */
	public function bind($properties) {
		$this->setProperties($properties);

		// Disable some experimental features
		$this->keywords = 0;
		$this->userkeywords = 0;
	}

	public function save() {
		$db = JFactory::getDBO ();

		// Perform custom validation of config data before we write it.
		$this->check ();

		// Get current configuration
		$params = $this->getProperties();
		unset($params['id']);

		$db->setQuery ( "REPLACE INTO #__kunena_configuration SET id=1, params={$db->quote(json_encode($params))}");
		$db->query ();
		KunenaError::checkDatabaseError ();

		// Clear cache.
		KunenaCacheHelper::clear();
	}

	public function reset() {
		$instance = new KunenaConfig ();
		$this->bind($instance->getProperties());
	}

	/**
	 * Load config settings from database table.
	 * @param null $userinfo Not used.
	 */
	public function load($userinfo = null) {
		$db = JFactory::getDBO ();
		$db->setQuery ( "SELECT * FROM #__kunena_configuration WHERE id=1" );
		$config = $db->loadAssoc ();
		KunenaError::checkDatabaseError ();

		if ($config) {
			$params = json_decode($config['params']);
			$this->bind ($params);
		}

		// Perform custom validation of config data before we let anybody access it.
		$this->check ();

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$plugins = array();
		$dispatcher->trigger('onKunenaGetConfiguration', array('kunena.configuration', &$plugins));
		$this->plugins = array();
		foreach ($plugins as $name => $registry) {
			if ($name == '38432UR24T5bBO6') $this->bind($registry->toArray());
			elseif ($name && $registry instanceof JRegistry) $this->plugins[$name] = $registry;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return JRegistry
	 *
	 * @internal
	 */
	public function getPlugin($name) {
		return isset($this->plugins[$name]) ? $this->plugins[$name] : new JRegistry();
	}

	public function check() {
		// Add anything that requires validation

		// Need to have at least two per page of these
		$this->messages_per_page = max ( $this->messages_per_page, 2 );
		$this->messages_per_page_search = max ( $this->messages_per_page_search, 2 );
		$this->threads_per_page = max ( $this->threads_per_page, 2 );

	}

	/**
	 * @return string
	 */
	public function getEmail() {
		$email = $this->get('email');
		return !empty($email) ? $email : JFactory::getApplication()->getCfg('mailfrom', '');
	}
}
