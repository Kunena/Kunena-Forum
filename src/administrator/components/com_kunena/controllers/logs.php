<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;

/**
 * Kunena Backend Logs Controller
 *
 * @since  5.0
 */
class KunenaAdminControllerLogs extends KunenaController
{
	/**
	 * @var null|string
	 *
	 * @since    5.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since    5.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=logs';
	}

	/**
	 * Redirect user to the right layout in order to define some settings
	 *
	 * @since K5.0
	 *
	 * @return void
	 * @since Kunena
	 */
	public function cleanentries()
	{
		$this->setRedirect(Route::_("index.php?option=com_kunena&view=logs&layout=clean", false));
	}

	/**
	 * Clean
	 *
	 * @return boolean|void
	 *
	 * @throws Exception
	 * @since  K5.0
	 *
	 * @since  Kunena
	 * @throws null
	 */
	public function clean()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$days      = $this->app->input->getInt('clean_days', 0);
		$timestamp = new \Joomla\CMS\Date\Date('now -' . $days . ' days');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__kunena_logs')
			->where('time < ' . $timestamp->toUnix());

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return false;
		}

		$num_rows = $db->getAffectedRows();

		if ($num_rows > 0)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_LOG_ENTRIES_DELETED', $num_rows));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_LOG_ENTRIES_DELETED_NOTHING_TO_DELETE'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since K5.0
	 * @throws null
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
