<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Install;

\defined('_JEXEC') or die();

use Exception;
use Joomla\Archive\Archive;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use stdClass;
use const KPATH_ADMIN;

/**
 *
 */
\define('KUNENA_INSTALLER_PATH', __DIR__);
/**
 *
 */
\define('KUNENA_INSTALLER_ADMINPATH', \dirname(KUNENA_INSTALLER_PATH));
/**
 *
 */
\define('KUNENA_INSTALLER_SITEPATH', JPATH_SITE . '/components/' . basename(KUNENA_INSTALLER_ADMINPATH));
/**
 *
 */
\define('KUNENA_INSTALLER_MEDIAPATH', JPATH_SITE . '/media/kunena');

/**
 * Install Model for Kunena
 *
 * @since   Kunena 6.0
 */
class KunenaModelInstall extends BaseDatabaseModel
{
	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	public $steps = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_versionprefix = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_installed = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_versions = [];

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_action = false;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_errormsg = null;

	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	protected $_versiontablearray = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_versionarray = null;

	private $tables;

	/**
	 * @var DatabaseDriver|null
	 * @since version
	 */
	private $db;

	/**
	 * @var array
	 * @since version
	 */
	private $_sbVersions;

	/**
	 * @var array
	 * @since version
	 */
	private $_fbVersions;

	/**
	 * @var \null[][]
	 * @since version
	 */
	private $_kVersions;

	/**
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct()
	{
		// Load installer language file only from the component
		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_kunena.install', KUNENA_INSTALLER_ADMINPATH, 'en-GB');
		$lang->load('com_kunena.install', KUNENA_INSTALLER_ADMINPATH);
		$lang->load('com_kunena.libraries', KUNENA_INSTALLER_ADMINPATH, 'en-GB');
		$lang->load('com_kunena.libraries', KUNENA_INSTALLER_ADMINPATH);

		parent::__construct();
		$this->db = Factory::getContainer()->get('DatabaseDriver');

		if (\function_exists('ignore_user_abort'))
		{
			ignore_user_abort(true);
		}

		$this->setState('default_max_time', @ini_get('max_execution_time'));
		@set_time_limit(300);
		$this->setState('max_time', @ini_get('max_execution_time'));

		// TODO: move to migration
		$this->_versiontablearray = [['prefix' => 'kunena_', 'table' => 'kunena_version'], ['prefix' => 'fb_', 'table' => 'fb_version']];

		// TODO: move to migration
		$this->_kVersions = [
			['component' => null, 'prefix' => null, 'version' => null, 'date' => null], ];

		// TODO: move to migration
		$this->_fbVersions = [
			['component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.4', 'date' => '2007-12-23',
			 'table'     => 'fb_sessions', 'column' => 'currvisit', ],
			['component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.3', 'date' => '2007-09-04',
			 'table'     => 'fb_categories', 'column' => 'headerdesc', ],
			['component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.2', 'date' => '2007-08-03',
			 'table'     => 'fb_users', 'column' => 'rank', ],
			['component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.1', 'date' => '2007-05-20',
			 'table'     => 'fb_users', 'column' => 'uhits', ],
			['component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.0', 'date' => '2007-04-15',
			 'table'     => 'fb_messages', ],
			['component' => null, 'prefix' => null, 'version' => null, 'date' => null], ];

		// TODO: move to migration
		$this->_sbVersions = [
			['component' => 'JoomlaBoard', 'prefix' => 'sb_', 'version' => 'v1.0.5', 'date' => '1000-01-01',
			 'table'     => 'sb_messages', ],
			['component' => null, 'prefix' => null, 'version' => null, 'date' => null], ];

		$this->steps = [
			['step' => '', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_INSTALL')],
			['step' => 'Prepare', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_PREPARE')],
			['step' => 'Plugins', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_PLUGINS')],
			['step' => 'Database', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_DATABASE')],
			['step' => 'Finish', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_FINISH')],
			['step' => '', 'menu' => Text::_('COM_KUNENA_INSTALL_STEP_COMPLETE')], ];
	}

	/**
	 * Initialise Kunena, run from Joomla installer.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function install(): void
	{
		// Make sure that we are using the latest English language files
		$this->installLanguage('en-GB');

		$this->setStep(0);
	}

	/**
	 * @param   string  $tag   tag
	 * @param   string  $name  name
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function installLanguage(string $tag, $name = ''): bool
	{
		$exists       = false;
		$success      = true;
		$destinations = [
			'site'  => JPATH_SITE . '/components/com_kunena',
			'admin' => JPATH_ADMINISTRATOR . '/components/com_kunena',
		];

		foreach ($destinations as $dest)
		{
			if ($success != true)
			{
				continue;
			}

			$installdir = "{$dest}/language/{$tag}";

			// Install language from dest/language/xx-XX
			if (is_dir($installdir))
			{
				$exists = $success;

				// Older versions installed language files into main folders

				// Those files need to be removed to bring language up to date!
				$files = Folder::files($installdir, '\.ini$');

				foreach ($files as $filename)
				{
					if (is_file(JPATH_SITE . "/language/{$tag}/{$filename}"))
					{
						File::delete(JPATH_SITE . "/language/{$tag}/{$filename}");
					}

					if (is_file(JPATH_ADMINISTRATOR . "/language/{$tag}/{$filename}"))
					{
						File::delete(JPATH_ADMINISTRATOR . "/language/{$tag}/{$filename}");
					}
				}
			}
		}

		if ($exists && $name)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_LANGUAGE', $name), $success);
		}

		return $success;
	}

	/**
	 * @param   string  $task    task
	 * @param   bool    $result  result
	 * @param   string  $msg     message
	 * @param   null    $id      id
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function addStatus(string $task, $result = false, $msg = '', $id = null): void
	{
		$status = $this->getState('status');
		$step   = $this->getStep();

		if ($id === null)
		{
			$status [] = ['step' => $step, 'task' => $task, 'success' => $result, 'msg' => $msg];
		}
		else
		{
			unset($status [$id]);
			$status [$id] = ['step' => $step, 'task' => $task, 'success' => $result, 'msg' => $msg];
		}

		$this->setState('status', $status);
		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.status', $status);
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param   string  $property  Optional parameter name.
	 * @param   mixed   $default   The default value to use if no state property exists by name.
	 *
	 * @return    object    The property where specified, the state object where omitted.
	 *
	 * @throws  Exception
	 * @since    1.6
	 */
	public function getState($property = null, $default = null): object
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false)
		{
			$app = Factory::getApplication();
			$this->setState('action', $app->getUserState('com_kunena.install.action', null));
			$this->setState('step', $step = $app->getUserState('com_kunena.install.step', 0));
			$this->setState('task', $app->getUserState('com_kunena.install.task', 0));
			$this->setState('version', $app->getUserState('com_kunena.install.version', null));

			if ($step == 0)
			{
				$app->setUserState('com_kunena.install.status', []);
			}

			$this->setState('status', $app->getUserState('com_kunena.install.status'));

			$this->__state_set = true;
		}

		$value = parent::getState($property);

		return \is_null($value) ? $default : $value;
	}

	/**
	 * @return  object
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getStep(): object
	{
		return $this->getState('step', 0);
	}

	/**
	 * @param   string  $step  step
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function setStep(string $step): void
	{
		$this->setState('step', (int) $step);
		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.step', (int) $step);
		$this->setTask(0);
	}

	/**
	 * @param   string  $task  task
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function setTask(string $task): void
	{
		$this->setState('task', (int) $task);
		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.task', (int) $task);
	}

	/**
	 * Uninstall Kunena, run from Joomla installer.
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function uninstall(): bool
	{
		// Put back file that was removed during installation.
		$contents = '';
		File::write(KPATH_ADMIN . '/install.php', $contents);

		// Uninstall all plugins.
		$this->uninstallPlugin('kunena', 'altauserpoints');
		$this->uninstallPlugin('kunena', 'community');
		$this->uninstallPlugin('kunena', 'comprofiler');
		$this->uninstallPlugin('kunena', 'easyprofile');
		$this->uninstallPlugin('kunena', 'easysocial');
		$this->uninstallPlugin('kunena', 'gravatar');
		$this->uninstallPlugin('kunena', 'joomla');
		$this->uninstallPlugin('kunena', 'kunena');
		$this->uninstallPlugin('kunena', 'finder');
		$this->uninstallPlugin('sampleData', 'kunena');
		$this->uninstallPlugin('finder', 'kunena');
		$this->uninstallPlugin('quickicon', 'kunena');
		$this->uninstallPlugin('content', 'kunena');

		// Uninstall menu module.
		$this->uninstallModule('mod_kunenamenu');

		// Remove all Kunena related menu items, including aliases
		if (class_exists('KunenaMenuFix'))
		{
			$items = KunenaMenuFix::getAll();

			foreach ($items as $item)
			{
				KunenaMenuFix::delete($item->id);
			}
		}

		$this->deleteMenu();

		// Uninstall Kunena library
		$this->uninstallLibrary();

		// Uninstall Kunena media
		$this->uninstallMedia();

		// Uninstall Kunena system plugin
		$this->uninstallPlugin('system', 'kunena');

		return true;
	}

	/**
	 * @param   string  $folder  folder
	 * @param   string  $name    name
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstallPlugin(string $folder, string $name): void
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' =' . $db->quote('plugin') . ' AND ' . $db->quoteName('folder') . '=' . $db->quote($folder) . ' AND ' . $db->quoteName('element') . '=' . $db->quote($name));
		$db->setQuery($query);

		$pluginid = $db->loadResult();

		if ($pluginid)
		{
			$installer = new Installer;
			$installer->uninstall('plugin', $pluginid);
		}
	}

	/**
	 * @param   string  $name  name
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstallModule(string $name): void
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . '=' . $db->quote('module') . ' AND' . $db->quoteName('element') . '=' . $db->quote($name));
		$db->setQuery($query);

		$moduleid = $db->loadResult();

		if ($moduleid)
		{
			$installer = new Installer;
			$installer->uninstall('module', $moduleid);
		}
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function deleteMenu(): void
	{
		$table = Table::getInstance('MenuType');
		$table->load(['menutype' => 'kunenamenu']);

		if ($table->id)
		{
			$success = $table->delete();

			if (!$success)
			{
				Factory::getApplication()->enqueueMessage($table->getError(), 'error');
			}
		}

		/** @var Cache|CacheController $cache */
		$cache = Factory::getCache();
		$cache->clean('mod_menu');
	}

	/**
	 * Method to uninstall the Kunena library during uninstall process
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstallLibrary(): void
	{
		$libraryid = $this->uninstallMediaLibraryQuery('library', 'kunena');

		if ($libraryid)
		{
			$installer = new Installer;
			$installer->uninstall('library', $libraryid);
		}
	}

	/**
	 * Method to uninstall the Kunena media during uninstall process
	 *
	 * @param   string  $type     type
	 * @param   string  $element  Name of the package or of the component
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	private function uninstallMediaLibraryQuery(string $type, string $element): int
	{
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('extension_id'));
		$query->from($this->db->quoteName('#__extensions'));
		$query->where($this->db->quoteName('type') . ' = ' . $this->db->quote($type));
		$query->where($this->db->quoteName('element') . ' = ' . $this->db->quote($element));
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Method to uninstall the Kunena media during uninstall process
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstallMedia(): void
	{
		$mediaid = $this->uninstallMediaLibraryQuery('file', 'kunena_media');

		if ($mediaid)
		{
			$installer = new Installer;
			$installer->uninstall('file', $mediaid);
		}
	}

	/**
	 * Get model
	 *
	 * @return  $this
	 * @since   Kunena 6.0
	 */
	public function getModel(): KunenaModelInstall
	{
		return $this;
	}

	/**
	 * Get Status
	 *
	 * @return  object
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getStatus(): object
	{
		return $this->getState('status', []);
	}

	/**
	 * @return  array|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getSteps(): ?array
	{
		return $this->steps;
	}

	// TODO: move to migration (exists in 2.0)

	/**
	 * @param   string  $path  path
	 * @param   string  $name  name
	 *
	 * @return bool|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function installModule(string $path, string $name): ?bool
	{
		$success = false;

		$dest = KunenaPath::tmpdir() . "/kinstall_mod_{$name}";

		if (is_dir($dest))
		{
			Folder::delete($dest);
		}

		if (is_dir(KUNENA_INSTALLER_ADMINPATH . '/' . $path))
		{
			// Copy path
			$success = Folder::copy(KUNENA_INSTALLER_ADMINPATH . '/' . $path, $dest);
		}
		elseif (is_file(KUNENA_INSTALLER_ADMINPATH . '/' . $path))
		{
			// Extract file
			$success = $this->extract(KUNENA_INSTALLER_ADMINPATH, $path, $dest);
		}

		if ($success)
		{
			$success = Folder::create($dest . '/language/en-GB');
		}

		if ($success && is_file(KUNENA_INSTALLER_SITEPATH . "/language/en-GB/en-GB.mod_{$name}.ini"))
		{
			$success = File::copy(KUNENA_INSTALLER_SITEPATH . "/language/en-GB/en-GB.mod_{$name}.ini", "{$dest}/language/en-GB/en-GB.mod_{$name}.ini");
		}

		if ($success && is_file(KUNENA_INSTALLER_SITEPATH . "/language/en-GB/en-GB.mod_{$name}.sys.ini"))
		{
			$success = File::copy(KUNENA_INSTALLER_SITEPATH . "/language/en-GB/en-GB.mod_{$name}.sys.ini", "{$dest}/language/en-GB/en-GB.mod_{$name}.sys.ini");
		}

		// Only install module if it can be used in current Joomla version (manifest exists)
		if ($success && is_file("{$dest}/mod_{$name}.xml"))
		{
			$installer = new Installer;
			$success   = $installer->install($dest);
			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_MODULE_STATUS', ucfirst($name)), $success);
		}
		elseif (!$success)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_MODULE_STATUS', ucfirst($name)), $success);
		}

		Folder::delete($dest);

		return $success;
	}

	/**
	 * @param   string  $path      path
	 * @param   string  $filename  filename
	 * @param   null    $dest      dest
	 * @param   bool    $silent    silent
	 *
	 * @return bool|null
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function extract(string $path, string $filename, $dest = null, bool $silent = false): ?bool
	{
		$success = null;

		if (!$dest)
		{
			$dest = $path;
		}

		$file = "{$path}/{$filename}";

		$text = '';

		if (is_file($file))
		{
			$success = true;

			if (!Folder::exists($dest))
			{
				$success = Folder::create($dest);
			}

			if ($success)
			{
				$archive = new Archive;
				$success = $archive->extract($file, $dest);
			}

			if (!$success)
			{
				$text .= Text::sprintf('COM_KUNENA_INSTALL_EXTRACT_FAILED', $file);
			}
		}
		else
		{
			$success = true;
			$text    .= Text::sprintf('COM_KUNENA_INSTALL_EXTRACT_MISSING', $file);
		}

		if ($success !== null && !$silent)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_EXTRACT_STATUS', $filename), $success, $text);
		}

		return $success;
	}

	/**
	 * @param   string   $path      path
	 * @param   string   $group     group
	 * @param   string   $name      name
	 * @param   boolean  $publish   publish
	 * @param   integer  $ordering  ordering
	 *
	 * @return  boolean|null
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function installPlugin(string $path, string $group, string $name, bool $publish, $ordering = 0): ?bool
	{
		$success = false;

		$dest = KunenaPath::tmpdir() . "/kinstall_plg_{$group}_{$name}";

		if (is_dir($dest))
		{
			Folder::delete($dest);
		}

		if (is_dir(KUNENA_INSTALLER_PATH . '/' . $path))
		{
			// Copy path
			$success = Folder::copy(KUNENA_INSTALLER_PATH . '/' . $path, $dest);
		}
		elseif (is_file(KUNENA_INSTALLER_PATH . '/' . $path))
		{
			// Extract file
			$success = $this->extract(KUNENA_INSTALLER_PATH, $path, $dest);
		}

		if ($success)
		{
			$success = Folder::create($dest . '/language/en-GB');
		}

		if ($success && is_file(KUNENA_INSTALLER_ADMINPATH . "/language/en-GB/en-GB.plg_{$group}_{$name}.ini"))
		{
			$success = File::copy(KUNENA_INSTALLER_ADMINPATH . "/language/en-GB/en-GB.plg_{$group}_{$name}.ini", "{$dest}/language/en-GB/en-GB.plg_{$group}_{$name}.ini");
		}

		if ($success && is_file(KUNENA_INSTALLER_ADMINPATH . "/language/en-GB/en-GB.plg_{$group}_{$name}.sys.ini"))
		{
			$success = File::copy(KUNENA_INSTALLER_ADMINPATH . "/language/en-GB/en-GB.plg_{$group}_{$name}.sys.ini", "{$dest}/language/en-GB/en-GB.plg_{$group}_{$name}.sys.ini");
		}

		// Only install plugin if it can be used in current Joomla version (manifest exists)
		if ($success && is_file("{$dest}/{$name}.xml"))
		{
			$installer = new Installer;
			$success   = $installer->install($dest);

			if ($success)
			{
				// First change plugin ordering
				$plugin = $this->loadPlugin((array) $group, $name);

				if ($ordering && !$plugin->ordering)
				{
					$plugin->ordering = $ordering;
				}

				if ($publish)
				{
					$plugin->enabled = $publish;
				}

				$success = $plugin->store();
			}

			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_PLUGIN_STATUS', ucfirst($group) . ' - ' . ucfirst($name)), $success);
		}
		elseif (!$success)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_PLUGIN_STATUS', ucfirst($group) . ' - ' . ucfirst($name)), $success);
		}

		Folder::delete($dest);

		return $success;
	}

	/**
	 * @param   array   $group    group
	 * @param   string  $element  element
	 *
	 * @return  boolean|\Joomla\CMS\Table\Table
	 *
	 * @since   Kunena 6.0
	 */
	public function loadPlugin(array $group, string $element)
	{
		$plugin = Table::getInstance('extension');
		$plugin->load(['type' => 'plugin', 'folder' => $group, 'element' => $element]);

		return $plugin;
	}

	/**
	 * @return  void
	 *
	 * @throws  KunenaInstallerException
	 * @throws  KunenaSchemaException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function stepPrepare(): void
	{
		$results = [];

		$this->setVersion(0);
		$this->setAvatarStatus();
		$this->setAttachmentStatus();
		$this->addStatus(Text::_('COM_KUNENA_INSTALL_STEP_PREPARE'), true);

		$cache = Cache::getInstance();
		$cache->clean('kunena');
		$action = $this->getAction();

		if ($action == 'install' || $action == 'migrate')
		{
			// Let's start from clean database
			$this->deleteTables('kunena_');
			$this->deleteMenu();
		}

		$installed = $this->getDetectVersions();

		if ($action == 'migrate' && $installed['fb']->component)
		{
			$version    = $installed['fb'];
			$results [] = $this->migrateTable($version->prefix, $version->prefix . 'version', 'kunena_version');
		}
		else
		{
			$version = $installed['kunena'];
		}

		$this->setVersion($version);

		// Always enable the System - Kunena plugin
		$query = $this->db->getQuery(true);
		$query->clear()
			->update($this->db->quoteName('#__extensions'))
			->set($this->db->quoteName('enabled') . ' = 1')
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('system'))
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('kunena'));
		$this->db->setQuery($query);
		$this->db->execute();

		$schema    = new KunenaModelSchema;
		$results[] = $schema->updateSchemaTable('kunena_version');

		// Insert data from the old version, if it does not exist in the version table
		if ($version->id == 0 && $version->component)
		{
			$this->insertVersionData($version->version, $version->versiondate, $version->versionname, null);
		}

		/*
				foreach ( $results as $result )
					if (!empty($result['action']) && empty($result['success']))
						$this->addStatus ( Text::_('COM_KUNENA_INSTALL_'.strtoupper($result['action'])) . ' ' . $result ['name'], $result ['success'] );
		*/
		$this->insertVersion('migrateDatabase');

		if (!$this->getInstallError())
		{
			$this->setStep($this->getStep() + 1);
		}

		$this->checkTimeout(true);
	}

	/**
	 * @param   integer  $version  version
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function setVersion(int $version): void
	{
		$this->setState('version', $version);
		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.version', $version);
	}

	/**
	 * @param   null  $stats  stats
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function setAvatarStatus($stats = null): void
	{
		if (!$stats)
		{
			$stats          = new stdClass;
			$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		}

		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.avatars', $stats);
	}

	/**
	 * @param   null  $stats  stats
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function setAttachmentStatus($stats = null): void
	{
		if (!$stats)
		{
			$stats          = new stdClass;
			$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		}

		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.attachments', $stats);
	}

	/**
	 * Get Action
	 *
	 * @return  object
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getAction(): object
	{
		return $this->getState('action', null);
	}

	/**
	 * Set Action
	 *
	 * @param   string  $action  action
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function setAction(string $action): void
	{
		$this->setState('action', $action);
		$app = Factory::getApplication();
		$app->setUserState('com_kunena.install.action', $action);
	}

	/**
	 * @param   string  $prefix  prefix
	 *
	 * @return void
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function deleteTables(string $prefix): void
	{
		$tables = $this->listTables($prefix);

		foreach ($tables as $table)
		{
			$this->db->setQuery("DROP TABLE IF EXISTS " . $this->db->quoteName($this->db->getPrefix() . $table));

			try
			{
				$this->db->execute();
			}
			catch (Exception $e)
			{
				throw new KunenaInstallerException($e->getMessage(), $e->getCode());
			}
		}

		unset($this->tables [$prefix]);
	}

	/**
	 * @param   string  $prefix  prefix
	 *
	 * @return void
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function cleanMailTemplates()
	{
		$query = $this->db->getQuery(true);

		$conditions = array(
			$this->db->quoteName('template_id') . ' = ' . $this->db->quote('com_kunena.reply'),
		);

		$query->delete($this->db->quoteName('#__mail_templates'));
		$query->where($conditions);

		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$query = $this->db->getQuery(true);

		$conditions = array(
			$this->db->quoteName('template_id') . ' = ' . $this->db->quote('com_kunena.replymoderator'),
		);

		$query->delete($this->db->quoteName('#__mail_templates'));
		$query->where($conditions);

		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$query = $this->db->getQuery(true);

		$conditions = array(
			$this->db->quoteName('template_id') . ' = ' . $this->db->quote('com_kunena.report'),
		);

		$query->delete($this->db->quoteName('#__mail_templates'));
		$query->where($conditions);

		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @param   string  $prefix  prefix
	 * @param   bool    $reload  reload
	 *
	 * @return  mixed
	 *
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function listTables(string $prefix, $reload = false): array
	{
		if (isset($this->tables [$prefix]) && !$reload)
		{
			return $this->tables [$prefix];
		}

		$this->db->setQuery("SHOW TABLES LIKE " . $this->db->quote($this->db->getPrefix() . $prefix . '%'));

		try
		{
			$list = (array) $this->db->loadColumn();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$this->tables [$prefix] = [];

		foreach ($list as $table)
		{
			$table                           = preg_replace('/^' . $this->db->getPrefix() . '/', '', $table);
			$this->tables [$prefix] [$table] = $table;
		}

		return $this->tables [$prefix];
	}

	/**
	 * @return  array
	 *
	 * @throws  KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function getDetectVersions(): array
	{
		if (!empty($this->_versions))
		{
			return $this->_versions;
		}

		$kunena    = $this->getInstalledVersion('kunena_', $this->_kVersions);
		$fireboard = $this->getInstalledVersion('fb_', $this->_fbVersions);

		if (!empty($kunena->state))
		{
			$this->_versions['failed'] = $kunena;
			$kunena                    = $this->getInstalledVersion('kunena_', $this->_kVersions, true);

			if (version_compare($kunena->version, '1.6.0-ALPHA', "<"))
			{
				$kunena->ignore = true;
			}
		}

		if ($kunena->component && empty($kunena->ignore))
		{
			$this->_versions['kunena'] = $kunena;
			$migrate                   = false;
		}
		else
		{
			$migrate = $this->isMigration($kunena, $fireboard);
		}

		if (!empty($fireboard->component) && $migrate)
		{
			$this->_versions['fb'] = $fireboard;
		}

		if (empty($kunena->component))
		{
			$this->_versions['kunena'] = $kunena;
		}
		else
		{
			if (!empty($fireboard->component))
			{
				$uninstall                    = clone $fireboard;
				$uninstall->action            = 'RESTORE';
				$this->_versions['uninstall'] = $uninstall;
			}
			else
			{
				$uninstall                    = clone $kunena;
				$uninstall->action            = 'UNINSTALL';
				$this->_versions['uninstall'] = $uninstall;
			}
		}

		foreach ($this->_versions as $version)
		{
			$version->label       = $this->getActionText($version);
			$version->description = $this->getActionText($version, 'desc');
			$version->hint        = $this->getActionText($version, 'hint');
			$version->warning     = $this->getActionText($version, 'warn');
			$version->link        = Uri::base(true) . '/index.php?option=com_kunena&view=install&task=' . strtolower($version->action) . '&' . Session::getFormToken() . '=1';
		}

		if ($migrate)
		{
			$kunena->warning = $this->getActionText((string) $fireboard, 'warn', 'upgrade');
		}
		else
		{
			$kunena->warning = '';
		}

		return $this->_versions;
	}

	/**
	 * @param   string   $prefix       prefix
	 * @param   array    $versionlist  versionlist
	 * @param   boolean  $state        state
	 *
	 * @return \stdClass|null
	 *
	 * @throws \Kunena\Forum\Libraries\Install\KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function getInstalledVersion(string $prefix, array $versionlist, $state = false): ?stdClass
	{
		if (!$state && isset($this->_installed[$prefix]))
		{
			return $this->_installed[$prefix];
		}

		if ($prefix === null)
		{
			$versionprefix = $this->getVersionPrefix();
		}
		else
		{
			$test = [$prefix . 'version'];

			if ($this->detectTable($test))
			{
				$versionprefix = $prefix;
			}
			else
			{
				$versionprefix = null;
			}
		}

		if ($versionprefix)
		{
			// Version table exists, try to get installed version
			$state = $state ? " WHERE state=''" : "";
			$this->db->setQuery("SELECT * FROM " . $this->db->quoteName($this->db->getPrefix() . $versionprefix . 'version') . $state . " ORDER BY `id` DESC", 0, 1);

			try
			{
				$version = $this->db->loadObject();
			}
			catch (Exception $e)
			{
				throw new KunenaInstallerException($e->getMessage(), $e->getCode());
			}

			if ($version)
			{
				$version->version = strtolower($version->version);
				$version->prefix  = $versionprefix;

				if (version_compare($version->version, '1.0.5', ">"))
				{
					$version->component = 'Kunena';
				}
				else
				{
					$version->component = 'FireBoard';
				}

				$version->version = strtoupper($version->version);

				// Version table may contain dummy version.. Ignore it
				if (!$version || version_compare($version->version, '0.1.0', "<"))
				{
					unset($version);
				}
			}
		}

		if (!isset($version))
		{
			// No version found -- try to detect version by searching some missing fields
			$match = $this->detectTable($versionlist);

			// Clean install
			if (empty($match))
			{
				return $this->_installed = null;
			}

			// Create version object
			$version              = new stdClass;
			$version->id          = 0;
			$version->component   = $match ['component'];
			$version->version     = strtoupper($match ['version']);
			$version->versiondate = $match ['date'];
			$version->installdate = '';
			$version->versionname = '';
			$version->prefix      = $match ['prefix'];
		}

		$version->action = $this->getInstallAction($version);

		return $this->_installed[$prefix] = $version;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function getVersionPrefix()
	{
		if ($this->_versionprefix !== false)
		{
			return $this->_versionprefix;
		}

		$match = $this->detectTable($this->_versiontablearray);

		if (isset($match ['prefix']))
		{
			$this->_versionprefix = $match ['prefix'];
		}
		else
		{
			$this->_versionprefix = null;
		}

		return $this->_versionprefix;
	}

	/**
	 * @param   array  $detectlist  detect list
	 *
	 * @return  array
	 *
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function detectTable(array $detectlist)
	{
		// Cache
		static $tables = [];
		static $fields = [];

		$found = 0;

		if (\is_string($detectlist))
		{
			$detectlist = [['table' => $detectlist]];
		}

		foreach ($detectlist as $detect)
		{
			// If no detection is needed, return current item
			if (!isset($detect ['table']))
			{
				return $detect;
			}

			$table = $this->db->getPrefix() . $detect ['table'];

			// Match if table exists
			if (!isset($tables [$table])) // Not cached
			{
				$this->db->setQuery("SHOW TABLES LIKE " . $this->db->quote($table));

				try
				{
					$result = $this->db->loadResult();
				}
				catch (Exception $e)
				{
					throw new KunenaInstallerException($e->getMessage(), $e->getCode());
				}

				$tables [$table] = $result;
			}

			if (!empty($tables [$table]))
			{
				$found = 1;
			}

			// Match if column in a table exists
			if ($found && isset($detect ['column']))
			{
				if (!isset($fields [$table])) // Not cached
				{
					$this->db->setQuery("SHOW COLUMNS FROM " . $this->db->quoteName($table));

					try
					{
						$result = $this->db->loadObjectList('Field');
					}
					catch (Exception $e)
					{
						throw new KunenaInstallerException($e->getMessage(), $e->getCode());
					}

					$fields [$table] = $result;
				}

				if (!isset($fields [$table] [$detect ['column']]))
				{
					$found = 0;
				}
			}

			if ($found)
			{
				return $detect;
			}
		}

		return [];
	}

	/**
	 * @param   object  $version  version
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInstallAction($version = null)
	{
		if ($version->component === null)
		{
			$this->_action = 'INSTALL';
		}
		else
		{
			if ($version->prefix != 'kunena_')
			{
				$this->_action = 'MIGRATE';
			}
			else
			{
				if (version_compare(strtolower(KunenaForum::version()), strtolower($version->version), '>'))
				{
					$this->_action = 'UPGRADE';
				}
				else
				{
					if (version_compare(strtolower(KunenaForum::version()), strtolower($version->version), '<'))
					{
						$this->_action = 'DOWNGRADE';
					}
					else
					{
						$this->_action = 'REINSTALL';
					}
				}
			}
		}

		return $this->_action;
	}

	/**
	 * @param   object  $new  new
	 * @param   object  $old  new
	 *
	 * @return  boolean
	 *
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function isMigration(object $new, object $old): bool
	{
		// If K1.6 not installed: migrate
		if (!$new->component || !$this->detectTable([$new->prefix . 'messages']))
		{
			return true;
		}

		// If old not installed: upgrade
		if (!$old->component || !$this->detectTable([$old->prefix . 'messages']))
		{
			return false;
		}

		// If K1.6 is installed and old is not Kunena: upgrade
		if ($old->component != 'Kunena')
		{
			return false;
		}

		// User is currently using K1.6: upgrade
		if (strtotime($new->installdate) > strtotime($old->installdate))
		{
			return false;
		}

		// User is currently using K1.0/K1.5: migrate
		if (strtotime($new->installdate) < strtotime($old->installdate))
		{
			return true;
		}

		// Both K1.5 and K1.6 were installed during the same day.. Not going to be easy choice..

		// Let's assume that this could be migration
		return true;
	}

	// TODO: move to migration

	/**
	 * @param   object  $version  version
	 * @param   string  $type     type
	 * @param   null    $action   action
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getActionText(object $version, $type = '', $action = null): string
	{
		/*
		 Translations generated:

		Installation types: COM_KUNENA_INSTALL_UPGRADE, COM_KUNENA_INSTALL_DOWNGRADE, COM_KUNENA_INSTALL_REINSTALL,
		COM_KUNENA_INSTALL_MIGRATE, COM_KUNENA_INSTALL_INSTALL, COM_KUNENA_INSTALL_UNINSTALL, COM_KUNENA_INSTALL_RESTORE

		Installation descriptions: COM_KUNENA_INSTALL_UPGRADE_DESC, COM_KUNENA_INSTALL_DOWNGRADE_DESC, COM_KUNENA_INSTALL_REINSTALL_DESC,
		COM_KUNENA_INSTALL_MIGRATE_DESC, COM_KUNENA_INSTALL_INSTALL_DESC, COM_KUNENA_INSTALL_UNINSTALL_DESC, COM_KUNENA_INSTALL_RESTORE_DESC

		Installation hints: COM_KUNENA_INSTALL_UPGRADE_HINT, COM_KUNENA_INSTALL_DOWNGRADE_HINT, COM_KUNENA_INSTALL_REINSTALL_HINT,
		COM_KUNENA_INSTALL_MIGRATE_HINT, COM_KUNENA_INSTALL_INSTALL_HINT, COM_KUNENA_INSTALL_UNINSTALL_HINT, COM_KUNENA_INSTALL_RESTORE_HINT

		Installation warnings: COM_KUNENA_INSTALL_UPGRADE_WARN, COM_KUNENA_INSTALL_DOWNGRADE_WARN,
		COM_KUNENA_INSTALL_MIGRATE_WARN, COM_KUNENA_INSTALL_UNINSTALL_WARN, COM_KUNENA_INSTALL_RESTORE_WARN

		 */

		static $search = ['#COMPONENT_OLD#', '#VERSION_OLD#', '#VERSION#'];
		$replace = [$version->component, $version->version, KunenaForum::version()];

		if (!$action)
		{
			$action = $version->action;
		}

		if ($type == 'warn' && ($action == 'INSTALL' || $action == 'REINSTALL'))
		{
			return '';
		}

		$str = '';

		if ($type == 'hint' || $type == 'warn')
		{
			$str .= '<strong class="k' . $type . '">' . Text::_('COM_KUNENA_INSTALL_' . $type) . '</strong> ';
		}

		if ($action && $type)
		{
			$type = '_' . $type;
		}

		$str .= str_replace($search, $replace, Text::_('COM_KUNENA_INSTALL_' . $action . $type));

		return $str;
	}

	/**
	 * @param   string  $oldprefix  old prefix
	 * @param   string  $oldtable   old table
	 * @param   string  $newtable   newtable
	 *
	 * @return array
	 *
	 * @throws \Kunena\Forum\Libraries\Install\KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function migrateTable(string $oldprefix, string $oldtable, string $newtable): array
	{
		$tables    = $this->listTables('kunena_');
		$oldtables = $this->listTables($oldprefix);

		if ($oldtable == $newtable || !isset($oldtables [$oldtable]) || isset($tables [$newtable]))
		{
			return false;
		}

		// Make identical copy from the table with new name
		$create = $this->db->getTableCreate($this->db->getPrefix() . $oldtable);
		$create = implode(' ', $create);

		$collation = $this->db->getCollation();

		if (!strstr($collation, 'utf8') && !strstr($collation, 'utf8mb4'))
		{
			$collation = 'utf8_general_ci';
		}

		if (strstr($collation, 'utf8mb4'))
		{
			$str = 'utf8mb4';
		}
		else
		{
			$str = 'utf8';
		}

		if (!$create)
		{
			return false;
		}

		$create = preg_replace('/(DEFAULT )?CHARACTER SET [\w\d]+/', '', $create);
		$create = preg_replace('/(DEFAULT )?CHARSET=[\w\d]+/', '', $create);

		if (strstr($collation, 'utf8mb4'))
		{
			$create .= ' ENGINE=InnoDB';
		}
		else
		{
			$create = preg_replace('/TYPE\s*=?/', 'ENGINE=', $create);
		}

		$create .= " DEFAULT CHARACTER SET {$str} COLLATE {$collation}";
		$query  = preg_replace('/' . $this->db->getPrefix() . $oldtable . '/', $this->db->getPrefix() . $newtable, $create);
		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$this->tables ['kunena_'] [$newtable] = $newtable;

		// And copy data into it
		$sql = $this->db->getQuery(true);
		$sql->insert($this->db->quoteName($this->db->getPrefix() . $newtable) . ' ' . $this->selectWithStripslashes($this->db->getPrefix() . $oldtable));
		$this->db->setQuery($sql);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		return ['name' => $oldtable, 'action' => 'migrate', 'sql' => $sql];
	}

	// TODO: move to migration

	/**
	 * @param   string  $table  table
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function selectWithStripslashes(string $table): string
	{
		$fields = $this->db->getTableColumns($table);
		$select = [];

		foreach ($fields as $field => $type)
		{
			$isString = preg_match('/text|char/', $type);
			$select[] = ($isString ? "REPLACE(REPLACE(REPLACE({$this->db->quoteName($field)}, {$this->db->Quote('\\\\')}, {$this->db->Quote('\\')}),{$this->db->Quote('\\\'')} ,{$this->db->Quote('\'')}),{$this->db->Quote('\"')} ,{$this->db->Quote('"')}) AS " : '') . $this->db->quoteName($field);
		}

		$select = implode(', ', $select);

		return "SELECT {$select} FROM {$table}";
	}

	/**
	 * @param   integer  $version      version
	 * @param   integer  $versiondate  version date
	 * @param   integer  $versionname  version name
	 * @param   string   $state        state
	 *
	 * @return  void
	 *
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function insertVersionData(int $version, int $versiondate, int $versionname, $state = ''): void
	{
		$query = $this->db->getQuery(true);

		// Insert columns.
		$columns = ['version', 'versiondate', 'installdate', 'versionname', 'build', 'state'];

		// Insert values.
		$values = [$this->db->quote($version), $this->db->quote($versiondate), $this->db->quote(Factory::getDate('now')->toSql()), $this->db->quote($versionname), $this->db->quote($version), $this->db->quote($state)];

		// Prepare the insert query.
		$query
			->insert($this->db->quoteName($this->db->getPrefix() . 'kunena_version'))
			->columns($this->db->quoteName($columns))
			->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @param   string  $state  state
	 *
	 * @return  void
	 *
	 * @throws  KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function insertVersion($state = 'beginInstall'): void
	{
		// Insert data from the new version
		$this->insertVersionData(KunenaForum::version(), KunenaForum::versionDate(), KunenaForum::versionName(), $state);
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getInstallError()
	{
		$status = $this->getState('status', []);

		foreach ($status as $cur)
		{
			$error = !$cur['success'];

			if ($error)
			{
				return $cur['task'] . ' ... ' . ($cur['success'] > 0 ? 'SUCCESS' : 'FAILED');
			}
		}

		return false;
	}

	// TODO: move to migration

	/**
	 * @param   bool  $stop     stop
	 * @param   int   $timeout  timeout
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function checkTimeout($stop = false, $timeout = 1): bool
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

		if ($time - $start < $timeout)
		{
			return false;
		}

		return true;
	}

	// TODO: move to migration

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function stepExtract(): void
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_kunena/archive';

		if (KunenaForum::isDev() || !is_file("{$path}/fileformat"))
		{
			// Git install
			$dir = JPATH_ADMINISTRATOR . '/components/com_kunena/media/kunena';

			if (is_dir($dir))
			{
				Folder::copy($dir, KUNENA_INSTALLER_MEDIAPATH, false, true);
			}

			$this->setStep($this->getStep() + 1);

			return;
		}

		$ext = file_get_contents("{$path}/fileformat");

		static $files = [
			['name' => 'com_kunena-admin', 'dest' => KUNENA_INSTALLER_ADMINPATH],
			['name' => 'com_kunena-site', 'dest' => KUNENA_INSTALLER_SITEPATH],
			['name' => 'com_kunena-media', 'dest' => KUNENA_INSTALLER_MEDIAPATH],
		];
		static $ignore = [
			KUNENA_INSTALLER_ADMINPATH => ['index.html', 'kunena.xml', 'kunena.j25.xml', 'kunena.php', 'api.php', 'archive', 'install', 'language'],
			KUNENA_INSTALLER_SITEPATH  => ['index.html', 'kunena.php', 'router.php', 'COPYRIGHT.php', 'template', 'language'],
		];
		$task = $this->getTask();

		// Extract archive files
		if (isset($files[$task]))
		{
			$file = $files[$task];

			if (is_file("{$path}/{$file['name']}{$ext}"))
			{
				$dest = $file['dest'];

				if (!empty($ignore[$dest]))
				{
					// Delete all files and folders (cleanup)
					$this->deleteFolder($dest, $ignore[$dest]);

					if ($dest == KUNENA_INSTALLER_SITEPATH)
					{
						$this->deleteFolder("$dest/template/aurelia", ['params.ini']);
					}
				}

				// Copy new files into folder
				$this->extract($path, $file['name'] . $ext, $dest, KunenaForum::isDev());
			}

			$this->setTask($task + 1);
		}
		else
		{
			if (\function_exists('apc_clear_cache'))
			{
				@apc_clear_cache('system');
			}

			// Force page reload to avoid MySQL timeouts after extracting
			$this->checkTimeout(true);

			if (!$this->getInstallError())
			{
				$this->setStep($this->getStep() + 1);
			}
		}
	}

	// TODO: move to migration

	/**
	 * @return  object
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTask(): object
	{
		return $this->getState('task', 0);
	}

	// TODO: move to migration

	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function deleteFolder(string $path, $ignore = []): void
	{
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}

	// TODO: move to migration

	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function deleteFiles(string $path, $ignore = []): void
	{
		$ignore = array_merge($ignore, ['.git', '.svn', 'CVS', '.DS_Store', '__MACOSX']);

		if (Folder::exists($path))
		{
			foreach (Folder::files($path, '.', false, true, $ignore) as $file)
			{
				if (File::exists($file))
				{
					File::delete($file);
				}
			}
		}
	}

	// TODO: move to migration

	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function deleteFolders(string $path, $ignore = []): void
	{
		$ignore = array_merge($ignore, ['.git', '.svn', 'CVS', '.DS_Store', '__MACOSX']);

		if (Folder::exists($path))
		{
			foreach (Folder::folders($path, '.', false, true, $ignore) as $folder)
			{
				if (Folder::exists($folder))
				{
					Folder::delete($folder);
				}
			}
		}
	}

	// TODO: move to migration

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function stepPlugins(): void
	{
		$this->enablePlugin('kunena', 'kunena');
		$this->enablePlugin('kunena', 'joomla');

		if (is_file(JPATH_ROOT . '/plugins/kunena/alphauserpoints/avatar.php'))
		{
			$this->uninstallPlugin('kunena', 'alphauserpoints');
		}

		if (\function_exists('apc_clear_cache'))
		{
			@apc_clear_cache('system');
		}

		if (!$this->getInstallError())
		{
			$this->setStep($this->getStep() + 1);
		}
	}

	// TODO: move to migration

	/**
	 * @param   string  $group    group
	 * @param   string  $element  element
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function enablePlugin(string $group, string $element): bool
	{
		$plugin = Table::getInstance('extension');

		if (!$plugin->load(['type' => 'plugin', 'folder' => $group, 'element' => $element]))
		{
			return false;
		}

		$plugin->enabled = 1;

		return $plugin->store();
	}

	/**
	 * @return  void
	 *
	 * @throws  KunenaInstallerException
	 * @throws  KunenaSchemaException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function stepDatabase(): void
	{
		$task = $this->getTask();

		switch ($task)
		{
			case 0:
				if ($this->migrateDatabase())
				{
					$this->setTask($task + 1);
				}
				break;
			case 1:
				if ($this->installDatabase())
				{
					$this->setTask($task + 1);
				}
				break;
			case 2:
				if ($this->upgradeDatabase())
				{
					$this->setTask($task + 1);
				}
				break;
			case 3:
				if ($this->installSampleData())
				{
					$this->setTask($task + 1);
				}
				break;
			case 4:
				if ($this->migrateCategoryImages())
				{
					$this->setTask($task + 1);
				}
				break;
			case 5:
				if ($this->migrateAvatars())
				{
					$this->setTask($task + 1);
				}
				break;
			case 6:
				if ($this->migrateAvatarGalleries())
				{
					$this->setTask($task + 1);
				}
				break;
			case 7:
				if ($this->migrateAttachments())
				{
					$this->setTask($task + 1);
				}
				break;
			default:
				if (!$this->getInstallError())
				{
					$this->setStep($this->getStep() + 1);
				}
		}
	}

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateDatabase(): bool
	{
		$version = $this->getVersion();

		if (!empty($version->prefix))
		{
			// Migrate all tables from old installation

			$app   = Factory::getApplication();
			$state = $app->getUserState('com_kunena.install.dbstate', null);

			// First run: find tables that potentially need migration
			if ($state === null)
			{
				$state = $this->listTables($version->prefix);
			}

			// Handle only first table in the list
			$oldtable = array_shift($state);

			if ($oldtable)
			{
				$newtable = preg_replace('/^' . $version->prefix . '/i', 'kunena_', $oldtable);
				$result   = $this->migrateTable($version->prefix, $oldtable, $newtable);

				if ($result)
				{
					$this->addStatus(ucfirst($result ['action']) . ' ' . $result ['name'], true);
				}

				// Save user state with remaining tables
				$app->setUserState('com_kunena.install.dbstate', $state);

				// Database migration continues
				return false;
			}
			else
			{
				// Reset user state
				$this->updateVersionState('installDatabase');
				$app->setUserState('com_kunena.install.dbstate', null);
			}
		}

		// Database migration complete
		return true;
	}

	// TODO: move to migration

	/**
	 * Get version
	 *
	 * @return  object
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getVersion(): object
	{
		return $this->getState('version', null);
	}

	/**
	 * @param   string  $state  state
	 *
	 * @return  void
	 *
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	protected function updateVersionState(string $state): void
	{
		// Insert data from the new version
		$this->db->setQuery("UPDATE " . $this->db->quoteName($this->db->getPrefix() . 'kunena_version') . " SET state = " . $this->db->Quote($state) . " ORDER BY id DESC LIMIT 1");

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}
	}

	// TODO: move to migration

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @throws  KunenaSchemaException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function installDatabase(): bool
	{
		static $schema = null;
		static $create = null;
		static $tables = null;

		if ($schema === null)
		{
			// Run only once: get table creation SQL and existing tables
			$schema = new KunenaModelSchema;
			$create = $schema->getCreateSQL();
			$tables = $this->listTables('kunena_', true);
		}

		$app   = Factory::getApplication();
		$state = $app->getUserState('com_kunena.install.dbstate', null);

		// First run: get all tables
		if ($state === null)
		{
			$state = array_keys($create);
		}

		// Handle only first table in the list
		$table = array_shift($state);

		if ($table)
		{
			if (!isset($tables[$table]))
			{
				$result = $schema->updateSchemaTable($table);

				if ($result)
				{
					$this->addStatus(Text::_('COM_KUNENA_INSTALL_CREATE') . ' ' . $result ['name'], $result ['success']);
				}
			}

			// Save user state with remaining tables
			$app->setUserState('com_kunena.install.dbstate', $state);

			// Database install continues
			return false;
		}
		else
		{
			// Reset user state
			$this->updateVersionState('upgradeDatabase');
			$app->setUserState('com_kunena.install.dbstate', null);
		}

		// Database install complete
		return true;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function upgradeDatabase(): bool
	{
		static $xml = null;

		// If there's no version installed, there's nothing to do
		$curversion = $this->getVersion();

		if (!$curversion->component)
		{
			return true;
		}

		if ($xml === null)
		{
			// Run only once: Get migration SQL from our XML file
			$xml = simplexml_load_file(KUNENA_INSTALLER_PATH . '/kunena.install.upgrade.xml');
		}

		if ($xml === false)
		{
			$this->addStatus(Text::_('COM_KUNENA_INSTALL_DB_UPGRADE_FAILED_XML'), false, '', 'upgrade');
		}

		$app   = Factory::getApplication();
		$state = $app->getUserState('com_kunena.install.dbstate', null);

		// First run: initialize state and migrate configuration
		if ($state === null)
		{
			$state = [];

			// Migrate configuration from FB <1.0.5, otherwise update it
			$this->migrateConfig();
		}

		// Allow queries to fail
		// $this->db->setDebug(false);

		foreach ($xml->upgrade[0] as $version)
		{
			// If we have already upgraded to this version, continue to the next one
			$vernum = (string) $version['version'];

			if (!empty($state[$vernum]))
			{
				continue;
			}

			// Update state
			$state[$vernum] = 1;

			if ($version['version'] == '@' . 'kunenaversion' . '@')
			{
				$git    = 1;
				$vernum = KunenaForum::version();
			}

			if (isset($git) || version_compare(strtolower($version['version']), strtolower($curversion->version), '>'))
			{
				foreach ($version as $action)
				{
					$result = $this->processUpgradeXMLNode($action);

					if ($result)
					{
						$this->addStatus($result ['action'] . ' ' . $result ['name'], $result ['success']);
					}
				}

				$this->addStatus(Text::sprintf('COM_KUNENA_INSTALL_VERSION_UPGRADED', $vernum), true, '', 'upgrade');

				// Save user state with remaining tables
				$app->setUserState('com_kunena.install.dbstate', $state);

				// Database install continues
				return false;
			}
		}

		// Reset user state
		$this->updateVersionState('InstallSampleData');
		$app->setUserState('com_kunena.install.dbstate', null);

		// Database install complete
		return true;
	}

	/**
	 * @return  void
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateConfig(): void
	{
		$config  = KunenaFactory::getConfig();
		$version = $this->getVersion();

		// Migrate configuration from FB < 1.0.5
		if (version_compare($version->version, '1.0.4', "<="))
		{
			$file = JPATH_ADMINISTRATOR . '/components/com_fireboard/fireboard_config.php';

			if (is_file($file))
			{
				require_once $file;

				if (isset($fbConfig))
				{
					$fbConfig = (array) $fbConfig;
					$config->bind($fbConfig);
					$config->id = 1;
				}
			}
		}

		// Migrate configuration from FB 1.0.5 and Kunena 1.0-1.7
		if (!$config->id && !empty($version->prefix))
		{
			$tables   = $this->listTables($version->prefix);
			$cfgtable = "{$version->prefix}config";

			if (isset($tables[$cfgtable]))
			{
				$this->db->setQuery("SELECT * FROM #__{$cfgtable}");
				$config->bind((array) $this->db->loadAssoc());
				$config->id = 1;
			}
		}

		$config->save();
	}

	/**
	 * @param   object  $action  action
	 *
	 * @return array|null
	 *
	 * @since   Kunena 6.0
	 */
	public function processUpgradeXMLNode(object $action): ?array
	{
		$result   = null;
		$nodeName = $action->getName();
		$mode     = strtolower((string) $action['mode']);
		$success  = false;

		switch ($nodeName)
		{
			case 'phpfile':
				$filename = $action['name'];
				$include  = KUNENA_INSTALLER_PATH . "/sql/updates/php/{$filename}.php";
				$function = 'kunena_' . strtr($filename, ['.' => '', '-' => '_']);

				if (is_file($include))
				{
					require $include;

					if (\is_callable($function))
					{
						$result = \call_user_func($function, $this);

						if (\is_array($result))
						{
							$success = $result['success'];
						}
						else
						{
							$success = true;
						}
					}
				}

				if (!$success && !$result)
				{
					$result = ['action' => Text::_('COM_KUNENA_INSTALL_INCLUDE_STATUS'), 'name' => $filename . '.php', 'success' => $success];
				}

				break;
			case 'query':
				$query = (string) $action;
				$this->db->setQuery($query);

				$success = true;

				try
				{
					$this->db->execute();
				}
				catch (Exception $e)
				{
					$success = false;
				}

				if ($action['mode'] == 'silenterror' || !$this->db->getAffectedRows() || $success)
				{
					$result = null;
				}
				else
				{
					$result = ['action' => 'SQL Query: ' . $query, 'name' => '', 'success' => $success];
				}
				break;
			default:
				$result = ['action' => 'fail', 'name' => $nodeName, 'success' => false];
		}

		return $result;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function installSampleData(): bool
	{
		require_once KUNENA_INSTALLER_PATH . '/sql/install/php/sampleData.php';

		if (installSampleData())
		{
			$this->addStatus(Text::_('COM_KUNENA_INSTALL_SAMPLEDATA'), true);
		}

		return true;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateCategoryImages(): bool
	{
		$action = $this->getAction();

		if ($action != 'migrate')
		{
			return true;
		}

		$srcpath = JPATH_ROOT . '/images/fbfiles/category_images';
		$dstpath = KUNENA_INSTALLER_MEDIAPATH . '/category_images';

		if (Folder::exists($srcpath))
		{
			if (!Folder::delete($dstpath) || !Folder::copy($srcpath, $dstpath))
			{
				$this->addStatus("Could not copy category images from $srcpath to $dstpath", true);
			}
			else
			{
				$this->addStatus(Text::_('COM_KUNENA_MIGRATE_CATEGORY_IMAGES'), true);
			}
		}

		return true;
	}

	// Helper function to migrate table
	// TODO: move to migration

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateAvatars(): bool
	{
		$stats = $this->getAvatarStatus();

		static $dirs = [
			'media/kunena/avatars',
			'images/fbfiles/avatars',
			'components/com_fireboard/avatars',
		];

		$query = "SELECT COUNT(*) FROM `#__kunena_users`
			WHERE userid>{$this->db->quote($stats->current)} AND avatar != '' AND avatar NOT LIKE 'gallery/%' AND avatar NOT LIKE 'users/%'";
		$this->db->setQuery($query);

		try
		{
			$count = $this->db->loadResult();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		if (!$stats->current && !$count)
		{
			return true;
		}

		$query = "SELECT userid, avatar FROM `#__kunena_users`
			WHERE userid>{$this->db->quote($stats->current)} AND avatar != '' AND avatar NOT LIKE 'gallery/%' AND avatar NOT LIKE 'users/%'";
		$query->setLimit(1023);
		$this->db->setQuery($query);

		try
		{
			$users = $this->db->loadObjectList();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		foreach ($users as $user)
		{
			$userid = $stats->current = $user->userid;
			$avatar = $user->avatar;
			$count--;

			$file = $newfile = '';

			foreach ($dirs as $dir)
			{
				if (!File::exists(JPATH_ROOT . "/$dir/$avatar"))
				{
					continue;
				}

				$file = JPATH_ROOT . "/$dir/$avatar";
				break;
			}

			if ($file)
			{
				$file = Path::clean($file, '/');

				// Make sure to copy only supported fileformats
				$match = preg_match('/\.(gif|jpg|jpeg|png)$/ui', $file, $matches);

				if ($match)
				{
					$ext = StringHelper::strtolower($matches[1]);

					// Use new format: users/avatar62.jpg
					$newfile  = "users/avatar{$userid}.{$ext}";
					$destpath = (KUNENA_INSTALLER_MEDIAPATH . "/avatars/{$newfile}");

					if (File::exists($destpath))
					{
						$success = true;
					}
					else
					{
						@chmod($file, 0644);
						$success = File::copy($file, $destpath);
					}

					if ($success)
					{
						$stats->migrated++;
					}
					else
					{
						$this->addStatus("User: {$userid}, Avatar copy failed: {$file} to {$destpath}", true);
						$stats->failed++;
					}
				}
				else
				{
					$this->addStatus("User: {$userid}, Avatar type not supported: {$file}", true);
					$stats->failed++;
					$success = true;
				}
			}
			else
			{
				// $this->addStatus ( "User: {$userid}, Avatar file was not found: {$avatar}", true );
				$stats->missing++;
				$success = true;
			}

			if ($success)
			{
				$query = "UPDATE `#__kunena_users` SET avatar={$this->db->quote($newfile)} WHERE userid={$this->db->quote($userid)}";
				$this->db->setQuery($query);

				try
				{
					$this->db->execute();
				}
				catch (Exception $e)
				{
					throw new KunenaInstallerException($e->getMessage(), $e->getCode());
				}
			}

			if ($this->checkTimeout())
			{
				break;
			}
		}

		$this->setAvatarStatus($stats);

		if ($count)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_AVATARS', $count), true, '', 'avatar');
		}
		else
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_AVATARS_DONE', $stats->migrated, $stats->missing, $stats->failed), true, '', 'avatar');
		}

		return !$count;
	}

	// TODO: move to migration

	/**
	 * @return \stdClass
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	protected function getAvatarStatus(): stdClass
	{
		$app            = Factory::getApplication();
		$stats          = new stdClass;
		$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		$stats          = $app->getUserState('com_kunena.install.avatars', $stats);

		return $stats;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateAvatarGalleries(): bool
	{
		$action = $this->getAction();

		if ($action != 'migrate')
		{
			return true;
		}

		$srcpath = JPATH_ROOT . '/images/fbfiles/avatars/gallery';
		$dstpath = KUNENA_INSTALLER_MEDIAPATH . '/avatars/gallery';

		if (Folder::exists($srcpath))
		{
			if (!Folder::delete($dstpath) || !Folder::copy($srcpath, $dstpath))
			{
				$this->addStatus("Could not copy avatar galleries from $srcpath to $dstpath", true);
			}
			else
			{
				$this->addStatus(Text::_('COM_KUNENA_MIGRATE_AVATAR_GALLERY'), true);
			}
		}

		return true;
	}

	// Also insert old version if not in the table

	/**
	 * @return  boolean
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function migrateAttachments(): bool
	{
		// Only perform this stage if we are upgrading from older version
		$version = $this->getVersion();

		if (version_compare($version->version, '1.7.0', ">"))
		{
			return true;
		}

		$stats = $this->getAttachmentStatus();

		static $dirs = [
			'images/fbfiles/attachments',
			'components/com_fireboard/uploaded',
			'media/kunena/attachments/legacy',
		];

		$query = $this->db->getQuery(true);
		$query->select('COUNT(*)')
			->from($this->db->quoteName('#__kunena_attachments'))
			->where('id > ' . $this->db->quote($stats->current) . ' AND hash IS NULL');
		$this->db->setQuery($query);

		try
		{
			$count = $this->db->loadResult();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		if (!$stats->current && !$count)
		{
			return true;
		}

		$destpath = KUNENA_INSTALLER_MEDIAPATH . '/attachments/legacy';

		if (!Folder::exists($destpath . '/images'))
		{
			if (!Folder::create($destpath . '/images'))
			{
				$this->addStatus("Could not create directory for legacy attachments in {$destpath}/images", true);

				return true;
			}
		}

		if (!Folder::exists($destpath . '/files'))
		{
			if (!Folder::create($destpath . '/files'))
			{
				$this->addStatus("Could not create directory for legacy attachments in {$destpath}/files", true);

				return true;
			}
		}

		$query = $this->db->getQuery(true);
		$query->select('COUNT(*)')
			->from($this->db->quoteName('#__kunena_attachments'))
			->where('id > ' . $this->db->quote($stats->current) . ' AND hash IS NULL');
		$query->setLimit(251);
		$this->db->setQuery($query);

		try
		{
			$attachments = $this->db->loadObjectList();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		foreach ($attachments as $attachment)
		{
			$stats->current = $attachment->id;
			$count--;

			if (preg_match('|/images$|', $attachment->folder))
			{
				$lastpath             = 'images';
				$attachment->filetype = 'image/' . strtolower($attachment->filetype);
			}
			else
			{
				if (preg_match('|/files$|', $attachment->folder))
				{
					$lastpath = 'files';
				}
				else
				{
					// Only process files in legacy locations, either in original folders or manually copied into /media/kunena/attachments/legacy
					continue;
				}
			}

			$file = '';

			if (File::exists(JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}"))
			{
				$file = JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}";
			}
			else
			{
				foreach ($dirs as $dir)
				{
					if (File::exists(JPATH_ROOT . "/{$dir}/{$lastpath}/{$attachment->filename}"))
					{
						$file = JPATH_ROOT . "/{$dir}/{$lastpath}/{$attachment->filename}";
						break;
					}
				}
			}

			$success  = false;
			$destfile = null;

			if ($file)
			{
				$file     = Path::clean($file, '/');
				$destfile = "{$destpath}/{$lastpath}/{$attachment->filename}";

				if (File::exists($destfile))
				{
					$success = true;
				}
				else
				{
					@chmod($file, 0644);
					$success = File::copy($file, $destfile);
				}

				if ($success)
				{
					$stats->migrated++;
				}
				else
				{
					$this->addStatus("Attachment copy failed: {$file} to {$destfile}", true);
					$stats->failed++;
				}
			}
			else
			{
				// $this->addStatus ( "Attachment file was not found: {$file}", true );
				$stats->missing++;
			}

			if ($success && $destfile)
			{
				clearstatcache();
				$stat  = stat($destfile);
				$size  = (int) $stat['size'];
				$hash  = md5_file($destfile);
				$query = $this->db->getQuery(true);
				$query->update($this->db->quoteName('#__kunena_attachments'))
					->set(
						'folder=\'media/kunena/attachments/legacy/' . $lastpath . '\', size=' .
						$this->db->quote($size) . ', hash=' . $this->db->quote($hash) . ', filetype=' . $this->db->quote($attachment->filetype)
					)
					->where('id=' . $this->db->quote($attachment->id));
				$this->db->setQuery($query);

				try
				{
					$this->db->execute();
				}
				catch (Exception $e)
				{
					throw new KunenaInstallerException($e->getMessage(), $e->getCode());
				}
			}

			if ($this->checkTimeout())
			{
				break;
			}
		}

		$this->setAttachmentStatus($stats);

		if ($count)
		{
			$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_ATTACHMENTS', $count), true, '', 'attach');
		}
		else
		{
			// Note: com_fireboard has been replaced by com_kunena during 1.0.8 upgrade, use it instead
			$query = "UPDATE `#__kunena_messages_text` SET message = REPLACE(REPLACE(message, '/images/fbfiles', '/media/kunena/attachments/legacy'), '/components/com_kunena/uploaded', '/media/kunena/attachments/legacy');";
			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (Exception $e)
			{
				throw new KunenaInstallerException($e->getMessage(), $e->getCode());
			}

			$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_ATTACHMENTS_DONE', $stats->migrated, $stats->missing, $stats->failed), true, '', 'attach');
		}

		return !$count;
	}

	/**
	 * @return \stdClass
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	protected function getAttachmentStatus(): stdClass
	{
		$app            = Factory::getApplication();
		$stats          = new stdClass;
		$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		$stats          = $app->getUserState('com_kunena.install.attachments', $stats);

		return $stats;
	}

	/**
	 * @return  void
	 *
	 * @throws  KunenaInstallerException
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function stepFinish(): void
	{
		KunenaForum::setup();

		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_kunena', JPATH_SITE) || $lang->load('com_kunena', KUNENA_INSTALLER_SITEPATH);

		$this->createMenu();

		// Fix broken category aliases (workaround for < 2.0-DEV12 bug)
		KunenaCategoryHelper::fixAliases();

		// Clean cache, just in case
		KunenaMenuHelper::cleanCache();

		$cache = Factory::getCache();
		$cache->clean('com_kunena');

		/*
		// Resync bbcode plugins
		$editor = KunenaBbcodeEditor::getInstance();
		$editor->initializeHMVC();*/

		// Delete the tmp install directory
		foreach (glob(KunenaPath::tmpdir() . '/install_*') as $dir)
		{
			if (is_dir($dir))
			{
				Folder::delete($dir);
			}
		}

		$version = '';
		$date    = '';
		$file    = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

		if (file_exists($file))
		{
			$manifest = simplexml_load_file($file);
			$version  = (string) $manifest->version;
			$date     = (string) $manifest->creationDate;
		}
		else
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('version')->from('#__kunena_version')->order('id');
			$query->setLimit(1);
			$db->setQuery($query);

			$version = $db->loadResult();
			$date    = (string) $version->versiondate;
		}

		$tmpfile = KunenaPath::tmpdir() . '/pkg_kunena_v' . $version . '_' . $date . '.zip';

		if (is_file($tmpfile))
		{
			File::delete(KunenaPath::tmpdir() . '/pkg_kunena_v' . $version . '_' . $date . '.zip');
		}
	}

	/**
	 * Create a Joomla menu for the main
	 * navigation tab and publish it in the Kunena module position kunena_menu.
	 * In addition it checks if there is a link to Kunena in any of the menus
	 * and if not, adds a forum link in the mainmenu.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function createMenu(): void
	{
		KunenaFactory::loadLanguage('com_kunena.install', 'admin');
		$menu = ['name' => Text::_('COM_KUNENA_MENU_ITEM_FORUM'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_FORUM_ALIAS'), 'forum'),
		         'link' => 'index.php?option=com_kunena&view=home', 'access' => 1, 'params' => ['catids' => 0], ];

		$this->buildMenu($menu);
		KunenaMenuHelper::cleanCache();
	}

	/**
	 * Build the Kunena menu
	 *
	 * @param   array  $menu  menu
	 *
	 * @return  boolean
	 *
	 * @throws KunenaInstallerException
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function buildMenu(array $menu)
	{
		$config = KunenaFactory::getConfig();

		$component_id = (int) ComponentHelper::getComponent('com_kunena')->id;

		KunenaFactory::loadLanguage('com_kunena.install', 'admin');
		$languages = LanguageHelper::getLanguages('default');
		$langCode = $languages[0]->lang_code;

		// First fix all broken menu items
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->update($db->quoteName('#__menu'))
			->set($db->quoteName('component_id') . ' = ' . $component_id)
			->where("link LIKE '%option=com_kunena%'")
			->andWhere('type = "component"');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$table = Table::getInstance('MenuType');
		$data  = [
			'menutype'    => 'kunenamenu',
			'title'       => Text::_('COM_KUNENA_MENU_TITLE'),
			'description' => Text::_('COM_KUNENA_MENU_TITLE_DESC'),
		];

		if (!$table->bind($data) || !$table->check())
		{
			// Menu already exists, do nothing
			return true;
		}

		if (!$table->store())
		{
			throw new KunenaInstallerException($table->getError());
		}

		$table = Table::getInstance('menu');
		$table->load(['menutype' => 'kunenamenu', 'link' => $menu ['link']]);
		$paramdata = ['menu-anchor_title'     => '',
		              'menu-anchor_css'       => '',
		              'menu_image'            => '',
		              'menu_text'             => 1,
		              'page_title'            => '',
		              'show_page_heading'     => 0,
		              'page_heading'          => '',
		              'pageclass_sfx'         => '',
		              'menu-meta_description' => '',
		              'robots'                => '',
		              'secure'                => 0, ];

		$gparams = new Registry($paramdata);

		$params = clone $gparams;
		$params->loadArray($menu['params']);
		$data = [
			'menutype'     => 'kunenamenu',
			'title'        => $menu ['name'],
			'alias'        => $menu ['alias'],
			'link'         => $menu ['link'],
			'type'         => 'component',
			'published'    => 1,
			'parentid'     => 1,
			'component_id' => $component_id,
			'access'       => $menu ['access'],
			'params'       => (string) $params,
			'home'         => 0,
			'language'     => '*',
			'client_id'    => 0,
		];
		$table->setLocation(1, 'last-child');

		if (!$table->bind($data) || !$table->check() || !$table->store())
		{
			$table->alias = 'kunena';

			if (!$table->check() || !$table->store())
			{
				// Menu already exists, do nothing
				return true;
			}
		}

		$parent      = $table;
		$defaultmenu = 0;

		/*foreach ($languages as $langCode => $language)
		{*/
			$lang = Factory::getApplication()->getLanguage();
			$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', $langCode);

			$submenu = [
				'index'     => ['name' => Text::_('COM_KUNENA_MENU_ITEM_INDEX'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_INDEX_ALIAS'), 'index'),
				                'link' => 'index.php?option=com_kunena&view=category&layout=list', 'access' => 1, 'default' => 'categories', 'params' => [], ],
				'recent'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_RECENT'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_RECENT_ALIAS'), 'recent'),
				                'link' => 'index.php?option=com_kunena&view=topics&mode=replies', 'access' => 1, 'default' => 'recent', 'params' => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''], ],
				'unread'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_UNREAD'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_UNREAD_ALIAS'), 'unread'),
				                'link' => 'index.php?option=com_kunena&view=topics&layout=unread', 'access' => 2, 'params' => [], ],
				'newtopic'  => ['name' => Text::_('COM_KUNENA_MENU_ITEM_NEWTOPIC'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_NEWTOPIC_ALIAS'), 'newtopic'),
				                'link' => 'index.php?option=com_kunena&view=topic&layout=create', 'access' => 2, 'params' => [], ],
				'noreplies' => ['name' => Text::_('COM_KUNENA_MENU_ITEM_NOREPLIES'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_NOREPLIES_ALIAS'), 'noreplies'),
				                'link' => 'index.php?option=com_kunena&view=topics&mode=noreplies', 'access' => 2, 'params' => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''], ],
				'mylatest'  => ['name' => Text::_('COM_KUNENA_MENU_ITEM_MYLATEST'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_MYLATEST_ALIAS'), 'mylatest'),
				                'link' => 'index.php?option=com_kunena&view=topics&layout=user&mode=default', 'access' => 2, 'default' => 'my', 'params' => ['topics_catselection' => '2', 'topics_categories' => '0', 'topics_time' => ''], ],
				'profile'   => ['name' => Text::_('COM_KUNENA_MENU_ITEM_PROFILE'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_PROFILE_ALIAS'), 'profile'),
				                'link' => 'index.php?option=com_kunena&view=user', 'access' => 2, 'params' => ['integration' => 1], ],
				'help'      => ['name' => Text::_('COM_KUNENA_MENU_ITEM_HELP'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_HELP_ALIAS'), 'help'),
				                'link' => 'index.php?option=com_kunena&view=misc', 'access' => 3, 'params' => ['body' => Text::_('COM_KUNENA_MENU_HELP_BODY'), 'body_format' => 'bbcode'], ],
				'search'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_SEARCH'), 'alias' => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_SEARCH_ALIAS'), 'search'),
				                'link' => 'index.php?option=com_kunena&view=search', 'access' => 1, 'params' => [], ],
			];

			foreach ($submenu as $menuitem)
			{
				$params = clone $gparams;
				$params->loadArray($menuitem['params']);
				$table = Table::getInstance('menu');
				$table->load(['menutype' => 'kunenamenu', 'link' => $menuitem ['link'], 'language' => $langCode]);
				$data = [
					'menutype'     => 'kunenamenu',
					'title'        => $menuitem ['name'],
					'alias'        => $menuitem ['alias'],
					'link'         => $menuitem ['link'],
					'type'         => 'component',
					'published'    => 1,
					'parentid'     => $parent->id,
					'component_id' => $component_id,
					'access'       => $menuitem ['access'],
					'params'       => (string) $params,
					'home'         => 0,
					'language'     => $langCode,
					'client_id'    => 0,
				];
				$table->setLocation($parent->id, 'last-child');

				if (!$table->bind($data) || !$table->check() || !$table->store())
				{
					throw new KunenaInstallerException($table->getError());
				}

				if (!$defaultmenu || (isset($menuitem ['default']) && $config->defaultPage == $menuitem ['default']))
				{
					$defaultmenu = $table->id;
				}
			}
		//}

		// Update forum menuitem to point into default page
		$parent->link .= "&defaultmenu={$defaultmenu}";

		if (!$parent->check() || !$parent->store())
		{
			throw new KunenaInstallerException($table->getError());
		}

		// Finally create alias
		$defaultmenu = AbstractMenu::getInstance('site')->getDefault();

		if (!$defaultmenu)
		{
			return true;
		}

		$table = Table::getInstance('menu');
		$table->load(['menutype' => $defaultmenu->menutype, 'type' => 'alias', 'title' => Text::_('COM_KUNENA_MENU_ITEM_FORUM'), 'language' => $langCode]);

		if (!$table->id)
		{
			$data = [
				'menutype' => $defaultmenu->menutype,
				'title' => Text::_('COM_KUNENA_MENU_ITEM_FORUM'),
				'alias' => 'kunena-' . Factory::getDate()->format('Y-m-d'),
				'note' => '',
				'link' => 'index.php?Itemid=' . $parent->id,
				'type' => 'alias',
				'published' => 0,
				'parent_id' => 1,
				'component_id' => 0,
				'checked_out' => null,
				'checked_out_time' => null,
				'browserNav' => 0,
				'access' => 1,
				'img' => '',
				'template_style_id' => 0,
				'params' => '{"aliasoptions":"' . (int) $parent->id . '","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
				'home' => 0,
				'language' => '*',
				'client_id' => 0,
			];

			$table->setLocation(1, 'last-child');
		}
		else
		{
			$data = [
				'alias'  => 'kunena-' . Factory::getDate()->format('Y-m-d'),
				'link'   => 'index.php?Itemid=' . $parent->id,
				'params' => '{"aliasoptions":"' . (int) $parent->id . '","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
			];
		}

		if (!$table->bind($data))
		{
			throw new KunenaInstallerException($table->getError());
		}

		if (!$table->check() || !$table->store())
		{
			// Menu already exists, do nothing
			return true;
		}
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function recountCategories(): bool
	{
		$app   = Factory::getApplication();
		$state = $app->getUserState('com_kunena.install.recount', null);

		// Only perform this stage if database needs recounting (upgrade from older version)
		$version = $this->getVersion();

		if (version_compare($version->version, '2.0.0-DEV', ">"))
		{
			return true;
		}

		if ($state === null)
		{
			// First run
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('MAX(id)')->from('#__kunena_messages');
			$db->setQuery($query);

			$state        = new stdClass;
			$state->step  = 0;
			$state->maxId = (int) $db->loadResult();
			$state->start = 0;
		}

		while (1)
		{
			$count = mt_rand(4500, 5500);

			switch ($state->step)
			{
				case 0:
					// Update topic statistics
					KunenaTopicHelper::recount(false, $state->start, $state->start + $count);
					$state->start += $count;
					$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_RECOUNT_TOPICS', min($state->start, $state->maxId), $state->maxId), true, '', 'recount');
					break;
				case 1:
					// Update usertopic statistics
					KunenaTopicUserHelper::recount(false, $state->start, $state->start + $count);
					$state->start += $count;
					$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_RECOUNT_USERTOPICS', min($state->start, $state->maxId), $state->maxId), true, '', 'recount');
					break;
				case 2:
					// Update user statistics
					KunenaUserHelper::recount();
					$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_RECOUNT_USER'), true, '', 'recount');
					break;
				case 3:
					// Update category statistics
					KunenaCategoryHelper::recount();
					$this->addStatus(Text::sprintf('COM_KUNENA_MIGRATE_RECOUNT_CATEGORY'), true, '', 'recount');
					break;
				default:
					$app->setUserState('com_kunena.install.recount', null);
					$this->addStatus(Text::_('COM_KUNENA_MIGRATE_RECOUNT_DONE'), true, '', 'recount');

					return true;
			}

			if (!$state->start || $state->start > $state->maxId)
			{
				$state->step++;
				$state->start = 0;
			}

			if ($this->checkTimeout(false, 14))
			{
				break;
			}
		}

		$app->setUserState('com_kunena.install.recount', $state);

		return false;
	}

	/**
	 * @return array|null
	 *
	 * @throws \Kunena\Forum\Libraries\Install\KunenaInstallerException
	 * @since   Kunena 6.0
	 */
	public function createVersionTable(): ?array
	{
		$tables = $this->listTables('kunena_');

		if (isset($tables ['kunena_version']))
		{
			// Nothing to migrate
			return false;
		}

		$collation = $this->db->getCollation();

		if (!strstr($collation, 'utf8') && !strstr($collation, 'utf8mb4'))
		{
			$collation = 'utf8_general_ci';
		}

		if (strstr($collation, 'utf8mb4'))
		{
			$str = 'utf8mb4';
		}
		else
		{
			$str = 'utf8';
		}

		$query = "CREATE TABLE IF NOT EXISTS `" . $this->db->getPrefix() . "kunena_version` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`version` varchar(20) NOT NULL,
		`versiondate` date NOT NULL,
		`installdate` date NOT NULL,
		`build` varchar(20) DEFAULT NULL,
		`versionname` varchar(40) DEFAULT NULL,
		`state` text DEFAULT NULL,
		PRIMARY KEY (`id`)
		) DEFAULT CHARACTER SET {$str} COLLATE {$collation};";
		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaInstallerException($e->getMessage(), $e->getCode());
		}

		$this->tables ['kunena_'] ['kunena_version'] = 'kunena_version';

		return ['action' => Text::_('COM_KUNENA_INSTALL_CREATE'), 'name' => 'kunena_version', 'sql' => $query];
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function recountThankyou(): bool
	{
		// Only perform this action if upgrading form previous version
		$version = $this->getVersion();

		if (version_compare($version->version, '2.0.0-BETA2', ">"))
		{
			return true;
		}

		// If the migration is from previous version thant 1.6.0 doesn't need to recount
		if (version_compare($version->version, '1.6.0', "<"))
		{
			return true;
		}

		KunenaMessageThankyouHelper::recount();

		return true;
	}
}

/**
 * Class KunenaInstallerException
 *
 * @since   Kunena 6.0
 */
class KunenaInstallerException extends Exception
{
}
