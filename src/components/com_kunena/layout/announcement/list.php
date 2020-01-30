<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.List
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Announcement;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\User\Helper;
use function defined;

/**
 * KunenaLayoutAnnouncementList
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutAnnouncementList extends Layout
{
	/**
	 * Method to get moderation action in announcements list
	 *
	 * @see     KunenaCompatLayoutBase::getOptions()
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function getOptions()
	{
		$options = [];

		if (Helper::getMyself()->isModerator())
		{
			$options[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
			$options[] = HTMLHelper::_('select.option', 'unpublish', Text::_('COM_KUNENA_UNPUBLISH'));
			$options[] = HTMLHelper::_('select.option', 'publish', Text::_('COM_KUNENA_PUBLISH'));
			$options[] = HTMLHelper::_('select.option', 'edit', Text::_('COM_KUNENA_EDIT'));
			$options[] = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_DELETE'));
		}

		return $options;
	}
}
