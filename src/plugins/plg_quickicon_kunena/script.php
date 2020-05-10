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
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgQuickiconKunenaInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'plg_quickicon_kunena';

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
	 * @since   Kunena 6.0
	 */
	protected $app;

	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since   Kunena 6.0
	 */
	private $oldRelease;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 *
	 * @since   4.0.0
	 */
	protected $db;

	/**
	 *  Constructor
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($type)
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
	 * @since   Kunena 6.0
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * Method to install the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
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
	 * @since   Kunena 6.0
	 */
	public function update($parent)
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                                         $type    'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function postflight($type, $parent)
	{
		$this->enablePlugin('plg_quickicon_kunena');
	}

	/**
	 * @param   string $group   group
	 * @param   string $element element
	 *
	 * @since version
	 */
	public function enablePlugin($pluginName)
	{
		// Create a new db object.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('name') . ' = :pluginname')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->bind(':pluginname', $pluginName);

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			return false;
		}
	}
}
