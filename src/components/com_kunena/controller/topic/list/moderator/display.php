<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

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
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$this->me       = KunenaUserHelper::getMyself();
		$access         = KunenaAccess::getInstance();
		$this->moreUri  = null;
		$this->embedded = $this->getOptions()->get('embedded', true);

		$params = $this->app->getParams('com_kunena');
		$start  = $this->input->getInt('limitstart', 0);
		$limit  = $this->input->getInt('limit', 0);
		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && KunenaConfig::getInstance()->sef_redirect)
		{
			if (KunenaConfig::getInstance()->moderator_id)
			{
				$itemidfix = KunenaConfig::getInstance()->moderator_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=moderator"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=moderator&Itemid={$itemidfix}", false));
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

		$threadsId = KunenaForumMessageHelper::getMessagesFromUsers(array_keys($access->getModerators() + $access->getAdmins()));
		$threadSearch = [];

		foreach($threadsId as $thread)
		{
			$threadSearch[] = $thread->thread;
		}

		$threadSearch = implode(',', $threadSearch);

		$finder = new KunenaForumTopicFinder;
		$this->topics = $finder
			->filterByCategories($categories)
			->filterTopicNotIn($threadSearch)
			->filterByMoved(false)
			->where('locked', '=', 0)
			->find();

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

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

		$this->headerText = Text::_('COM_KUNENA_TOPICS_NEEDS_ATTENTION');
	}
}
