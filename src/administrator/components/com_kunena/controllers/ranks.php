<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Ranks Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerRanks extends KunenaController
{
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since    2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
	}

	/**
	 * Add
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function add()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=rank&layout=add', false));
	}

	/**
	 * Edit
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    2.0
	 */
	public function edit()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = JFactory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);

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

	/**
	 * Save
	 *
	 * @return void
	 *
	 * @since    2.0
	 *
	 * @throws Exception
	 */
	public function save()
	{
		$db = JFactory::getDbo();

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$rank_title   = JFactory::getApplication()->input->getString('rank_title');
		$rank_image   = basename(JFactory::getApplication()->input->getString('rank_image'));
		$rank_special = JFactory::getApplication()->input->getInt('rank_special');
		$rank_min     = JFactory::getApplication()->input->getInt('rank_min');
		$rankid       = JFactory::getApplication()->input->getInt('rankid', 0);

		if (!$rankid)
		{
			$db->setQuery("INSERT INTO #__kunena_ranks SET
					rank_title={$db->quote($rank_title)},
					rank_image={$db->quote($rank_image)},
					rank_special={$db->quote($rank_special)},
					rank_min={$db->quote($rank_min)}");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

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

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_RANK_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Rank upload
	 *
	 * @return void
	 *
	 * @since    2.0
	 *
	 * @throws Exception
	 */
	public function rankupload()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = $this->app->input->files->get('Filedata');

		// TODO : change this part to use other method than KunenaUploadHelper::upload()
		$upload = KunenaUploadHelper::upload($file, JPATH_ROOT . '/' . KunenaFactory::getTemplate()->getRankPath(), 'html');

		if ($upload)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_RANKS_UPLOAD_SUCCESS'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_RANKS_UPLOAD_ERROR_UNABLE'), 'error');
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove
	 *
	 * @return void
	 *
	 * @since    2.0
	 *
	 * @throws Exception
	 */
	public function remove()
	{
		$db = JFactory::getDbo();

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = JFactory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);

		$cids = implode(',', $cid);

		if ($cids)
		{
			$db->setQuery("DELETE FROM #__kunena_ranks WHERE rank_id IN ($cids)");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

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
