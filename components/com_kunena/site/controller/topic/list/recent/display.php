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
 * Class ComponentKunenaControllerTopicListRecentDisplay
 */
class ComponentKunenaControllerTopicListRecentDisplay extends ComponentKunenaControllerTopicListDisplay
{
	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Topic/List')
			->set('me', $this->me)
			->set('config', $this->config)
			->set('topics', $this->topics)
			->set('headerText', $this->headerText)
			->set('pagination', $this->pagination)
			->set('state', $this->state);
		return $content;
	}

	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics();
		$this->state = $this->model->getState();
		$this->me = KunenaUserHelper::getMyself();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Handle &sel=x parameter.
		$time = $this->state->get('list.time');
		if ($time < 0) {
			$time = null;
		} elseif ($time == 0) {
			$time = new JDate(KunenaFactory::getSession()->lasttime);
		} else {
			$time = new JDate(JFactory::getDate()->toUnix() - ($time * 3600));
		}

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse = !$this->state->get('list.categories.in');
		$authorise = 'read';
		$order = 'last_post_time';

		$finder = new KunenaForumTopicFinder();
		$finder->filterByMoved(false);

		switch ($this->state->get('list.mode')) {
			case 'topics' :
				$order = 'first_post_time';
				$finder
					->filterByHold(array(0))
					->filterByTime($time, null, false);
				break;
			case 'sticky' :
				$finder
					->filterByHold(array(0))
					->filterBy('t.ordering', '>', 0)
					->filterByTime($time);
				break;
			case 'locked' :
				$finder
					->filterByHold(array(0))
					->filterBy('t.locked', '>', 0)
					->filterByTime($time);
				break;
			case 'noreplies' :
				$finder
					->filterByHold(array(0))
					->filterBy('t.posts', '=', 1)
					->filterByTime($time);
				break;
			case 'unapproved' :
				$authorise = 'topic.approve';
				$finder
					->filterByHold(array(1))
					->filterByTime($time);
				break;
			case 'deleted' :
				$authorise = 'topic.undelete';
				$finder
					->filterByHold(array(2, 3))
					->filterByTime($time);
				break;
			case 'replies' :
			default :
				$finder
					->filterByHold(array(0))
					->filterByTime($time);
				break;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse, $authorise);
		$finder->filterByCategories($categories);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$this->topics = $finder
			->order($order, -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		if ($this->topics) $this->prepareTopics();

		switch ($this->state->get('list.mode')) {
			case 'topics' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				break;
			case 'sticky' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				break;
			case 'locked' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				break;
			case 'noreplies' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				break;
			case 'unapproved' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				break;
			case 'deleted' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				break;
			case 'replies' :
			default :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT');
		}
	}
}
