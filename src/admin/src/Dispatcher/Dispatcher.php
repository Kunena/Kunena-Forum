<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Dispatcher
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;

/**
 * ComponentDispatcher class for com_kunena
 *
 * @since  6.0
 */
class Dispatcher extends ComponentDispatcher
{
	/**
	 * Kunena have to check for extension permission
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function checkAccess()
	{
		if ($this->app->isClient('administrator') && !$this->app->getIdentity()->authorise('core.manage', 'com_kunena'))
		{
			throw new KunenaExceptionAuthorise($this->app->getLanguage()->_('COM_KUNENA_NO_ACCESS'), 401);
		}
	}
}
