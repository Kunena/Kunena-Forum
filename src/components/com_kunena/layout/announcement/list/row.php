<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.List
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutAnnouncementListRow
 *
 * @since  K4.0
 */
class KunenaLayoutAnnouncementListRow extends KunenaLayout
{
	/**
	 * Method to check if the user can publish an announcement
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function canPublish()
	{
		return $this->announcement->isAuthorised('edit');
	}

	/**
	 * Method to check if the user can edit an announcement
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function canEdit()
	{
		return $this->announcement->isAuthorised('edit');
	}

	/**
	 * Method to check if the user can delete an announcement
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function canDelete()
	{
		return $this->announcement->isAuthorised('delete');
	}

	/**
	 * Method to display an announcement field
	 *
	 * @param   string $name The name of the field
	 * @param   string $mode Define the way to display the date on the field
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function displayField($name, $mode = null)
	{
		return $this->announcement->displayField($name, $mode);
	}
}
