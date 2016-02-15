<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicListModeratorDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * Prepare topic list for moderators.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();
		$access = KunenaAccess::getInstance();
		$this->moreUri = null;

		$params = $this->app->getParams('com_kunena');
		$start = $this->input->getInt('limitstart', 0);
		$limit = $this->input->getInt('limit', 0);

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->threads_per_page;
		}

		// Get configuration from menu item.
		$categoryIds = $params->get('topics_categories', array());
		$reverse = !$params->get('topics_catselection', 1);

		// Make sure that category list is an array.
		if (!is_array($categoryIds)) {
			$categoryIds = explode (',', $categoryIds);
		}

		if ((!$reverse && empty($categoryIds)) || in_array(0, $categoryIds)) {
			$categoryIds = false;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse);

		$finder = new KunenaForumTopicFinder;
		$finder
			->filterByCategories($categories)
			->filterAnsweredBy(array_keys($access->getModerators() + $access->getAdmins()), true)
			->filterByMoved(false)
			->where('locked', '=', 0);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		$this->topics = $finder
			->order('last_post_time', -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		$actions = array('delete', 'approve', 'undelete', 'move', 'permdelete');
		$this->actions = $this->getTopicActions($this->topics, $actions);

		$this->headerText = JText::_('COM_KUNENA_TOPICS_NEEDS_ATTENTION');
	}
}
