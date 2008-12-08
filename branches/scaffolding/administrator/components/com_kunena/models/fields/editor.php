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
 * JXtended Form Field Type Class for an Editor.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JXFieldTypeEditor extends JXFieldType
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Editor';

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
		// editor attribute can be in the form of:
		// editor="desired|alternative"
		if ($editorName = trim( $node->attributes('editor') ))
		{
			$parts	= explode( '|', $editorName );
			$db		= &JFactory::getDBO();
			$query	= 'SELECT element' .
					' FROM #__plugins' .
					' WHERE element	= '.$db->Quote( $parts[0] ) .
					'  AND folder = '.$db->Quote( 'editors' ) .
					'  AND published = 1';
			$db->setQuery( $query );
			if ($db->loadResult()) {
				$editorName	= $parts[0];
			}
			else if (isset( $parts[1] )) {
				$editorName	= $parts[1];
			}
			else {
				$editorName	= '';
			}
			$node->addAttribute( 'editor', $editorName );
		}
		$editor		= &JFactory::getEditor( $editorName ? $editorName : null );
		$rows		= $node->attributes('rows');
		$cols		= $node->attributes('cols');
		$height		= ($node->attributes('height')) ? $node->attributes('height') : '200';
		$width		= ($node->attributes('width')) ? $node->attributes('width') : '100%';
		$class		= ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"');
		$buttons	= $node->attributes('buttons');

		$editor->set( 'TemplateXML',	$node->attributes('templatexml') );
		if ($buttons == 'true') {
			$buttons	= true;
		} else {
			$buttons	= explode( ',', $buttons );
		}
		// convert <br /> tags so they are not visible when editing
		//$value	= str_replace('<br />', "\n", $value);

		return $editor->display( $controlName.'['.$name.']', htmlspecialchars( $value ), $width, $height, $cols, $rows, $buttons ) ;
	}

	function render(&$xmlElement, $value, $controlName = 'jxform')
	{
		$result		= &parent::render( $xmlElement, $value, $controlName );
		$editorName	= trim( $xmlElement->attributes('editor') );
		$result->editor	= &JFactory::getEditor( $editorName ? $editorName : null );
		return $result;
	}
}