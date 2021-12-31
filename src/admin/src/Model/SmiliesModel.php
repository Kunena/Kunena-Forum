<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Smileys Model for Kunena
 *
 * @since  3.0
 */
class SmiliesModel extends ListModel
{
	/**
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id',
				'code',
				'location',
				'greylocation',
				'emoticonbar',
			];
		}

		parent::__construct($config);
	}

	/**
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   ordering
	 * @param   string  $direction  direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.smilies';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout        = $app->input->get('layout');
		$this->context = 'com_kunena.admin.smilies';

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$filterActive = '';

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.code', 'filter_code', '', 'string');
		$this->setState('filter.code', $value !== '' ? $value : null);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.location', 'filter_location', '', 'string');
		$this->setState('filter.location', $value !== '' ? $value : null);

		$this->setState('filter.active', !empty($filterActive));

		// List state information.
		parent::populateState('id', 'asc');
	}

	/**
	 * @param   string  $id  id
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.code');
		$id .= ':' . $this->getState('filter.url');

		return parent::getStoreId($id);
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected function getListQuery(): QueryInterface
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.code, a.location, a.greylocation, a.emoticonbar'
			)
		);

		$query->from($db->quoteName('#__kunena_smileys', 'a'));

		$filter = $this->getState('filter.code');

		if (!empty($filter))
		{
			$code = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.code LIKE ' . $code . ')');
		}

		$filter = $this->getState('filter.location');

		if (!empty($filter))
		{
			$location = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.location LIKE ' . $location . ')');
		}

		// Add the list ordering clause.
		$direction = strtoupper($this->state->get('list.direction'));

		switch ($this->state->get('list.ordering'))
		{
			case 'code':
				$query->order('a.code ' . $direction);
				break;
			case 'location':
				$query->order('a.location ' . $direction);
				break;
			case 'greylocation':
				$query->order('a.greylocation ' . $direction);
				break;
			case 'emoticonbar':
				$query->order('a.emoticonbar ' . $direction);
				break;
			default:
				$query->order('a.id ' . $direction);
		}

		$db->setQuery($query);

		return $query;
	}
}
