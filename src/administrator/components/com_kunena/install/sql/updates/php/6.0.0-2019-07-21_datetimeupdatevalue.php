<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Kunena 6.0.0: Update value of type datetime in all tables to changes default value
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena
 */
function kunena_600_2019_07_21_datetimeupdatevalue($parent)
{
	$db  = Factory::getDbo();

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_DATETIME_VALUE_IN_TABLES'), 'success' => true);
}
