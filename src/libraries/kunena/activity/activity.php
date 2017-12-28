<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Activity
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
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
	 */
	public function onBeforePost($message)
{}

	/**
	 * Triggered after posting a new topic.
	 *
	 * @param $message
	 */
	public function onAfterPost($message)
{}

	/**
	 * Triggered before replying to a topic.
	 *
	 * @param $message
	 */
	public function onBeforeReply($message)
{}

	/**
	 * Triggered after replying to a topic.
	 *
	 * @param $message
	 */
	public function onAfterReply($message)
{}

	/**
	 * Triggered before editing a post.
	 *
	 * @param $message
	 */
	public function onBeforeEdit($message)
{}

	/**
	 * Triggered after editing a post.
	 *
	 * @param $message
	 */
	public function onAfterEdit($message)
{}

	/** TODO: Looks like these aren't fully working..
	 *
	 * @param $message
	 */
	public function onAfterDelete($message)
{}

	/**
	 * @param $message
	 */
	public function onAfterUndelete($message)
{}

	/**
	 * @param $message
	 */
	public function onAfterDeleteTopic($message)
{}

	/**
	 * Triggered after (un)subscribing a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = subscribe, 0 = unsuscribe.
	 */
	public function onAfterSubscribe($topicid, $action)
{}

	/**
	 * Triggered after (un)favoriting a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = favorite, 0 = unfavorite.
	 */
	public function onAfterFavorite($topicid, $action)
{}

	/**
	 * Triggered after (un)stickying a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = sticky, 0 = unsticky.
	 */
	public function onAfterSticky($topicid, $action)
{}

	/**
	 * Triggered after (un)locking a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = lock, 0 = unlock.
	 */
	public function onAfterLock($topicid, $action)
{}

	/**
	 * Triggered after giving thankyou to a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 */
	public function onAfterThankyou($actor, $target, $message)
{}

	/**
	 * Triggered after removing thankyou from a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 */
	public function onAfterUnThankyou($actor, $target, $message)
{}

	/**
	 * Triggered after changing user karma.
	 *
	 * @param   int  $target  Target user Id.
	 * @param   int  $actor   Actor user Id (usually current user).
	 * @param   int  $delta   Points added / removed.
	 */
	public function onAfterKarma($target, $actor, $delta)
{}

	/**
	 * Get list of medals.
	 *
	 * @param $userid
	 *
	 * @return void
	 */
	public function getUserMedals($userid)
{}

	/**
	 * Get user points.
	 *
	 * @param   int  $userid
	 *
	 * @return  integer|void  Number of points.
	 */
	public function getUserPoints($userid)
{}
}
