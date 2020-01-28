<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Kunena Component Controller
 *
 * @since  1.5
 */
class DisplayController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  This object to support chaining.
	 *
	 * @since   1.5
	 * @throws Exception
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return parent::display();
	}
}
