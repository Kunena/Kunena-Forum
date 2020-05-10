<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Attachment\AttachmentHelper;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\MessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\Poll\PollHelper;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\TopicUserHelper;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Forum\Libraries\Install\KunenaSampleData;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Kunena Backend Logs Controller
 *
 * @since   Kunena 5.0
 */
class InstallController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 5.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 *
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=cpanel';
	}

	/**
	 * Uninstall Kunena
	 *
	 * @return  boolean|void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 *
	 */
	public function uninstall()
	{
		if (!Session::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_kunena');

			return;
		}

		$app = Factory::getApplication();

		$allowed = $app->getUserState('com_kunena.uninstall.allowed');

		if ($allowed)
		{
			$installer = new KunenaModelInstall;
			$installer->setAction('uninstall');
			$installer->deleteTables('kunena_');

			$app->enqueueMessage(Text::_('COM_KUNENA_INSTALL_REMOVED'));

			$app->setUserState('com_kunena.uninstall.allowed', null);

			if (class_exists('KunenaForum') && !KunenaForum::isDev())
			{
				$installer = new \Joomla\CMS\Installer\Installer;
				$component = ComponentHelper::getComponent('com_kunena');
				$installer->uninstall('component', $component->id);

				if (Folder::exists(KPATH_MEDIA))
				{
					Folder::delete(KPATH_MEDIA);
				}

				if (Folder::exists(JPATH_ROOT . '/plugins/kunena'))
				{
					Folder::delete(JPATH_ROOT . '/plugins/kunena');
				}

				if (File::exists(JPATH_ADMINISTRATOR . '/manifests/packages/pkg_kunena.xml'))
				{
					File::delete(JPATH_ADMINISTRATOR . '/manifests/packages/pkg_kunena.xml');
				}

				$this->setRedirect('index.php?option=com_installer');
			}
			else
			{
				$this->setRedirect('index.php?option=com_kunena&view=install');
			}

			return;
		}
	}

	/**
	 * Set proper response for both AJAX and traditional calls.
	 *
	 * @param   array  $response  response
	 * @param   bool   $ajax      ajax
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 */
	protected function setResponse($response, $ajax)
	{
		if (!$ajax)
		{
			if (!empty($response['error']))
			{
				$this->setMessage($response['error'], 'error');
			}

			if (!empty($response['href']))
			{
				$this->setRedirect($response['href']);
			}
		}
		else
		{
			while (@ob_end_clean())
			{
			}

			header('Content-type: application/json');
			echo json_encode($response);
			flush();
			jexit();
		}
	}

	/**
	 * Check timeout
	 *
	 * @param   bool  $stop  stop
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 2.0
	 */
	protected function checkTimeout($stop = false)
	{
		static $start = null;

		if ($stop)
		{
			$start = 0;
		}

		$time = microtime(true);

		if ($start === null)
		{
			$start = $time;

			return false;
		}

		if ($time - $start < 14)
		{
			return false;
		}

		return true;
	}

	/**
	 * Perform recount on statistics in smaller chunks.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 *
	 */
	public function InstallSampleData()
	{
		$ajax = $this->input->getWord('format', 'html') == 'json';

		if (!Session::checkToken('request'))
		{
			$this->setResponse(
				[
					'success' => false,
					'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
					'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => Text::_('COM_KUNENA_ERROR_TOKEN'),
				],
				$ajax
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$state = $this->app->getUserState('com_kunena.admin.recount', null);

		try
		{
			$this->checkTimeout();

			while (1)
			{
				switch ($state->step)
				{
					default:
						KunenaSampleData::installSampleData();
						$header = Text::_('COM_KUNENA_RECOUNTFORUMS_DONE');
						$msg    = Text::_('COM_KUNENA_AJAX_REQUESTED_RECOUNTED');
						$this->app->setUserState('com_kunena.admin.installsampledata', null);
						$this->setResponse(
							[
								'success' => true,
								'header'  => $header,
								'message' => $msg,
							],
							$ajax
						);
						$this->setRedirect(KunenaRoute::_($this->baseurl, false), $header);

						return;
				}
			}

			$state->reload++;
			$this->app->setUserState('com_kunena.admin.installsampledata', $state);
		}
		catch (Exception $e)
		{
			if (!$ajax)
			{
				throw $e;
			}

			$this->setResponse(
				[
					'success' => false,
					'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
					'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => $e->getMessage(),
				],
				$ajax
			);
		}

		$token    = Session::getFormToken() . '=1';
		$redirect = KunenaRoute::_("{$this->baseurl}&task=InstallSampleData&i={$state->reload}&{$token}", false);
		$this->setResponse(
			[
				'success' => true,
				'header'  => Text::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
				'message' => $msg,
				'href'    => $redirect,
			], $ajax
		);
	}
}

