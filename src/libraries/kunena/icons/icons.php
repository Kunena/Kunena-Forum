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
 */
class KunenaIcons
{
	/**
	 *
	 */
	public function arrowdown()
	{
		$this->ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $this->ktemplate->params->get('topicicontype');

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
	 *
	 */
	public function arrowup()
	{
		$this->ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $this->ktemplate->params->get('topicicontype');

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
