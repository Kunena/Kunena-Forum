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
class JXFieldTypeList_Access extends JXFieldTypeList
{
	function _getOptions(&$node)
	{
		$config	= &JComponentHelper::getParams('com_zine');
		$db		= &JFactory::getDBO();
		$query	= new JXQuery;

		$query->select('a.value, a.name AS text');
		$query->select('COUNT(DISTINCT g2.id) AS level');
		$query->from('#__core_acl_axo_groups AS a');
		$query->join('LEFT OUTER', '#__core_acl_axo_groups AS g2 ON a.lft > g2.lft AND a.rgt < g2.rgt');
		$query->group('a.id');

		$db->setQuery($query->toString());
		$options	= $db->loadObjectList();

		foreach ($options as $i => $option) {
			$options[$i]->text = str_pad($options[$i]->text, strlen($options[$i]->text) + $options[$i]->level * 2, '- ', STR_PAD_LEFT);
		}

		return $options;
	}
}