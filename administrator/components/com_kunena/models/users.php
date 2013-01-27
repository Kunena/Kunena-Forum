<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.modellist' );

/**
 * Users Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelUsers extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id',
					'username',
					'name',
					'email',
					'signature',
					'enabled',
					'banned',
					'moderator'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.search', 'filter_search', '', 'string' );
		$this->setState ( 'filter.search', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.username', 'filter_username', '', 'string' );
		$this->setState ( 'filter.username', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.email', 'filter_email', '', 'string' );
		$this->setState ( 'filter.email', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.signature', 'filter_signature', '', 'string' );
		$this->setState ( 'filter.signature', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.block', 'filter_block', '', 'string' );
		$this->setState ( 'filter.block', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.banned', 'filter_banned', '', 'string' );
		$this->setState ( 'filter.banned', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.moderator', 'filter_moderator', '', 'string' );
		$this->setState ( 'filter.moderator', $value );

		// List state information.
		parent::populateState('username', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.username');
		$id	.= ':'.$this->getState('filter.email');
		$id	.= ':'.$this->getState('filter.signature');
		$id	.= ':'.$this->getState('filter.block');
		$id	.= ':'.$this->getState('filter.banned');
		$id	.= ':'.$this->getState('filter.moderator');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__users AS a');

		// Join over the users for the linked user.
		$query->select('ku.*');
		$query->join('LEFT', '#__kunena_users AS ku ON a.id=ku.userid');

		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.username LIKE '.$search.' OR a.name LIKE '.$search.' OR a.email LIKE '.$search.')');
			}
		}

		// Filter by username or name.
		$search = $this->getState('filter.username');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('a.username LIKE '.$search . 'OR a.name LIKE '.$search);
		}

		// Filter by email.
		$search = $this->getState('filter.email');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('a.email LIKE '.$search);
		}

		// Filter by signature.
		$filter = $this->getState('filter.signature');
		if ($filter != '') {
			if ($filter) $query->where("ku.signature!={$db->quote('')} AND ku.signature IS NOT NULL");
			else $query->where("ku.signature={$db->quote('')} OR ku.signature IS NULL");
		}

		// Filter by block state.
		$filter = $this->getState('filter.block');
		if ($filter != '') {
			$query->where('a.block='.(int) $filter);
		}

		// Filter by banned state.
		$filter = $this->getState('filter.banned');
		if ($filter != '') {
			$now = new JDate ();
			if ($filter) $query->where("ku.banned={$db->quote($db->getNullDate())} OR ku.banned>{$db->quote($now->toSql())}");
			else $query->where("ku.banned IS NULL OR (ku.banned>{$db->quote($db->getNullDate())} AND ku.banned<{$db->quote($now->toSql())})");
		}

		// Filter by moderator state.
		$filter = $this->getState('filter.moderator');
		if ($filter != '') {
			$query->where('ku.moderator ='.(int) $filter);
		}

		// Add the list ordering clause.
		$direction	= strtoupper($this->state->get('list.direction'));
		switch ($this->state->get('list.ordering')) {
			case 'id':
				$query->order('a.id ' . $direction);
				break;
			case 'email':
				$query->order('a.email ' . $direction);
				break;
			case 'signature':
				$query->order('ku.signature ' . $direction);
				break;
			case 'enabled':
				$query->order('a.block ' . $direction);
				break;
			case 'banned':
				$query->order('ku.banned ' . $direction);
				break;
			case 'moderator':
				$query->order('ku.moderator ' . $direction);
				break;
			case 'name':
				$query->order('a.name ' . $direction);
			case 'username':
			default:
				$query->order('a.username ' . $direction);
		}

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}
