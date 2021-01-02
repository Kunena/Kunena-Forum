<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
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
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
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

		$model = new TopicsModel([], $this->input);
		$model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$state    = $model->getState();
		$me       = KunenaUserHelper::getMyself();
		$moreUri  = null;
		$access   = KunenaAccess::getInstance();
		$start    = $state->get('list.start');
		$limit    = $state->get('list.limit');
		$params   = ComponentHelper::getParams('com_kunena');
		$Itemid   = $this->input->getInt('Itemid');
		$embedded = $this->getOptions()->get('embedded', true);

		// Handle &sel=x parameter.
		$time = $state->get('list.time');

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
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=unread"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=unread&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$finder = new KunenaTopicFinder;

		$topics = $finder
			->start($start)
			->limit($limit)
			->filterByTime($time)
			->order('id', 0)
			->filterByUserAccess($me)
			->filterByUserUnread($me)
			->find();

		$mesIds = [];

		$mesIds += KunenaTopicHelper::fetchNewStatus($topics, $me->userid);

		$list = [];

		$count = 0;

		foreach ($topics as $topic)
		{
			if ($topic->unread)
			{
				$list[] = $topic;
				$count++;
			}
		}

		$topics = $list;

		$pagination = new KunenaPagination($finder->count(), $start, $limit);

		if ($moreUri)
		{
			$pagination->setUri($moreUri);
		}

		if ($topics)
		{
			$this->prepareTopics();
		}

		$actions    = ['delete', 'approve', 'undelete', 'move', 'permdelete'];
		$actions1   = $this->getTopicActions($topics, $actions);
		$headerText = Text::_('COM_KUNENA_UNREAD');
	}
}
