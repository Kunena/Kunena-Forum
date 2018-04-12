<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaAdminTemplate
 * @since Kunena
 */
class KunenaAdminTemplate4
{
	/**
	 * @since Kunena
	 */
	public function initialize()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		/** @noinspection PhpDeprecationInspection */
		$document->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/components/com_kunena/media/css/layout.css');
		/** @noinspection PhpDeprecationInspection */
		$document->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/components/com_kunena/media/css/styles.css');
	}

	/**
	 * Get template paths.
	 *
	 * @param   string     $path     path
	 *
	 * @param   bool|false $fullpath fullpath
	 *
	 * @return array
	 * @since Kunena
	 */
	public function getTemplatePaths($path = '', $fullpath = false)
	{
		if ($path)
		{
			$path = KunenaPath::clean("/$path");
		}

		$array   = array();
		$array[] = ($fullpath ? KPATH_ADMIN : KPATH_COMPONENT_RELATIVE) . '/template/j4/' . $path;

		return $array;
	}

	/**
	 * Renders an item in the pagination block
	 *
	 * @param   \Joomla\CMS\Pagination\PaginationObject $item The current pagination object
	 *
	 * @return  string  HTML markup for active item
	 *
	 * @since   3.0
	 */
	public function paginationItem(\Joomla\CMS\Pagination\PaginationObject $item)
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
		if ($item->base !== null)
		{
			$limit = 'limitstart.value=' . (int) $item->base;

			return '<li><a href="#" title="' . $item->text . '" onclick="document.adminForm.' . $item->prefix . $limit . ';
			 Joomla.submitform();return false;">' . $display . '</a></li>';
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
