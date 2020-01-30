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

namespace Kunena\Forum\Site\Controller\Topic\KunenaList\Unread;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Forum\Topic\TopicFinder;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Site\Model\TopicsModel;
use function defined;

/**
 * Class ComponentTopicControllerListDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerListUnreadDisplay extends KunenaControllerDisplay
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

		$this->model = new TopicsModel([], $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state    = $this->model->getState();
		$this->me       = \Kunena\Forum\Libraries\User\KunenaUserHelper::getMyself();
		$this->moreUri  = null;
		$access         = Access::getInstance();
		$start          = $this->state->get('list.start');
		$limit          = $this->state->get('list.limit');
		$params         = ComponentHelper::getParams('com_kunena');
		$Itemid         = $this->input->getInt('Itemid');
		$this->embedded = $this->getOptions()->get('embedded', true);

		// Handle &sel=x parameter.
		$time = $this->state->get('list.time');

		if ($time < 0)
		{
			$time = null;
		}
		elseif ($time == 0)
		{
			$time = new Date(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new Date(Factory::getDate()->toUnix() - ($time * 3600));
		}

		if (!$Itemid && $this->config->sef_redirect)
		{
			if ($this->config->moderator_id)
			{
				$itemidfix = $this->config->moderator_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(\Kunena\Forum\Libraries\Route\KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=unread"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=unread&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$finder = new TopicFinder;

		$this->topics = $finder
			->start($start)
			->limit($limit)
			->filterByTime($time)
			->order('id', 0)
			->filterByUserAccess($this->me)
			->filterByUserUnread($this->me)
			->find();

		$mesIds = [];

		$mesIds += TopicHelper::fetchNewStatus($this->topics, $this->me->userid);

		$list = [];

		$this->count = 0;

		foreach ($this->topics as $topic)
		{
			if ($topic->unread)
			{
				$list[] = $topic;
				$this->count++;
			}
		}

		$this->topics = $list;

		$this->pagination = new Pagination($finder->count(), $start, $limit);

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		$actions          = ['delete', 'approve', 'undelete', 'move', 'permdelete'];
		$this->actions    = $this->getTopicActions($this->topics, $actions);
		$this->headerText = Text::_('COM_KUNENA_UNREAD');
	}
}
