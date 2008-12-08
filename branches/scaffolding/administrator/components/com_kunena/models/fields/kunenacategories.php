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
class JXFieldTypeKunenaCategories extends JXFieldType
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_type = 'KunenaCategories';

	/**
	 * Method to generate the form field markup.
	 *
	 * @access	public
	 * @param	string	The form field name.
	 * @param	string	The form field value.
	 * @param	object	The JXFormField object.
	 * @param	string	The form field control name.
	 * @return	string	Form field markup.
	 * @since	1.0
	 */
	function fetchField($name, $value, &$node, $controlName)
	{
		// Initialize standard field attributes.
		$id		= str_replace(']', '', str_replace('[', '_', $controlName.'_'.$name));
		$size	= $node->attributes('size');
		$class	= ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		// Get the category options.
		$options = $this->_getOptions($node);
		if (!$options) {
			// TODO: handle the error somehow?
			$options = array();
		}

		// If the field is disabled, build it as such.
		if ($node->attributes('disabled') == 'true')
		{
			$html = JHTML::_('select.genericlist', $options, $controlName.'['.$name.']', $class.' disabled="disabled"', 'value', 'text', $value, $id);
		}
		// If the field is readonly, build it as such and add a hidden field so we get the value.
		else if ($node->attributes('readonly') == 'true')
		{
			$html = JHTML::_('select.genericlist', $options, '', $class.' disabled="disabled"', 'value', 'text', $value, $id) .
					'<input type="hidden" name="'.$controlName.'['.$name.']'.'" value="'.$value.'" />';
		}
		// The field is neither disabled or readonly, just build it.
		else {
			$html = JHTML::_('select.genericlist', $options, $controlName.'['.$name.']', $class, 'value', 'text', $value, $id);
		}

		return $html;
	}

	/**
	 * Method to get the category options.
	 *
	 * @access	private
	 * @param	object	The JXFormField object.
	 * @return	mixed	Array of category options on success, false on failure.
	 * @since	1.0
	 */
	function _getOptions(&$node)
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
		if ($node->attributes('allow_none') == 1) {
			array_unshift($options, JHTML::_('select.option', 1, '- None -'));
		}

		return $options;
	}
}