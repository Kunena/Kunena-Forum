<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Message
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerMessageListRecentDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerMessageListRecentDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * @var array|KunenaForumMessage[]
	 */
	protected $messages;

	/**
	 * Return display layout.
	 *
	 * @return KunenaLayout
	 */
	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Message/List')
			->set('me', $this->me)
			->set('config', $this->config)
			->set('messages', $this->messages)
			->set('headerText', $this->headerText)
			->set('pagination', $this->pagination)
			->set('state', $this->state);

		return $content;
	}

	/**
	 * Prepare category list display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
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

		$userid = $this->state->get('user');
		$user = is_numeric($userid) ? KunenaUserHelper::get($userid) : null;

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse = !$this->state->get('list.categories.in');
		$authorise = 'read';
		$order = 'time';

		$finder = new KunenaForumMessageFinder;
		$finder->filterByTime($time);

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved' :
				$authorise = 'topic.post.approve';
				$finder
					->filterByUser($user, 'author')
					->filterByHold(array(1));
				break;
			case 'deleted' :
				$authorise = 'topic.post.undelete';
				$finder
					->filterByUser($user, 'author')
					->filterByHold(array(2, 3));
				break;
			case 'mythanks' :
				$finder
					->filterByUser($user, 'thanker')
					->filterByHold(array(0));
				break;
			case 'thankyou' :
				$finder
					->filterByUser($user, 'thankee')
					->filterByHold(array(0));
				break;
			default :
				$finder
					->filterByUser($user, 'author')
					->filterByHold(array(0));
				break;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse, $authorise);
		$finder->filterByCategories($categories);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$this->messages = $finder
			->order($order, -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		// Load topics...
		$topicIds = array();

		foreach ($this->messages as $message)
		{
			$topicIds[(int) $message->thread] = (int) $message->thread;
		}

		$this->topics = KunenaForumTopicHelper::getTopics($topicIds, 'none');

		$userIds = $mesIds = array();

		foreach ($this->messages as $message)
		{
			$userIds[(int) $message->userid] = (int) $message->userid;
			$mesIds[(int) $message->id] = (int) $message->id;
		}

		if ($this->topics)
		{
			$this->prepareTopics($userIds, $mesIds);
		}

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved':
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
				break;
			case 'deleted':
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				break;
			case 'mythanks':
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				break;
			case 'thankyou':
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				break;
			case 'recent':
			default:
				$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
		}
	}
}
