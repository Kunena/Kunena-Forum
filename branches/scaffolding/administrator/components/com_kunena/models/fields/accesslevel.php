<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

jimport('joomla.html.html');

/**
 * List form field type object
 *
 * @package 	Zine
 */
class JXFieldTypeAccessLevel extends JXFieldTypeList
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_type = 'AccessLevel';

	function _getOptions(&$node)
	{
		$db		= &JFactory::getDBO();
		$query	= new JXQuery;

		$query->select('a.id AS value, a.title AS text');
		$query->select('COUNT(DISTINCT g2.id) AS level');
		$query->from('#__access_assetgroups AS a');
		$query->join('LEFT OUTER', '#__access_assetgroups AS g2 ON a.left_id > g2.left_id AND a.right_id < g2.right_id');
		$query->group('a.id');

		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		return $options;
	}
}