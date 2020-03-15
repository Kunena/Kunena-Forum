<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Installer;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class Pkg_KunenaInstaller extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'com_kunena';

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $minimumPhp = '7.2.5';

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '4.0.0-dev';

	/**
	 * List of required PHP extensions.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * @var  Joomla\CMS\Application\CMSApplication  Holds the application object
	 *
	 * @since ?
	 */
	private $app;

	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since ?
	 */
	private $oldRelease;

	/**
	 *  Constructor
	 *
	 * @since ?
	 */
	public function __construct()
	{
		$this->app = Factory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since ?
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'element', $this->extension);
			$this->oldRelease = $manifest['version'];

			// Check if update is allowed (only update from 5.1.0 and higher)
			if (version_compare($this->oldRelease, '5.1.0', '<'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_kunena_UPDATE_UNSUPPORTED', $this->oldRelease, '5.1.0'), 'error');

				return false;
			}
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to install the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
		$parent->getParent()->setRedirectUrl('index.php?option=com_kunena');

		$this->addDashboardMenu('kunena', 'kunena');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '6.0.0', '<'))
		{
			// Remove integrated player classes
			$this->deleteFiles[]   = '/administrator/components/com_kunena/models/fields/player.php';
			$this->deleteFolders[] = '/components/com_kunena/helpers/player';

			// Remove old SQL files
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.4.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.5.0.sql';
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function postflight($type, $parent)
	{
		$type = strtolower($type);

		if ($type == 'install' || $type == 'discover_install')
		{
		}

		$this->enablePlugin('system', 'kunena');
		$this->enablePlugin('quickicon', 'kunena');
	}

	/**
	 * @param   string $group   group
	 * @param   string $element element
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function enablePlugin($group, $element)
	{
		$plugin = Table::getInstance('extension');

		if (!$plugin->load(array('type' => 'plugin', 'folder' => $group, 'element' => $element)))
		{
			return false;
		}

		$plugin->enabled = 1;

		return $plugin->store();
	}
}
