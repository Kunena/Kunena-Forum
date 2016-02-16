<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Html
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.html.pagination' );

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Joomla! Framework.
 *
 * @since		2.0
 * @deprecated	3.0
 */
class KunenaHtmlPagination extends KunenaPagination
{
	public function setDisplay($displayedPages = 7, $uri = null)
	{
		$this->setDisplayedPages($displayedPages);

		if ($uri instanceof JUri)
		{
			$this->setUri($uri);
		}
	}
}
