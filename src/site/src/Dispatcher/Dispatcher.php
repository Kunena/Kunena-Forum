<?php

/**
 * @package         Joomla.Site
 * @subpackage      com_content
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Dispatcher;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * ComponentDispatcher class for com_kunena
 *
 * @since  4.0.0
 */
class Dispatcher extends ComponentDispatcher
{
	public $option = 'com_kunena';

	/**
	 * Dispatch a controller task. Redirecting the user if appropriate.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function dispatch()
	{
		parent::dispatch();
	}
}
