<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

require_once dirname(__FILE__) . '/integration.php';

/**
 * Class KunenaActivityComprofiler
 * @since Kunena
 */
class KunenaActivityComprofiler extends KunenaActivity
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaActivityComprofiler constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   int $userid userid
	 *
	 * @return null
	 * @throws Exception
	 * @since Kunena
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
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onBeforePost($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforePost', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onBeforeReply($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforeReply', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onBeforeEdit($message)
	{
		$params = array('actor' => $message->get('modified_by'), 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onBeforeEdit', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterPost($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterPost', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterReply($message)
	{
		$params = array('actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterReply', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterEdit($message)
	{
		$params = array('actor' => $message->get('modified_by'), 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterEdit', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterDelete($message)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterDelete', $params);
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterUndelete($message)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterUndelete', $params);
	}

	/**
	 * @param   int $actor   actor
	 * @param   int $target  target
	 * @param   int $message message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		$params = array('actor' => $actor, 'target' => $target, 'message' => $message);
		KunenaIntegrationComprofiler::trigger('onAfterThankyou', $params);
	}

	/**
	 * @param   int $topic  topic
	 * @param   int $action action
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterSubscribe($topic, $action)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterSubscribe', $params);
	}

	/**
	 * @param   int $topic  topic
	 * @param   int $action action
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterFavorite($topic, $action)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterFavorite', $params);
	}

	/**
	 * @param   int $topic  topic
	 * @param   int $action action
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterSticky($topic, $action)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterSticky', $params);
	}

	/**
	 * @param   int $topic  topic
	 * @param   int $action action
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterLock($topic, $action)
	{
		$my     = Factory::getUser();
		$params = array('actor' => $my->id, 'topic' => $topic, 'action' => $action);
		KunenaIntegrationComprofiler::trigger('onAfterLock', $params);
	}

	/**
	 * @param   int $target target
	 * @param   int $actor  actor
	 * @param   int $delta  delta
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterKarma($target, $actor, $delta)
	{
		$params = array('actor' => $actor, 'target' => $target, 'delta' => $delta);
		KunenaIntegrationComprofiler::trigger('onAfterKarma', $params);
	}
}
