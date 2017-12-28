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
defined('_JEXEC') or die();

/**
 * Kunena Smileys Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerSmilies extends KunenaController
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
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=smilies';
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

		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=add', false));
	}

	/**
	 * Edit
	 *
	 * @return void
	 *
	 * @since    2.0
	 * @throws Exception
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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_NO_SMILEYS_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=smiley&layout=edit&id={$id}", false));
	}

	/**
	 * Save
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
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

		$smiley_code        = JFactory::getApplication()->input->getString('smiley_code');
		$smiley_location    = basename(JFactory::getApplication()->input->getString('smiley_url'));
		$smiley_emoticonbar = JFactory::getApplication()->input->getInt('smiley_emoticonbar', 0);
		$smileyid           = JFactory::getApplication()->input->getInt('smileyid', 0);

		if (!$smileyid)
		{
			$db->setQuery(
					"INSERT INTO #__kunena_smileys SET
					code={$db->quote($smiley_code)},
					location={$db->quote($smiley_location)},
					emoticonbar={$db->quote($smiley_emoticonbar)}");

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
			$db->setQuery(
				"UPDATE #__kunena_smileys SET
					code={$db->quote($smiley_code)},
					location={$db->quote($smiley_location)},
					emoticonbar={$db->quote($smiley_emoticonbar)}
				WHERE id = '$smileyid'");

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

		$this->app->enqueueMessage(JText::_('COM_KUNENA_SMILEY_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Smiley upload
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function smileyupload()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = $this->app->input->files->get('Filedata');

		// TODO : change this part to use other method than KunenaUploadHelper::upload()
		$upload = KunenaUploadHelper::upload($file, JPATH_ROOT . '/' . KunenaFactory::getTemplate()->getSmileyPath(), 'html');

		if ($upload)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_SUCCESS'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_UNABLE'), 'error');
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function remove()
	{
		jimport('joomla.utilities.arrayhelper');
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
			$db->setQuery("DELETE FROM #__kunena_smileys WHERE id IN ($cids)");

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

		$this->app->enqueueMessage(JText::_('COM_KUNENA_SMILEY_DELETED'));
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
