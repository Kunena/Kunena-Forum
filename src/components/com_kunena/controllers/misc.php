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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Kunena Misc Controller
 *
 * @since  2.0
 */
class KunenaControllerMisc extends KunenaController
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

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function template()
	{
		$name = Factory::getApplication()->input->getString('name',
			Factory::getApplication()->input->cookie->getString('kunena_template', '')
		);

		if ($name)
		{
			$name = KunenaPath::clean($name);

			if (!is_readable(KPATH_SITE . "/template/{$name}/config/template.xml"))
			{
				$name = 'crypsis';
			}

			setcookie('kunena_template', $name, 0, Uri::root(true) . '/', '', true);
		}
		else
		{
			setcookie('kunena_template', null, time() - 3600, Uri::root(true) . '/', '', true);
		}

		$this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
	}
}
