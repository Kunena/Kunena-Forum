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
 * JXtended Form Field Type Class for Kunena category ordering.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JXFormFieldCategoryOrdering extends JXFormField
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'CategoryOrdering';

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
	function _getInput()
	{
		// Initialize standard field attributes.
		$size	= $this->_element->attributes('size');
		$class	= ( $this->_element->attributes('class') ? 'class="'.$this->_element->attributes('class').'"' : 'class="inputbox"' );

		// Get the current parent id.
		$parent_id = $this->_form->getValue('parent_id', 1);

		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the category options.
		$db->setQuery(
			'SELECT node.ordering AS value, node.title AS text' .
			' FROM #__kunena_categories AS node' .
			' WHERE node.parent_id = '.(int)$parent_id .
			' ORDER BY node.left_id'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Set the option description.
		foreach ($options as $i => $option)
		{
//			$options[$i]->text = str_pad($option->text, strlen($option->text) + 2*($option->level - 1), '- ', STR_PAD_LEFT);
		}

		// If first is allowed, add it to the front of the list.
		if ($this->_element->attributes('allow_first') == 1) {
			array_unshift($options, JHTML::_('select.option', -1, '- '.JText::_('First').' -'));
		}

		// If last is allowed, add it to the end of the list.
		if ($this->_element->attributes('allow_last') == 1) {
			array_push($options, JHTML::_('select.option', -2, '- '.JText::_('Last').' -'));
		}

		// If the field is disabled, build it as such.
		if ($this->_element->attributes('disabled') == 'true')
		{
			$html = JHTML::_('select.genericlist', $options, $this->inputName, $class.' disabled="disabled"', 'value', 'text', $this->value, $this->inputId);
		}
		// If the field is readonly, build it as such and add a hidden field so we get the value.
		else if ($this->_element->attributes('readonly') == 'true')
		{
			$html = JHTML::_('select.genericlist', $options, '', $class.' disabled="disabled"', 'value', 'text', $this->value, $this->inputId) .
					'<input type="hidden" name="'.$this->inputName.'" value="'.$this->value.'" />';
		}
		// The field is neither disabled or readonly, just build it.
		else {
			$html = JHTML::_('select.genericlist', $options, $this->inputName, $class, 'value', 'text', $this->value, $this->inputId);
		}

		return $html;
	}
}