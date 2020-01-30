<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\KunenaList\Moderator;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Forum\Category\Helper;
use Kunena\Forum\Libraries\Forum\Topic\Finder;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use function defined;

/**
 * Class ComponentTopicControllerListDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerListModeratorDisplay extends KunenaControllerDisplay
{
	/**
	 * Prepare topic list for moderators.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$this->me       = \Kunena\Forum\Libraries\User\Helper::getMyself();
		$access         = Access::getInstance();
		$this->moreUri  = null;
		$this->embedded = $this->getOptions()->get('embedded', true);

		$params = ComponentHelper::getParams('com_kunena');
		$start  = $this->input->getInt('limitstart', 0);
		$limit  = $this->input->getInt('limit', 0);
		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && $this->config->sef_redirect)
		{
			if ($this->config->moderators_id)
			{
				$itemidfix = $this->config->moderators_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(\Kunena\Forum\Libraries\Route\KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=moderator"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=moderator&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->threads_per_page;
		}

		// Get configuration from menu item.
		$categoryIds = $params->get('topics_categories', []);
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

		$categories = Helper::getCategories($categoryIds, $reverse);

		$finder = new Finder;
		$finder
			->filterByCategories($categories)
			->filterAnsweredBy(array_keys($access->getModerators() + $access->getAdmins()), true)
			->filterByMoved(false)
			->where('locked', '=', 0);

		$this->pagination = new Pagination($finder->count(), $start, $limit);

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

		$actions       = ['delete', 'approve', 'undelete', 'move', 'permdelete'];
		$this->actions = $this->getTopicActions($this->topics, $actions);

		$this->headerText = Text::_('COM_KUNENA_TOPICS_NEEDS_ATTENTION');
	}
}
