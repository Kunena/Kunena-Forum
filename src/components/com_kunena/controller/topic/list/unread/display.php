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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

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

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state    = $this->model->getState();
		$this->me       = KunenaUserHelper::getMyself();
		$this->moreUri  = null;
		$access         = KunenaAccess::getInstance();
		$start          = $this->state->get('list.start');
		$limit          = $this->state->get('list.limit');
		$params         = $this->app->getParams('com_kunena');
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
			$time = new \Joomla\CMS\Date\Date(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new \Joomla\CMS\Date\Date(Factory::getDate()->toUnix() - ($time * 3600));
		}

		if (!$Itemid && KunenaConfig::getInstance()->sef_redirect)
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

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=unread&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$finder = new KunenaForumTopicFinder;

		$this->topics = $finder
			->start($start)
			->limit($limit)
			->filterByTime($time)
			->order('id', 0)
			->filterByUserAccess($this->me)
			->find();

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$limitnew = $finder->count();

		$mesIds = array();

		$mesIds += KunenaForumTopicHelper::fetchNewStatus($this->topics, $this->me->userid);

		$list = array();

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

		$this->pagination = new KunenaPagination($limitnew, $start, $limit);

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		$actions          = array('delete', 'approve', 'undelete', 'move', 'permdelete');
		$this->actions    = $this->getTopicActions($this->topics, $actions);
		$this->headerText = Text::_('COM_KUNENA_UNREAD');
	}
}
