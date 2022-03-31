<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaInstallerException;

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
	protected $minimumJoomla = '4.0.6';

	/**
	 * List of supported versions. Newest version first!
	 *
	 * @var array
	 * @since Kunena 2.0
	 */
	protected $versions = [
		'PHP'     => [
			'8.1' => '8.1.0',
			'8.0' => '8.0.0',
			'7.4' => '7.4.1',
			'0'   => '7.4.1', // Preferred version
		],
		'MySQL'   => [
			'8.0' => '8.0.0',
			'5.7' => '5.7.8',
			'0'   => '5.7.8', // Preferred version
		],
		'Joomla!' => [
			'4.1' => '4.1.2',
			'4.0' => '4.0.6',
			'0'   => '4.1.2', // Preferred version
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

		$installed = $db->setQuery(
			$db->getQuery(true)
				->select('version')
				->from('#__kunena_version')->order('id DESC')
				->setLimit(1)
		)->loadResult();

		if (!$installed)
		{
			return true;
		}

		// Don't allow to upgrade before the version 5.1.0
		if (version_compare($installed, '5.1.0', '<'))
		{
			$app->enqueueMessage('You should not upgrade Kunena from the version ' . $installed . ', you can do the upgrade only since 5.1.0', 'notice');

			return false;
		}


		return true;
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

			// Remove kunena templates from K5.0
			$templatename = ['crypsis', 'crypsisb3', 'crypsisb4', 'crypsisb5', 'blue_eagle5', 'blue_eagle'];

			foreach ($templatename as $template)
			{
				$templatepath = JPATH_SITE . '/components/com_kunena/template/' . $template;

				if (is_dir($templatepath))
				{
					Folder::delete($templatepath);
				}
			}
		}
	}

	// Internal functions

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

		$db = Factory::getDbo();

		$table = $db->getPrefix() . 'kunena_version';

		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");
		$upgrade = 0;

		if ($db->loadResult() == $table)
		{
			$db->setQuery("SELECT version FROM #__kunena_version ORDER BY `id` DESC", 0, 1);
			$installed = $db->loadResult();

			if (!empty($installed))
			{
				if (version_compare($installed, '5.2.99', '<'))
				{
					$query = "ALTER TABLE `#__kunena_version` ADD `sampleData` boolean NOT NULL default '0' AFTER `versionname`;";
					$db->setQuery($query);

					$db->execute();

					$upgrade = 1;
				}
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
			$state       = '';
			$sampleData  = 0;

			if ($upgrade == 1)
			{
				$state = $installed;
				$sampleData = 1;
			}

			$query = $db->getQuery(true);

			$values = [
				$db->quote($version),
				$db->quote($build),
				$db->quote($date),
				$db->quote($versionname),
				$db->quote($sampleData),
				$db->quote($installdate),
				$db->quote($state),
			];

			$query->insert($db->quoteName('#__kunena_version'))
			->columns(
				[
					$db->quoteName('version'),
					$db->quoteName('build'),
					$db->quoteName('versiondate'),
					$db->quoteName('versionname'),
					$db->quoteName('sampleData'),
					$db->quoteName('installdate'),
					$db->quoteName('state'),
				]
			)
				->values(implode(', ', $values));
				$db->setQuery($query);

				$db->execute();
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

			if (!empty($version->versiondate))
			{
				$date = (string) $version->versiondate;
			}
			else
			{
				$date = new Date('now');
			}
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
			->set($db->quoteName('name') . '=' . $db->quote('Kunena 6.0 Update Site'))
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
		$db = Factory::getDbo();

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
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_REPLY_SUBJECT')),
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_BODY')),
				$db->quote(''),
				$db->quote(''),
				$db->quote('{"tags":["mail", "subject", "message", "messageUrl", "once"]}'),
			];

			$values2 = [
				$db->quote('com_kunena.replymoderator'),
				$db->quote('com_kunena'),
				$db->quote(''),
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_REPLYMODERATOR_SUBJECT')),
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_BODY')),
				$db->quote(''),
				$db->quote(''),
				$db->quote('{tags":["mail", "subject", "message", "messageUrl", "once"]}'),
			];

			$values3 = [
				$db->quote('com_kunena.report'),
				$db->quote('com_kunena'),
				$db->quote(''),
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_REPORT_SUBJECT')),
				$db->quote(Text::_('COM_KUNENA_SENDMAIL_BODY')),
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
}
