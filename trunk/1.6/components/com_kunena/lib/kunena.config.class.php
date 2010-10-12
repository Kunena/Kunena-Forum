<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

require_once (JPATH_ROOT . DS . 'administrator/components/com_kunena/libraries/api.php');
kimport ( 'error' );

abstract class CKunenaConfigBase {
	public function __construct() {
		$this->_db = JFactory::getDBO ();
	}

	//
	// The following functions MUST be overridden in derived classes
	//
	abstract public function &getInstance();
	abstract public function GetClassVars();
	abstract protected function GetConfigTableName();

	// Override this to perform certain custom validations of the config data
	// Is being executed before save and after load
	abstract public function ValidateConfig();

	//
	//  binds a named array/hash to this object
	//  @param array $hash named array
	//  @return null|string null is operation was satisfactory, otherwise returns an error
	//
	protected function bind($array, $ignore = '') {
		if (! is_array ( $array )) {
			$this->_error = strtolower ( get_class ( $this ) ) . '::bind failed.';
			return false;
		} else {
			foreach ( $array as $k => $v ) {
				if (isset ( $this->$k ))
					$this->$k = $v;
			}
		}

		return true;
	}

	//
	// Create the config table for Kunena and add initial default values
	//
	public function create() {
		$fields = array ();

		// Perform custom validation of config data before we write it.
		$this->ValidateConfig ();

		$vars = $this->GetClassVars ();

		foreach ( $vars as $name => $value ) {
			//
			// Need to provide ability to override certain settings
			// in derived class without the need to recode this entire function
			//


			// Exclude private class variables
			if ($name != '_db') {
				switch (gettype ( $value )) {
					case 'integer' :
						$fields [] = "`$name` INTEGER NULL";

						break;

					case 'string' :
						$fields [] = "`$name` TEXT NULL";

						break;
				}
			}
		}

		$collation = $this->_db->getCollation ();
		if (!strstr($collation, 'utf8')) $collation = 'utf8_general_ci';
		$this->_db->setQuery ( "CREATE TABLE {$this->GetConfigTableName ()} (" . implode ( ', ', $fields ) . ", PRIMARY KEY (`id`) ) DEFAULT CHARACTER SET utf8 COLLATE {$collation}" );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Insert current Settings
		$vars = get_object_vars ( $this ); // for the actual values we must not use the class vars funtion
		$vars ['id'] = 1;
		$fields = array ();

		foreach ( $vars as $name => $value ) {
			// Exclude internal class vars e.g. _db
			if ($name [0] != '_' && array_key_exists ( $name, $this->GetClassVars () )) {
				$fields [] = "{$this->_db->nameQuote($name)}={$this->_db->quote($value)}";
			}
		}

		$this->_db->setQuery ( "INSERT INTO " . $this->GetConfigTableName () . " SET " . implode ( ', ', $fields ) );
		$this->_db->query ();
		KunenaError::checkDatabaseError ();
	}

	//
	// Create a backup of most current config table
	//
	public function backup() {
		// remove old backup if one exists
		$this->_db->setQuery ( "DROP TABLE IF EXISTS " . $this->GetConfigTableName () . "_backup" );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		$this->_db->setQuery ( "CREATE TABLE " . $this->GetConfigTableName () . "_backup SELECT * FROM " . $this->GetConfigTableName () );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError ())
			return;
	}

	//
	// Remove the current config table
	//
	public function remove() {
		$this->_db->setQuery ( "DROP TABLE IF EXISTS " . $this->GetConfigTableName () );
		$this->_db->query ();
		KunenaError::checkDatabaseError ();
	}

	//
	// Load config settings from database table
	//
	public function load($userinfo = null) {
		$this->_db->setQuery ( "SELECT * FROM " . $this->GetConfigTableName () );
		$config = $this->_db->loadAssoc ();
		if ($this->_db->getErrorNum ()) {
			$app = JFactory::getApplication ();
			$app->enqueueMessage ( 'Kunena ' . JText::_ ( 'COM_KUNENA_INTERNAL_ERROR_CONFIG' ), 'error' );
			return;
		}

		if ($config != null) {
			$this->bind ( $config );
		}

		// Perform custom validation of config data before we let anybody access it.
		$this->ValidateConfig ();
	}
}

class CKunenaConfig extends CKunenaConfigBase {
	// All vars MUST BE LOWER CASE!
	// New in Kunena 1.5.2: $id for JoomFish support
	var $id = 0;
	var $board_title = 'Kunena';
	var $email = 'change@me.com';
	var $board_offline = 0;
	var $board_ofset = '0.00'; // DEPRECATED, needed in installer
	var $offline_message = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>";
	var $enablerss = 1;
	var $enablepdf = 1;
	var $threads_per_page = 20;
	var $messages_per_page = 6;
	var $messages_per_page_search = 15;
	var $showhistory = 1;
	var $historylimit = 6;
	var $shownew = 1;
	var $jmambot = 0;
	var $disemoticons = 0;
	var $template = 'default';
	var $showannouncement = 1;
	var $avataroncat = 0;
	var $catimagepath = 'category_images/';
	var $showchildcaticon = 1;
	var $annmodid = '62';
	var $rtewidth = 450;
	var $rteheight = 300;
	var $enableforumjump = 1;
	var $reportmsg = 1;
	var $username = 1;
	var $askemail = 0;
	var $showemail = 0;
	var $showuserstats = 1;
	var $showkarma = 1;
	var $useredit = 1;
	var $useredittime = 0;
	var $useredittimegrace = 600;
	var $editmarkup = 1;
	var $allowsubscriptions = 1;
	var $subscriptionschecked = 1;
	var $allowfavorites = 1;
	var $maxsubject = 50;
	var $maxsig = 300;
	var $regonly = 0;
	var $changename = 0;
	var $pubwrite = 0;
	var $floodprotection = 0;
	var $mailmod = 0;
	var $mailadmin = 0;
	var $captcha = 0;
	var $mailfull = 1;
	var $allowavatar = 1; // DEPRECATED, needed in installer
	var $allowavatarupload = 1;
	var $allowavatargallery = 1;
	var $imageprocessor = 'gd2';
	var $avatarquality = 65;
	var $avatarsize = 2048;
	var $allowimageupload = 0;
	var $allowimageregupload = 1;
	var $imageheight = 800;
	var $imagewidth = 800;
	var $imagesize = 150;
	var $allowfileupload = 0;
	var $allowfileregupload = 1;
	var $filetypes = 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2';
	var $filesize = 120;
	var $showranking = 1;
	var $rankimages = 1;
	var $avatar_src = 'fb'; // DEPRECATED, needed in installer
	var $fb_profile = 'fb'; // DEPRECATED, needed in installer
	var $pm_component = 'no'; // DEPRECATED, needed in installer
	var $userlist_rows = 30;
	var $userlist_online = 1;
	var $userlist_avatar = 1;
	var $userlist_name = 1;
	var $userlist_username = 1;
	var $userlist_posts = 1;
	var $userlist_karma = 1;
	var $userlist_email = 0;
	var $userlist_usertype = 0;
	var $userlist_joindate = 1;
	var $userlist_lastvisitdate = 1;
	var $userlist_userhits = 1;
	var $latestcategory = '';
	var $showstats = 1;
	var $showwhoisonline = 1;
	var $showgenstats = 1;
	var $showpopuserstats = 1;
	var $popusercount = 5;
	var $showpopsubjectstats = 1;
	var $popsubjectcount = 5;
	var $usernamechange = 0;
	var $rules_infb = 1; // retired in 1.6 controlled via joommla menu
	var $rules_cid = 1;
	var $help_infb = 1; // retired in 1.6 controlled via joommla menu
	var $help_cid = 1;
	// New 1.0.5 config variables
	// bbcode options
	var $showspoilertag = 1;
	var $showvideotag = 1;
	var $showebaytag = 1;
	var $trimlongurls = 1;
	var $trimlongurlsfront = 40;
	var $trimlongurlsback = 20;
	var $autoembedyoutube = 1;
	var $autoembedebay = 1;
	var $ebaylanguagecode = 'en-us';
	var $fbsessiontimeout = 1800; // in seconds
	// New 1.0.5RC2 config variables
	var $highlightcode = 0;
	// New 1.6 rss config vars
	var $rss_type = 'topic';
	var $rss_timelimit = 'month';
	var $rss_limit = 100;
	var $rss_included_categories = '';
	var $rss_excluded_categories = '';
	var $rss_specification = 'rss2.0';
	var $rss_allow_html = 1;
	var $rss_author_format = 'name';
	var $rss_author_in_title = 1;
	var $rss_word_count = '0';
	var $rss_old_titles = 1;
	var $rss_cache = 900;
	var $fbdefaultpage = 'recent';
	// New 1.0.8 config variables
	var $default_sort = 'asc'; // 'desc' for latest post first
	// New 1.5.7 config variables
	var $alphauserpointsnumchars = 0; // Integration feature for AlphaUserPoints component
	// New 1.5.8 config variables
	var $sef = 1;
	var $sefcats = 0;
	var $sefutf8 = 0;
	// New for 1.6 -> Hide images and files for guests
	var $showimgforguest = 1;
	var $showfileforguest = 1;
	//New for 1.6 -> Poll
	var $pollnboptions = 4; //For poll integration, set the number maximum of options
	var $pollallowvoteone = 1; //For poll integration, set if yes or no the user can vote one or more time for a poll
	var $pollenabled = 1; //For poll integration, for disable the poll
	var $poppollscount = 5;
	var $showpoppollstats = 1;
	var $polltimebtvotes = '00:15:00';
	var $pollnbvotesbyuser = 100;
	var $pollresultsuserslist = 1;
	// New for 1.6 -> Max length for personnal text
	var $maxpersotext = 50;
	// New for 1.6 -> Choose ordering system
	var $ordering_system = 'mesid';
	// New for 1.6 -> dateformat
	var $post_dateformat = 'ago'; // See CKunenaTimeformat::showDate()
	var $post_dateformat_hover = 'datetime'; // See CKunenaTimeformat::showDate()
	// New for 1.6 -> hide IP
	var $hide_ip = 1;
	// New for 1.6 -> disable/enable activity stream
	var $js_actstr_integration = 0; // DEPRECATED, used in installer
	// New for 1.6 -> image file types
	var $imagetypes = 'jpg,jpeg,gif,png';
	var $checkmimetypes = 1;
	var $imagemimetypes = 'image/jpeg,image/jpg,image/gif,image/png';
	var $imagequality = 50;
	var $thumbheight = 32;
	var $thumbwidth = 32;
	// New for 1.6: hide profile info when user is deleted from joomla!
	var $hideuserprofileinfo = 'put_empty';
	// New for 1.6 -> New integration options
	var $integration_access = 'auto';
	var $integration_login = 'auto';
	var $integration_avatar = 'auto';
	var $integration_profile = 'auto';
	var $integration_private = 'auto';
	var $integration_activity = 'auto';
	//New for 1.6: choose if you want that ghost message box checked by default
	var $boxghostmessage = 0;
	var $userdeletetmessage = 0;
	var $latestcategory_in = 1;
	var $topicicons = 1;
	var $onlineusers = 1;
	var $debug = 0;
	var $catsautosubscribed = 0;
	var $showbannedreason = 0;
	var $version_check = 1;
	//New for 1.6: choose if you want a Thank you function
	var $showthankyou = 1;
	var $showpopthankyoustats = 1;
	var $popthankscount = 5;
	//New for 1.6: choose to allow moderators to see deleted messages
	var $mod_see_deleted = 0;
	//New for 1.6: allow only secure image extensions (jpg/gif/png) in IMG tag
	var $bbcode_img_secure = 'text';
	var $listcat_show_moderators = 1;
	//New for 1.6.1: allow the user to disable lightbox
	var $lightbox = 1;

	public function __construct($userinfo = null) {
		parent::__construct ();
	}

	//
	// Mandatory overrides from abstract base class
	//


	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$instance = new CKunenaConfig ();
			$instance->load ();
		}
		return $instance;
	}

	public function GetClassVars() {
		return get_class_vars ( 'CKunenaConfig' );
	}

	protected function GetConfigTableName() {
		return "#__kunena_config";
	}

	public function ValidateConfig() {
		// Add anything that requires validation

		// Need to have at least two per page of these
		$this->messages_per_page = max ( $this->messages_per_page, 2 );
		$this->messages_per_page_search = max ( $this->messages_per_page_search, 2 );
		$this->threads_per_page = max ( $this->threads_per_page, 2 );

	}
}