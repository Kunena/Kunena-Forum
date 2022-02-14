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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;

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
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=cpanel';
	}

	/**
	 * Uninstall Kunena
	 *
	 * @return bool
	 *
	 * @since   Kunena 6.0
	 * @throws \Kunena\Forum\Libraries\Install\KunenaInstallerException
	 */
	public function uninstall(): bool
	{
		if (!Session::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_kunena');

			return false;
		}

		$app = Factory::getApplication();

		$allowed = $app->getUserState('com_kunena.uninstall.allowed');

		if ($allowed)
		{
			$installer = new KunenaModelInstall;
			$installer->uninstall();
			$installer->deleteTables('kunena_');
			$installer->cleanMailTemplates();

			$app->enqueueMessage(Text::_('COM_KUNENA_INSTALL_REMOVED'));

			$app->setUserState('com_kunena.uninstall.allowed', null);

			$installer = new Installer;
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

			return true;
		}

		return false;
	}
}
