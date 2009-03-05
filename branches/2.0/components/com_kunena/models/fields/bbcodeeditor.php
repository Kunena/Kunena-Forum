<?php
/**
 * @version		$Id$
 * @package		JXtended.Packages
 * @subpackage	com_packages
 * @copyright	(C) 2008 JXtended, LLC. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.html.html');
jximport('jxtended.form.field');

/**
 * JXtended Form Field Type Class for a BBCode Editor.
 *
 * @package		JXtended.Packages
 * @subpackage	com_packages
 * @version		1.0
 */
class JXFieldTypeBBCodeEditor extends JXFieldType
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_type = 'BBCodeEditor';

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

		// Load the behaviors.
		JHtml::script('bbcode.js', 'components/com_kunena/media/js/');
		JHtml::stylesheet('bbcode.css', 'components/com_kunena/media/css/');

		// build a list of language strings to insert into the document head
		$lang = array(
			'BOLD' => JText::_('Bold', true),
			'ITALIC' => JText::_('Italic', true),
			'UNDERLINE' => JText::_('Underline', true),
			'STRIKETHROUGH' => JText::_('Strikethrough', true),
			'QUOTE' => JText::_('Quote', true),
			'IMAGE' => JText::_('Image', true),
			'ENTER_IMAGE' => JText::_('Enter Image', true),
			'ENTER_LINK' => JText::_('Enter Link', true),
			'LINK' => JText::_('Link', true),
			'LINK_TEXT' => JText::_('Link Text', true),
			'ENTER_LANG' => JText::_('Enter Language', true),
			'CODE' => JText::_('Code', true)
		);

		// get the application object
		$app = &JFactory::getApplication();

		// merge our list of language strings into any existing ones and set them in the application object
		$lang = array_merge($app->get('jx.jslang', array()), $lang);
		$app->set('jx.jslang', $lang);

		// Get the category options.
		$options = $this->_getOptions($node);
		if (!$options) {
			// TODO: handle the error somehow?
			$options = array();
		}

		$html[] = '';
		$html[] = '<div id="bbcode-editor">';
		$html[] = '	<ul class="toolbar"></ul>';
		$html[] = '	<span class="help"></span>';
		$html[] = '	<br class="clear" />';

		$html[] = '	<textarea id="'.$id.'" name="'.$controlName.'['.$name.']'.'" class="editor inputbox required" rows="8" cols="60"></textarea>';

		if (!empty($options)) {
			$html[] = '	<ul class="emoticon-palette">';
			foreach ($options as $emoticon)
			{
				$html[] = '		<li>';
				$html[] = '			<img class="emoticon-palette" src="'.$emoticon->path.'" alt="'.$emoticon->code.'" title="'.$emoticon->code.'" />';
				$html[] = '		</li>';
			}
			$html[] = '	</ul>';
		}

		$html[] = '	<br class="clear" />';
		$html[] = '</div>';
		$html[] = '';


		return implode("\n", $html);
	}

	/**
	 * Method to get the package options.
	 *
	 * @access	private
	 * @param	object	The JXFormField object.
	 * @return	mixed	Array of package options on success, false on failure.
	 * @since	1.0
	 */
	function _getOptions(&$node)
	{
		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the package options.
		$db->setQuery(
			'SELECT `code`, `file_path` AS path' .
			' FROM `#__kunena_smileys`' .
			' ORDER BY `ordering`, `file_path`'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// If none is allowed, add it to the front of the list.
		if ($node->attributes('allow_none') == 1) {
			array_unshift($options, JHTML::_('select.option', 1, '- None -'));
		}

		return $options;
	}
}