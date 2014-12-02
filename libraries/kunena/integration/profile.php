<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaProfile
 */
class KunenaProfile
{
	protected static $instance = false;

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JDispatcher::getInstance();
			$classes = $dispatcher->trigger('onKunenaGetProfile');
			foreach ($classes as $class) {
				if (!is_object($class)) continue;
				self::$instance = $class;
				break;
			}
			if (!self::$instance) {
				self::$instance = new KunenaProfile();
			}
		}
		return self::$instance;
	}

	public function getTopHits($limit=0) {
		if (!$limit) $limit = KunenaFactory::getConfig ()->popusercount;
		return (array) $this->_getTopHits($limit);
	}

	public function getUserListURL($action='', $xhtml = true) {}
	public function getProfileURL($user, $task='', $xhtml = true) {}
	public function showProfile($view, &$params) {}
	protected function _getTopHits($limit=0) { return array(); }

	public function getEditProfileURL($userid, $xhtml = true) {}
}
