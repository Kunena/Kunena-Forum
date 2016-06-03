<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  Icons
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();


/**
 * Class KunenaIcons
 *
 * @since  5.0
 *
 */
class KunenaIcons
{
	/**
	 * Return the arrow down icon
	 *
	 * @return string
	 */
	static public function arrowdown()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-down hasTooltip"></span>';
		}
	}

	/**
	 * Return the arrwo up icon
	 *
	 * @return string
	 */
	static public function arrowup()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-up hasTooltip"></span>';
		}
	}

	/**
	 * Return the members icon
	 *
	 * @return string
	 */
	static public function members()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-user fa-big" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-user icon-big"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user glyphicon-super"></span>';
		}
		else
		{
			return '<span class="icon icon-user icon-big"></span>';
		}
	}

	/**
	 * Return the stats icon
	 *
	 * @return string
	 */
	static public function stats()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bar-chart fa-big" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-bars icon-big"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats glyphicon-super"></span>';
		}
		else
		{
			return '<span class="icon icon-bars icon-big"></span>';
		}
	}
}
