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
 * JXtended Form Field Type Class for Kunena emoticon.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JXFieldTypeEmoticon extends JXFieldType
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Emoticon';

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
		$id			= str_replace(']', '', str_replace('[', '_', $controlName.'_'.$name));
		$class		= ( $node->attributes('class') ? $node->attributes('class') : 'emoticon' );
		$disabled	= ($node->attributes('disabled') == 'true');
		$readonly	= ($node->attributes('readonly') == 'true');
		$allowNone	= ($node->attributes('allow_none') == 'true');

		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the category options.
		$db->setQuery(
			'SELECT `id`, `code`, `file_path`' .
			' FROM `#__kunena_smileys`' .
			' ORDER BY `ordering`'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Build the radio list.
		$html = array('<fieldset class="'.$class.'">');

		if ($allowNone) {
			$checked = ($value == 0) ? ' checked="checked"' : '';
			$html[] = '<input id="'.$id.'_0" type="radio" name="'.$controlName.'['.$name.']" value="0"'.$checked.' />';
			$html[] = '<label for="'.$id.'_0">'.JText::_('KUNENA NONE').'</label>';
		}

		foreach ($options as $i => $option)
		{
			$checked = ($options[$i]->id == $value) ? ' checked="checked"' : '';
			$html[] = '<input id="'.$id.'_'.$options[$i]->id.'" type="radio" name="'.$controlName.'['.$name.']" value="'.$options[$i]->id.'"'.$checked.' />';
			$html[] = '<label for="'.$id.'_'.$options[$i]->id.'">'.JHTML::_('image', $options[$i]->file_path, $options[$i]->code).'</label>';
		}
		$html[] = '</fieldset>';

		$html = implode("\n", $html);

		return $html;
	}
}