<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaActivity
{
	protected static $instance = false;

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JDispatcher::getInstance();
			$classes = $dispatcher->trigger('onKunenaGetActivity');
			foreach ($classes as $class) {
				if (!is_object($class)) continue;
				self::$instance = $class;
				break;
			}
			if (!self::$instance) {
				self::$instance = new KunenaActivity();
			}
		}
		return self::$instance;
	}

	public function getUserMedals($userid) {}
	public function getUserPoints($userid) {}

	public function onBeforePost($message) {}
	public function onBeforeReply($message) {}
	public function onBeforeEdit($message) {}

	public function onAfterPost($message) {}
	public function onAfterReply($message) {}
	public function onAfterEdit($message) {}
	public function onAfterDelete($message) {}
	public function onAfterUndelete($message) {}
	public function onAfterThankyou($target, $actor, $message) {}
	public function onAfterUnThankyou($target, $actor, $message) {}
	public function onAfterDeleteTopic($message) {}

	public function onAfterSubscribe($topicid, $action) {}
	public function onAfterFavorite($topicid, $action) {}
	public function onAfterSticky($topicid, $action) {}
	public function onAfterLock($topicid, $action) {}

	public function onAfterKarma($target, $actor, $delta) {}
}
