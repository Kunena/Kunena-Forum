<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicListUserDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerTopicListUserDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * Return display layout.
	 *
	 * @return KunenaLayout
	 */
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

	/**
	 * Prepare user's topic list.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics;
		$this->state = $this->model->getState();
		$this->me = KunenaUserHelper::getMyself();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Handle &sel=x parameter.
		$time = $this->state->get('list.time');

		if ($time < 0)
		{
			$time = null;
		}
		elseif ($time == 0)
		{
			$time = new JDate(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new JDate(JFactory::getDate()->toUnix() - ($time * 3600));
		}

		$user = KunenaUserHelper::get($this->state->get('user'));

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse = !$this->state->get('list.categories.in');
		$authorise = 'read';
		$order = 'last_post_time';

		$finder = new KunenaForumTopicFinder;
		$finder
			->filterByMoved(false)
			->filterByHold(array(0))
			->filterByTime($time);

		switch ($this->state->get('list.mode'))
		{
			case 'posted' :
				$finder
					->filterByUser($user, 'posted')
					->order('last_post_id', -1, 'ut');
				break;

			case 'started' :
				$finder->filterByUser($user, 'owner');
				break;

			case 'favorites' :
				$finder->filterByUser($user, 'favorited');
				break;

			case 'subscriptions' :
				$finder->filterByUser($user, 'subscribed');
				break;

			default :
				$finder
					->filterByUser($user, 'involved')
					->order('favorite', -1, 'ut');
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

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		switch ($this->state->get('list.mode'))
		{
			case 'posted' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
				break;
			case 'started' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
				break;
			case 'favorites' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
				break;
			case 'subscriptions' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
				break;
			case 'plugin' :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_PLUGIN_' . strtoupper($this->state->get('list.modetype')));
				break;
			default :
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
		}
	}
}
