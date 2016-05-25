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
		}
		elseif ($topicicontype == 'B2')
		{
		}
		elseif ($topicicontype == 'B3')
		{
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
		}
		elseif ($topicicontype == 'B2')
		{
		}
		elseif ($topicicontype == 'B3')
		{
		}
	}
}
