<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.forum.topic.poll');

/**
 * Kunena Forum Topic Poll Helper Class
 */
class KunenaForumTopicPollHelper {
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumTopic object
	 *
	 * @access	public
	 * @param	identifier		The poll to load - Can be only an integer.
	 * @return	KunenaForumTopicPoll		The poll object.
	 * @since	2.0
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumTopicPoll) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumTopicPoll ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumTopicPoll ( $id );
		}

		return self::$_instances [$id];
	}
}
