<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Announcement
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerAnnouncementListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerAnnouncementListDisplay extends KunenaControllerDisplay
{
	protected $name = 'Announcement/List';

	public $announcements;

	public $pagination;

	/**
	 * Prepare announcement list display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

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

		$moderator = KunenaUserHelper::getMyself()->isModerator();
		$this->pagination = new KunenaPagination(KunenaForumAnnouncementHelper::getCount(!$moderator), $limitstart, $limit);
		$this->announcements = KunenaForumAnnouncementHelper::getAnnouncements(
			$this->pagination->limitstart,
			$this->pagination->limit,
			!$moderator
		);
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
			$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
		}
	}
}
