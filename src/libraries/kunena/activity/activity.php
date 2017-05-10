<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Activity
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaActivity
 *
 * @since 2.0
 */
class KunenaActivity
{
	/**
	 * Triggered before posting a new topic.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onBeforePost($message)
	{
	}

	/**
	 * Triggered after posting a new topic.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterPost($message)
	{
	}

	/**
	 * Triggered before replying to a topic.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onBeforeReply($message)
	{
	}

	/**
	 * Triggered after replying to a topic.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterReply($message)
	{
	}

	/**
	 * Triggered before editing a post.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onBeforeEdit($message)
	{
	}

	/**
	 * Triggered after editing a post.
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterEdit($message)
	{
	}

	/**
	 *
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterDelete($message)
	{
	}

	/**
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterUndelete($message)
	{
	}

	/**
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterDeleteTopic($message)
	{
	}

	/**
	 * Triggered after (un)subscribing a topic.
	 *
	 * @param   int $topicid Topic Id.
	 * @param   int $action  1 = subscribe, 0 = unsuscribe.
	 *
	 * @since Kunena
	 */
	public function onAfterSubscribe($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)favoriting a topic.
	 *
	 * @param   int $topicid Topic Id.
	 * @param   int $action  1 = favorite, 0 = unfavorite.
	 *
	 * @since Kunena
	 */
	public function onAfterFavorite($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)stickying a topic.
	 *
	 * @param   int $topicid Topic Id.
	 * @param   int $action  1 = sticky, 0 = unsticky.
	 *
	 * @since Kunena
	 */
	public function onAfterSticky($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)locking a topic.
	 *
	 * @param   int $topicid Topic Id.
	 * @param   int $action  1 = lock, 0 = unlock.
	 *
	 * @since Kunena
	 */
	public function onAfterLock($topicid, $action)
	{
	}

	/**
	 * Triggered after giving thankyou to a message.
	 *
	 * @param   int $actor   Actor user Id (usually current user).
	 * @param   int $target  Target user Id.
	 * @param   int $message Message Id.
	 *
	 * @since Kunena
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
	}

	/**
	 * Triggered after removing thankyou from a message.
	 *
	 * @param   int $actor   Actor user Id (usually current user).
	 * @param   int $target  Target user Id.
	 * @param   int $message Message Id.
	 *
	 * @since Kunena
	 */
	public function onAfterUnThankyou($actor, $target, $message)
	{
	}

	/**
	 * Triggered after changing user karma.
	 *
	 * @param   int $target Target user Id.
	 * @param   int $actor  Actor user Id (usually current user).
	 * @param   int $delta  Points added / removed.
	 *
	 * @since Kunena
	 */
	public function onAfterKarma($target, $actor, $delta)
	{
	}

	/**
	 * Get list of medals.
	 *
	 * @param $userid
	 *
	 * @return void
	 * @since Kunena
	 */
	public function getUserMedals($userid)
	{
	}

	/**
	 * Get user points.
	 *
	 * @param   int $userid
	 *
	 * @return  integer|void  Number of points.
	 * @since Kunena
	 */
	public function getUserPoints($userid)
	{
	}
}
