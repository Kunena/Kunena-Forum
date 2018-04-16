<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.List
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

/**
 * KunenaLayoutAnnouncementList
 *
 * @since  K4.0
 */
class KunenaLayoutAnnouncementList extends KunenaLayout
{
	/**
	 * Method to get moderation action in annoucements list
	 *
	 * @see   KunenaCompatLayoutBase::getOptions()
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function getOptions()
	{
		$options = array();

		if (KunenaUserHelper::getMyself()->isModerator())
		{
			$options[] = HTMLHelper::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
			$options[] = HTMLHelper::_('select.option', 'unpublish', JText::_('COM_KUNENA_UNPUBLISH'));
			$options[] = HTMLHelper::_('select.option', 'publish', JText::_('COM_KUNENA_PUBLISH'));
			$options[] = HTMLHelper::_('select.option', 'edit', JText::_('COM_KUNENA_EDIT'));
			$options[] = HTMLHelper::_('select.option', 'delete', JText::_('COM_KUNENA_DELETE'));
		}

		return $options;
	}
}
