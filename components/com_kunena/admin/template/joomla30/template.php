<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Template.Joomla30
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaAdminTemplate30
{

	public function initialize()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true) . '/components/com_kunena/media/css/joomla30/layout.css');
		$document->addStyleSheet(JUri::base(true) . '/components/com_kunena/media/css/joomla30/styles.css');
	}

	public function getTemplatePaths($path = '', $fullpath = false)
	{
		if ($path)
		{
			$path = KunenaPath::clean("/$path");
		}

		$array   = array();
		$array[] = ($fullpath ? KPATH_ADMIN : KPATH_COMPONENT_RELATIVE) . '/template/joomla30' . $path;

		return $array;
	}

	/**
	 * Renders an item in the pagination block
	 *
	 * @param   JPaginationObject $item The current pagination object
	 *
	 * @return  string  HTML markup for active item
	 *
	 * @since   3.0
	 */
	public function paginationItem(JPaginationObject $item)
	{
		// Special cases for "Start", "Prev", "Next", "End".
		switch ($item->text)
		{
			case JText::_('JLIB_HTML_START') :
				$display = '<i class="icon-first"></i>';
				break;
			case JText::_('JPREV') :
				$display = '<i class="icon-previous"></i>';
				break;
			case JText::_('JNEXT') :
				$display = '<i class="icon-next"></i>';
				break;
			case JText::_('JLIB_HTML_END') :
				$display = '<i class="icon-last"></i>';
				break;
			default:
				$display = $item->text;
		}

		// Check if the item can be clicked.
		if (!is_null($item->base))
		{
			$limit = 'limitstart.value=' . (int) $item->base;

			return '<li><a href="#" title="' . $item->text . '" onclick="document.adminForm.' . $item->prefix . $limit . '; Joomla.submitform();return false;">' . $display . '</a></li>';
		}

		// Check if the item is the active (or current) page.
		if (!empty($item->active))
		{
			return '<li class="active"><a>' . $display . '</a></li>';
		}

		// Doesn't match any other condition, render disabled item.
		return '<li class="disabled"><a>' . $display . '</a></li>';
	}
}
