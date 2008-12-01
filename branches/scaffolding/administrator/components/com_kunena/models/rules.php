<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jximport('jxtended.application.component.modellist');
jximport('jxtended.database.query');

/**
 * Rules model for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelRules extends JXModelList
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	 var $_context		= 'kunena.rules';

	/**
	 * Gets a list of whether the three ACL types are available for a given section
	 *
	 * This method is used to determine whether which of the "New" toolbar icons
	 * are available to this extension
	 *
	 * @return array	An array of boolean values
	 */
	function getAclTypes()
	{
		$section = $this->getState('filter.section');
		if (is_array($section)) {
			$section = $section[0];
		}

		$this->_db->setQuery(
			'SELECT DISTINCT acl_type'
			.' FROM #__core_acl_aco'
			.' WHERE section_value = '.$this->_db->quote($section)
			.' ORDER BY acl_type'
		);
		$types	= $this->_db->loadResultArray();
		$result	= array();
		foreach (array(1, 2, 3) as $aclType) {
			$result[$aclType] = in_array($aclType, $types);
		}
		return $result;
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access	public
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.0
	 */
	function &getItems()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);

		// Try to load the value from internal storage.
		if (!empty ($this->_lists[$key])) {
			return $this->_lists[$key];
		}

		// Run the parent get items method.
		parent::getItems();

		// If the items were successfully loaded, lets process them further.
		if (!empty($this->_lists[$key])) {

			$aclIds	= array();
			$rlu	= array();
			for ($i=0,$n=count($this->_lists[$key]); $i<$n; $i++)
			{
				$aclIds[] = $this->_lists[$key][$i]->id;
				$rlu[$this->_lists[$key][$i]->id] = $i;
			}

			$db		= &$this->getDBO();
			$acls	= array();

			// run sql to get ACO's, ARO's and AXO's
			if (!empty($aclIds))
			{
				$ids = implode(',', $aclIds);
				foreach (array('aco', 'aro', 'axo') as $type)
				{
					$query = 'SELECT	a.acl_id,o.name,s.name AS section_name' .
							' FROM	#__core_acl_'. $type .'_map a' .
							' INNER JOIN #__core_acl_'. $type .' o ON (o.section_value=a.section_value AND o.value=a.value)' .
							' INNER JOIN #__core_acl_'. $type . '_sections s ON s.value=a.section_value' .
							' WHERE	a.acl_id IN ('. $ids . ')';
					$db->setQuery($query);
					$temp	= $db->loadObjectList();
					foreach ($temp as $item)
					{
						$i	= $rlu[$item->acl_id];
						$k	= $type.'s';

						if (!isset($this->_lists[$key][$i]->$k)) {
							$this->_lists[$key][$i]->$k = array();
						}
						$r = &$this->_lists[$key][$i]->$k;
						$r[$item->section_name][] = $item->name;
					}
				}

				// grab ARO and AXO groups
				foreach (array('aro', 'axo') as $type)
				{
					$query = 'SELECT a.acl_id,g.name' .
							' FROM #__core_acl_'. $type .'_groups_map a' .
							' INNER JOIN #__core_acl_'. $type .'_groups g ON g.id=a.group_id' .
							' WHERE	a.acl_id IN ('. $ids . ')';
					$db->setQuery($query);
					$temp	= $db->loadObjectList();
					foreach ($temp as $item)
					{
						$i	= $rlu[$item->acl_id];
						$k	= $type.'Groups';
						if (!isset($this->_lists[$key][$i]->$k)) {
							$this->_lists[$key][$i]->$type = array();
						}
						$r = &$this->_lists[$key][$i]->$k;
						$r[] = $item->name;
					}
				}
			}



		}

		return $this->_lists[$key];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @access	protected
	 * @return	string		An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		// Create a new query object.
		$query = new JXQuery;

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__core_acl_acl` AS a');

		// Filter the items over the section(s) if set.
		$section = $this->getState('filter.section');
		if (!empty($section)) {
			if (is_array($section)) {
				foreach ($section as $k => $v)
				{
					$section[$k] = $this->_db->Quote($v);
				}
				$query->where('a.section_value IN ('.implode(',', $section).')');
			}
			else {
				$query->where('a.section_value = '.$this->_db->Quote($section));
			}
		}

		// Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where('a.note LIKE '.$this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%'));
		}

		// Filter the items over the search string if set.
		$type_id = $this->getState('filter.type');
		if ($type_id !== '*' and $type_id !== null) {
			$query->where('a.acl_type = '.(int)$type_id);
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering', 'a.name')).' '.$this->_db->getEscaped($this->getState('list.direction', 'ASC')));

		//echo nl2br($query->toString());
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @access	protected
	 * @param	string		$context	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.0
	 */
	function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.select');
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.section');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.type');

		return md5($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function _populateState()
	{
		$app		= &JFactory::getApplication('administrator');
		$user		= &JFactory::getUser();
		$params		= JComponentHelper::getParams('com_kunena');
		$context	= 'com_kunena.rules.';

		// Load the filter state.
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
		$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));
		$this->setState('filter.type', $app->getUserStateFromRequest($context.'filter.state', 'filter_type', '2', 'string'));
		$this->setState('filter.section', 'com_kunena');


		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'a.id', 'cmd'));
		$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

		// Load the user parameters.
		$this->setState('user',	$user);
		$this->setState('user.id', (int)$user->id);
		$this->setState('user.aid', (int)$user->get('aid'));

		// Load the check parameters.
		if ($this->_state->get('filter.state') === '*') {
			$this->setState('check.state', false);
		} else {
			$this->setState('check.state', true);
		}

		// Load the parameters.
		$this->setState('params', $params);
	}
}