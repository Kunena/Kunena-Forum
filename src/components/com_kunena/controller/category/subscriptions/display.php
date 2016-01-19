<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerCategorySubscriptionsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategorySubscriptionsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/List';

	public $total;

	public $pagination;

	public $categories = array();

	/**
	 * Prepare category subscriptions display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$me = KunenaUserHelper::getMyself();

		if (!$me->exists())
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 401);
		}

		$limit = $this->input->getInt('limit', 0);

		if ($limit < 1 || $limit > 100)
		{
			$limit = 20;
		}

		$limitstart = $this->input->getInt('limitstart', 0);

		if ($limitstart < 0)
		{
			$limitstart = 0;
		}

		list($total, $this->categories) = KunenaForumCategoryHelper::getLatestSubscriptions($me->userid);

		$topicIds = array();
		$userIds = array();
		$postIds = array();

		foreach ($this->categories as $category)
		{
			// Get list of topics.
			if ($category->last_topic_id)
			{
				$topicIds[$category->last_topic_id] = $category->last_topic_id;
			}
		}

		// Pre-fetch topics (also display unauthorized topics as they are in allowed categories).
		$topics = KunenaForumTopicHelper::getTopics($topicIds, 'none');

		// Pre-fetch users (and get last post ids for moderators).
		foreach ($topics as $topic)
		{
			$userIds[$topic->last_post_userid] = $topic->last_post_userid;
			$postIds[$topic->id] = $topic->last_post_id;
		}

		KunenaUserHelper::loadUsers($userIds);
		KunenaForumMessageHelper::getMessages($postIds);

		// Pre-fetch user related stuff.
		if ($me->exists() && !$me->isBanned())
		{
			// Load new topic counts.
			KunenaForumCategoryHelper::getNewTopics(array_keys($this->categories));
		}

		$this->actions = $this->getActions();

		$this->pagination = new JPagination($total, $limitstart, $limit);

		$this->headerText = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');
	}

	/**
	 * Get topic action option list.
	 *
	 * @return array
	 */
	public function getActions()
	{
		$options = array();
		$options[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$options[] = JHtml::_('select.option', 'unsubscribe', JText::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));

		return $options;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if (!empty($params_title))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$title = JText::_('COM_KUNENA_VIEW_CATEGORIES_USER');
			$this->setTitle($title);
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$keywords = JText::_('COM_KUNENA_CATEGORIES');
			$this->setKeywords($keywords);
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$description = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS') . ' - ' . $this->config->board_title;
			$this->setDescription($description);
		}
	}
}
