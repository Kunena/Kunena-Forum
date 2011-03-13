<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Minimum version requirements
DEFINE('KUNENA_MIN_PHP', '5.2.3');
DEFINE('KUNENA_MIN_MYSQL', '5.0.4');
DEFINE ( 'KUNENA_MIN_JOOMLA', '1.5.22' );

jimport ( 'joomla.version' );
jimport ( 'joomla.application.component.model' );
jimport ( 'joomla.filesystem.folder' );
jimport ( 'joomla.filesystem.file' );
jimport ( 'joomla.filesystem.path' );
jimport ( 'joomla.filesystem.archive' );
jimport ( 'joomla.installer.installer' );

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

/**
 * Install Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelInstall extends JModel {
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	protected $_req = false;
	protected $_versionprefix = false;
	protected $_installed = array();
	protected $_versions = array();
	protected $_action = false;

	protected $_errormsg = null;

	protected $_versiontablearray = null;
	protected $_versionarray = null;

	public $steps = null;

	public function __construct() {
		parent::__construct ();
		$this->db = JFactory::getDBO ();

		ignore_user_abort ( true );
		$this->setState ( 'default_max_time', @ini_get ( 'max_execution_time' ) );
		@set_time_limit ( 300 );
		$this->setState ( 'max_time', @ini_get ( 'max_execution_time' ) );

		$this->_versiontablearray = array (array ('prefix' => 'kunena_', 'table' => 'kunena_version' ), array ('prefix' => 'fb_', 'table' => 'fb_version' ) );

		$this->_kVersions = array (
			array ('component' => null, 'prefix' => null, 'version' => null, 'date' => null ) );

		$this->_fbVersions = array (
			array ('component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.4', 'date' => '2007-12-23', 'table' => 'fb_sessions', 'column' => 'currvisit' ),
			array ('component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.3', 'date' => '2007-09-04', 'table' => 'fb_categories', 'column' => 'headerdesc' ),
			array ('component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.2', 'date' => '2007-08-03', 'table' => 'fb_users', 'column' => 'rank' ),
			array ('component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.1', 'date' => '2007-05-20', 'table' => 'fb_users', 'column' => 'uhits' ),
			array ('component' => 'FireBoard', 'prefix' => 'fb_', 'version' => '1.0.0', 'date' => '2007-04-15', 'table' => 'fb_messages' ),
			array ('component' => null, 'prefix' => null, 'version' => null, 'date' => null ) );

		$this->_sbVersions = array (
			array('component'=>'JoomlaBoard','prefix'=> 'sb_', 'version' =>'v1.0.5', 'date' => '0000-00-00', 'table' => 'sb_messages'),
			array ('component' => null, 'prefix' => null, 'version' => null, 'date' => null ) );

		$this->steps = array (
			array ('step' => '', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_INSTALL') ),
			array ('step' => 'Prepare', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_PREPARE') ),
			array ('step' => 'Extract', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_EXTRACT') ),
			array ('step' => 'Language', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_LANGUAGES') ),
			array ('step' => 'Plugins', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_PLUGINS') ),
			array ('step' => 'Database', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_DATABASE') ),
			array ('step' => 'Finish', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_FINISH') ),
			array ('step' => '', 'menu' => JText::_('COM_KUNENA_INSTALL_STEP_COMPLETE') ) );
	}

	/**
	 * Installer object destructor
	 *
	 * @access public
	 * @since 1.6
	 */
	public function __destruct() {
	}

	/**
	 * Installer cleanup after installation
	 *
	 * @access public
	 * @since 1.6
	 */
	public function cleanup() {
	}

	public function getModel() {
		return $this;
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	The default value to use if no state property exists by name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null, $default = null) {
		// if the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false) {
			$app = JFactory::getApplication ();
			$this->setState ( 'action', $step = $app->getUserState ( 'com_kunena.install.action', null ) );
			$this->setState ( 'step', $step = $app->getUserState ( 'com_kunena.install.step', 0 ) );
			$this->setState ( 'task', $task = $app->getUserState ( 'com_kunena.install.task', 0 ) );
			$this->setState ( 'version', $task = $app->getUserState ( 'com_kunena.install.version', null ) );
			if ($step == 0)
				$app->setUserState ( 'com_kunena.install.status', array () );
			$this->setState ( 'status', $app->getUserState ( 'com_kunena.install.status' ) );

			$this->__state_set = true;
		}

		$value = parent::getState ( $property );
		return (is_null ( $value ) ? $default : $value);
	}

	public function getStatus() {
		return $this->getState ( 'status', array() );
	}

	public function getAction() {
		return $this->getState ( 'action', null );
	}

	public function getStep() {
		return $this->getState ( 'step', 0 );
	}

	public function getTask() {
		return $this->getState ( 'task', 0 );
	}

	public function getVersion() {
		return $this->getState ( 'version', null );
	}

	public function setAction($action) {
		$this->setState ( 'action', $action );
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.action', $action );
	}

	public function setStep($step) {
		$this->setState ( 'step', ( int ) $step );
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.step', ( int ) $step );
		$this->setTask(0);
	}

	public function setTask($task) {
		$this->setState ( 'task', ( int ) $task );
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.task', ( int ) $task );
	}

	public function setVersion($version) {
		$this->setState ( 'version', $version );
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.version', $version );
	}

	public function addStatus($task, $result = false, $msg = '', $id = null) {
		$status = $this->getState ( 'status' );
		$step = $this->getStep();
		if ($id === null) {
			$status [] = array ('step' => $step, 'task'=>$task, 'success' => $result, 'msg' => $msg );
		} else {
			unset($status [$id]);
			$status [$id] = array ('step' => $step, 'task'=>$task, 'success' => $result, 'msg' => $msg );
		}
		$this->setState ( 'status', $status );
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.status', $status );
	}

	function getError() {
		$status = $this->getState ( 'status', array () );
		$error = 0;
		foreach ( $status as $cur ) {
			$error = ! $cur ['success'];
			if ($error)
				break;
		}
		return $error;
	}

	public function getSteps() {
		return $this->steps;
	}

	public function extract($path, $filename, $dest = null, $silent = false) {
		if (! $dest)
			$dest = $path;
		$file = $path . DS . $filename;

		$text = '';

		if (file_exists ( $file )) {
			$success = JArchive::extract ( $file, $dest );
			if (! $success) {
				$text .= JText::sprintf('COM_KUNENA_INSTALL_EXTRACT_FAILED', $file);

				$text .= $this->_getJoomlaArchiveError($file);
			}
		} else {
			$success = true;
			$text .= JText::sprintf('COM_KUNENA_INSTALL_EXTRACT_MISSING', $file);
		}
		if ($success && !$silent)
			$this->addStatus ( JText::sprintf('COM_KUNENA_INSTALL_EXTRACT_STATUS', $filename), $success, $text );

		return $success;
	}

	function installLanguage($tag, $name = '') {
		$exists = false;
		$success = true;
		$destinations = array(
			'site'=>JPATH_ROOT . '/components/com_kunena',
			'admin'=>JPATH_ADMINISTRATOR . '/components/com_kunena'
		);

		foreach ($destinations as $key=>$dest) {
			if ($success != true) continue;
			$installdir = "{$dest}/language/{$tag}";
			// If we are installing Kunena from archive, we need to unzip language file
			if (!KunenaForum::isSVN() && JFolder::exists(KPATH_ADMIN . '/archive')) {
				$path = JPATH_ADMINISTRATOR . '/components/com_kunena/archive';
				$file = "{$tag}.com_kunena-{$key}".file_get_contents("{$path}/fileformat");

				if (file_exists("$path/$file")) {
					$success = $this->extract ( $path, $file, $installdir, true );
				}
			}
			// Install language from dest/language/xx-XX
			if ($success == true && is_dir($installdir)) {
				$exists = true;
				$installer = new JInstaller ( );
				if ($installer->install ( $installdir )) {
					$success = true;
				} else {
					$success = -1;
				}
			}
		}
		if ($exists && $name) $this->addStatus ( JText::sprintf('COM_KUNENA_INSTALL_LANGUAGE', $name), $success);
		return $success;
	}

	function publishPlugin($folder, $name, $enable = 1) {
		$jversion = new JVersion ();
		if ($jversion->RELEASE == '1.5') {
			$query = "UPDATE #__plugins SET published='{$enable}' WHERE folder='{$folder}' AND element='{$name}'";
		} else {
			$query = "UPDATE #__extensions SET enabled='{$enable}' WHERE type='plugin' AND folder='{$folder}' AND element='{$name}'";
		}
		$this->db->setQuery ( $query );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		return true;
	}

	function installPlugin($path, $file, $name) {
		$success = false;
		$dest = JPATH_ROOT.'/tmp/kinstall_plugin';

		$query = "SELECT * FROM #__plugins WHERE element='$name'";
		$this->db->setQuery ( $query );
		$plugin = $this->db->loadObject ();
		if (is_object($plugin)) {
			$installer = new JInstaller ( );
			$installer->uninstall ( 'plugin', $plugin->id );
		}
		$this->extract ( $path, $file, $dest );
		$installer = new JInstaller ( );
		if ($installer->install ( $dest )) {
			// TODO: fix this when needed again
			$success = $this->publishPlugin('', $name);
		}
		JFolder::delete($dest);
		$this->addStatus ( JText::sprintf('COM_KUNENA_INSTALL_PLUGIN_STATUS', $name), $success);
	}

	function uninstallPlugin($folder, $name) {
		$jversion = new JVersion ();
		if ($jversion->RELEASE == '1.5') {
			$query = "SELECT id FROM #__plugins WHERE folder='{$folder}' AND element='{$name}'";
		} else {
			$query = "SELECT extension_id FROM #__extensions WHERE type='plugin' AND folder='{$folder}' AND element='{$name}'";
		}
		$this->db->setQuery ( $query );
		$pluginid = $this->db->loadResult ();
		if ($pluginid) {
			$installer = new JInstaller ( );
			$installer->uninstall ( 'plugin', $pluginid );
		}
	}

	function installSystemPlugin() {
		$src = KPATH_ADMIN . '/install/system';
		$dest = JPATH_ROOT.'/tmp/kinstall_plugin';
		JFolder::copy($src, $dest);
		$jversion = new JVersion ();
		// We need to have only one manifest which is named as kunena.xml
		if ($jversion->RELEASE == '1.5') {
			JFile::delete($dest.'/kunena.j16.xml');
		} else {
			JFile::delete($dest.'/kunena.xml');
			JFile::move($dest.'/kunena.j16.xml', $dest.'/kunena.xml');
		}
		$installer = new JInstaller ( );
		if ($installer->install ( $dest )) {
			$success = $this->publishPlugin('system', 'kunena');
		}
		JFolder::delete($dest);
		$this->addStatus ( JText::sprintf('COM_KUNENA_INSTALL_PLUGIN_STATUS', 'System - Kunena'), $success);
	}

	public function stepPrepare() {
		$results = array ();

		$this->setVersion(null);
		$this->setAvatarStatus();
		$this->setAttachmentStatus();
		$this->addStatus ( JText::_('COM_KUNENA_INSTALL_STEP_PREPARE'), true );
		$action = $this->getAction();
		if ($action == 'install' || $action == 'migrate') {
			// Let's start from clean database
			$this->deleteTables('kunena_');
			$this->deleteMenu();
		}
		$installed = $this->getDetectVersions();
		if ($action == 'migrate' && $installed['fb']->component) {
			$version = $installed['fb'];
			$results [] = $this->migrateTable ( $version->prefix, $version->prefix . 'version', 'kunena_version' );
		} else {
			$version = $installed['kunena'];
		}
		$this->setVersion($version);

		require_once KPATH_ADMIN.'/install/schema.php';
		$schema = new KunenaModelSchema();
		$results[] = $schema->updateSchemaTable('kunena_version');

		// Insert data from the old version, if it does not exist in the version table
		if ($version->id == 0 && $version->component) {
			$this->insertVersionData ( $version->version, $version->versiondate, $version->build, $version->versionname, null );
		}

		foreach ( $results as $i => $r )
			if ($r)
				$this->addStatus ( ucfirst($r ['action']) . ' ' . $r ['name'], true );
		$this->insertVersion ( 'migrateDatabase' );
		if (! $this->getError ())
			$this->setStep ( $this->getStep()+1 );
		$this->checkTimeout(true);
	}

	public function stepLanguage() {
		$lang = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages();
		foreach ($languages as $language) {
			$this->installLanguage($language['tag'], $language['name']);
		}
		if (! $this->getError ())
			$this->setStep($this->getStep()+1);
	}

	public function stepExtract() {
		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'archive';
		if (!is_file("{$path}/fileformat")) {
			$this->setStep($this->getStep()+1);
			return;
		}
		$ext = file_get_contents("{$path}/fileformat");
		static $files = array(
			array('name'=>'com_kunena-admin', 'dest'=>KPATH_ADMIN),
			array('name'=>'com_kunena-site', 'dest'=>KPATH_SITE),
			array('name'=>'com_kunena-media', 'dest'=>KPATH_MEDIA)
		);
		$task = $this->getTask();
		if (isset($files[$task])) {
			$file = $files[$task];
			if (file_exists ( $path . DS . $file['name'] . $ext )) {
				$this->extract ( $path, $file['name'] . $ext, $file['dest'], KunenaForum::isSVN() );
			}
			$this->setTask($task+1);
		} else {
			// Force page reload to avoid MySQL timeouts after extracting
			$this->checkTimeout(true);
			if (! $this->getError ())
				$this->setStep($this->getStep()+1);
		}
	}

	public function stepPlugins() {
		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'archive';

		$this->installSystemPlugin();

		if (! $this->getError ())
			$this->setStep ( $this->getStep()+1 );
	}

	public function stepDatabase() {
		kimport ('kunena.factory');
		$task = $this->getTask();
		switch ($task) {
			case 0:
				if ($this->migrateDatabase ())
					$this->setTask($task+1);
				break;
			case 1:
				if ($this->installDatabase ())
					$this->setTask($task+1);
				break;
			case 2:
				if ($this->upgradeDatabase ())
					$this->setTask($task+1);
				break;
			case 3:
				if ($this->installSampleData ())
					$this->setTask($task+1);
				break;
			case 4:
				if ($this->migrateCategoryImages ())
					$this->setTask($task+1);
				break;
			case 5:
				if ($this->migrateAvatars ())
					$this->setTask($task+1);
				break;
			case 6:
				if ($this->migrateAvatarGalleries ())
					$this->setTask($task+1);
				break;
			case 7:
				if ($this->migrateAttachments ())
					$this->setTask($task+1);
				break;
			default:
				if (! $this->getError ())
					$this->setStep ( $this->getStep()+1 );
		}
	}

	public function stepFinish() {
		kimport ('kunena.factory');
		$entryfiles = array(
			array(KPATH_ADMIN, 'api', 'php'),
			array(KPATH_ADMIN, 'admin.kunena', 'php'),
			array(KPATH_SITE, 'router', 'php'),
			array(KPATH_SITE, 'kunena', 'php'),
		);

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena',JPATH_SITE);

		$this->createMenu(false);

		foreach ($entryfiles as $fileparts) {
			list($path, $filename, $ext) = $fileparts;
			if (is_file("{$path}/{$filename}.new.{$ext}")) {
				$success = JFile::delete("{$path}/{$filename}.{$ext}");
				if (!$success) $this->addStatus ( JText::_('COM_KUNENA_INSTALL_DELETE_STATUS_FAIL')." {$filename}.{$ext}", false, '' );
				$success = JFile::move("{$path}/{$filename}.new.{$ext}", "{$path}/{$filename}.{$ext}");
				if (!$success) $this->addStatus ( JText::_('COM_KUNENA_INSTALL_RENAMING_FAIL')." {$filename}.new.{$ext}", false, '' );
			}
		}

		// Cleanup directory structure
		if (!KunenaForum::isSVN()) {
			if( JFolder::exists(KPATH_ADMIN . '/language') ) JFolder::delete(KPATH_ADMIN . '/language');
			if( JFolder::exists(KPATH_SITE . '/language') ) JFolder::delete(KPATH_SITE . '/language');
		}

		if (! $this->getError ()) {
			$this->updateVersionState ( '' );
			$this->addStatus ( JText::_('COM_KUNENA_INSTALL_SUCCESS'), true, '' );

			$this->setStep ( $this->getStep()+1 );
		}
	}

	public function migrateDatabase() {
		$version = $this->getVersion();
		if (! empty ( $version->prefix )) {
			// Migrate all tables from old installation

			$app = JFactory::getApplication ();
			$state = $app->getUserState ( 'com_kunena.install.dbstate', null );

			// First run: find tables that potentially need migration
			if ($state === null) {
				$state = $this->listTables ( $version->prefix );
			}

			// Handle only first table in the list
			$oldtable = array_shift($state);
			if ($oldtable) {
				$newtable = preg_replace ( '/^' . $version->prefix . '/i', 'kunena_', $oldtable );
				$result = $this->migrateTable ( $version->prefix, $oldtable, $newtable );
				if ($result) {
					$this->addStatus ( ucfirst($result ['action']) . ' ' . $result ['name'], true );
				}
				// Save user state with remaining tables
				$app->setUserState ( 'com_kunena.install.dbstate', $state );

				// Database migration continues
				return false;
			} else {
				// Reset user state
				$this->updateVersionState ( 'installDatabase' );
				$app->setUserState ( 'com_kunena.install.dbstate', null );
			}
		}
		// Database migration complete
		return true;
	}

	public function installDatabase() {
		static $schema = null;
		static $create = null;
		static $tables = null;
		if ($schema === null) {
			// Run only once: get table creation SQL and existing tables
			require_once KPATH_ADMIN.'/install/schema.php';
			$schema = new KunenaModelSchema();
			$create = $schema->getCreateSQL();
			$tables = $this->listTables ( 'kunena_', true );
		}

		$app = JFactory::getApplication ();
		$state = $app->getUserState ( 'com_kunena.install.dbstate', null );

		// First run: get all tables
		if ($state === null) {
			$state = array_keys($create);
		}

		// Handle only first table in the list
		$table = array_shift($state);
		if ($table) {
			$query = $create[$table];
			if (!isset($tables[$table])) {
				$result = $schema->updateSchemaTable($table);
				if ($result) {
					$this->addStatus ( ucfirst($result ['action']) . ' ' . $result ['name'], $result ['success'] );
				}
			}
			// Save user state with remaining tables
			$app->setUserState ( 'com_kunena.install.dbstate', $state );

			// Database install continues
			return false;
		} else {
			// Reset user state
			$this->updateVersionState ( 'upgradeDatabase' );
			$app->setUserState ( 'com_kunena.install.dbstate', null );
		}
		// Database install complete
		return true;
	}

	function migrateConfig() {
		kimport('kunena.factory');
		$config = KunenaFactory::getConfig();
		$version = $this->getVersion();
		if (version_compare ( $version->version, '1.0.4', "<=" ) ) {
			$file = JPATH_ADMINISTRATOR . '/components/com_fireboard/fireboard_config.php';
			if (is_file($file)) {
				require_once $file;
				$fbConfig = (array)$fbConfig;
				$keys = $config->GetClassVars ();
				foreach ($fbConfig as $key=>$value) {
					if (isset ( $keys[$key] )) {
						if (is_string ( $config->$key )) {
							$config->$key = $value;
						} else {
							$config->$key = (int)$value;
						}
					}
				}
			}
		}
		$config->remove ();
		$config->create ();
	}

	public function upgradeDatabase() {
		static $xml = null;

		// If there's no version installed, there's nothing to do
		$curversion = $this->getVersion();
		if (!$curversion->component) return true;

		if ($xml === null) {
			// Run only once: Get migration SQL from our XML file
			$xml = simplexml_load_file(KPATH_ADMIN.'/install/kunena.install.upgrade.xml');
		}

		$app = JFactory::getApplication ();
		$state = $app->getUserState ( 'com_kunena.install.dbstate', null );

		// First run: initialize state and migrate configuration
		if ($state === null) {
			$state = array();

			// Migrate configuration from FB <1.0.5, otherwise update it
			$this->migrateConfig();
		}

		// Allow queries to fail
		$this->db->debug(0);

		$results = array();
		foreach ($xml->upgrade[0] as $version) {
			// If we have already upgraded to this version, continue to the next one
			$vernum = (string) $version['version'];
			if (!empty($state[$vernum]))
				continue;

			// Update state
			$state[$vernum] = 1;

			if ($version['version'] == '@'.'kunenaversion'.'@') {
				$svn = 1;
				$vernum = KunenaForum::version();
			}
			if(isset($svn) ||
					($version['versiondate'] > $curversion->versiondate) ||
					(version_compare(strtolower($version['version']), strtolower($curversion->version), '>')) ||
					(version_compare(strtolower($version['version']), strtolower($curversion->version), '==') &&
					$version['build'] > $curversion->build)) {
				foreach ($version as $action) {
					$result = $this->processUpgradeXMLNode($action);
					if ($result) $this->addStatus ( $result ['action'] . ' ' . $result ['name'], $result ['success'] );
				}

				$this->addStatus ( JText::sprintf('COM_KUNENA_INSTALL_VERSION_UPGRADED',$vernum), true, '', 'upgrade' );

				// Save user state with remaining tables
				$app->setUserState ( 'com_kunena.install.dbstate', $state );

				// Database install continues
				return false;
			}
		}
		// Reset user state
		$this->updateVersionState ( 'InstallSampleData' );
		$app->setUserState ( 'com_kunena.install.dbstate', null );

		// Database install complete
		return true;
	}

	function processUpgradeXMLNode($action)
	{
		$result = null;
		$nodeName = $action->getName();
		$mode = strtolower((string) $action['mode']);
		$success = false;
		switch($nodeName) {
			case 'phpfile':
				$filename = $action['name'];
				$include = KPATH_ADMIN . "/install/upgrade/$filename.php";
				$function = 'kunena_'.strtr($filename, array('.'=>'', '-'=>'_'));
				if(file_exists($include)) {
					require( $include );
					if (is_callable($function)) {
						$result = call_user_func($function, $this);
						if (is_array($result)) $success = $result['success'];
						else $success = true;
					}
				}
				if (!$success && !$result) {
					$result = array('action'=>JText::_('COM_KUNENA_INSTALL_INCLUDE_STATUS'), 'name'=>$filename.'.php', 'success'=>$success);
				}
				break;
			case 'query':
				$query = (string)$action;
				$this->db->setQuery($query);
				$this->db->query();
				if (!$this->db->getErrorNum()) {
					$success = true;
				}
				if ($action['mode'] == 'silenterror' || !$this->db->getAffectedRows() || $success)
					$result = null;
				else
					$result = array('action'=>'SQL Query: '.$query, 'name'=>'', 'success'=>$success);
				break;
			default:
				$result = array('action'=>'fail', 'name'=>$nodeName, 'success'=>false);
		}
		return $result;
	}
/*
	public function upgradeDatabase() {
		$schema = new KunenaModelSchema ();
		$results = $schema->updateSchema ();
		foreach ( $results as $i => $r )
			if ($r)
				$this->addStatus ( $r ['action'] . ' ' . $r ['name'], true );
		$this->updateVersionState ( 'installSampleData' );
	}
*/

	public function installSampleData() {
		require_once ( KPATH_ADMIN.'/install/data/sampledata.php' );
		if (installSampleData ())
			$this->addStatus ( JText::_('COM_KUNENA_INSTALL_SAMPLEDATA'), true );
		return true;
	}

	protected function setAvatarStatus($stats = null) {
		if (!$stats) {
			$stats = new stdClass();
			$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		}
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.avatars', $stats );
	}

	protected function getAvatarStatus() {
		$app = JFactory::getApplication ();
		$stats = new stdClass();
		$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		$stats = $app->getUserState ( 'com_kunena.install.avatars', $stats );
		return $stats;
	}

	public function migrateAvatars() {
		$app = JFactory::getApplication ();
		$stats = $this->getAvatarStatus();

		static $dirs = array (
			'media/kunena/avatars',
			'images/fbfiles/avatars',
			'components/com_fireboard/avatars'
		);

		$query = "SELECT COUNT(*) FROM #__kunena_users
			WHERE userid>{$this->db->quote($stats->current)} AND avatar != '' AND avatar NOT LIKE 'gallery/%' AND avatar NOT LIKE 'users/%'";
		$this->db->setQuery ( $query );
		$count = $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		if (!$stats->current && !$count) return true;

		$query = "SELECT userid, avatar FROM #__kunena_users
			WHERE userid>{$this->db->quote($stats->current)} AND avatar != '' AND avatar NOT LIKE 'gallery/%' AND avatar NOT LIKE 'users/%'";
		$this->db->setQuery ( $query, 0, 1023 );
		$users = $this->db->loadObjectList ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		foreach ($users as $user) {
			$userid = $stats->current = $user->userid;
			$avatar = $user->avatar;
			$count--;

			$file = $newfile = '';
			foreach ($dirs as $dir) {
				if (!JFile::exists(JPATH_ROOT . "/$dir/$avatar")) continue;
				$file = JPATH_ROOT . "/$dir/$avatar";
				break;
			}
			$success = false;
			if ($file) {
				$file = JPath::clean($file, '/');
				// Make sure to copy only supported fileformats
				$match = preg_match('/\.(gif|jpg|jpeg|png)$/ui', $file, $matches);
				if ($match) {
					$ext = JString::strtolower($matches[1]);
					// Use new format: users/avatar62.jpg
					$newfile = "users/avatar{$userid}.{$ext}";
					$destpath = (KPATH_MEDIA ."/avatars/{$newfile}");
					if (JFile::exists($destpath)) {
						$success = true;
					} else {
						@chmod($file, 0644);
						$success = JFile::copy($file, $destpath);
					}
					if ($success) {
						$stats->migrated++;
					} else {
						$this->addStatus ( "User: {$userid}, Avatar copy failed: {$file} to {$destpath}", true );
						$stats->failed++;
					}
				} else {
					$this->addStatus ( "User: {$userid}, Avatar type not supported: {$file}", true );
					$stats->failed++;
					$success = true;
				}
			} else {
				// $this->addStatus ( "User: {$userid}, Avatar file was not found: {$avatar}", true );
				$stats->missing++;
				$success = true;
			}
			if ($success) {
				$query = "UPDATE #__kunena_users SET avatar={$this->db->quote($newfile)} WHERE userid={$this->db->quote($userid)}";
				$this->db->setQuery ( $query );
				$this->db->query ();
				if ($this->db->getErrorNum ())
					throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
			}
			if ($this->checkTimeout()) break;
		}
		$this->setAvatarStatus($stats);
		if ($count) {
			$this->addStatus ( JText::sprintf('COM_KUNENA_MIGRATE_AVATARS',$count), true, '', 'avatar' );
		} else {
			$this->addStatus ( JText::sprintf('COM_KUNENA_MIGRATE_AVATARS_DONE', $stats->migrated, $stats->missing, $stats->failed), true, '', 'avatar' );
		}
		return !$count;
	}

	public function migrateAvatarGalleries() {
		$action = $this->getAction();
		if ($action != 'migrate') return true;

		$srcpath = JPATH_ROOT.'/images/fbfiles/avatars/gallery';
		$dstpath = KPATH_MEDIA.'/avatars/gallery';
		if (JFolder::exists($srcpath)) {
			if (!JFolder::delete($dstpath) || !JFolder::copy($srcpath, $dstpath)) {
				$this->addStatus ( "Could not copy avatar galleries from $srcpath to $dstpath", true );
			} else {
				$this->addStatus ( JText::_('COM_KUNENA_MIGRATE_AVATAR_GALLERY'), true );
			}
		}
		return true;
	}

	public function migrateCategoryImages() {
		$action = $this->getAction();
		if ($action != 'migrate') return true;

		$srcpath = JPATH_ROOT.'/images/fbfiles/category_images';
		$dstpath = KPATH_MEDIA.'/category_images';
		if (JFolder::exists($srcpath)) {
			if (!JFolder::delete($dstpath) || !JFolder::copy($srcpath, $dstpath)) {
				$this->addStatus ( "Could not copy category images from $srcpath to $dstpath", true );
			} else {
				$this->addStatus ( JText::_('COM_KUNENA_MIGRATE_CATEGORY_IMAGES'), true );
			}
		}
		return true;
	}

	protected function setAttachmentStatus($stats = null) {
		if (!$stats) {
			$stats = new stdClass();
			$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		}
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunena.install.attachments', $stats );
	}

	protected function getAttachmentStatus() {
		$app = JFactory::getApplication ();
		$stats = new stdClass();
		$stats->current = $stats->migrated = $stats->failed = $stats->missing = 0;
		$stats = $app->getUserState ( 'com_kunena.install.attachments', $stats );
		return $stats;
	}

	public function migrateAttachments() {
		$app = JFactory::getApplication ();
		$stats = $this->getAttachmentStatus();

		static $dirs = array (
			'images/fbfiles/attachments',
			'components/com_fireboard/uploaded',
			'media/kunena/attachments/legacy',
			);

		$query = "SELECT COUNT(*) FROM #__kunena_attachments
			WHERE id>{$this->db->quote($stats->current)} AND hash IS NULL";
		$this->db->setQuery ( $query );
		$count = $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		if (!$stats->current && !$count) return true;

		$destpath = KPATH_MEDIA . '/attachments/legacy';
		if (!JFolder::exists($destpath.'/images')) {
			if (!JFolder::create($destpath.'/images')) {
				$this->addStatus ( "Could not create directory for legacy attachments in {$destpath}/images", true );
				return true;
			}
		}
		if (!JFolder::exists($destpath.'/files')) {
			if (!JFolder::create($destpath.'/files')) {
				$this->addStatus ( "Could not create directory for legacy attachments in {$destpath}/files", true );
				return true;
			}
		}

		$query = "SELECT * FROM #__kunena_attachments
			WHERE id>{$this->db->quote($stats->current)} AND hash IS NULL";
		$this->db->setQuery ( $query, 0, 251 );
		$attachments = $this->db->loadObjectList ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		foreach ($attachments as $attachment) {
			$stats->current = $attachment->id;
			$count--;

			if (preg_match('|/images$|', $attachment->folder)) {
				$lastpath = 'images';
				$attachment->filetype = 'image/'.strtolower($attachment->filetype);
			} else if (preg_match('|/files$|', $attachment->folder)) {
				$lastpath = 'files';
			} else {
				// Only process files in legacy locations, either in original folders or manually copied into /media/kunena/attachments/legacy
				continue;
			}

			$file = '';
			if (JFile::exists(JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}")) {
				$file = JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}";
			} else {
				foreach ($dirs as $dir) {
					if (JFile::exists(JPATH_ROOT . "/{$dir}/{$lastpath}/{$attachment->filename}")) {
						$file = JPATH_ROOT . "/{$dir}/{$lastpath}/{$attachment->filename}";
						break;
					}
				}
			}
			$success = false;
			if ($file) {
				$file = JPath::clean ( $file, '/' );
				$destfile = "{$destpath}/{$lastpath}/{$attachment->filename}";
				if (JFile::exists ( $destfile )) {
					$success = true;
				} else {
					@chmod ( $file, 0644 );
					$success = JFile::copy ( $file, $destfile );
				}
				if ($success) {
					$stats->migrated ++;
				} else {
					$this->addStatus ( "Attachment copy failed: {$file} to {$destfile}", true );
					$stats->failed ++;
				}
			} else {
				// $this->addStatus ( "Attachment file was not found: {$file}", true );
				$stats->missing++;
			}
			if ($success) {
				clearstatcache();
				$stat = stat($destfile);
				$size = (int)$stat['size'];
				$hash = md5_file ( $destfile );
				$query = "UPDATE #__kunena_attachments SET folder='media/kunena/attachments/legacy/{$lastpath}', size={$this->db->quote($size)}, hash={$this->db->quote($hash)}, filetype={$this->db->quote($attachment->filetype)}
					WHERE id={$this->db->quote($attachment->id)}";
				$this->db->setQuery ( $query );
				$this->db->query ();
				if ($this->db->getErrorNum ())
					throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
			}
			if ($this->checkTimeout()) break;
		}
		$this->setAttachmentStatus($stats);
		if ($count) {
			$this->addStatus ( JText::sprintf('COM_KUNENA_MIGRATE_ATTACHMENTS',$count), true, '', 'attach' );
		} else {
			// Note: com_fireboard has been replaced by com_kunena during 1.0.8 upgrade, use it instead
			$query = "UPDATE #__kunena_messages_text SET message = REPLACE(REPLACE(message, '/images/fbfiles', '/media/kunena/attachments/legacy'), '/components/com_kunena/uploaded', '/media/kunena/attachments/legacy');";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
			$this->addStatus ( JText::sprintf('COM_KUNENA_MIGRATE_ATTACHMENTS_DONE', $stats->migrated, $stats->missing, $stats->failed), true, '', 'attach' );
		}
		return !$count;
	}

	public function getRequirements() {
		if ($this->_req !== false) {
			return $this->_req;
		}

		$req = new StdClass ();
		$req->mysql = $this->db->getVersion ();
		$req->php = phpversion ();
		$req->joomla = JVERSION;
		$req->domdocument = 'DOMDocument';

		$req->fail = array ();
		if (version_compare ( $req->mysql, KUNENA_MIN_MYSQL, "<" ))
			$req->fail ['mysql'] = true;
		if (version_compare ( $req->php, KUNENA_MIN_PHP, "<" ))
			$req->fail ['php'] = true;
		if (version_compare ( $req->joomla, KUNENA_MIN_JOOMLA, "<" ))
			$req->fail ['joomla'] = true;
		if(!class_exists('DOMDocument')){
			$req->fail ['domdocument'] = true;
		}

		$this->_req = $req;
		return $this->_req;
	}

	public function getVersionPrefix() {
		if ($this->_versionprefix !== false) {
			return $this->_versionprefix;
		}

		$match = $this->detectTable ( $this->_versiontablearray );
		if (isset ( $match ['prefix'] ))
			$this->_versionprefix = $match ['prefix'];
		else
			$this->_versionprefix = null;

		return $this->_versionprefix;
	}

	public function getDetectVersions() {
		if (!empty($this->_versions)) {
			return $this->_versions;
		}
		$kunena = $this->getInstalledVersion('kunena_', $this->_kVersions);
		$fireboard = $this->getInstalledVersion('fb_', $this->_fbVersions);
		if (!empty($kunena->state)) {
			$this->_versions['failed'] = $kunena;
			$kunena = $this->getInstalledVersion('kunena_', $this->_kVersions, true);
			if (version_compare ( $kunena->version, '1.6.0-ALPHA', "<" ) ) $kunena->ignore = true;
		}
		if ($kunena->component && empty($kunena->ignore)) {
			$this->_versions['kunena'] = $kunena;
			$migrate = false;
		} else {
			$migrate = $this->isMigration($kunena, $fireboard);
		}
		if (!empty($fireboard->component) && $migrate) $this->_versions['fb'] = $fireboard;
		if (empty($kunena->component)) $this->_versions['kunena'] = $kunena;
		else if (!empty($fireboard->component)) {
			$uninstall = clone $fireboard;
			$uninstall->action = 'RESTORE';
			$this->_versions['uninstall'] = $uninstall;
		} else {
			$uninstall = clone $kunena;
			$uninstall->action = 'UNINSTALL';
			$this->_versions['uninstall'] = $uninstall;
		}
		foreach ($this->_versions as $version) {
			$version->label = $this->getActionText($version);
			$version->description = $this->getActionText($version, 'desc');
			$version->hint = $this->getActionText($version, 'hint');
			$version->warning = $this->getActionText($version, 'warn');
			$version->link = JURI::root(true).'/administrator/index.php?option=com_kunena&view=install&task='.strtolower($version->action).'&'.JUtility::getToken() .'=1';
		}
		if ($migrate) {
			$kunena->warning = $this->getActionText($fireboard, 'warn', 'upgrade');
		} else {
			$kunena->warning = '';
		}

		return $this->_versions;
	}

	public function isMigration($new, $old) {
		// If K1.6 not installed: migrate
		if (!$new->component || !$this->detectTable ( $new->prefix . 'messages' )) return true;
		// If old not installed: upgrade
		if (!$old->component || !$this->detectTable ( $old->prefix . 'messages' )) return false;
		// If K1.6 is installed and old is not Kunena: upgrade
		if ($old->component != 'Kunena') return false;
		// User is currently using K1.6: upgrade
		if (strtotime($new->installdate) > strtotime($old->installdate)) return false;
		// User is currently using K1.0/K1.5: migrate
		if (strtotime($new->installdate) < strtotime($old->installdate)) return true;
		// Both K1.5 and K1.6 were installed during the same day.. Not going to be easy choice..

		// Let's assume that this could be migration
		return true;
	}

	public function getInstalledVersion($prefix, $versionlist, $state = false) {
		if (!$state && isset($this->_installed[$prefix])) {
			return $this->_installed[$prefix];
		}

		if ($prefix === null) {
			$versionprefix = $this->getVersionPrefix ();
		} else if ($this->detectTable ( $prefix . 'version') ) {
			$versionprefix = $prefix;
		} else {
			$versionprefix = null;
		}

		if ($versionprefix) {
			// Version table exists, try to get installed version
			$state = $state ? " WHERE state=''" : "";
			$this->db->setQuery ( "SELECT * FROM " . $this->db->nameQuote ( $this->db->getPrefix () . $versionprefix . 'version' ) . $state . " ORDER BY `id` DESC", 0, 1 );
			$version = $this->db->loadObject ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

			if ($version) {
				$version->version = strtolower ( $version->version );
				$version->prefix = $versionprefix;
				if (version_compare ( $version->version, '1.0.5', ">" ))
					$version->component = 'Kunena';
				else
					$version->component = 'FireBoard';
				$version->version = strtoupper ( $version->version );

				// Version table may contain dummy version.. Ignore it
				if (! $version || version_compare ( $version->version, '0.1.0', "<" ))
					unset ( $version );
			}
		}

		if (!isset ( $version )) {
			// No version found -- try to detect version by searching some missing fields
			$match = $this->detectTable ( $versionlist );

			// Clean install
			if (empty ( $match ))
				return $this->_installed = null;

			// Create version object
			$version = new StdClass ();
			$version->id = 0;
			$version->component = $match ['component'];
			$version->version = strtoupper ( $match ['version'] );
			$version->versiondate = $match ['date'];
			$version->installdate = '';
			$version->build = '';
			$version->versionname = '';
			$version->prefix = $match ['prefix'];
		}
		$version->action = $this->getInstallAction($version);
		return $this->_installed[$prefix] = $version;
	}

	protected function insertVersion($state = 'beginInstall') {
		// Insert data from the new version
		$this->insertVersionData ( KunenaForum::version(), KunenaForum::versionDate(), KunenaForum::versionBuild(), KunenaForum::versionName(), $state );
	}

	protected function updateVersionState($state) {
		// Insert data from the new version
		$this->db->setQuery ( "UPDATE " . $this->db->nameQuote ( $this->db->getPrefix () . 'kunena_version' ) . " SET state = " . $this->db->Quote ( $state ) . " ORDER BY id DESC LIMIT 1" );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
	}

	function getActionText($version, $type='', $action=null) {
		static $search = array ('#COMPONENT_OLD#','#VERSION_OLD#','#BUILD_OLD#','#VERSION#','#BUILD#');
		$replace = array ($version->component, $version->version, $version->build, KunenaForum::version(), KunenaForum::versionBuild());
		if (!$action) $action = $version->action;
		$str = '';
		if ($type == 'hint' || $type == 'warn') {
			$str .= '<strong class="k'.$type.'">'.JText::_('COM_KUNENA_INSTALL_'.$type).'</strong> ';
		}
		if ($action && $type) $type = '_'.$type;
		$str .= str_replace($search, $replace, JText::_('COM_KUNENA_INSTALL_'.$action.$type));
		return $str;
	}

	public function getInstallAction($version = null) {
		if ($version->component === null)
			$this->_action = 'INSTALL';
		else if ($version->prefix != 'kunena_')
			$this->_action = 'MIGRATE';
		else if (version_compare ( strtolower(KunenaForum::version()), strtolower($version->version), '>' ))
			$this->_action = 'UPGRADE';
		else if (version_compare ( strtolower(KunenaForum::version()), strtolower($version->version), '<' ))
			$this->_action = 'DOWNGRADE';
		else if (KunenaForum::versionBuild() && KunenaForum::versionBuild() > $version->build)
			$this->_action = 'UP_BUILD';
		else if (KunenaForum::versionBuild() && KunenaForum::versionBuild() < $version->build)
			$this->_action = 'DOWN_BUILD';
		else
			$this->_action = 'REINSTALL';

		return $this->_action;
	}

	protected function detectTable($detectlist) {
		// Cache
		static $tables = array ();
		static $fields = array ();

		$found = 0;
		if (is_string($detectlist)) $detectlist = array(array('table'=>$detectlist));
		foreach ( $detectlist as $detect ) {
			// If no detection is needed, return current item
			if (! isset ( $detect ['table'] ))
				return $detect;

			$table = $this->db->getPrefix () . $detect ['table'];

			// Match if table exists
			if (! isset ( $tables [$table] )) // Not cached
{
				$this->db->setQuery ( "SHOW TABLES LIKE " . $this->db->quote ( $table ) );
				$result = $this->db->loadResult ();
				if ($this->db->getErrorNum ())
					throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
				$tables [$table] = $result;
			}
			if (! empty ( $tables [$table] ))
				$found = 1;

			// Match if column in a table exists
			if ($found && isset ( $detect ['column'] )) {
				if (! isset ( $fields [$table] )) // Not cached
				{
					$this->db->setQuery ( "SHOW COLUMNS FROM " . $this->db->nameQuote ( $table ) );
					$result = $this->db->loadObjectList ( 'Field' );
					if ($this->db->getErrorNum ())
						throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
					$fields [$table] = $result;
				}
				if (! isset ( $fields [$table] [$detect ['column']] ))
					$found = 0; // Sorry, no match
			}
			if ($found)
				return $detect;
		}
		return array ();
	}

	// helper function to migrate table
	protected function migrateTable($oldprefix, $oldtable, $newtable) {
		$tables = $this->listTables ( 'kunena_' );
		$oldtables = $this->listTables ( $oldprefix );
		if ($oldtable == $newtable || !isset ( $oldtables [$oldtable] ) || isset ( $tables [$newtable] ))
			return; // Nothing to migrate

		// Make identical copy from the table with new name
		$create = array_pop($this->db->getTableCreate($this->db->getPrefix () . $oldtable));
		$collation = $this->db->getCollation ();
		if (!strstr($collation, 'utf8')) $collation = 'utf8_general_ci';
		if (!$create) return;
		$create = preg_replace('/(DEFAULT )?CHARACTER SET [\w\d]+/', '', $create);
		$create = preg_replace('/(DEFAULT )?CHARSET=[\w\d]+/', '', $create);
		$create = preg_replace('/COLLATE [\w\d_]+/', '', $create);
		$create = preg_replace('/TYPE\s*=?/', 'ENGINE=', $create);
		$create .= " DEFAULT CHARACTER SET utf8 COLLATE {$collation}";
		$query = preg_replace('/'.$this->db->getPrefix () . $oldtable.'/', $this->db->getPrefix () . $newtable, $create);
		$this->db->setQuery ( $query );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		$this->tables ['kunena_'] [$newtable] = $newtable;

		// And copy data into it
		$sql = "INSERT INTO " . $this->db->nameQuote ( $this->db->getPrefix () . $newtable ) . ' ' . $this->selectWithStripslashes($this->db->getPrefix () . $oldtable );
		$this->db->setQuery ( $sql );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		return array ('name' => $oldtable, 'action' => 'migrate', 'sql' => $sql );
	}

	function selectWithStripslashes($table) {
		$fields = array_pop($this->db->getTableFields($table));
		$select = array();
		foreach ($fields as $field=>$type) {
			$isString = preg_match('/text|char/', $type);
			$select[] = ($isString ? "REPLACE(REPLACE(REPLACE({$this->db->nameQuote($field)}, {$this->db->Quote('\\\\')}, {$this->db->Quote('\\')}),{$this->db->Quote('\\\'')} ,{$this->db->Quote('\'')}),{$this->db->Quote('\"')} ,{$this->db->Quote('"')}) AS " : '') . $this->db->nameQuote($field);
		}
		$select = implode(', ', $select);
		return "SELECT {$select} FROM {$table}";
	}

	function createVersionTable()
	{
		$tables = $this->listTables ( 'kunena_' );
		if (isset ( $tables ['kunena_version'] ))
			return; // Nothing to migrate
		$collation = $this->db->getCollation ();
		if (!strstr($collation, 'utf8')) $collation = 'utf8_general_ci';
		$query = "CREATE TABLE IF NOT EXISTS `".$this->db->getPrefix()."kunena_version` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`version` varchar(20) NOT NULL,
		`versiondate` date NOT NULL,
		`installdate` date NOT NULL,
		`build` varchar(20) NOT NULL,
		`versionname` varchar(40) DEFAULT NULL,
		`state` varchar(32) NOT NULL,
		PRIMARY KEY (`id`)
		) DEFAULT CHARACTER SET utf8 COLLATE {$collation};";
		$this->db->setQuery($query);
		$this->db->query();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		$this->tables ['kunena_'] ['kunena_version'] = 'kunena_version';
		return array('action'=>'create', 'name'=>'kunena_version', 'sql'=>$query);
	}

	// also insert old version if not in the table
	protected function insertVersionData($version, $versiondate, $build, $versionname, $state = '') {
		$this->db->setQuery ( "INSERT INTO  `#__kunena_version`" . "SET `version` = " . $this->db->quote ( $version ) . "," . "`versiondate` = " . $this->db->quote ( $versiondate ) . "," . "`installdate` = CURDATE()," . "`build` = " . $this->db->quote ( $build ) . "," . "`versionname` = " . $this->db->quote ( $versionname ) . "," . "`state` = " . $this->db->quote ( $state ) );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
	}

	protected function listTables($prefix, $reload = false) {
		if (isset ( $this->tables [$prefix] ) && ! $reload) {
			return $this->tables [$prefix];
		}
		$this->db->setQuery ( "SHOW TABLES LIKE " . $this->db->quote ( $this->db->getPrefix () . $prefix . '%' ) );
		$list = $this->db->loadResultArray ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		$this->tables [$prefix] = array ();
		foreach ( $list as $table ) {
			$table = preg_replace ( '/^' . $this->db->getPrefix () . '/', '', $table );
			$this->tables [$prefix] [$table] = $table;
		}
		return $this->tables [$prefix];
	}

	function deleteTables($prefix) {
		$tables = $this->listTables($prefix);
		foreach ($tables as $table) {
			$this->db->setQuery ( "DROP TABLE IF EXISTS " . $this->db->nameQuote ( $this->db->getPrefix () . $table ) );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		}
		unset($this->tables [$prefix]);
	}

	/**
	 * Create a Joomla menu for the main
	 * navigation tab and publish it in the Kunena module position kunena_menu.
	 * In addition it checks if there is a link to Kunena in any of the menus
	 * and if not, adds a forum link in the mainmenu.
	 */
	function createMenu() {
		$menu = array('name'=>JText::_ ( 'COM_KUNENA_MENU_FORUM' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_FORUM_ALIAS' )),
			'link'=>'index.php?option=com_kunena&view=home', 'access'=>0, 'params'=>array('catids'=>0));
		$submenu = array(
			'index'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_INDEX' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_INDEX_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=category&layout=index', 'access'=>0, 'default'=>'categories', 'params'=>array()),
			'recent'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_RECENT' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_RECENT_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=topics&mode=replies', 'access'=>0, 'default'=>'recent', 'params'=>array('topics_catselection'=>1, 'topics_categories'=>0, 'topics_time'=>720)),
			'newtopic'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_NEWTOPIC' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_NEWTOPIC_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=topic&layout=create', 'access'=>1, 'params'=>array()),
			'noreplies'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_NOREPLIES' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_NOREPLIES_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=topics&mode=noreplies', 'access'=>1, 'params'=>array('topics_catselection'=>1, 'topics_categories'=>0, 'topics_time'=>-1)),
			'mylatest'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_MYLATEST' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_MYLATEST_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=topics&layout=user&mode=default', 'access'=>1, 'default'=>'my' , 'params'=>array('topics_catselection'=>1, 'topics_categories'=>0, 'topics_time'=>-1)),
			'profile'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_PROFILE' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_PROFILE_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=user', 'access'=>1, 'params'=>array('integration'=>1)),
			'rules'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_RULES' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_RULES_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=misc', 'access'=>0, 'params'=>array('body'=>JText::_ ( 'COM_KUNENA_MENU_MISC_DEFAULT_BODY' ), 'body_format'=>'text')),
			'help'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_HELP' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_HELP_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=misc', 'access'=>0, 'params'=>array('body'=>JText::_ ( 'COM_KUNENA_MENU_MISC_DEFAULT_BODY' ), 'body_format'=>'text')),
			'search'=>array('name'=>JText::_ ( 'COM_KUNENA_MENU_SEARCH' ), 'alias'=>JString::strtolower(JText::_ ( 'COM_KUNENA_MENU_SEARCH_ALIAS' )),
				'link'=>'index.php?option=com_kunena&view=search', 'access'=>0, 'params'=>array()),
		);

		kimport ('kunena.factory');
		$config = KunenaFactory::getConfig();
		if (!empty($config->rules_cid)) {
			$submenu['rules']['params']['body'] = "[article=full]{$config->rules_cid}[/article]";
			$submenu['rules']['params']['body_format'] = 'bbcode';
		}
		if (!empty($config->help_cid)) {
			$submenu['help']['params']['body'] = "[article=full]{$config->help_cid}[/article]";
			$submenu['help']['params']['body_format'] = 'bbcode';
		}
		$jversion = new JVersion ();
		if ($jversion->RELEASE == '1.5') {
			$this->createMenuJ15($menu, $submenu);
		} else {
			$this->createMenuJ16($menu, $submenu);
		}
	}

	function createMenuJ15($menu, $submenu) {
		jimport( 'joomla.utilities.string' );
		jimport( 'joomla.application.component.helper' );
		kimport('kunena.factory');

		$config = KunenaFactory::getConfig();

		$component_id = JComponentHelper::getComponent('com_kunena')->id;

		// First fix all broken menu items
		$query = "UPDATE #__menu SET componentid={$this->db->quote($component_id)} WHERE type = 'component' AND link LIKE '%option=com_kunena%'";
		$this->db->setQuery ( $query );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		// Find out if menu exists
		$query = "SELECT id FROM `#__menu_types` WHERE `menutype`='kunenamenu';";
		$this->db->setQuery ( $query );
		$moduleid = ( int ) $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		// Do not touch existing menu
		if ($moduleid) {
			return;
		}

		// Create new Joomla menu for Kunena
		if (! $moduleid) {
			// Create a menu type for the Kunena menu
			$query = "REPLACE INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES
							($moduleid, 'kunenamenu', {$this->db->Quote( JText::_ ( 'COM_KUNENA_MENU_TITLE' ))} , {$this->db->Quote(JText::_ ( 'COM_KUNENA_MENU_TITLE_DESC' ))} )";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

			// Now get the menu id again, we need it, in order to publish the menu module
			$query = "SELECT id FROM `#__menu_types` WHERE `menutype`='kunenamenu';";
			$this->db->setQuery ( $query );
			$moduleid = ( int ) $this->db->loadResult ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		}

		// Forum
		$query = "SELECT id FROM `#__menu` WHERE `link`={$this->db->quote($menu['link'])} AND `menutype`='kunenamenu';";
		$this->db->setQuery ( $query );
		$parentid = ( int ) $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		if (! $parentid) {
			$params = new JParameter('');
			$params->bind($menu['params']);
			$query = "REPLACE INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							($parentid, 'kunenamenu', {$this->db->quote($menu['name'])}, {$this->db->quote($menu['alias'])}, {$this->db->quote($menu['link'])}, 'component', 1, 0, $component_id, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, {$menu['access']}, 0, {$this->db->quote($params->toString('INI'))}, 0, 0, 0);";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
			$parentid = ( int ) $this->_db->insertId ();
		}

		// Submenu (shown in Kunena)
		$defaultmenu = 0;
		$ordering = 0;
		foreach ($submenu as $menuitem) {
			$ordering++;
//			$query = "SELECT id FROM `#__menu` WHERE `link`={$this->db->quote($menuitem['link'])} AND `menutype`='kunenamenu';";
//			$this->db->setQuery ( $query );
//			$id = ( int ) $this->db->loadResult ();
//			if ($this->db->getErrorNum ())
//				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
			$id = 0;
			if (! $id) {
				$params = new JParameter('');
				$params->bind($menuitem['params']);
				$query = "REPLACE INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
								($id, 'kunenamenu', {$this->db->quote($menuitem['name'])}, {$this->db->quote($menuitem['alias'])}, {$this->db->quote($menuitem['link'])},'component', 1, $parentid, $component_id, 1, $ordering, 0, '0000-00-00 00:00:00', 0, 0, {$menuitem['access']}, 0, {$this->db->quote($params->toString('INI'))}, 0, 0, 0);";
				$this->db->setQuery ( $query );
				$this->db->query ();
				if ($this->db->getErrorNum ())
					throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
				$id = ( int ) $this->_db->insertId ();
				if (!$defaultmenu || (isset($menuitem['default']) && $config->fbdefaultpage == $menuitem['default'])) {
					$defaultmenu = $id;
				}
			}
		}
		if ($defaultmenu) {
			$query = "UPDATE `#__menu` SET `link`={$this->db->quote($menu['link']."&defaultmenu=$defaultmenu")} WHERE id={$parentid}";
			$this->db->setQuery ( $query );
			$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		}

		$query = "SELECT id FROM `#__modules` WHERE `position`='kunena_menu';";
		$this->db->setQuery ( $query );
		$moduleid = ( int ) $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		// Check if it exists, if not create it
		if (! $moduleid) {
			// Create a module for the Kunena menu
			$query = "REPLACE INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
					($moduleid, {$this->db->quote(JText::_ ( 'COM_KUNENA_MENU_TITLE' ))}, '', 0, 'kunena_menu', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=kunenamenu\nmenu_style=list\nstartLevel=1\nendLevel=2\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

			// Now get the module id again, we need it, in order to publish the menu module
			$query = "SELECT id FROM `#__modules` WHERE `position`='kunena_menu';";
			$this->db->setQuery ( $query );
			$moduleid = ( int ) $this->db->loadResult ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

			// Now publish the module
			$query = "REPLACE INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ($moduleid, 0);";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		}

		// Finally add forum menu link to default menu
		$jmenu = JMenu::getInstance('site');
		$dmenu = $jmenu->getDefault();
		$query = "SELECT id, name, type, link, published FROM `#__menu` WHERE `alias` IN ('forum', 'kunenaforum', {$this->db->quote(JText::_ ( 'COM_KUNENA_MENU_FORUM_ALIAS' ))}) AND `menutype`={$this->db->quote($dmenu->menutype)}";
		$this->db->setQuery ( $query, 0, 1 );
		$menualias = $this->db->loadObject ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		// We do not want to replace users own menu items (just alias or deprecated link to Kunena)
		if (!$menualias || $menualias->type == 'menulink' || $menualias->link == 'index.php?option=com_kunena') {
			$id = $menualias ? intval($menualias->id) : 0;
			// Keep state (default=unpublished) and name (default=Forum)
			$published = $menualias ? intval($menualias->published) : 0;
			$name = $menualias ? $menualias->name : $menu['name'];
			$query = "REPLACE INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
						($id, {$this->db->quote($dmenu->menutype)}, {$this->db->quote($name)}, 'kunenaforum', 'index.php?Itemid={$parentid}', 'menulink', {$published}, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, {$menu['access']}, 0, 'menu_item=$parentid{$menu['params']}\r\n\r\n', 0, 0, 0);";
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum ())
				throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		}
		require_once (JPATH_ADMINISTRATOR . '/components/com_menus/helpers/helper.php');
		MenusHelper::cleanCache ();
	}

	function createMenuJ16($menu, $submenu) {
		jimport ( 'joomla.utilities.string' );
		jimport ( 'joomla.application.component.helper' );
		kimport('kunena.factory');

		$config = KunenaFactory::getConfig ();

		$component_id = JComponentHelper::getComponent ( 'com_kunena' )->id;

		// First fix all broken menu items
		$query = "UPDATE #__menu SET component_id={$this->db->quote($component_id)} WHERE type = 'component' AND link LIKE '%option=com_kunena%'";
		$this->db->setQuery ( $query );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		$table = JTable::getInstance ( 'menutype' );
		$data = array (
			'menutype' => 'kunenamenu',
			'title' => JText::_ ( 'COM_KUNENA_MENU_TITLE' ),
			'description' => JText::_ ( 'COM_KUNENA_MENU_TITLE_DESC' )
		);
		if (! $table->bind ( $data ) || ! $table->check ()) {
			// Menu already exists, do nothing
			return true;
		}
		if (! $table->store ()) {
			throw new KunenaInstallerException ( $table->getError () );
		}

		$table = JTable::getInstance ( 'menu' );
		$table->load(array('menutype'=>'kunenamenu', 'link'=>$menu ['link']));
		$params = '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}';
		// FIXME: Joomla 1.6: add menu params for current item, too
		$data = array (
			'menutype' => 'kunenamenu',
			'title' => $menu ['name'],
			'alias' => $menu ['alias'],
			'link' => $menu ['link'],
			'type' => 'component',
			'published' => 1,
			'parent_id' => 1,
			'component_id' => $component_id,
			'access' => $menu ['access'] + 1,
			'params' => $params,
			'home' => 0,
			'language' => '*',
			'client_id' => 0
		);
		if (! $table->setLocation ( 1, 'last-child' ) || ! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) {
			throw new KunenaInstallerException ( $table->getError () );
		}
		$parent = $table;
		$defaultmenu = 0;
		foreach ( $submenu as $menuitem ) {
			$table = JTable::getInstance ( 'menu' );
			$table->load(array('menutype'=>'kunenamenu', 'link'=>$menuitem ['link']));
			$data = array (
				'menutype' => 'kunenamenu',
				'title' => $menuitem ['name'],
				'alias' => $menuitem ['alias'],
				'link' => $menuitem ['link'],
				'type' => 'component',
				'published' => 1,
				'parent_id' => 1,
				'component_id' => $component_id,
				'access' => $menu ['access'] + 1,
				'params' => $params,
				'home' => 0,
				'language' => '*',
				'client_id' => 0
			);

			if (! $table->setLocation ( $parent->id, 'last-child' ) || ! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) {
				throw new KunenaInstallerException ( $table->getError () );
			}
			if (! $defaultmenu || (isset ( $menuitem ['default'] ) && $config->fbdefaultpage == $menuitem ['default'])) {
				$defaultmenu = $table->id;
			}
		}

		// Update forum menuitem to point into default page
		$parent->link .= "&defaultmenu={$defaultmenu}";
		if (! $parent->check () || ! $parent->store ()) {
			throw new KunenaInstallerException ( $table->getError () );
		}

		$module = JTable::getInstance ( 'module' );
		$data = array (
			'title' => JText::_ ( 'COM_KUNENA_MENU_TITLE' ),
			'ordering' => 1,
			'position' => 'kunena_menu',
			'published' => 1,
			'module' => 'mod_menu',
			'access' => 1,
			'showtitle' => 0,
			'params' => '{"menutype":"kunenamenu","startLevel":"2","endLevel":"3","showAllChildren":"0","tag_id":"","class_sfx":"","window_open":"","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid"}',
			'client_id' => 0,
			'language' => '*' );
		if (! $module->bind ( $data ) || ! $module->check ()) {
			// Menu already exists, do nothing
			return true;
		}
		if (! $module->store ()) {
			throw new KunenaInstallerException ( $module->getError () );
		}
		$moduleid = $module->id;

		// Now publish the module
		$query = "REPLACE INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ($moduleid, 0);";
		$this->db->setQuery ( $query );
		$this->db->query ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );

		// Finally create alias
		// TODO: contains workaround for J1.6.1 bug:
		$defaultmenu = JMenu::getInstance('site')->getDefault('workaround');
		if (!$defaultmenu) return true;
		$table = JTable::getInstance ( 'menu' );
		$table->load(array('menutype'=>$defaultmenu->menutype, 'type'=>'alias', 'title'=>JText::_ ( 'COM_KUNENA_MENU_FORUM' )));
		if (!$table->id) {
			$data = array (
				'menutype' => $defaultmenu->menutype,
				'title' => JText::_ ( 'COM_KUNENA_MENU_FORUM' ),
				'link' => 'index.php?Itemid='.$parent->id,
				'type' => 'alias',
				'published' => 0,
				'parent_id' => 1,
				'component_id' => 0,
				'access' => 1,
				'params' => '{"aliasoptions":"'.(int)$parent->id.'","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
				'home' => 0,
				'language' => '*',
				'client_id' => 0
			);
			if (! $table->setLocation ( 1, 'last-child' )) {
				throw new KunenaInstallerException ( $table->getError () );
			}
		} else {
			$data = array (
				'link' => 'index.php?Itemid='.$parent->id,
				'params' => '{"aliasoptions":"'.(int)$parent->id.'","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
			);
		}
		if (! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) {
			throw new KunenaInstallerException ( $table->getError () );
		}
	}

	function deleteMenu() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE == '1.5') {
			$this->DeleteMenuJ15();
		} else {
			$this->DeleteMenuJ16();
		}
	}

	function deleteMenuJ16() {
		$table = JTable::getInstance ( 'menutype' );
		$table->load(array('menutype'=>'kunenamenu'));
		if ($table->id) {
			$success = $table->delete();
			if (!$success) {
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}
		}
	}

	function deleteMenuJ15() {
		$query = "SELECT id FROM `#__menu_types` WHERE `menutype`='kunenamenu';";
		$this->db->setQuery ( $query );
		$menuid = $this->db->loadResult ();
		if ($this->db->getErrorNum ())
			throw new KunenaInstallerException ( $this->db->getErrorMsg (), $this->db->getErrorNum () );
		if (!$menuid) return;

		require_once (JPATH_ADMINISTRATOR . '/components/com_menus/helpers/helper.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_menus/models/menutype.php');
		$menuhelper = new MenusModelMenutype ();
		$menuhelper->delete ( $menuid );
	}

	function checkTimeout($stop = false) {
		static $start = null;
		if ($stop) $start = 0;
		$time = microtime (true);
		if ($start === null) {
			$start = $time;
			return false;
		}
		if ($time - $start < 1)
			return false;

		return true;
	}

	protected function _getJoomlaArchiveError($archive) {
		$jversion = new JVersion ();
		$error = '';
		if ($jversion->RELEASE == '1.5') {
			// Unfortunately Joomla 1.5 needs this rather ugly hack to get the error message
			$ext = JFile::getExt(strtolower($archive));
			$adapter = null;

			switch ($ext)
			{
				case 'zip':
					$adapter =& JArchive::getAdapter('zip');
					break;
				case 'tar':
					$adapter =& JArchive::getAdapter('tar');
					break;
				case 'tgz'  :
				case 'gz'   :	// This may just be an individual file (e.g. sql script)
				case 'gzip' :
					$adapter =& JArchive::getAdapter('gzip');
					break;
				case 'tbz2' :
				case 'bz2'  :	// This may just be an individual file (e.g. sql script)
				case 'bzip2':
					$adapter =& JArchive::getAdapter('bzip2');
					break;
				default:
					$adapter = null;
					break;
			}

			if ($adapter) {
				$error .= $adapter->get('error.message'). ': ' . $archive;
			}

			// End of Joomla 1.5 error message hackathon
		} else {
			// J1.6 and beyond - Not yet implemented
		}

		return $error;
	}

}
class KunenaInstallerException extends Exception {
}