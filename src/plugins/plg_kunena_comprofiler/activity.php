<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Comprofiler;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Integration\Activity;
use function defined;

require_once dirname(__FILE__) . '/integration.php';

/**
 * Class KunenaActivityComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaActivityComprofiler extends Activity
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaActivityComprofiler constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function __construct($params)
	{
		$this->params = $params;

		parent::__construct();
	}

	/**
	 * @param   int  $userid  userid
	 *
	 * @return  null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUserPoints($userid)
	{
		$points = null;
		$params = ['userid' => $userid, 'points' => &$points];
		KunenaIntegrationComprofiler::trigger('getUserPoints', $params);

		return $points;
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onBeforePost($message)
	{
		$params = ['actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onBeforePost', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onBeforeReply($message)
	{
		$params = ['actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onBeforeReply', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onBeforeEdit($message)
	{
		$params = ['actor' => $message->get('modified_by'), 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onBeforeEdit', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterPost($message)
	{
		$params = ['actor' => $message->get('userid'), 'replyto' => 0, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterPost', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterReply($message)
	{
		$params = ['actor' => $message->get('userid'), 'replyto' => (int) $message->getParent()->userid, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterReply', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterEdit($message)
	{
		$params = ['actor' => $message->get('modified_by'), 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterEdit', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterDelete($message)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterDelete', $params);
	}

	/**
	 * @param   string  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterUndelete($message)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterUndelete', $params);
	}

	/**
	 * @param   int  $actor    actor
	 * @param   int  $target   target
	 * @param   int  $message  message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		$params = ['actor' => $actor, 'target' => $target, 'message' => $message];
		KunenaIntegrationComprofiler::trigger('onAfterThankyou', $params);
	}

	/**
	 * @param   int  $topic   topic
	 * @param   int  $action  action
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterSubscribe($topic, $action)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'topic' => $topic, 'action' => $action];
		KunenaIntegrationComprofiler::trigger('onAfterSubscribe', $params);
	}

	/**
	 * @param   int  $topic   topic
	 * @param   int  $action  action
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterFavorite($topic, $action)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'topic' => $topic, 'action' => $action];
		KunenaIntegrationComprofiler::trigger('onAfterFavorite', $params);
	}

	/**
	 * @param   int  $topic   topic
	 * @param   int  $action  action
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterSticky($topic, $action)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'topic' => $topic, 'action' => $action];
		KunenaIntegrationComprofiler::trigger('onAfterSticky', $params);
	}

	/**
	 * @param   int  $topic   topic
	 * @param   int  $action  action
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterLock($topic, $action)
	{
		$my     = Factory::getApplication()->getIdentity();
		$params = ['actor' => $my->id, 'topic' => $topic, 'action' => $action];
		KunenaIntegrationComprofiler::trigger('onAfterLock', $params);
	}

	/**
	 * @param   int  $target  target
	 * @param   int  $actor   actor
	 * @param   int  $delta   delta
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function onAfterKarma($target, $actor, $delta)
	{
		$params = ['actor' => $actor, 'target' => $target, 'delta' => $delta];
		KunenaIntegrationComprofiler::trigger('onAfterKarma', $params);
	}
}
