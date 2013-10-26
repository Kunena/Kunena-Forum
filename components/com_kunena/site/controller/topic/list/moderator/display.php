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
 * Class ComponentKunenaControllerTopicListDisplay
 *
 * @since  3.1
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

		$start = $this->input->getInt('limitstart', 0);
		$limit = $this->input->getInt('limit', 0);

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->threads_per_page;
		}

		$finder = new KunenaForumTopicFinder;
		$finder->filterByUserAccess($this->me)
			->filterAnsweredBy(array_keys($access->getModerators() + $access->getAdmins()), true)
			->filterByMoved(false)
			->filterBy('locked', '=', 0);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$this->topics = $finder
			->order('last_post_time', -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		// TODO <-
		$this->headerText = JText::_('Topics Needing Attention');
	}
}
