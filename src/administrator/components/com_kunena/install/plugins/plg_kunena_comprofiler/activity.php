<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Comprofiler
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once dirname(__FILE__) . '/integration.php';

class KunenaActivityComprofiler extends KunenaActivity
{
	protected $params = null;

	/**
	 * KunenaActivityComprofiler constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param int $userid
	 *
	 * @return null
	 */
	public function getUserPoints($userid)
	{
		$points = null;
		$params = array('userid' => $userid, 'points' => &$points);
		KunenaIntegrationComprofiler::trigger('getUserPoints', $params);

		return $points;
	}

	/**
	 * @param $message
	 */
	public function onBeforePost($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforePost', $params);
	}

	/**
	 * @param $message
	 */
	public function onBeforeReply($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforeReply', $params);
	}

	/**
	 * @param $message
	 */
	public function onBeforeEdit($message)
	{
		$params = array('actor' => $message->get('modified_by'), 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforeEdit', $params);
	}

	/**
	 * @param $message
	 */
	public function onAfterPost($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterPost', $params);
	}

	/**
	 * @param $message
	 */
	public function onAfterReply($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterReply', $params);
	}

	/**
	 * @param $message
	 */
	public function onAfterEdit($message)
	{
		$params = array('actor' => $message->get('modified_by'), 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterEdit', $params);
	}

	/**
	 * @param $message
	 */
	public function onAfterDelete($message)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterDelete', $params);
	}

	/**
	 * @param $message
	 */
	public function onAfterUndelete($message)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterUndelete', $params);
	}

	/**
	 * @param int $actor
	 * @param int $target
	 * @param int $message
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		$params = array('actor' => $actor, 'target' => $target, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterThankyou', $params);
	}

	/**
	 * @param int $topic
	 * @param int $action
	 */
	public function onAfterSubscribe($topic, $action)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterSubscribe', $params);
	}

	/**
	 * @param int $topic
	 * @param int $action
	 */
	public function onAfterFavorite($topic, $action)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterFavorite', $params);
	}

	/**
	 * @param int $topic
	 * @param int $action
	 */
	public function onAfterSticky($topic, $action)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterSticky', $params);
	}

	/**
	 * @param int $topic
	 * @param int $action
	 */
	public function onAfterLock($topic, $action)
	{
		$my     = JFactory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterLock', $params);
	}

	/**
	 * @param int $target
	 * @param int $actor
	 * @param int $delta
	 */
	public function onAfterKarma($target, $actor, $delta)
	{
		$params = array('actor' => $actor, 'target' => $target, 'delta' => $delta);
		KunenaIntegrationComprofiler::trigger('onAfterKarma', $params);
	}
}
