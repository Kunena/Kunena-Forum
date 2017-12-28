<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

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
	 * @param   array  $config  config
	 *
	 * @since    5.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl  = 'administrator/index.php?option=com_kunena&view=logs';
	}

	/**
	 * Redirect user to the right layout in order to define some settings
	 *
	 * @since K5.0
	 *
	 * @return $this
	 */
	public function cleanentries()
	{
		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=logs&layout=clean", false));
	}

	/**
	 * Clean
	 *
	 * @return void
	 *
	 * @since  K5.0
	 *
	 */
	public function clean()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$days = $this->app->input->getInt('clean_days', 0);
		$timestamp = new JDate('now -' . $days . ' days');

		$db = JFactory::getDbo();
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
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_LOG_ENTRIES_DELETED', $num_rows));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_LOG_ENTRIES_DELETED_NOTHING_TO_DELETE'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since K5.0
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
