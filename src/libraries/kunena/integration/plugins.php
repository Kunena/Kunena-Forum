<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaIntegrationActivity
 *
 * @since 3.0.4
 */
class KunenaIntegrationPlugins
{
	/**
	 * Returns total kunena plugins.
	 *
	 * @return boolean Codename.
	 * @since Kunena
	 * @throws Exception
	 */
	public static function getTotalPlugins()
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('kunena'));
		$db->setQuery($query);

		try
		{
			$total = $db->setQuery($query)->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return (int) $total;
	}
}
