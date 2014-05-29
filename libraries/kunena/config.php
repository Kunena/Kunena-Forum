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
	public $id = 0; // hidden
	public $board_title = 'Kunena'; // input, text
	public $email = ''; // input, email
	public $board_offline = 0; // select, boolean
	public $offline_message = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>"; // input, text
	public $enablerss = 1; // select, boolean
	public $threads_per_page = 20; // input, number
	public $messages_per_page = 6; // input, number
	public $messages_per_page_search = 15; // input, number
	public $showhistory = 1; // select, boolean
	public $historylimit = 6; // input, number
	public $shownew = 1; // select, boolean
	public $disemoticons = 0; // select, boolean
	public $template = 'blue_eagle';
	public $showannouncement = 1; // select, boolean
	public $avataroncat = 0; // select, boolean
	public $catimagepath = 'category_images'; // input, text
	public $showchildcaticon = 1; // select, boolean
	public $rtewidth = 450; // input, number
	public $rteheight = 300; // input, number
	public $enableforumjump = 1; // select, boolean
	public $reportmsg = 1; // select, boolean
	public $username = 1; // select, boolean
	public $askemail = 0; // select, boolean
	public $showemail = 0; // select, boolean
	public $showuserstats = 1; // select, boolean
	public $showkarma = 1; // select, boolean
	public $useredit = 1; // select, boolean
	public $useredittime = 0; // input, number
	public $useredittimegrace = 600; // input, number, time
	public $editmarkup = 1; // select, boolean
	public $allowsubscriptions = 1; // select, boolean
	public $subscriptionschecked = 1; // select, boolean
	public $allowfavorites = 1; // select, boolean
	public $maxsubject = 50; // input, number
	public $maxsig = 300; // input, number
	public $regonly = 0; // select, boolean
	public $pubwrite = 0; // select, boolean
	public $floodprotection = 0; // select, boolean
	public $mailmod = 0; // select, selection
	public $mailadmin = 0; // select, selection
	public $captcha = 0; // select, boolean
	public $mailfull = 1; // select, selection
	public $allowavatarupload = 1; // select, boolean
	public $allowavatargallery = 1; // select, boolean
	public $avatarquality = 75; // input, number
	public $avatarsize = 2048; // input, number
	public $imageheight = 800; // input, number
	public $imagewidth = 800; // input, number
	public $imagesize = 150; // input, number
	public $filetypes = 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2'; // input, text
	public $filesize = 120; // input, number
	public $showranking = 1; // select, boolean
	public $rankimages = 1; // select, boolean
	public $userlist_rows = 30; // input, number
	public $userlist_online = 1; // select, boolean
	public $userlist_avatar = 1; // select, boolean
	public $userlist_name = 1; // select, boolean
	public $userlist_posts = 1; // select, boolean
	public $userlist_karma = 1; // select, boolean
	public $userlist_email = 0; // select, boolean
	public $userlist_joindate = 1; // select, boolean
	public $userlist_lastvisitdate = 1; // select, boolean
	public $userlist_userhits = 1; // select, boolean
	public $latestcategory = ''; // select, integer multiple
	public $showstats = 1; // select, boolean
	public $showwhoisonline = 1; // select, boolean
	public $showgenstats = 1; // select, boolean
	public $showpopuserstats = 1; // select, boolean
	public $popusercount = 5; // input, number
	public $showpopsubjectstats = 1; // select, boolean
	public $popsubjectcount = 5; // input, number
	// New 1.0.5 config variables
	// bbcode options
	public $showspoilertag = 1; // select, boolean
	public $showvideotag = 1; // select, boolean
	public $showebaytag = 1; // select, boolean
	public $trimlongurls = 1; // select, boolean
	public $trimlongurlsfront = 40; // input, number
	public $trimlongurlsback = 20; // input, number
	public $autoembedyoutube = 1; // select, boolean
	public $autoembedebay = 1; // select, boolean
	public $ebaylanguagecode = 'en-us'; // input, text
	public $sessiontimeout = 1800; // in seconds // input, number, time
	// New 1.0.5RC2 config variables
	public $highlightcode = 0; // select, boolean
	// New 1.6 rss config vars
	public $rss_type = 'topic'; // select, selection
	public $rss_timelimit = 'month'; // select, selection
	public $rss_limit = 100; // input, number
	public $rss_included_categories = ''; // input, number
	public $rss_excluded_categories = ''; // input, number
	public $rss_specification = 'rss2.0'; // select, selection
	public $rss_allow_html = 1;  // select, boolean
	public $rss_author_format = 'name'; // select, selection
	public $rss_author_in_title = 1;  // select, boolean
	public $rss_word_count = '0'; // select, selection
	public $rss_old_titles = 1; // select, boolean
	public $rss_cache = 900; // input, number
	public $defaultpage = 'recent'; // select, boolean
	// New 1.0.8 config variables
	public $default_sort = 'asc'; // 'desc' for latest post first, select, boolean
	// New 1.5.8 config variables
	public $sef = 1; // select, boolean
	// New for 1.6 -> Hide images and files for guests
	public $showimgforguest = 1; // select, boolean
	public $showfileforguest = 1; // select, boolean
	//New for 1.6 -> Poll
	public $pollnboptions = 4; //For poll integration, set the number maximum of options, input, number
	public $pollallowvoteone = 1; //For poll integration, set if yes or no the user can vote one or more time for a poll, select, boolean
	public $pollenabled = 1; //For poll integration, for disable the poll, select, boolean
	public $poppollscount = 5; // input, number
	public $showpoppollstats = 1; // select, boolean
	public $polltimebtvotes = '00:15:00'; // input, time
	public $pollnbvotesbyuser = 100; // input, number
	public $pollresultsuserslist = 1; // select, boolean
	// New for 1.6 -> Max length for personnal text
	public $maxpersotext = 50; // input, number
	// New for 1.6 -> Choose ordering system
	public $ordering_system = 'mesid'; // select, selection
	// New for 1.6 -> dateformat
	public $post_dateformat = 'ago'; // See KunenaDate, select, selection
	public $post_dateformat_hover = 'datetime'; // See KunenaDate, select, selection
	// New for 1.6 -> hide IP
	public $hide_ip = 1; // select, boolean
	// New for 1.6 -> image file types
	public $imagetypes = 'jpg,jpeg,gif,png'; // textbox, string
	public $checkmimetypes = 1; // select, boolean
	public $imagemimetypes = 'image/jpeg,image/jpg,image/gif,image/png'; // textbox, string
	public $imagequality = 50; // input, number
	public $thumbheight = 32; // input, number
	public $thumbwidth = 32; // input, number
	// New for 1.6: hide profile info when user is deleted from joomla!
	public $hideuserprofileinfo = 'put_empty';
	//New for 1.6: choose if you want that ghost message box checked by default
	public $boxghostmessage = 0; // select, selection
	public $userdeletetmessage = 0; // select, selection
	public $latestcategory_in = 1; // select, boolean
	public $topicicons = 1; // select, boolean
	public $debug = 0; // select, boolean
	public $catsautosubscribed = 0; // select, boolean
	public $showbannedreason = 0; // select, boolean
	public $version_check = 1; // select, boolean
	//New for 1.6: choose if you want a Thank you function
	public $showthankyou = 1; // select, boolean
	public $showpopthankyoustats = 1; // select, boolean
	public $popthankscount = 5; // input, number
	//New for 1.6: choose to allow moderators to see deleted messages
	public $mod_see_deleted = 0; // select, boolean
	//New for 1.6: allow only secure image extensions (jpg/gif/png) in IMG tag
	public $bbcode_img_secure = 'text'; // select, selection
	public $listcat_show_moderators = 1; // select, boolean
	//New for 1.6.1: allow the admin to disable lightbox
	public $lightbox = 1; // select, boolean
	//New for 1.6.2: choose the time since which to show the topics
	public $show_list_time = 720; // select, selection
	//New for 1.6.2: configuration option to show online users by minutes or session time
	public $show_session_type = 0; // select, selection
	public $show_session_starttime = 0; // select, selection
	// New for 1.6.2: configuration option to set all users or only registred users to see userlist
	public $userlist_allowed = 0; // select, boolean
	// New for 1.6.4
	public $userlist_count_users = 1; // select, selection
	public $enable_threaded_layouts = 0; // select, boolean
	public $category_subscriptions = 'post'; // select, selection
	public $topic_subscriptions = 'every'; // select, selection
	public $pubprofile = 1; // select, boolean
	// New for 1.6.5
	public $thankyou_max = 10; // input, number
	// New for 1.6.6
	public $email_recipient_count = 0; //select, integerlist
	public $email_recipient_privacy = 'bcc'; // select, selection
	public $email_visible_address = ''; // input, email
	public $captcha_post_limit = 0; // input, number
	public $recaptcha_publickey = ''; // input, number
	public $recaptcha_privatekey = ''; // input, number
	public $recaptcha_theme = 'white'; // select, selection
	// New for 2.0.0
	public $keywords = 1; // select, boolean
	public $userkeywords = 0; // select, boolean
	public $image_upload = 'registered'; // select, selection
	public $file_upload = 'registered'; // select, selection
	public $topic_layout = 'flat'; // select, selection
	public $time_to_create_page = 1; // select, boolean
	public $show_imgfiles_manage_profile = 1; // select, boolean
	public $hold_newusers_posts = 0; // select, boolean
	public $hold_guest_posts = 0; // select, boolean
	public $attachment_limit = 8; // input, number
	public $pickup_category = 0; // select, boolean
	public $article_display = 'intro'; // select, selection
	public $send_emails = 1; // select, boolean
	public $stopforumspam_key = ''; // input, text
	public $fallback_english = 1; // select, boolean
	public $cache = 1; // select, boolean
	public $cache_time = 60; // 1 minute  input, time
	public $ebay_affiliate_id = 5337089937; // input, text
	public $iptracking = 1; // select, boolean
	// New for 2.0.3
	public $rss_feedburner_url = ''; // input, text
	// New for 3.0.0
	public $autolink = 1; // select, boolean
	public $access_component = 1; // select, boolean
	// New for 3.0.4
	public $statslink_allowed = 1; // select, boolean
	// New for 3.1.0
	public $legacy_urls = 1; // select, boolean
	public $attachment_protection = 0; // select, boolean
	public $categoryicons = 1; // select, boolean
	public $avatarresizemethod = 1; // select, selection
	public $avatarcrop = 0; // select, boolean
	public $superadmin_userlist = 0; // select, boolean

	public function __construct() {
		parent::__construct ();
	}

	public static function getInstance() {
		static $instance = null;

		if (! $instance) {
			/** @var JCache|JCacheController $cache */
			$cache = JFactory::getCache('com_kunena', 'output');
			$instance = $cache->get('configuration', 'com_kunena');
			if (!$instance) {
				$instance = new KunenaConfig();
				$instance->load();
			}
			$cache->store($instance, 'configuration', 'com_kunena');
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
