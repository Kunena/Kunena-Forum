<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\PaginationObject;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Path\KunenaPath;

/**
 * Class KunenaAdminTemplate
 *
 * @since   Kunena 6.0
 */
class KunenaAdminTemplate
{
	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function initialize()
	{
		$document = Factory::getApplication()->getDocument();

		// $document->addStyleSheet(Uri::base(true) . '/components/com_kunena/media/css/layout.css');
		// $document->addStyleSheet(Uri::base(true) . '/components/com_kunena/media/css/styles.css');
		$document->addStyleSheet(Uri::base(true) . '/components/com_kunena/media/css/theme.min.css');
	}

	/**
	 * Get template paths.
	 *
	 * @param   string      $path      path
	 *
	 * @param   bool|false  $fullpath  fullpath
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getTemplatePaths($path = '', $fullpath = false)
	{
		if ($path)
		{
			$path = KunenaPath::clean("/$path");
		}

		$array   = [];
		$array[] = KPATH_ADMIN . '/tmpl/' . $path;

		return $array;
	}

	/**
	 * Renders an item in the pagination block
	 *
	 * @param   PaginationObject  $item  The current pagination object
	 *
	 * @return  string  HTML markup for active item
	 *
	 * @since   3.0
	 */
	public function paginationItem(PaginationObject $item)
	{
		// Special cases for "Start", "Prev", "Next", "End".
		switch ($item->text)
		{
			case Text::_('JLIB_HTML_START') :
				$display = '<i class="icon-first"></i>';
				break;
			case Text::_('JPREV') :
				$display = '<i class="icon-previous"></i>';
				break;
			case Text::_('JNEXT') :
				$display = '<i class="icon-next"></i>';
				break;
			case Text::_('JLIB_HTML_END') :
				$display = '<i class="icon-last"></i>';
				break;
			default:
				$display = $item->text;
		}

		// Check if the item can be clicked.
		if ($item->base !== null)
		{
			$limit = 'limitstart.value=' . (int) $item->base;

			return '<li class="page-item hidden-sm-down"><a class="page-link" href="#" title="' . $item->text . '" onclick="document.adminForm.' . $item->prefix . $limit . ';
			 Joomla.submitform();return false;">' . $display . '</a></li>';
		}

		// Check if the item is the active (or current) page.
		if (!empty($item->active))
		{
			return '<li class="page-item active hidden-sm-down"><a class="page-link">' . $display . '</a></li>';
		}

		// Doesn't match any other condition, render disabled item.
		return '<li class="page-item disabled"><a class="page-link">' . $display . '</a></li>';
	}
}
