<?php
/**
 * @package     Forum.Administrator
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Component Controller
 *
 * @since  1.5
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'cpanel';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return BaseController
	 *
	 * @since   1.5
	 * @throws \Exception
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$view   = $this->input->get('view', 'users');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		\KunenaFactory::loadLanguage('com_kunena.views','admin');
		\KunenaFactory::loadLanguage('com_kunena.libraries','admin');
		\KunenaFactory::loadLanguage('com_kunena.sys','admin');
		\KunenaFactory::loadLanguage('com_kunena.install','admin');
		\KunenaFactory::loadLanguage('com_kunena.models','admin');
		\KunenaFactory::loadLanguage('com_kunena','site');

		$document = Factory::getApplication()->getDocument();
		$document->addStyleSheet(Uri::base(true) . '/components/com_kunena/media/css/theme.min.css');

		return parent::display($cachable, $urlparams);
	}
}
