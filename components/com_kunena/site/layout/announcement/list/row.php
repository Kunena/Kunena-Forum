<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Announcement.List
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutAnnouncementListRow
 *
 * @since  K4.0
 *
 */
class KunenaLayoutAnnouncementListRow extends KunenaLayout
{
	/**
	 * Method to check if the user can publish an announcement
	 *
	 * @return boolean
	 */
	public function canPublish()
	{
		return $this->announcement->authorise('edit');
	}

	/**
	 * Method to check if the user can edit an announcement
	 *
	 * @return boolean
	 */
	public function canEdit()
	{
		return $this->announcement->authorise('edit');
	}

	/**
	 * Method to check if the user can delete an announcement
	 *
	 * @return boolean
	 */
	public function canDelete()
	{
		return $this->announcement->authorise('delete');
	}

	/**
	 * Method to display an announcement field
	 *
	 * @param   string  $name  The name of the field
	 * @param   string  $mode  Define the way to display the date on the field
	 *
	 * @return boolean
	 */
	public function displayField($name, $mode=null)
	{
		return $this->announcement->displayField($name, $mode);
	}
}
