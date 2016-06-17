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


	/**
	 * Return the search icon
	 *
	 * @return string
	 */
	static public function search()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-search" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-search"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-search"></span>';
		}
		else
		{
			return '<span class="icon icon-search"></span>';
		}
	}

	/**
	 * Return the collapse icon
	 *
	 * @return string
	 */
	static public function collapse()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return 'fa';
		}
		elseif ($topicicontype == 'B2')
		{
			return 'b2';
		}
		elseif ($topicicontype == 'B3')
		{
			return 'b3';
		}
		else
		{
			return 'b3';
		}
	}

	/**
	 * Return the cancel icon
	 *
	 * @return string
	 */
	static public function cancel()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-times"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-remove-sign"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-remove-sign"></span>';
		}
		else
		{
			return '<i class="icon icon-remove-sign"></i>';
		}
	}

	/**
	 * Return the ip icon
	 *
	 * @return string
	 */
	static public function ip()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $ktemplate->params->get('topicicontype');

		if (!KunenaUserHelper::getMyself()->isModerator())
		{
			return;
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-compass"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-compass"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-compass"></span>';
		}
		else
		{
			return '<i class="icon icon-compass"></i>';
		}
	}
}
