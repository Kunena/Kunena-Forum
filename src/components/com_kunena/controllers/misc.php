<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Misc Controller
 *
 * @since  2.0
 */
class KunenaControllerMisc extends KunenaController
{
	/**
	 * @param   array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * @throws Exception
	 */
	public function template()
	{
		$name = JFactory::getApplication()->input->getString('name', JFactory::getApplication()->input->getString('kunena_template', '', 'COOKIE'));

		if ($name)
		{
			$name = KunenaPath::clean($name);

			if (!is_readable(KPATH_SITE . "/template/{$name}/config/template.xml"))
			{
				$name = 'crypsis';
			}

			setcookie('kunena_template', $name, 0, JUri::root(true) . '/');
		}
		else
		{
			setcookie('kunena_template', null, time() - 3600, JUri::root(true) . '/');
		}

		$this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
	}
}
