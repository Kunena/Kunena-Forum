<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerAnnouncementEditDisplay
 */
class ComponentKunenaControllerAnnouncementEditDisplay extends KunenaControllerDisplay
{
	protected $name = 'Announcement/Edit';

	public $announcement;

	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id', null);

		$this->announcement = KunenaForumAnnouncementHelper::get($id);
		$this->announcement->tryAuthorise($id ? 'edit' : 'create');

		return true;
	}

	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
	}
}
