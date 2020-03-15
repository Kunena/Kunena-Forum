<?php
/**
 * @package     Kunena.Site
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Kunena Component Controller
 *
 * @since   Kunena 6.0
 */
class DisplayController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return BaseController
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return parent::display();
	}
}
