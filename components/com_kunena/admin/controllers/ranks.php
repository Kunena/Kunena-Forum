<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Ranks Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerRanks extends KunenaController
{
	protected $baseurl = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
	}

	function add()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=rank&layout=add', false));
	}

	function edit()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$id = array_shift($cid);

		if (!$id)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_NO_RANKS_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}
		else
		{
			$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=rank&layout=edit&id={$id}", false));
		}
	}

	function save()
	{
		$db = JFactory::getDBO();

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$rank_title   = JRequest::getString('rank_title');
		$rank_image   = basename(JRequest::getString('rank_image'));
		$rank_special = JRequest::getInt('rank_special');
		$rank_min     = JRequest::getInt('rank_min');
		$rankid       = JRequest::getInt('rankid', 0);

		if (!$rankid)
		{
			$db->setQuery("INSERT INTO #__kunena_ranks SET
					rank_title={$db->quote($rank_title)},
					rank_image={$db->quote($rank_image)},
					rank_special={$db->quote($rank_special)},
					rank_min={$db->quote($rank_min)}");
			$db->query();

			if (KunenaError::checkDatabaseError())
			{
				return;
			}
		}
		else
		{
			$db->setQuery("UPDATE #__kunena_ranks SET
					rank_title={$db->quote($rank_title)},
					rank_image={$db->quote($rank_image)},
					rank_special={$db->quote($rank_special)},
					rank_min={$db->quote($rank_min)}
				WHERE rank_id={$db->quote($rankid)}");
			$db->query();

			if (KunenaError::checkDatabaseError())
			{
				return;
			}
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_RANK_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function rankupload()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = JRequest::getVar('Filedata', null, 'files', 'array'); // File upload
		$format = JRequest::getCmd('format', 'html');

		$upload = KunenaUploadHelper::upload($file, JPATH_ROOT . '/' . KunenaFactory::getTemplate()->getRankPath(), $format);
		if ($upload)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_RANKS_UPLOAD_SUCCESS'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_RANKS_UPLOAD_ERROR_UNABLE'));
		}
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function remove()
	{
		$db = JFactory::getDBO();

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$cids = implode(',', $cid);

		if ($cids)
		{
			$db->setQuery("DELETE FROM #__kunena_ranks WHERE rank_id IN ($cids)");
			$db->query();

			if (KunenaError::checkDatabaseError())
			{
				return;
			}
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_RANK_DELETED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since K4.0
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
