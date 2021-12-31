<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Common Controller
 *
 * @since  2.0
 */
class KunenaControllerCommon extends KunenaController
{
	/**
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}
