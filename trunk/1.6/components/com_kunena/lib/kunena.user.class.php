<?php
/**
* @version $Id$
* Kunena Component - CKunenaUser class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');
require_once (KUNENA_PATH . DS . 'class.kunena.php');

/**

* Kunena Users Table Class

* Provides access to the #__fb_users table

*/

class CKunenaUserprofile extends JTable
{

	/**
	* User ID
	* @var int
	**/
	var $userid = null;

	var $view = null;

	/**
	* Signature
	* @var string
	**/
	var $signature = null;

	/**
	* Is moderator?
	* @var int
	**/
	var $moderator = null;

	/**
	* Ordering of posts
	* @var int
	**/
	var $ordering = null;

	/**
	* User post count
	* @var int
	**/
	var $posts = null;

	/**
	* Avatar image file
	* @var string
	**/
	var $avatar = null;

	/**
	* User karma
	* @var int
	**/
	var $karma = null;

	var $karma_time = null;

	/**
	* Kunena Group ID
	* @var int
	**/
	var $group_id = null;

	/**
	* Kunena Profile hits
	* @var int
	**/
	var $uhits = null;

	/**
	* Personal text
	* @var string
	**/
	var $personalText = null;

	/**
	* Gender
	* @var int
	**/
	var $gender = null;

	/**
	* Birthdate
	* @var string
	**/
	var $birthdate = null;

	/**
	* User Location
	* @var string
	**/
	var $location = null;

	/**
	* Name of web site
	* @var string
	**/
	var $websitename = null;

	/**
	* URL to web site
	* @var string
	**/
	var $websiteurl = null;

	/**
	* User rank
	* @var int
	**/
	var $rank = null;
	/**
	* Hide Email address
	* @var int
	**/
	var $hideEmail = null;

	/**
	* Show online
	* @var int
	**/
	var $showOnline = null;
	/**
	* ICQ ID
	* @var string
	**/
	var $ICQ = null;

	/**
	* AIM ID
	* @var string
	**/
	var $AIM = null;

	/**
	* YIM ID
	* @var string
	**/
	var $YIM = null;

	/**
	* MSN ID
	* @var string
	**/
	var $MSN = null;

	/**
	* SKYPE ID
	* @var string
	**/
	var $SKYPE = null;
	/**
	* TWITTER ID
	* @var string
	**/
	var $TWITTER = null;
	/**
	* FACEBOOK ID
	* @var string
	**/
	var $FACEBOOK = null;

	/**
	* GTALK ID
	* @var string
	**/
	var $GTALK = null;

	/**
	* MYSPACE ID
	* @var string
	**/
	var $MYSPACE = null;
	/**
	* LINKEDIN ID
	* @var string
	**/
	var $LINKEDIN = null;
	/**
	* DELICIOUS ID
	* @var string
	**/
	var $DELICIOUS = null;
	/**
	* FRIENDFEED ID
	* @var string
	**/
	var $FRIENDFEED = null;
	/**
	* $DIGG ID
	* @var string
	**/
	var $DIGG = null;
	/**
	* BLOGSPOT ID
	* @var string
	**/
	var $BLOGSPOT = null;
	/**
	* FLICKR ID
	* @var string
	**/
	var $FLICKR = null;
	/**
	* BEBO ID
	* @var string
	**/
	var $BEBO = null;

	protected $_online = null;
	protected static $_ranks = null;

	function __construct($userid)
	{
		$db = JFactory::getDBO();
		$this->_app = JFactory::getApplication ();

		parent::__construct('#__fb_users', 'userid', $db);
		if ($userid) $this->load($userid);

	}

	function &getInstance($userid=null, $reload=false)
	{
		return CKunenaUserHelper::getInstance($userid, $reload);
	}

	function online() {
		$my = JFactory::getUser ();
		if ($this->_online === null && ($this->showOnline || CKunenaTools::isModerator($my->id))) {
			$query = 'SELECT MAX(s.time) FROM #__session AS s WHERE s.userid = ' . $this->userid . ' AND s.client_id = 0 GROUP BY s.userid';
			$this->_db->setQuery ( $query );
			$lastseen = $this->_db->loadResult ();
			check_dberror ( "Unable get user online information." );
			$timeout = $this->_app->getCfg ( 'lifetime', 15 ) * 60;
			$this->_online = ($lastseen + $timeout) > time ();
		}
		return $this->_online;
	}

	function isAdmin() {
		CKunenaTools::isAdmin($this->userid);
	}

	function isModerator($catid=0) {
		CKunenaTools::isModerator($catid);
	}

	function getRank($catid=0) {
		// Default rank
		$rank = new stdClass();
		$rank->rank_id = false;
		$rank->rank_title = null;
		$rank->rank_min = 0;
		$rank->rank_special = 0;
		$rank->rank_image = null;

		$config = CKunenaConfig::getInstance ();
		if (!$config->showranking) return $rank;
		if (self::$_ranks === null) {
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks" );
			self::$_ranks = $kunena_db->loadObjectList ('rank_id');
			check_dberror ( "Unable to load ranks." );
		}

		$rank->rank_title = JText::_('COM_KUNENA_RANK_USER');
		$rank->rank_image = 'rank0.gif';

		if ($this->userid == 0) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_VISITOR');
			$rank->rank_special = 1;
		}
		else if ($this->rank != '0' && isset(self::$_ranks[$this->rank])) {
			$rank = self::$_ranks[$this->rank];
		}
		else if ($this->rank == '0' && self::isAdmin()) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_ADMINISTRATOR');
			$rank->rank_special = 1;
			$rank->rank_image = 'rankadmin.gif';
			jimport ('joomla.filesystem.file');
			foreach (self::$_ranks as $cur) {
				if ($cur->rank_special == 1 && JFile::stripExt($cur->rank_image) == 'rankadmin') {
					$rank = $cur;
					break;
				}
			}
		}
		else if ($this->rank == '0' && self::isModerator($catid)) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_MODERATOR');
			$rank->rank_special = 1;
			$rank->rank_image = 'rankmod.gif';
			jimport ('joomla.filesystem.file');
			foreach (self::$_ranks as $cur) {
				if ($cur->rank_special == 1 && JFile::stripExt($cur->rank_image) == 'rankadmin') {
					$rank = $cur;
					break;
				}
			}
		}
		if ($rank->rank_id === false) {
			//post count rank
			$rank->rank_id = 0;
			foreach (self::$_ranks as $cur) {
				if ($cur->rank_special == 0 && $cur->rank_min <= $this->posts && $cur->rank_min >= $rank->rank_min) {
					$rank = $cur;
				}
			}
		}
		if (!$config->rankimages) $rank->rank_image = null;
		return $rank;
	}

	function profileIcon($name) {
		switch ($name) {
			case 'gender':
				switch ($this->gender) {
					case 1:
						$gender = 'male';
						break;
					case 2:
						$gender = 'female';
						break;
					default:
						$gender = 'unknown';
				}
				$title = JText::_('COM_KUNENA_MYPROFILE_GENDER') . ': '.JText::_('COM_KUNENA_MYPROFILE_GENDER_'.$gender);
				return '<span class="gender-'.$gender.'" title="'.$title.'"></span>';
				break;
			case 'birthdate':
				if ($this->birthdate)
					return '<span class="birthdate" title="'. JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE') . ': ' . CKunenaTimeformat::showDate($this->birthdate, 'date').'"></span>';
				break;
			case 'location':
				if ($this->location)
					return '<span class="location" title="' . JText::_('COM_KUNENA_MYPROFILE_LOCATION').': '. kunena_htmlspecialchars(stripslashes($this->location)).'"></span>';
				break;
			case 'website':
				$url = 'http://'.$this->websiteurl;
				if (!$this->websitename) $websitename = $this->websiteurl;
				else $websitename = $this->websitename;
				if ($this->websiteurl)
					return '<a href="'.kunena_htmlspecialchars(stripslashes($url)).'" target="_blank"><span class="website" title="'. JText::_('COM_KUNENA_MYPROFILE_WEBSITE') . ': ' .  kunena_htmlspecialchars(stripslashes($websitename)).'"></span></a>';
				break;
		}
	}

	function socialButton($name) {
		$social = array (
			'twitter' => array( 'name'=>'TWITTER', 'url'=>'http://twitter.com/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_TWITTER') ),
			'facebook' => array( 'name'=>'FACEBOOK', 'url'=>'##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_FACEBOOK') ),
			'myspace' => array( 'name'=>'MYSPACE', 'url'=>'http://www.myspace.com/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_MYSPACE') ),
			'linkedin' => array( 'name'=>'LINKEDIN', 'url'=>'http://www.linkedin.com/pub/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_LINKEDIN') ),

			'delicious' => array( 'name'=>'DELICIOUS', 'url'=>'http://delicious.com/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_DELICIOUS') ),
			'friendfeed' => array( 'name'=>'FRIENDFEED', 'url'=>'http://friendfeed.com/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_FRIENDFEED') ),
			'digg' => array( 'name'=>'DIGG', 'url'=>'http://www.digg.com/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_DIGG') ),

			'skype' => array( 'name'=>'SKYPE', 'url'=>'skype:##VALUE##?chat', 'title'=>'' ),
			'yim' => array( 'name'=>'YIM', 'url'=>'ymsgr:sendim?##VALUE##', 'title'=>'' ),
			'aim' => array( 'name'=>'AIM', 'url'=>'aim:goim?screenname=##VALUE##', 'title'=>'' ),
			'gtalk' => array( 'name'=>'GTALK', 'url'=>'gtalk:chat?jid=##VALUE##', 'title'=>'' ),
			'msn' => array( 'name'=>'MSN', 'url'=>'msn:##VALUE##', 'title'=>'' ),
			'icq' => array( 'name'=>'ICQ', 'url'=>'http://www.icq.com/people/cmd.php?uin=##VALUE##&action=message', 'title'=>'' ),

			'blogspot' => array( 'name'=>'BLOGSPOT', 'url'=>'http://##VALUE##.blogspot.com/', 'title'=>JText::_('COM_KUNENA_MYPROFILE_BLOGSPOT') ),
			'flickr' => array( 'name'=>'FLICKR', 'url'=>'http://www.flickr.com/photos/##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_FLICKR') ),
			'bebo' => array( 'name'=>'BEBO', 'url'=>'http://www.bebo.com/Profile.jsp?MemberId=##VALUE##', 'title'=>JText::_('COM_KUNENA_MYPROFILE_BEBO') )
		);
		if (!isset($social[$name])) return;
		$title = $social[$name]['title'];
		$item = $social[$name]['name'];
		$value = kunena_htmlspecialchars(stripslashes($this->$item));
		$url = strtr($social[$name]['url'], array('##VALUE##'=>$value));
		if (!empty($this->$item)) return '<a href="'.$url.'" target="_blank" title="'.$title.'"><span class="'.$name.'"></span></a>';
		return '<span class="'.$name.'_off"></span>';
	}
}

class CKunenaUserHelper {
	static $instances = array();
	function &getInstance($userid=null, $reload=false)
	{
		if ($userid === null) {
			$user =& JFactory::getUser();
			$userid = $user->get('id');
		}
		if ($reload || !isset(self::$instances[$userid])) {
			self::$instances[$userid] = new CKunenaUserprofile($userid);
		}
		return self::$instances[$userid];
	}

}