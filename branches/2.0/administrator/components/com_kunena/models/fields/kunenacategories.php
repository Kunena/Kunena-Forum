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

jimport('joomla.html.html');
jximport('jxtended.form.field');

/**
 * JXtended Form Field Type Class for Kunena Categories.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JXFormFieldKunenaCategories extends JXFormFieldList
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_type = 'KunenaCategories';

	/**
	 * Method to get the category options.
	 *
	 * @access	protected
	 * @return	mixed	Array of category options on success, false on failure.
	 * @since	1.0
	 */
	function _getOptions()
	{
		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the category options.
		$db->setQuery(
			'SELECT node.id AS value, node.title AS text, (COUNT(parent.id) - 1) AS level' .
			' FROM #__kunena_categories AS node, #__kunena_categories AS parent' .
			' WHERE node.left_id BETWEEN parent.left_id AND parent.right_id' .
			' AND node.id > 1' .
			' GROUP BY node.id' .
			' ORDER BY node.left_id'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Pad out the text strings based on the item level.
		foreach ($options as $i => $option)
		{
			$options[$i]->text = str_pad($option->text, strlen($option->text) + 2*($option->level - 1), '- ', STR_PAD_LEFT);
		}

		// If none is allowed, add it to the front of the list.
		if ($this->_element->attributes('allow_none') == 1) {
			array_unshift($options, JHTML::_('select.option', 1, '- None -'));
		}

		return $options;
	}
}