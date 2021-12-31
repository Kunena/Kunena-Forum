<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\KunenaUploadHelper;
use RuntimeException;

/**
 * Kunena Ranks Controller
 *
 * @since   Kunena 2.0
 */
class RanksController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
	}

	/**
	 * Add
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function add()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(Route::_('index.php?option=com_kunena&view=rank&layout=add', false));
	}

	/**
	 * Edit
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function edit($key = null, $urlVar = null)
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		$id = array_shift($cid);

		if (!$id)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_RANKS_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(Route::_("index.php?option=com_kunena&view=rank&layout=edit&id={$id}", false));
	}

	/**
	 * Save
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function save($key = null, $urlVar = null)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$rankTitle   = $this->app->input->getString('rankTitle');
		$rankImage   = basename($this->app->input->getString('rankImage'));
		$rankSpecial = $this->app->input->getInt('rankSpecial', 0);
		$rankMin     = $this->app->input->getInt('rankMin');
		$rankid      = $this->app->input->getInt('rankid', 0);

		if (!$rankid)
		{
			$query = $db->getQuery(true);

			// Insert columns.
			$columns = array('rankTitle', 'rankImage', 'rankSpecial', 'rankMin');

			// Insert values.
			$values = array($db->quote($rankTitle), $db->quote($rankImage), $db->quote($rankSpecial), $db->quote($rankMin));

			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__kunena_ranks'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}
		}
		else
		{
			$query = $db->getQuery(true)
				->update("{$db->quoteName('#__kunena_ranks')}")
				->set("rankTitle={$db->quote($rankTitle)}")
				->set("rankImage={$db->quote($rankImage)}")
				->set("rankSpecial={$db->quote($rankSpecial)}")
				->set("rankMin={$db->quote($rankMin)}")
				->where("rankId={$db->quote($rankid)}");

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_RANK_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Rank upload
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function rankUpload(): void
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file = $this->app->input->files->get('Filedata');

		// TODO : change this part to use other method than \Kunena\Forum\Libraries\Upload\UploadHelper::upload()
		$upload = KunenaUploadHelper::upload($file, JPATH_ROOT . '/' . KunenaFactory::getTemplate()->getRankPath(), 'html');

		if ($upload)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_RANKS_UPLOAD_SUCCESS'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_RANKS_UPLOAD_ERROR_UNABLE'), 'error');
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function remove(): void
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		$cids = implode(',', $cid);

		if ($cids)
		{
			$query = $db->getQuery(true)
				->delete()
				->from("{$db->quoteName('#__kunena_ranks')}")
				->where("rankId IN ($cids)");

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_RANK_DELETED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function cancel($key = null, $urlVar = null)
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
