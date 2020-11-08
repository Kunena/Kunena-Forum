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
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgKunenaKunenaInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'plg_kunena_kunena';

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
	 *
	 * @param   string  $type  'install', 'update' or 'discover_install'
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function __construct(string $type)
	{
		$this->app = Factory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since   Kunena 6.0
	 */
	public function preflight(string $type, ComponentAdapter $parent): bool
	{
	}

	/**
	 * Method to install the component
	 *
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function install(ComponentAdapter $parent): void
	{
		// Notice $parent->getParent() returns JInstaller object
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstall(ComponentAdapter $parent): void
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function update(ComponentAdapter $parent): void
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function postflight(string $type, ComponentAdapter $parent): void
	{
		$type = strtolower($type);

		if ($type == 'install' || $type == 'discover_install')
		{
			$this->enablePlugin('plg_kunena_kunena');
		}
	}

	/**
	 * @param $pluginName
	 *
	 * @return void|boolean
	 * @since version
	 */
	public function enablePlugin($pluginName): bool
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
