<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

require_once (KUNENA_PATH_LIB . DS . 'kunena.debug.php');

$app =& JFactory::getApplication();
require_once (JPATH_ROOT . '/components/com_kunena/lib/kunena.debug.php');
require_once (JPATH_ROOT . '/components/com_kunena/lib/kunena.user.class.php');

class CKunenaTables
{
	var $tables = array();
	var $_tables = array ( '#__fb_announcement', '#__fb_attachments', '#__fb_categories', '#__fb_favorites', '#__fb_groups', '#__fb_messages', '#__fb_messages_text', '#__fb_moderation', '#__fb_ranks', '#__fb_sessions', '#__fb_smileys', '#__fb_subscriptions', '#__fb_users', '#__fb_version', '#__fb_whoisonline');

	function __construct()
	{
       		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery( "SHOW TABLES LIKE '" .$kunena_db->getPrefix(). "fb_%'");
		$tables = $kunena_db->loadResultArray();
		$prelen = strlen($kunena_db->getPrefix());
		foreach	($tables as $table) $this->tables['#__'.substr($table,$prelen)] = 1;
		check_dberror('Unable to check for existing tables.');
	}

	function &getInstance()
	{
		static $instance;
		if (!$instance) {
			$instance = new CKunenaTables();
		}
		return $instance;
	}

	function check($table)
	{
		return isset($this->tables[$table]);
	}

	function installed()
	{
		foreach ($this->_tables as $table) {
			if (!isset($this->tables[$table])) return false;
		}
		return true;
	}
}

abstract class CKunenaConfigBase
{
    public function __construct()
    {
        $this->_db = &JFactory::getDBO();
    }

    //
    // The following functions MUST be overridden in derived classes
    //
    abstract public function &getInstance();
    abstract public function GetClassVars();
    abstract protected function GetConfigTableName();
    
    // This function allows for the overload of user specific settings.
    // All settings can now be user specific. No further code changes
    // are required inside of Kunena.
    abstract public function DoUserOverrides($userid);

    //
    //  binds a named array/hash to this object
    //  @param array $hash named array
    //  @return null|string null is operation was satisfactory, otherwise returns an error
    //
    protected function bind($array, $ignore = '')
    {
        if (!is_array($array))
        {
            $this->_error = strtolower(get_class($this)) . '::bind failed.';
            return false;
        }
        else
        {
			foreach ($array as $k => $v)
			{
			    $this->$k = $v;
			}
		}

		return true;
    }

    //
    // Create the config table for Kunena and add initial default values
    //
    public function create()
    {
            $fields = array ();

        $vars = $this->GetClassVars();

        foreach ($vars as $name => $value)
        {
            //
            // Need to provide ability to override certain settings
            // in derived class without the need to recode this entire function
            //

            // Exclude private class variables
            if ($name!='_db')
            {
	            switch (gettype($value))
	            {
	                case 'integer':
	                    $fields[] = "`$name` INTEGER NULL";

	                    break;

	                case 'string':
	                    $fields[] = "`$name` TEXT NULL";

	                    break;
	            }
            }
        }

        $this->_db->setQuery("CREATE TABLE ".$this->GetConfigTableName()." (" . implode(', ', $fields) . " )");
        $this->_db->query();
        	check_dberror("Unable to create configuration table.");

        // Insert current Settings
        $vars = get_object_vars($this); // for the actual values we must not use the class vars funtion
        $fields = array ();

        foreach ($vars as $name => $value)
        {
        	// Exclude internal class vars e.g. _db
        	if($name[0] != '_' && array_key_exists($name , $this->GetClassVars()))
            {
	            $value = addslashes($value);
	        	$fields[] = "`$name`='$value'";
            }
        }

        $this->_db->setQuery("INSERT INTO ".$this->GetConfigTableName()." SET " . implode(', ', $fields));
        $this->_db->query();
        	check_dberror("Unable to insert configuration data.");
    }

    //
    // Create a backup of most current config table
    //
    public function backup()
    {
        // remove old backup if one exists
        $this->_db->setQuery("DROP TABLE IF EXISTS ".$this->GetConfigTableName()."_backup");
        $this->_db->query();
        	check_dberror("Unable to drop old configuration backup table.");

        // Only create backup if config table already exists
        $tables = CKunenaTables::getInstance();
        if ($tables->check($this->GetConfigTableName()))
		{
			// backup current settings
			$this->_db->setQuery("CREATE TABLE ".$this->GetConfigTableName()."_backup SELECT * FROM ".$this->GetConfigTableName());
			$this->_db->query();
				check_dberror("Unable to create new configuration backup table.");
		}
    }

    //
    // Remove the current config table
    //
    public function remove()
    {
        $this->_db->setQuery("DROP TABLE IF EXISTS ".$this->GetConfigTableName());
        $this->_db->query();
        	check_dberror("Unable to drop existing configuration table.");
    }

    //
    // Load config settings from database table
    //
    public function load($KunenaUser=null)
    {
        $tables = CKunenaTables::getInstance();
        if ($tables->check($this->GetConfigTableName())) 
	{
        	$this->_db->setQuery("SELECT * FROM ".$this->GetConfigTableName());
		$config = $this->_db->loadAssoc();
       		check_dberror("Unable to load configuration table.");

		if ($config!=null)
		{
			$this->bind($config);
		}
        }
        
        // Check for user specific overrides
        if(is_object($KunenaUser))
        {
            // overload the settings with user specific ones
            $this->DoUserOverrides($KunenaUser);
            // Now the variables of the class contain the global settings
            // overloaded with the user specific ones
            // No other code changes required to support user specific settings.
        }
    }
}


class CKunenaConfig extends CKunenaConfigBase
{
	// All vars MUST BE LOWER CASE!
    var $board_title             = 'Kunena';
    var $email                   = 'change@me.com';
    var $board_offline           = 0;
    var $board_ofset             = 0;
    var $offline_message         = "<h2>The Forum is currently offline for maintenance.</h2>\n<div>Check back soon!</div>";
    var $default_view            = 'flat';
    var $enablerss               = 1;
    var $enablepdf               = 1;
    var $threads_per_page        = 20;
    var $messages_per_page       = 6;
    var $messages_per_page_search = 15;
    var $showhistory             = 1;
    var $historylimit            = 6;
    var $shownew                 = 1;
    var $newchar                 = 'NEW!';
    var $jmambot                 = 0;
    var $disemoticons            = 0;
    var $template                = 'default_ex';
    var $templateimagepath       = 'default_ex';
    var $joomlastyle             = 0;
    var $showannouncement        = 1;
    var $avataroncat             = 0;
    var $catimagepath            = 'category_images/';
    var $numchildcolumn          = 2;
    var $showchildcaticon        = 1;
    var $annmodid                = '62';
    var $rtewidth                = 450;
    var $rteheight               = 300;
    var $enablerulespage         = 1;
    var $enableforumjump         = 1;
    var $reportmsg               = 1;
    var $username                = 1;
    var $askemail                = 0;
    var $showemail               = 0;
    var $showuserstats           = 1;
    var $poststats               = 1;
    var $statscolor              = 9;
    var $showkarma               = 1;
    var $useredit                = 1;
    var $useredittime            = 0;
    var $useredittimegrace       = 600;
    var $editmarkup              = 1;
    var $allowsubscriptions      = 1;
    var $subscriptionschecked    = 1;
    var $allowfavorites          = 1;
    var $wrap                    = 250;
    var $maxsubject              = 50;
    var $maxsig                  = 300;
    var $regonly                 = 0;
    var $changename              = 0;
    var $pubwrite                = 0;
    var $floodprotection         = 0;
    var $mailmod                 = 0;
    var $mailadmin               = 0;
    var $captcha                 = 0;
    var $mailfull                = 1;
    var $allowavatar             = 1;
    var $allowavatarupload       = 1;
    var $allowavatargallery      = 1;
    var $imageprocessor          = 'gd2';
    var $avatarsmallheight       = 50;
    var $avatarsmallwidth        = 50;
    var $avatarheight            = 100;
    var $avatarwidth             = 100;
    var $avatarlargeheight       = 250;
    var $avatarlargewidth        = 250;
    var $avatarquality           = 65;
    var $avatarsize              = 2048;
    var $allowimageupload        = 0;
    var $allowimageregupload     = 1;
    var $imageheight             = 800;
    var $imagewidth              = 800;
    var $imagesize               = 150;
    var $allowfileupload         = 0;
    var $allowfileregupload      = 1;
    var $filetypes               = 'zip,txt,doc,gz,tgz';
    var $filesize                = 120;
    var $showranking             = 1;
    var $rankimages              = 1;
    var $avatar_src              = 'fb';
    var $fb_profile              = 'fb';
    var $pm_component            = 'no';
    var $cb_profile              = 0;  // Depreciated legacy CB integration - Now controlled via avatar, profile and pm settings
    var $badwords                = 0;
    var $discussbot              = 0;
    var $userlist_rows           = 30;
    var $userlist_online         = 1;
    var $userlist_avatar         = 1;
    var $userlist_name           = 1;
    var $userlist_username       = 1;
    var $userlist_group          = 0;
    var $userlist_posts          = 1;
    var $userlist_karma          = 1;
    var $userlist_email          = 0;
    var $userlist_usertype       = 0;
    var $userlist_joindate       = 1;
    var $userlist_lastvisitdate  = 1;
    var $userlist_userhits       = 1;
    var $showlatest              = 1;
    var $latestcount             = 10;
    var $latestcountperpage      = 5;
    var $latestcategory          = ''; //Also used by default_ex recent topics
    var $latestsinglesubject     = 1;
    var $latestreplysubject      = 1;
    var $latestsubjectlength     = 100;
    var $latestshowdate          = 1;
    var $latestshowhits          = 1;
    var $latestshowauthor        = 1;
    var $showstats               = 1;
    var $showwhoisonline         = 1;
    var $showgenstats            = 1;
    var $showpopuserstats        = 1;
    var $popusercount            = 5;
    var $showpopsubjectstats     = 1;
    var $popsubjectcount         = 5;
    var $usernamechange          = 0;
    var $rules_infb              = 1;
    var $rules_cid               = 1;
    var $rules_link              = 'http://www.kunena.com/';
    var $enablehelppage          = 1;
    var $help_infb               = 1;
    var $help_cid                = 1;
    var $help_link               = 'http://www.kunena.com/';
    // New 1.0.5 config variables
    // bbcode options
    var $showspoilertag			 = 1;
    var $showvideotag			 = 1;
    var $showebaytag			 = 1;
    var $trimlongurls			 = 1;
    var $trimlongurlsfront		 = 40;
    var $trimlongurlsback		 = 20;
    var $autoembedyoutube		 = 1;
    var $autoembedebay			 = 1;
    var $ebaylanguagecode		 = 'en-us';
    var $fbsessiontimeout		 = 1800; // in seconds
    // New 1.0.5RC2 config variables
    var $highlightcode			 = 0;
    var $rsstype				 = 'thread';
    var $rsshistory				 = 'month';
    var $fbdefaultpage			 = 'recent';
    // New 1.0.8 config variables
    var $default_sort            = 'asc'; // 'desc' for latest post first

    public function __construct($KunenaUser=null)
    {
        parent::__construct();
		$this->load($KunenaUser);
    }

    //
    // Mandatory overrides from abstract base class
    //

    public function &getInstance()
    {
        static $instance;
        if (!$instance) {
            $userinfo = new CKunenaUserprofile();
	    $instance = new CKunenaConfig($userinfo);
	}
        return $instance;
    }

    public function GetClassVars()
    {
        return get_class_vars('CKunenaConfig');
    }

    protected function GetConfigTableName()
    {
        return "#__fb_config";
    }

    public function DoUserOverrides($KunenaUser)
    {
    	// Only perform overrides if we got a valid user handed to us
    	if (is_object($KunenaUser)==FALSE) return FALSE;
    	if ($KunenaUser->userid==0) return FALSE;

        $this->default_sort = $KunenaUser->ordering ? 'desc' : 'asc';

        // Add additional Overrides...

        return TRUE;
    }
}

?>
