<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Category View
 *
 * @since   Kunena 5.0
 */
class KunenaAdminViewCategories extends KunenaView
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function displayChkAliases()
	{
		$alias = $this->app->input->get('alias', null, 'string');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from($db->quoteName('#__kunena_categories'))
			->where('alias = ' . $db->quote($alias));
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{
			$response['msg'] = 0;
		}
		else
		{
			$response['msg'] = 1;
		}

		echo json_encode($response);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function displayDeleteAlias()
	{
		$alias = $this->app->input->get('alias', null, 'string');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('*')
			->from($db->quoteName('#__kunena_aliases'))
			->where('alias = ' . $db->quote($alias));
		$db->setQuery($query);

		$response['msg'] = 1;

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			$response['msg'] = 0;
		}

		echo json_encode($response);
	}
}
