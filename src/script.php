<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaInstallerException;
use Joomla\Registry\Registry;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class Pkg_KunenaInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '4.0.0';

	/**
	 * List of supported versions. Newest version first!
	 *
	 * @var array
	 * @since Kunena 2.0
	 */
	protected $versions = [
		'PHP'     => [
			'8.0' => '8.0.0',
			'7.4' => '7.4.0',
			'7.3' => '7.3.5',
			'0'   => '7.3.5', // Preferred version
		],
		'MySQL'   => [
			'5.7' => '5.7.8',
			'5.6' => '5.6.5',
			'0'   => '5.6.5', // Preferred version
		],
		'Joomla!' => [
			'4.0' => '4.0.0',
			'0'    => '4.0.0', // Preferred version
		],
	];

	/**
	 * List of required PHP extensions.
	 *
	 * @var array
	 * @since Kunena 2.0
	 */
	protected $extensions = ['dom', 'gd', 'json', 'pcre', 'SimpleXML', 'fileinfo', 'mbstring'];

	/**
	 * @var  CMSApplication  Holds the application object
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
	 * @since Kunena 6.0
	 */
	public function __construct()
	{
		$this->app = Factory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since Kunena 6.0
	 */
	public function preflight($type, $parent)
	{
		$manifest = $parent->getParent()->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version))
		{
			return false;
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to install the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function install($parent)
	{
		$db    = Factory::getDbo();

		$query = $db->getQuery(true);

		// Check first if one of the template items is already in he database
		$query->select($db->quoteName(array('template_id')))
				->from($db->quoteName('#__mail_templates'))
				->where($db->quoteName('template_id') . " = " . $db->quote('com_kunena.reply'));
		$db->setQuery($query);

		$templateExist = $db->loadResult();

		if (!$templateExist)
		{
			$query = $db->getQuery(true);

			$values = [
				$db->quote('com_kunena.reply'),
				$db->quote('com_kunena'),
				$db->quote(''),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_REPLY_SUBJECT')),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_BODY')),
				$db->quote(''),
				$db->quote(''),
				$db->quote('{"tags":["mail", "subject", "message", "messageUrl", "once"]}'),
			];

			$values2 = [
				$db->quote('com_kunena.replymoderator'),
				$db->quote('com_kunena'),
				$db->quote(''),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_REPLYMODERATOR_SUBJECT')),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_BODY')),
				$db->quote(''),
				$db->quote(''),
				$db->quote('{tags":["mail", "subject", "message", "messageUrl", "once"]}'),
			];

			$values3 = [
				$db->quote('com_kunena.report'),
				$db->quote('com_kunena'),
				$db->quote(''),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_REPORT_SUBJECT')),
				$db->quote(text::_('COM_KUNENA_SENDMAIL_BODY')),
				$db->quote(''),
				$db->quote(''),
				$db->quote('{"tags":["mail", "subject", "message", "messageUrl", "once"]}'),
			];

			$query->insert($db->quoteName('#__mail_templates'))
				->columns(
					[
						$db->quoteName('template_id'),
						$db->quoteName('extension'),
						$db->quoteName('language'),
						$db->quoteName('subject'),
						$db->quoteName('body'),
						$db->quoteName('htmlbody'),
						$db->quoteName('attachments'),
						$db->quoteName('params'),
					]
				)
				->values(implode(', ', $values))
				->values(implode(', ', $values2))
				->values(implode(', ', $values3));
			$db->setQuery($query);

			$db->execute();
		}

		// Notice $parent->getParent() returns JInstaller object
		$parent->getParent()->setRedirectUrl('index.php?option=com_kunena');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '6.0.0', '<'))
		{
			// Remove integrated player classes
			$this->deleteFiles[]   = '/administrator/components/com_kunena/models/fields/player.php';
			$this->deleteFolders[] = '/components/com_kunena/helpers/player';

			// Remove old SQL files
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.1.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.2.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.3.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.4.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.1.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.2.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.3.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.4.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.4.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.5.0.sql';
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @throws KunenaInstallerException
	 * @since Kunena 6.0
	 */
	public function postflight($type, $parent)
	{
		$this->fixUpdateSite();

		// Clear Joomla system cache.
		$cache = Factory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache'))
		{
			@apc_clear_cache();
		}

		$db    = Factory::getDbo();

		if (strtolower($type) == 'update')
		{
			// Get installed Kunena version.
			$db->setQuery("SELECT version FROM #__kunena_version ORDER BY `id` DESC", 0, 1);
			$installed = $db->loadResult();

			if (version_compare($installed, '5.2.99', '<'))
			{
				$query = "ALTER TABLE `#__kunena_version` ADD `sampleData` TINYINT(4) NOT NULL default '0' AFTER `versionname`;";
				$db->setQuery($query);

				$db->execute();
			}
		}

		if (strtolower($type) == 'install' || strtolower($type) == 'discover_install')
		{
			$file = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

			$manifest    = simplexml_load_file($file);
			$version     = (string) $manifest->version;
			$build       = (string) $manifest->version;
			$date        = (string) $manifest->creationDate;
			$versionname = (string) $manifest->versionname;
			$installdate = Factory::getDate('now');

			$query = $db->getQuery(true);

			$values = [
				$db->quote($version),
				$db->quote($build),
				$db->quote($date),
				$db->quote($versionname),
				$db->quote($installdate),
				$db->quote(''),
			];

			$query->insert($db->quoteName('#__kunena_version'))
				->columns(
					[
						$db->quoteName('version'),
						$db->quoteName('build'),
						$db->quoteName('versiondate'),
						$db->quoteName('versionname'),
						$db->quoteName('installdate'),
						$db->quoteName('state'),
					]
				)
				->values(implode(', ', $values));
			$db->setQuery($query);

			$db->execute();
		}

		$this->addDashboardMenu('kunena', 'kunena');
		$app = Factory::getApplication();

		// Delete the tmp install directory
		foreach (glob($app->get('tmp_path') . '/install_*') as $dir)
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
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('version')->from('#__kunena_version')->order('id');
			$query->setLimit(1);
			$db->setQuery($query);

			$version = $db->loadResult();
			$date    = (string) $version->versiondate;
		}

		$tmpfile = $app->get('tmp_path') . '/pkg_kunena_v' . $version . '_' . $date . '.zip';

		if (is_file($tmpfile))
		{
			File::delete($app->get('tmp_path') . '/pkg_kunena_v' . $version . '_' . $date . '.zip');
		}

		$this->buildMenu();

		return true;
	}

	/**
	 * @param   string  $parent  parent
	 *
	 * @return void
	 *
	 * @since Kunena
	 */
	public function discover_install($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param   string  $uri  uri
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function makeRoute($uri)
	{
		return Route::_($uri, false);
	}

	/**
	 * @param   string  $group    group
	 * @param   string  $element  element
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function enablePlugin($group, $element)
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
	 * @param   string  $version  version
	 *
	 * @return boolean|integer
	 *
	 * @since version
	 */
	public function checkRequirements($version)
	{
		$db   = Factory::getDbo();
		$pass = $this->checkVersion('PHP', $this->getCleanPhpVersion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion());
		$pass &= $this->checkDbo($db->name, ['mysql', 'mysqli', 'pdomysql']);
		$pass &= $this->checkPhpExtensions($this->extensions);
		$pass &= $this->checkKunena($version);

		return $pass;
	}

	// Internal functions

	/**
	 *  On some hosting the PHP version given with the version of the packet in the distribution
	 *
	 * @return string
	 *
	 * @since Kunena
	 */
	protected function getCleanPhpVersion()
	{
		$version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;

		return $version;
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $version  version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version
	 */
	protected function checkVersion($name, $version)
	{
		$app = Factory::getApplication();

		$major = $minor = 0;

		foreach ($this->versions[$name] as $major => $minor)
		{
			if (!$major || version_compare($version, $major, '<'))
			{
				continue;
			}

			if (version_compare($version, $minor, '>='))
			{
				return true;
			}

			break;
		}

		if (!$major)
		{
			$minor = reset($this->versions[$name]);
		}

		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(
			sprintf(
				"%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.",
				$name,
				$version,
				$name,
				$minor,
				$name,
				$recommended
			),
			'notice'
		);

		return false;
	}

	/**
	 * @param   string  $name   name
	 * @param   array   $types  types
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version 2.0
	 */
	protected function checkDbo($name, $types)
	{
		$app = Factory::getApplication();

		if (in_array($name, $types))
		{
			return true;
		}

		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');

		return false;
	}

	/**
	 * @param   array  $extensions  extensions
	 *
	 * @return integer
	 *
	 * @throws Exception
	 * @since version 2.0
	 */
	protected function checkPhpExtensions($extensions)
	{
		$app = Factory::getApplication();

		$pass = 1;

		foreach ($extensions as $name)
		{
			if (!extension_loaded($name))
			{
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}

		return $pass;
	}

	/**
	 * @param   string  $version  version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version 2.0
	 */
	protected function checkKunena($version)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Do not install over Git repository (K1.6+).
		if (class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())
		{
			$app->enqueueMessage('Oops! You should not install Kunena over your Git repository!', 'notice');

			return false;
		}

		// Get installed Kunena version.
		$table = $db->getPrefix() . 'kunena_version';

		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");
		if ($db->loadResult() != $table)
		{
			return true;
		}

		$db->setQuery("SELECT version FROM {$table} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();
		if (!$installed)
		{
			return true;
		}

		// Don't allow to upgrade before he version 5.1.0
		if (version_compare($installed, '5.1.0', '<'))
		{
			$app->enqueueMessage('You should not updgrade Kunena from the version '.$installed.', you can do the upgrade only since 5.1.0', 'notice');

			return false;
		}

		return true;
	}

	/**
	 * @return void
	 * @throws Exception
	 * @since version
	 */
	protected function fixUpdateSite()
	{
		$db = Factory::getDbo();

		// Find all update sites.
		$query = $db->getQuery(true)
			->select($db->quoteName('update_site_id'))->from($db->quoteName('#__update_sites'))
			->where($db->quoteName('location') . ' LIKE ' . $db->quote('https://update.kunena.org/%'))
			->order($db->quoteName('update_site_id') . ' ASC');
		$db->setQuery($query);
		$list = (array) $db->loadColumn();

		$query = $db->getQuery(true)
			->set($db->quoteName('name') . '=' . $db->quote('Kunena 5.1 Update Site'))
			->set($db->quoteName('type') . '=' . $db->quote('collection'))
			->set($db->quoteName('location') . '=' . $db->quote('https://update.kunena.org/6.0/list.xml'))
			->set($db->quoteName('enabled') . '=1')
			->set($db->quoteName('last_check_timestamp') . '=0');

		if (!$list)
		{
			// Create new update site.
			$query->insert($db->quoteName('#__update_sites'));
			$id = $db->insertid();
		}
		else
		{
			// Update last Kunena update site with new information.
			$id = array_pop($list);
			$query->update($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . '=' . $id);
		}

		$db->setQuery($query);
		$db->execute();

		if ($list)
		{
			$ids = implode(',', $list);

			// Remove old update sites.
			$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
		}

		// Currently only pkg_kunena gets registered to update site, so remove everything else.
		$list[] = $id;
		$ids    = implode(',', $list);

		// Remove old updates.
		$query = $db->getQuery(true)->delete($db->quoteName('#__updates'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
		$db->setQuery($query);
		$db->execute();

		// Remove old update extension bindings.
		$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites_extensions'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Create the Kunena menus
	 * 
	 * @throws KunenaInstallerException
	 * @return boolean
	 * 
	 * @since   Kunena 6.0
	 */
	private function buildMenu()
	{
		$menu = ['name' => Text::_('COM_KUNENA_MENU_ITEM_FORUM'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_FORUM_ALIAS'), 'forum'),
				'link' => 'index.php?option=com_kunena&view=home', 'access' => 1, 'params' => ['catids' => 0], ];

		$component_id = (int) ComponentHelper::getComponent('com_kunena')->id;

		$languages = LanguageHelper::getLanguages('default');
		$langCode = $languages[0]->lang_code;

		// First fix all broken menu items
		$db    = Factory::getDbo();
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

		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', $langCode);

		$submenu = [
			'index'     => ['name' => Text::_('COM_KUNENA_MENU_ITEM_INDEX'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_INDEX_ALIAS'), 'index'),
				'link' => 'index.php?option=com_kunena&view=category&layout=list', 'access' => 1, 'default' => 'categories', 'params' => [], ],
			'recent'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_RECENT'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_RECENT_ALIAS'), 'recent'),
				'link' => 'index.php?option=com_kunena&view=topics&mode=replies', 'access' => 1, 'default' => 'recent', 'params' => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''], ],
			'unread'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_UNREAD'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_UNREAD_ALIAS'), 'unread'),
				'link' => 'index.php?option=com_kunena&view=topics&layout=unread', 'access' => 2, 'params' => [], ],
			'newtopic'  => ['name' => Text::_('COM_KUNENA_MENU_ITEM_NEWTOPIC'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_NEWTOPIC_ALIAS'), 'newtopic'),
				'link' => 'index.php?option=com_kunena&view=topic&layout=create', 'access' => 2, 'params' => [], ],
			'noreplies' => ['name' => Text::_('COM_KUNENA_MENU_ITEM_NOREPLIES'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_NOREPLIES_ALIAS'), 'noreplies'),
				'link' => 'index.php?option=com_kunena&view=topics&mode=noreplies', 'access' => 2, 'params' => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''], ],
			'mylatest'  => ['name' => Text::_('COM_KUNENA_MENU_ITEM_MYLATEST'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_MYLATEST_ALIAS'), 'mylatest'),
				'link' => 'index.php?option=com_kunena&view=topics&layout=user&mode=default', 'access' => 2, 'default' => 'my', 'params' => ['topics_catselection' => '2', 'topics_categories' => '0', 'topics_time' => ''], ],
			'profile'   => ['name' => Text::_('COM_KUNENA_MENU_ITEM_PROFILE'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_PROFILE_ALIAS'), 'profile'),
				'link' => 'index.php?option=com_kunena&view=user', 'access' => 2, 'params' => ['integration' => 1], ],
			'help'      => ['name' => Text::_('COM_KUNENA_MENU_ITEM_HELP'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_HELP_ALIAS'), 'help'),
				'link' => 'index.php?option=com_kunena&view=misc', 'access' => 3, 'params' => ['body' => Text::_('COM_KUNENA_MENU_HELP_BODY'), 'body_format' => 'bbcode'], ],
			'search'    => ['name' => Text::_('COM_KUNENA_MENU_ITEM_SEARCH'), 'alias' => ApplicationHelper::stringURLSafe(Text::_('COM_KUNENA_MENU_SEARCH_ALIAS'), 'search'),
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
				'alias'        => $menuitem ['alias'] . '-' . $langCode,
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

			/*if (!$defaultmenu || (isset($menuitem ['default']) && $config->defaultPage == $menuitem ['default']))
			{
				$defaultmenu = $table->id;
			}*/
		}

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
				'client_id' => 0
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
}
