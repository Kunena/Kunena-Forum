<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicListUnreadDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * Prepare topic list for moderators.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$this->me      = KunenaUserHelper::getMyself();
		$access        = KunenaAccess::getInstance();
		$this->moreUri = null;

		$params = $this->app->getParams('com_kunena');
		$start  = $this->input->getInt('limitstart', 0);
		$limit  = $this->input->getInt('limit', 0);
		$Itemid = $this->input->getInt('Itemid');
		$this->embedded = $this->getOptions()->get('embedded', true);

		if (!$Itemid)
		{
			if (KunenaConfig::getInstance()->moderator_id)
			{
				$itemidfix = KunenaConfig::getInstance()->moderator_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=unread"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = JControllerLegacy::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=unread&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->threads_per_page;
		}

		// Get configuration from menu item.
		$categoryIds = $params->get('topics_categories', array());
		$reverse     = !$params->get('topics_catselection', 1);

		// Make sure that category list is an array.
		if (!is_array($categoryIds))
		{
			$categoryIds = explode(',', $categoryIds);
		}

		if ((!$reverse && empty($categoryIds)) || in_array(0, $categoryIds))
		{
			$categoryIds = false;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse);

		$finder = new KunenaForumTopicFinder;

		$this->topics = $finder
			->start($start)
			->limit($limit)
			->filterByCategories($categories)
			->filterByUserAccess($this->me)
			->find();

		$mesIds = array();

		$mesIds += KunenaForumTopicHelper::fetchNewStatus($this->topics, $this->me->userid);

		$list = array();

		foreach($this->topics as $topic)
		{
			if ($topic->unread)
			{
				$list[] = $topic;
			}
		}

		$this->topics = $list;

		$this->pagination = new KunenaPagination(count($list), $start, $limit);

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		$actions       = array('delete', 'approve', 'undelete', 'move', 'permdelete');
		$this->actions = $this->getTopicActions($this->topics, $actions);

		$this->headerText = JText::_('COM_KUNENA_UNREAD');
	}
}
