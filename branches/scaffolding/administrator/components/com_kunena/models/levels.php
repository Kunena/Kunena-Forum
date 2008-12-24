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

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_members'.DS.'models'.DS.'levels.php';

/**
 * Levels model for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelLevels extends MembersModelLevels
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_context = 'com_kunena.levels';

	/**
	 * Method to get a list of items.
	 *
	 * Special implementation which also connects ACL Rule information with the data.
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

		jximport('jxtended.acl.acladmin');

		// If the items were successfully loaded, lets process them further.
		if (!empty($this->_lists[$key]))
		{
			for ($i = 0, $n = count($this->_lists[$key]); $i < $n; $i++)
			{
				$section	= $this->_lists[$key][$i]->section_value;
				$ruleName	= $section.'-global.view-'.$this->_lists[$key][$i]->value;
				$rule		= JxAclAdmin::getRule($ruleName, $section);

				$this->_lists[$key][$i]->rule_name = $ruleName;

				if (is_a($rule, 'JxTableAcl')) {
					if ($references = &$rule->findReferences(true)) {
						$this->_lists[$key][$i]->references = &$references;
					}
					else {
						// Non fatal but lets alert the user somethings amiss
						JError::raiseWarning(500, $rule->getError());

						jximport('jxtended.acl.aclreferences');
						$this->_lists[$key][$i]->references = new JxAclReferences;
					}
				}
				else {
					jximport('jxtended.acl.aclreferences');
					$this->_lists[$key][$i]->references = new JxAclReferences;
				}
			}
		}

		return $this->_lists[$key];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * Slightly different from the parents as we want to do a few different things, and cut out some options
	 *
	 * @access	protected
	 * @return	string		An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		$state = &$this->getState();
		$query = new JXQuery;

		// Select all fields from the table.
		$query->select('a.*');
		$query->from('`#__core_acl_axo_groups` AS a');

		// Filter the comments over the search string if set.
		$search = $state->get('filter.search');
		if (!empty($search)) {
			$query->where('a.name LIKE '.$this->_db->Quote('%'.$search.'%'));
		}

		// Find only global and local levels
		$query->select('s.name AS section_name, s.value AS section_value');
		$query->join('LEFT', '#__core_acl_axo_sections AS s ON s.id = a.section_id');
		$query->where('(s.value IN ('.$this->_db->quote('core').','.$this->_db->quote('com_kunena').'))');

		// Get rid of ROOT node
		$query->where('a.parent_id <> 0');

		// Add the list ordering clause.
		//$query->order($this->_db->getEscaped($state->get('list.ordering', 'a.lft')).' '.$this->_db->getEscaped($state->get('list.direction', 'ASC')));
		$query->order('s.order_value, a.name');

		//echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		return $query;
	}

}