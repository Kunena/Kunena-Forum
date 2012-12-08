<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Announcement Helper Class
 */
class KunenaForumAnnouncementHelper {
	// Global for every instance
	public static $_instances = false;

	// Static class
	private function __construct() {}

	/**
	 * Returns the global KunenaForumAnnouncement object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id		The Announcement to load - Can be only an integer.
	 * @return	Announcement	The Announcement object.
	 * @since	1.6
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumAnnouncement) {
			return $identifier;
		}
		if (!is_numeric($identifier)) {
			return new KunenaForumAnnouncement;
		}

		$id = intval ( $identifier );
		if (empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumAnnouncement (array('id'=>$id));
			self::$_instances [$id]->load();
		} elseif ($reload) {
			self::$_instances [$id]->load();
		}

		return self::$_instances [$id];
	}

	static public function getUrl($layout = null, $xhtml = true) {
		$uri = self::getUri($layout);
		return KunenaRoute::_($uri, $xhtml);
	}

	static public function getUri($layout = null) {
		$uri = new JURI('index.php?option=com_kunena&view=announcement');
		if ($layout) $uri->setVar('layout', $layout);
		return $uri;
	}

	static public function getAnnouncements($start = 0, $limit = 1, $filter = true) {
		$db = JFactory::getDBO ();
		$where = $filter ? "WHERE published=1" : '';
		$query = "SELECT * FROM #__kunena_announcement {$where} ORDER BY created DESC";
		$db->setQuery ( $query, $start, $limit );
		$results = (array) $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		self::$_instances = array();
		$list = array();
		foreach ( $results as $announcement ) {
			if (isset(self::$_instances [$announcement['id']])) continue;
			$instance = new KunenaForumAnnouncement ($announcement);
			$instance->exists (true);
			self::$_instances [$instance->id] = $instance;
			$list[] = $instance;
		}
		unset ($results);
		return $list;
	}

	static public function getCount($filter = true) {
		$db = JFactory::getDBO ();
		$where = $filter ? "WHERE published=1" : '';

		$query = "SELECT COUNT(*) FROM #__kunena_announcement {$where}";
		$db->setQuery ( $query );
		$total = (int) $db->loadResult ();
		KunenaError::checkDatabaseError ();

		return $total;
	}
}
