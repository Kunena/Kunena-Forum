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
class ComponentKunenaControllerCategoryTopicsDisplay extends KunenaControllerDisplay
{
	protected $category;

	/**
	 * @var KunenaUser
	 */
	protected $me;
	/**
	 * @var KunenaConfig
	 */
	protected $config;

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Category/Item')
			->set('headerText', $this->headerText)
			->set('category', $this->category)
			->set('topics', $this->topics)
			->set('total', $this->total)
			->set('me', $this->me)
			->set('config', $this->config)
			->set('pagination', $this->pagination);
		return $content;
	}

	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();

		$catid = $this->input->getInt('catid');
		$limitstart = $this->input->getInt('limitstart', 0);
		$limit = $this->input->getInt('limit', 0);
		if ($limit < 1 || $limit > 100) $limit = $this->config->threads_per_page;

		// TODO:
		$direction = 'DESC';

		$this->category = KunenaForumCategoryHelper::get($catid);
		if (!$this->category->exists()) return;

		$this->headerText = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;

		$topic_ordering = $this->category->topic_ordering;

		$access = KunenaAccess::getInstance();
		$hold = $access->getAllowedHold($this->me, $catid);
		$moved = 1;
		$params = array(
			'hold'=>$hold,
			'moved'=>$moved);
		switch ($topic_ordering) {
			case 'alpha':
				$params['orderby'] = 'tt.ordering DESC, tt.subject ASC ';
				break;
			case 'creation':
				$params['orderby'] = 'tt.ordering DESC, tt.first_post_time ' . $direction;
				break;
			case 'lastpost':
			default:
				$params['orderby'] = 'tt.ordering DESC, tt.last_post_time ' . $direction;
		}

		list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);
		if ($this->total > 0) {
			// collect user ids for avatar prefetch when integrated
			$userlist = array();
			$lastpostlist = array();
			foreach ( $this->topics as $topic ) {
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
				$lastpostlist[intval($topic->last_post_id)] = intval($topic->last_post_id);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

			KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
			KunenaForumTopicHelper::getKeywords(array_keys($this->topics));
			$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);

			// Fetch last / new post positions when user can see unapproved or deleted posts
			if (($lastpostlist || $lastreadlist) && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus())) {
				KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
			}
		}
		$this->pagination = new KunenaPagination($this->total, $limitstart, $limit);
		$this->pagination->setDisplayedPages(5);
	}
}
