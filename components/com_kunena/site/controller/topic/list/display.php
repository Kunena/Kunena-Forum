<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 */
class ComponentKunenaControllerTopicListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaUser
	 */
	protected $me;
	/**
	 * @var KunenaConfig
	 */
	protected $config;
	protected $topics;

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Topic/List')
			->set('me', $this->me)
			->set('config', $this->config)
			->set('total', $this->total)
			->set('topics', $this->topics)
			->set('headerText', 'Topics Needing Attention') // TODO <-
			->set('pagination', $this->pagination);
		return $content;
	}

	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();
		$access = KunenaAccess::getInstance();

		$start = $this->input->getInt('limitstart', 0);
		$limit = $this->input->getInt('limit', 0);
		if ($limit < 1 || $limit > 100) $limit = $this->config->threads_per_page;

		// TODO: add more parameters from the model

		$finder = new KunenaForumTopicFinder();
		$finder->filterByUserAccess($this->me)
			->filterAnsweredBy(array_keys($access->getModerators() + $access->getAdmins()), true)
			->filterByMoved(false)
			->filterBy('locked', '=', 0);

		//$cache = JFactory::getCache('com_kunena', 'callback');
		//$cache->setLifeTime(180);
		//$this->total = $cache->get(array($finder, 'count'), array(), 'topics_count_need_attention');

		$this->total = $finder->count();
		$this->pagination = new KunenaPagination($this->total, $start, $limit);

		$this->topics = $finder
			->order('last_post_time', -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		if ($this->topics) {
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
	}
}
