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

require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');
require_once (KUNENA_PATH . DS . 'class.kunena.php');

/**

* Kunena Users Table Class

* Provides access to the #__fb_users table

*/
class KunenaUser extends JObject
{
	// Global for every instance
	protected static $_instances = array();
	protected static $_ranks = null;

	protected $_online = null;
	protected $_exists = false;
	protected $_db = null;

	/**
	* Constructor
	*
	* @access	protected
	*/
	public function __construct($identifier = 0)
	{
		// Always load the user -- if user does not exist: fill empty data
		$this->load($identifier);
		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->_config = KunenaFactory::getConfig ();
	}

	/**
	 * Returns the global KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @return	JUser			The User object.
	 * @since	1.6
	 */
	static public function getInstance($identifier = null, $reset = false)
	{
		if ($identifier instanceof KunenaUser) {
			return $identifier;
		}
		if ($identifier === null || $identifier === false) {
			$identifier = JFactory::getUser();
		}
		// Find the user id
		if ($identifier instanceof JUser) {
			$id = $identifier->id;
		} else if (is_numeric($identifier)) {
			$id = $identifier;
		} else {
			jimport('joomla.user.helper');
			$id = JUserHelper::getUserId((string)$identifier);
		}

		if (!$reset && empty(self::$_instances[$id])) {
			self::$_instances[$id] = new KunenaUser($id);
		}

		return self::$_instances[$id];
	}

	function exists() {
		return $this->_exists;
	}

	/**
	 * Method to get the user table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The user table name to be used
	 * @param	string	The user table prefix to be used
	 * @return	object	The user table object
	 * @since	1.6
	 */
	function getTable($type = 'KunenaUser', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype['name'] || $prefix != $tabletype['prefix']) {
			$tabletype['name']		= $type;
			$tabletype['prefix']	= $prefix;
		}

		// Create the user table object
		return JTable::getInstance($tabletype['name'], $tabletype['prefix']);
	}

	/**
	 * Method to load a KunenaUser object by userid
	 *
	 * @access	public
	 * @param	mixed	$identifier The user id of the user to load
	 * @param	string	$path		Path to a parameters xml file
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id)
	{
		// Create the user table object
		$table	= &$this->getTable();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->load($id);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaUser object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new user
	 * @return	boolean True on success
	 * @since 1.6
	 */
	function save($updateOnly = false)
	{
		// Create the user table object
		$table	= &$this->getTable();
		$ignore = array('name','username');
		$table->bind($this->getProperties(), $ignore);
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		//are we creating a new user
		$isnew = !$this->_exists;

		// If we aren't allowed to create new users return
		if (!$this->userid || ($isnew && $updateOnly)) {
			return true;
		}

		//Store the user data in the database
		if (!$result = $table->store()) {
			$this->setError($table->getError());
		}

		// Set the id for the KunenaUser object in case we created a new user.
		if ($result && $isnew) $this->load($table->get('userid'));

		return $result;
	}

	/**
	 * Method to delete the KunenaUser object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	function delete()
	{
		// Create the user table object
		$table	= &$this->getTable();

		$result = $table->delete($this->userid);
		if (!$result) {
			$this->setError($table->getError());
		}
		return $result;

	}

	public function isOnline($yesno=false) {
		$my = JFactory::getUser ();
		if ($this->_online === null && ($this->showOnline || CKunenaTools::isModerator($my->id))) {
			$query = 'SELECT MAX(s.time) FROM #__session AS s WHERE s.userid = ' . $this->userid . ' AND s.client_id = 0 GROUP BY s.userid';
			$this->_db->setQuery ( $query );
			$lastseen = $this->_db->loadResult ();
			CKunenaTools::checkDatabaseError();
			$timeout = $this->_app->getCfg ( 'lifetime', 15 ) * 60;
			$this->_online = ($lastseen + $timeout) > time ();
		}
		if ($yesno) return $this->_online ? 'yes' : 'no';
		return $this->_online;
	}

	public function isAdmin() {
		return CKunenaTools::isAdmin($this->userid);
	}

	public function isModerator($catid=0) {
		return CKunenaTools::isModerator($this->userid, $catid);
	}

	protected function getName() {
		if (! $this->userid) {
			$name = '';
		} else {
			$name = $this->_config->username ? $this->username : $this->name;
		}
		return $name;
	}

	public function getAvatarLink($class='', $size='thumb') {
		$avatars = KunenaFactory::getAvatarIntegration();
		return $avatars->getLink($this, $class, $size);
	}

	public function getAvatarURL($size='thumb') {
		$avatars = KunenaFactory::getAvatarIntegration();
		return $avatars->getURL($this, $size);
	}

	public function getType() {
		if ($this->userid == 0) {
			$type = JText::_('COM_KUNENA_VIEW_VISITOR');
		} elseif ($this->isAdmin ()) {
			$type = JText::_('COM_KUNENA_VIEW_ADMIN');
		} elseif ($this->isModerator ($this->catid)) {
			$type = JText::_('COM_KUNENA_VIEW_MODERATOR');
		} else {
			$type = JText::_('COM_KUNENA_VIEW_USER');
		}
		return $type;
	}
	public function getRank($catid=0, $type=false) {
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
			$this->_db->setQuery ( "SELECT * FROM #__fb_ranks" );
			self::$_ranks = $this->_db->loadObjectList ('rank_id');
			CKunenaTools::checkDatabaseError();
		}

		$rank->rank_title = JText::_('COM_KUNENA_RANK_USER');
		$rank->rank_image = 'rank0.gif';

		if ($this->userid == 0) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_VISITOR');
			$rank->rank_special = 1;
		}
		else if ($this->rank != 0 && isset(self::$_ranks[$this->rank])) {
			$rank = self::$_ranks[$this->rank];
		}
		else if ($this->rank == 0 && self::isAdmin()) {
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
		else if ($this->rank == 0 && self::isModerator($catid)) {
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
		if ($type == 'title') {
			return $rank->rank_title;
		}
		if ($type == 'image') {
			if (!$config->rankimages) return;
			return '<img src="' . KUNENA_URLRANKSPATH . $rank->rank_image . '" alt="" />';
		}
		if (!$config->rankimages) {
			$rank->rank_image = null;
		}
		return $rank;
	}

	public function profileIcon($name) {
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
			case 'private':
				$pms = KunenaFactory::getPrivateMessaging();
				return $pms->showIcon( $this->userid );
				break;
			case 'email':
				// TODO: show email
				return; // '<span class="email" title="'. JText::_('COM_KUNENA_MYPROFILE_EMAIL').'"></span>';
				break;
			case 'profile':
				if (!$this->userid) return;
				return CKunenaLink::GetProfileLink ( $this->userid, '<span class="profile" title="' . JText::_('COM_KUNENA_VIEW_PROFILE') . '"></span>' );
				break;
		}
	}

	public function socialButton($name) {
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
