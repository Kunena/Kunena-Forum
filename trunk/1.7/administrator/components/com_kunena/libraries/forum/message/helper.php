<?php
/**
 * @version $Id: message.php 3759 2010-10-20 13:48:28Z mahagr $
 * Kunena Component - KunenaForumMessageHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.forum.message');

/**
 * Kunena Forum Message Helper Class
 */
class KunenaForumMessageHelper {
	// Global for every instance
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access	public
	 * @param	identifier		The message to load - Can be only an integer.
	 * @return	KunenaForumMessage		The message object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumMessage) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumMessage ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumMessage ( $id );
		}

		return self::$_instances [$id];
	}

	static protected function loadMessages($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode($ids);
		$db = JFactory::getDBO ();
		$query = "SELECT m.*, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumMessage ();
				$instance->bind ( $results[$id] );
				$instance->_exists = true;
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}

	static public function getMessages($ids = false) {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadMessages($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise()) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}
}