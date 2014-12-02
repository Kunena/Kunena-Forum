<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Announcement
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumAnnouncementHelper
 */
abstract class KunenaForumAnnouncementHelper {
	/**
	 * @var KunenaForumAnnouncement[]
	 */
	public static $_instances = false;

	/**
	 * Returns the global KunenaForumAnnouncement object, only creating it if it doesn't already exist.
	 *
	 * @param int $identifier	Announcement to load - Can be only an integer.
	 * @param bool $reload
	 *
	 * @return KunenaForumAnnouncement
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

	/**
	 * @param string $layout
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	static public function getUrl($layout = null, $xhtml = true) {
		$uri = self::getUri($layout);
		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param string $layout
	 *
	 * @return JUri
	 */
	static public function getUri($layout = null) {
		$uri = new JUri('index.php?option=com_kunena&view=announcement');
		if ($layout) $uri->setVar('layout', $layout);
		return $uri;
	}

	/**
	 * @param int  $start
	 * @param int  $limit
	 * @param bool $filter
	 *
	 * @return KunenaForumAnnouncement[]
	 */
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

	/**
	 * @param bool $filter
	 *
	 * @return int
	 */
	static public function getCount($filter = true) {
		$db = JFactory::getDBO ();
		$where = $filter ? "WHERE published=1" : '';

		$query = "SELECT COUNT(*) FROM #__kunena_announcement {$where}";
		$db->setQuery ( $query );
		$total = (int) $db->loadResult ();
		KunenaError::checkDatabaseError ();

		return $total;
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 */
	public static function cleanup() {
		self::$_instances = array();
	}
}
