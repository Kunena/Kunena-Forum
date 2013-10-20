<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerTopicListDisplay
 */
abstract class ComponentKunenaControllerTopicListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaUser
	 */
	protected $me;
	/**
	 * @var array|KunenaForumTopic[]
	 */
	protected $topics;
		/**
	 * @var KunenaPagination
	 */
	protected $pagination;
	/**
	 * @var string
	 */
	protected $headerText;

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Topic/List')
			->set('me', $this->me)
			->set('config', $this->config)
			->set('topics', $this->topics)
			->set('headerText', $this->headerText)
			->set('pagination', $this->pagination);
		return $content;
	}

	protected function prepareTopics() {
		// collect user ids for avatar prefetch when integrated
		$userlist = array();
		$lastpostlist = array();
		foreach ($this->topics as $topic) {
			$userlist[(int) $topic->first_post_userid] = (int) $topic->first_post_userid;
			$userlist[(int) $topic->last_post_userid] = (int) $topic->last_post_userid;
			$lastpostlist[(int) $topic->last_post_id] = (int) $topic->last_post_id;
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		if (!empty($userlist)) KunenaUserHelper::loadUsers($userlist);

		$topicIds = array_keys($this->topics);
		KunenaForumTopicHelper::getUserTopics($topicIds);
		KunenaForumTopicHelper::getKeywords($topicIds);
		$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);

		// Fetch last / new post positions when user can see unapproved or deleted posts
		if ($lastreadlist || $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus()) {
			KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
		}
	}

	protected function prepareDocument()
	{
		$page = $this->pagination->pagesCurrent;
		$total = $this->pagination->pagesTotal;
		$headerText = $this->headerText . ($total > 1 ? " ({$page}/{$total})" : '');

		$this->setTitle($headerText);
	}
}
