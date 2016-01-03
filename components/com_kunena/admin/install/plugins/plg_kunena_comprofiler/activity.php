<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Comprofiler
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once dirname(__FILE__) . '/integration.php';

class KunenaActivityComprofiler extends KunenaActivity {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getUserPoints($userid) {
		$points = null;
		$params = array ('userid' => $userid, 'points' => &$points );
		KunenaIntegrationComprofiler::trigger ( 'getUserPoints', $params );
		return $points;
	}

	public function onBeforePost($message) {
		$params = array ('actor' => $message->get ( 'userid' ), 'replyto' => 0, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onBeforePost', $params );
	}

	public function onBeforeReply($message) {
		$params = array ('actor' => $message->get ( 'userid' ), 'replyto' => (int) $message->getParent()->userid, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onBeforeReply', $params );
	}

	public function onBeforeEdit($message) {
		$params = array ('actor' => $message->get ( 'modified_by' ), 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onBeforeEdit', $params );
	}

	public function onAfterPost($message) {
		$params = array ('actor' => $message->get ( 'userid' ), 'replyto' => 0, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterPost', $params );
	}

	public function onAfterReply($message) {
		$params = array ('actor' => $message->get ( 'userid' ), 'replyto' => (int) $message->getParent()->userid, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterReply', $params );
	}

	public function onAfterEdit($message) {
		$params = array ('actor' => $message->get ( 'modified_by' ), 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterEdit', $params );
	}

	public function onAfterDelete($message) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterDelete', $params );
	}

	public function onAfterUndelete($message) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterUndelete', $params );
	}

	public function onAfterThankyou($actor, $target, $message) {
		$params = array ('actor' => $actor, 'target' => $target, 'message' => $message );
		KunenaIntegrationComprofiler::trigger ( 'onAfterThankyou', $params );
	}

	public function onAfterSubscribe($topic, $action) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'topic' => $topic, 'action' => $action );
		KunenaIntegrationComprofiler::trigger ( 'onAfterSubscribe', $params );
	}

	public function onAfterFavorite($topic, $action) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'topic' => $topic, 'action' => $action );
		KunenaIntegrationComprofiler::trigger ( 'onAfterFavorite', $params );
	}

	public function onAfterSticky($topic, $action) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'topic' => $topic, 'action' => $action );
		KunenaIntegrationComprofiler::trigger ( 'onAfterSticky', $params );
	}

	public function onAfterLock($topic, $action) {
		$my = JFactory::getUser();
		$params = array ('actor' => $my->id, 'topic' => $topic, 'action' => $action );
		KunenaIntegrationComprofiler::trigger ( 'onAfterLock', $params );
	}

	public function onAfterKarma($target, $actor, $delta) {
		$params = array ('actor' => $actor, 'target' => $target, 'delta' => $delta );
		KunenaIntegrationComprofiler::trigger ( 'onAfterKarma', $params );
	}
}
