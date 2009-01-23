<?php
/**
* @version $Id: fb_config.class.php 1070 2008-10-06 08:11:18Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class boj_Config
{
    //
    // The following functions MUST be overridden in derived classes
    // Basically an abstract base class that must not be used by itself
    //
    function GetClassVars()
    {
    	echo '<div>Undefined GetClassVars() function in derived class!</div>';
    	die();
    }

    function GetConfigTableName()
    {
    	echo '<div>Undefined GetConfigTableName() function in derived class!</div>';
    	die();
    }

    //
    //  binds a named array/hash to this object
    //  @param array $hash named array
    //  @return null|string null is operation was satisfactory, otherwise returns an error
    //
    function bind($array, $ignore = '')
    {
        if (!is_array($array))
        {
            $this->_error = strtolower(get_class($this)) . '::bind failed.';
            return false;
        }
        else
        {
            return mosBindArrayToObject($array, $this, $ignore);
        }
    }

    //
    // Create the config table for FireBoard and add initial default values
    //
    function create()
    {
        global $database, $mosConfig_absolute_path;

        $fields = array ();

        $vars = $this->GetClassVars();

        foreach ($vars as $name => $value)
        {
            //
            // Need to provide ability to override certain settings
            // in derived class without the need to recode this entire function
            //
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

        $database->setQuery("CREATE TABLE #__".$this->GetConfigTableName()." (" . implode(', ', $fields) . ")");
        $database->query();
        	check_dberror("Unable to create configuration table.");

        // Insert current Settings
        $vars = get_object_vars($this); // for the actual values we must not use the class vars funtion
        $fields = array ();

        foreach ($vars as $name => $value)
        {
        	// Only allow variables that have been defined in the class
        	if(array_key_exists($name , $this->GetClassVars()))
            {
	            $value =addslashes($value);
	        	$fields[] = "`$name`='$value'";
            }
        }

        $database->setQuery("INSERT INTO #__".$this->GetConfigTableName()." SET " . implode(', ', $fields));
        $database->query();
        	check_dberror("Unable to insert configuration data.");
    }

    //
    // Create a backup of most current config table
    //
    function backup()
    {
        global $database;
        // remove old backup if one exists
        $database->setQuery("DROP TABLE IF EXISTS #__".$this->GetConfigTableName()."_backup");
        $database->query();
        	check_dberror("Unable to drop old configuration backup table.");

        // Only create backup if config table already exists
        $database->setQuery( "SHOW TABLES LIKE '%".$this->GetConfigTableName()."'" );
		$database->query();
			check_dberror('Unable to check for existing config table.');
		if($database->loadResult())
		{
			// backup current settings
			$database->setQuery("CREATE TABLE #__".$this->GetConfigTableName()."_backup SELECT * FROM #__".$this->GetConfigTableName());
			$database->query();
				check_dberror("Unable to create new configuration backup table.");
		}
    }

    //
    // Remove the current config table
    //
    function remove()
    {
        global $database;
        $database->setQuery("DROP TABLE IF EXISTS #__".$this->GetConfigTableName());
        $database->query();
        	check_dberror("Unable to drop existing configuration table.");
    }

    //
    // Load config settings from database table
    //
    function load($silent=false)
    {
        global $database;

        $database->setQuery("SELECT * FROM #__".$this->GetConfigTableName());

        // Special error handling!
        // This query might actually fail on new installs or upgrades when
        // no configuration table is present. That is ok. It only tells us to
        // create the table and populate it with defualt setting.
        // DO NOT THROW an error
        $database->loadObject($this);

        if($database->_errorNum != 0)
        {
        	// If no configuration is present, save default values
        	$this->create(); //create has its own error handling
        }
    }
}


class fb_Config extends boj_Config
{
	// All vars MUST BE LOWER CASE!
    var $board_title             = 'FireBoard';
    var $email                   = 'change@me.com';
    var $board_offline           = 0;
    var $board_ofset             = 0;
    var $offline_message         = '<h2>The Forum is currently offline for maintenance.</h2>
    Check back soon!                        ';
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
    var $jmambot                 = 1;
    var $disemoticons            = 0;
    var $template                = 'default_ex';
    var $templateimagepath       = 'default_ex';
    var $joomlastyle             = 0;
    var $showannouncement        = 1;
    var $avataroncat             = 0;
    var $catimagepath            = 'category_images/';
    var $numchildcolumn          = 2;
    var $showchildcaticon        = 1;
    var $annmodid                = 62;
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
    var $cb_profile              = 0;
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
    var $latestcategory          = 0;
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
    var $rules_link              = 'http://www.bestofjoomla.com/';
    var $enablehelppage          = 1;
    var $help_infb               = 1;
    var $help_cid                = 1;
    var $help_link               = 'http://www.bestofjoomla.com/';
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

    //
    // Mandatory overrides from abstract base class
    //

    function GetClassVars()
    {
        return get_class_vars('fb_Config');
    }

    function GetConfigTableName()
    {
        return "fb_config"; // w/o joomla prefix - is being added by based class
    }
}


?>