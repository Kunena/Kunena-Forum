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
 * Ranks Model for Kunena
 *
 * @since 3.0
 */
class KunenaAdminModelRanks extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'title',
				'min',
				'special',
				'image',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		// List state information
		$value = $this->getUserStateFromRequest ( $this->context .'.filter.title', 'filter_title', '', 'string' );
		$this->setState ( 'filter.title', $value );

		$value = $this->getUserStateFromRequest ( $this->context .'.filter.special', 'filter_special', '', 'string' );
		$this->setState ( 'filter.special', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( $this->context .'.filter.min', 'filter_min', '', 'string' );
		$this->setState ( 'filter.min', $value !== '' ? (int) $value : null );

		// List state information.
		parent::populateState('rank_id', 'asc');
	}

	protected function getStoreId($id = '') {
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.title');
		$id	.= ':'.$this->getState('filter.special');
		$id	.= ':'.$this->getState('filter.min');

		return parent::getStoreId($id);
	}

	protected function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
					'list.select',
					'a.rank_id, a.rank_title, a.rank_min, a.rank_special, a.rank_image'
			)
		);

		$query->from('#__kunena_ranks AS a');

		// Filter by access level.
		$title = $this->getState ( 'filter.title');
		if (!empty($title)) {
			$title = $db->Quote('%'.$db->escape($title, true).'%');
			$query->where('(a.rank_title LIKE '.$title.')');
		}

		$special = $this->getState('filter.special');
		if (is_numeric($special)) {
			$query->where('a.rank_special = ' . (int) $special);
		}

		$min = $this->getState ( 'filter.min');
		if (!empty($min)) {
			$query->where('a.rank_min > ' . (int) $min);
		}

		// Add the list ordering clause.
		$direction	= strtoupper($this->state->get('list.direction'));
		switch ($this->state->get('list.ordering')) {
			case 'title':
				$query->order('a.rank_title ' . $direction);
				break;
			case 'min':
				$query->order('a.rank_min ' . $direction);
				break;
			case 'special':
				$query->order('a.rank_special ' . $direction);
				break;
			case 'image':
				$query->order('a.rank_image ' . $direction);
				break;
			default:
				$query->order('a.rank_id ' . $direction);
		}

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}
