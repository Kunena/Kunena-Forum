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

jximport('jxtended.acl.helper');
jximport('jxtended.database.query');

/**
 * Access control helper for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaHelperAccess
{
	function synchronize()
	{
		// Initialize values.
		$db		= &JFactory::getDBO();
		$query	= new JXQuery;

		/*
		 * Get the items to synchronize as assets.
		 */

		// Build the SELECT clause.
		$query->select('c.id');
		$query->select('CONCAT_WS(" / ", s.title, c.title) AS title');
		$query->select('(s.ordering*1000+c.ordering) AS ordering');
		$query->select('c.access');

		// Build the FROM clause.
		$query->from('#__categories AS c');
		$query->join('INNER', '#__sections AS s ON s.id = c.section');

		// Set the ORDERING clause.
		$query->order('s.ordering, s.title, c.ordering, c.title');

		// Query the database.
		$db->setQuery($query->toString());
		$items = $db->loadObjectList('id');

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg());
		}

		// Synchronize the assets with the access control system.
		$result = JXAclHelper::synchronizeAssets($items, 'com_kunena');

		// Check for errors.
		if (JError::isError($result)) {
			return $result;
		}

		return true;
	}

	/**
	 * Method to get the access levels for a user by ID as a comma delimited string.
	 *
	 * @param	integer		$uid		The ID of the user for which we are retrieving access levels.
	 * @param	string		$action		The action that the user is trying to perform.
	 * @return	string		Comma separated list of access level ID's
	 * @since	1.0
	 */
	function getAccessLevelsString($uid, $action='view.category')
	{
		static $results;

		// Sanitize the user ID.
		$uid = (int)$uid;

		// Only get the access levels once.
		if (!empty($results[$uid])) {
			return $results[$uid];
		}

		$db = &JFactory::getDBO();
		$query	= 'SELECT GROUP_CONCAT(DISTINCT axog.value SEPARATOR \',\')'
				. ' FROM #__core_acl_aco_map AS am'
				. ' INNER JOIN #__core_acl_acl AS acl ON acl.id = am.acl_id'
				. ' INNER JOIN #__core_acl_aro_groups_map AS agm ON agm.acl_id = am.acl_id'
				. ' LEFT JOIN #__core_acl_axo_groups_map AS axogm ON axogm.acl_id = am.acl_id'
				. ' INNER JOIN #__core_acl_axo_groups AS axog ON axog.id = axogm.group_id'
				. ' INNER JOIN #__core_acl_groups_aro_map AS garom ON garom.group_id = agm.group_id'
				. ' INNER JOIN #__core_acl_aro AS aro ON aro.id = garom.aro_id'
				. ' WHERE am.section_value = '.$db->Quote('com_kunena')
				. '  AND am.value = '.$db->Quote($action)
				. '  AND acl.enabled = 1'
				. '  AND acl.allow = 1'
				. '  AND aro.value = '.(int)$uid;

		// Load the access levels.
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg(), $db->getErrorNum());
		}

		// Load the value into the static array.
		$results[$uid] = $db->loadResult();

		return $results[$uid];
	}

	/**
	 * Method to get the access levels for a user by ID as an array.
	 *
	 * @param	integer		$uid		The ID of the user for which we are retrieving access levels.
	 * @param	string		$action		The action that the user is trying to perform.
	 * @return	array		Array list of access level ID's
	 * @since	1.0
	 */
	function getAccessLevelsArray($uid, $action='view.category')
	{
		static $results;

		// Sanitize the user ID.
		$uid = (int)$uid;

		// Only get the access levels once.
		if (!empty($results[$uid])) {
			return $results[$uid];
		}

		$db = &JFactory::getDBO();
		$query	= 'SELECT DISTINCT axog.value'
				. ' FROM #__core_acl_aco_map AS am'
				. ' INNER JOIN #__core_acl_acl AS acl ON acl.id = am.acl_id'
				. ' INNER JOIN #__core_acl_aro_groups_map AS agm ON agm.acl_id = am.acl_id'
				. ' LEFT JOIN #__core_acl_axo_groups_map AS axogm ON axogm.acl_id = am.acl_id'
				. ' INNER JOIN #__core_acl_axo_groups AS axog ON axog.id = axogm.group_id'
				. ' INNER JOIN #__core_acl_groups_aro_map AS garom ON garom.group_id = agm.group_id'
				. ' INNER JOIN #__core_acl_aro AS aro ON aro.id = garom.aro_id'
				. ' WHERE am.section_value = '.$db->Quote('com_kunena')
				. '  AND am.value = '.$db->Quote($action)
				. '  AND acl.enabled = 1'
				. '  AND acl.allow = 1'
				. '  AND aro.value = '.(int)$uid;

		// Load the access levels.
		$db->setQuery($query);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			return new JException($db->getErrorMsg(), $db->getErrorNum());
		}

		// Load the value into the static array.
		$results[$uid] = $db->loadResultArray();

		return $results[$uid];
	}
}