<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('JPATH_BASE') or die();

/**
 * @package		Kunena
 * @subpackage	com_kunena
 */
class JElementList_Layout extends JElement
{
	/**
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'List_Layout';

	function _getOptions(&$node)
	{
		$options	= array();
		$path1		= null;
		$path2		= null;

		// Load template entries for each menuid
		$db = &JFactory::getDBO();
		$db->setQuery(
			'SELECT template'
			. ' FROM #__templates_menu'
			. ' WHERE client_id = 0 AND menuid = 0'
		);
		$template	= $db->loadResult();

		if ($module = $node->attributes('module')) {
			$module	= preg_replace('#\W#', '', $module);
			$path1	= JPATH_SITE.DS.'modules'.DS.$module.DS.'tmpl';
			$path2	= JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.$module;
		}
		else if ($view = $node->attributes('view')) {
			$view	= preg_replace('#\W#', '', $view);
			$path1	= JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'views'.DS.$view.DS.'tmpl';
			$path2	= JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'com_kunena'.DS.$view;
		}

		if ($path1 && $path2) {
			$options = JFolder::files($path1, '^[^_]*\.php$');
			array_unshift($options, '');

			if (is_dir($path2) && $temp = JFolder::files($path2, '^[^_]*\.php$')) {
				$options[]	= '<em>'.JText::_('From Default Template').'</em>';
				$options	= array_merge($options, $temp);
				array_unique($options);
			}
		}

		foreach ($options as $i => $option) {
			$options[$i] = JHTML::_('select.option', JFile::stripExt($option));
		}

		return $options;
	}

	function fetchElement($name, $value, &$node, $controlName)
	{
		jimport('joomla.filesystem.file');
		$size		= ($node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '');
		$class		= ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"');
		$options	= $this->_getOptions($node);

		return JHTML::_('select.genericlist',  $options, ''.$controlName.'['.$name.']', $class, 'value', 'text', $value, $controlName.$name);
	}
}