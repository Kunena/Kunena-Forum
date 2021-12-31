<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Component Controller
 *
 * @since   Kunena 6.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 *
	 * @since  Kunena 6.0
	 */
	protected $default_view = 'cpanel';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see
	 *                               {@link \JFilterInput::clean()}.
	 *
	 * @return  BaseController
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function display($cachable = false, $urlparams = []): BaseController
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');
		KunenaFactory::loadLanguage('com_kunena.views', 'admin');
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
		KunenaFactory::loadLanguage('com_kunena.install', 'admin');
		KunenaFactory::loadLanguage('com_kunena.models', 'admin');
		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
		KunenaFactory::loadLanguage('com_plugins', 'admin');
		KunenaFactory::loadLanguage('com_kunena', 'site');

		$document = Factory::getApplication()->getDocument();
		$document->addStyleSheet(Uri::base(true) . '/components/com_kunena/media/css/theme.min.css');

		return parent::display($cachable, $urlparams);
	}
}
