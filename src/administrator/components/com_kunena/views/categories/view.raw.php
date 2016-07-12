<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Category View
 *
 * @since  K5.0
 */
class KunenaAdminViewCategory extends KunenaView
{
	public function chkAliases()
	{
		$jinput = JFactory::getApplication()->input;
		$aliases = $jinput->get('aliases', array(), 'post', 'array');

		$db = JFactory::getDbo();
		$query = 'SELECT id FROM #__kunena_categories WHERE alias = ' . $aliases;
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{
			$response['msg']  = 'false';
		}
		else
		{
			$response['msg']  = 'true';
		}

		echo json_encode($response);
	}
}
