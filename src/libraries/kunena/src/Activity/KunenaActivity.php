<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    KunenaActivity
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Activity;

\defined('_JEXEC') or die();

/**
 * Class KunenaActivity
 *
 * @since   Kunena 2.0
 */
class KunenaActivity
{
	/**
	 * Triggered before posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforePost(string $message): void
	{
	}

	/**
	 * Triggered after posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterPost(string $message): void
	{
	}

	/**
	 * Triggered before replying to a topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeReply(string $message): void
	{
	}

	/**
	 * Triggered after replying to a topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterReply(string $message): void
	{
	}

	/**
	 * Triggered before posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeHold(string $message): void
	{
	}

	/**
	 * Triggered before editing a post.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeEdit(string $message): void
	{
	}

	/**
	 * Triggered after editing a post.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterEdit(string $message): void
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterDelete(string $message): void
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterUndelete(string $message): void
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterDeleteTopic(string $message): void
	{
	}

	/**
	 * Triggered after (un)subscribing a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = subscribe, 0 = unsubscribe.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterSubscribe(int $topicid, int $action): void
	{
	}

	/**
	 * Triggered after (un)favoriting a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = favorite, 0 = unfavorite.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterFavorite(int $topicid, int $action): void
	{
	}

	/**
	 * Triggered after (un)stickying a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = sticky, 0 = unsticky.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterSticky(int $topicid, int $action): void
	{
	}

	/**
	 * Triggered after (un)locking a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = lock, 0 = unlock.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterLock(int $topicid, int $action): void
	{
	}

	/**
	 * Triggered after giving thankyou to a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterThankyou(int $actor, int $target, int $message): void
	{
	}

	/**
	 * Triggered after removing thankyou from a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterUnThankyou(int $actor, int $target, int $message): void
	{
	}

	/**
	 * Triggered after changing user karma.
	 *
	 * @param   int  $target  Target user Id.
	 * @param   int  $actor   Actor user Id (usually current user).
	 * @param   int  $delta   Points added / removed.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterKarma(int $target, int $actor, int $delta): void
	{
	}

	/**
	 * Get list of medals.
	 *
	 * @param   int  $userid  Users id
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserMedals(int $userid): void
	{
	}

	/**
	 * Get user points.
	 *
	 * @param   int  $userid  Users id
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserPoints(int $userid): void
	{
	}
}
