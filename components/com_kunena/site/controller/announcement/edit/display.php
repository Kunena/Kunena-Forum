<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Announcement
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerAnnouncementEditDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerAnnouncementEditDisplay extends KunenaControllerDisplay
{
	protected $name = 'Announcement/Edit';

	public $announcement;

	/**
	 * Prepare announcement form display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id', null);

		$this->announcement = KunenaForumAnnouncementHelper::get($id);
		$this->announcement->tryAuthorise($id ? 'edit' : 'create');
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

		if (!empty($params->get('page_title')))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
		}

		if (!empty($params->get('menu-meta_keywords')))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
		}

		if (!empty($params->get('menu-meta_description')))
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
