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
 * @since 5.0
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
			return '<span class="fa fa-arrow-down hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-arrow-down hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i class="glyphicon glyphicon-arrow-down hasTooltip"></i>';
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
			return '<span class="fa fa-arrow-up hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-arrow-up hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i class="glyphicon glyphicon-arrow-up hasTooltip"></i>';
		}
	}
}
