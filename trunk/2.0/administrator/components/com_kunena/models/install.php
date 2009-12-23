<?php
/**
 * @version		$Id: install.php 1244 2009-12-02 04:10:31Z mahagr$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

// Minimum requirements
DEFINE('KUNENA_MIN_PHP',	'5.0.3');
DEFINE('KUNENA_MIN_MYSQL',	'5.0.3');
DEFINE('KUNENA_MIN_JOOMLA',	'1.5.10');

jimport('joomla.application.component.model');

/**
 * Install Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelInstall extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	protected $_req = false;
	protected $_versionprefix = false;
	protected $_installed = false;
	protected $_action = false;

	protected $_errormsg = null;

	protected $_versiontablearray = null;
	protected $_versionarray = null;

	public function __construct()
	{
		parent::__construct();
		$this->db = JFactory::getDBO();

		ignore_user_abort(true);

		$this->_versiontablearray = array (
			array('prefix'=>'kunena_', 'table'=>'kunena_version'),
			array('prefix'=>'fb_',     'table'=>'fb_version'),
		);
		$this->_versionarray = array(
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'1.0.4', 'date'=>'2007-12-23', 'table'=>'fb_sessions',   'column'=>'currvisit'),
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'1.0.3', 'date'=>'2007-09-04', 'table'=>'fb_categories', 'column'=>'headerdesc'),
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'1.0.2', 'date'=>'2007-08-03', 'table'=>'fb_users',      'column'=>'rank'),
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'1.0.1', 'date'=>'2007-05-20', 'table'=>'fb_users',      'column'=>'uhits'),
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'1.0.0', 'date'=>'2007-04-15', 'table'=>'fb_categories', 'column'=>'image'),
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'0.9.9', 'date'=>'0000-00-00', 'table'=>'fb_messages'),
			//array('component'=>'JoomlaBoard','prefix'=>'sb_', 'version'=>'v1.1', 'date'=>'0000-00-00', 'table'=>'sb_messages'),
			array('component'=>null,		'prefix'=>null,	 'version'=>null,	 'date'=>null)
			);
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
	public function cleanup()
	{
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	The default value to use if no state property exists by name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null, $default = null)
	{
		// if the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false)
		{
			$app	= JFactory::getApplication();
			$this->setState('step', $step = $app->getUserState('com_kunena.install.step'));
			if($step == 0) $app->setUserState('com_kunena.install.status', array());
			else $this->setState('status', $app->getUserState('com_kunena.install.status'));
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	public function getStep()
	{
		return $this->getState('step', 0);
	}
	
	public function setStep($step)
	{
		$this->setState('step', (int)$step);
		$app = JFactory::getApplication();
		$app->setUserState('com_kunena.install.step', (int)$step); 
	}
	
	public function addStatus($step, $result=false, $msg='')
	{
		$status = $this->getState('status');
		$status[] = array('step'=>$step, 'success'=>$result, 'msg'=>$msg);
		$this->setState('status', $status);
		$app	= JFactory::getApplication();
		$app->setUserState('com_kunena.install.status', $status); 
	}
	
	function getError()
	{
		$status = $this->getState('status', array());
		$error = 0;
		foreach ($status as $cur) {
			$error = !$cur['success'];
			if ($error) break;
		}
		return $error;
	}
	
	public function getSteps() {
		$this->steps = array(
		array('step'=>'', 'menu'=>'Install Kunena'),
		array('step'=>'Prepare', 'menu'=>'Prepare Installation'),
		array('step'=>'Backend', 'menu'=>'Install Backend'),
		array('step'=>'Frontend', 'menu'=>'Install Frontend'),
		array('step'=>'Database', 'menu'=>'Update Database'),
		array('step'=>'Finish', 'menu'=>'Finish Installation'),
		array('step'=>'', 'menu'=>'Installation completed'));
		return $this->steps;
	}
	
	public function extract($path, $filename, $dest=null) {
		if (!$dest) $dest = $path;
		$file = $path.DS.$filename;

		$text = '';
		
		if (file_exists($file)) 
		{
			$error = JArchive::extract($file, $dest);
			if (!$error) 
				$text .= "The file '$file' could not be uncompressed";
		}
		else 
		{
			$error = true;
			$text .= "The file '$file' does not exists";
		}
		$this->addStatus("Uncompress ".$filename, $error, $text);
	}
	
	public function beginInstall()
	{
		$results = array();
		// Migrate version table from old installation
		$versionprefix = $this->getVersionPrefix();
		if (!empty($versionprefix))	{
			$results[] = $this->migrateTable($versionprefix.'version', 'kunena_version');
		}

		kimport('models.schema', 'admin');
		$schema = new KunenaModelSchema();
		$results[] = $schema->updateSchemaTable('kunena_version');

		// Insert data from the old version, if it does not exist in the version table
		$version = $this->getInstalledVersion();
		if ($version->id == 0 && $version->component)
			$this->insertVersionData($version->version, $version->versiondate, $version->build, $version->versionname, null);

		foreach ($results as $i=>$r) if ($r) $this->addStatus($r['action'].' '.$r['name'], true);
		
		$this->insertVersion('migrateDatabase');
		$this->addStatus("Prepare installation", true);
	}

	public function migrateDatabase()
	{
		$results = array();
		$version = $this->getInstalledVersion();
		if (!empty($version->prefix)) {

			// Migrate all tables from old installation
			$tables = $this->listTables($version->prefix);
			foreach ($tables as $oldtable)
			{
				$newtable = preg_replace('/^'.$version->prefix.'/', 'kunena_', $oldtable);
				$result = $this->migrateTable($oldtable, $newtable);
				if ($result) $results[] = $result;
			}
			foreach ($results as $i=>$r) if ($r) $this->addStatus($r['action'].' '.$r['name'], true);
		}
		$this->updateVersionState('upgradeDatabase');
		$this->addStatus("Updated version state", true);
	}

	public function upgradeDatabase()
	{
		kimport('models.schema', 'admin');
		$schema = new KunenaModelSchema();
		$results = $schema->updateSchema();
		foreach ($results as $i=>$r) if ($r) $this->addStatus($r['action'].' '.$r['name'], true);
		$this->updateVersionState('installSampleData');
	}

	public function installSampleData()
	{
		kimport('install.sampledata', 'admin');
		installSampleData();
		$this->addStatus("Install Sample Data", true);
		$this->updateVersionState('');
	}

	public function getRequirements()
	{
		if ($this->_req !== false) {
			return $this->_req;
		}

		$req = new StdClass();
		$req->mysql = $this->db->getVersion();
		$req->php = phpversion();
		$req->joomla = JVERSION;

		$req->fail = array();
		if (version_compare($req->mysql, KUNENA_MIN_MYSQL, "<")) $req->fail['mysql'] = true;
		if (version_compare($req->php, KUNENA_MIN_PHP, "<")) $req->fail['php'] = true;
		if (version_compare($req->joomla, KUNENA_MIN_JOOMLA, "<")) $req->fail['joomla'] = true;

		$this->_req = $req;
		return $this->_req;
	}

	public function getVersionPrefix()
	{
		if ($this->_versionprefix !== false) {
			return $this->_versionprefix;
		}

		$match = $this->detectTable($this->_versiontablearray);
		if (isset($match['prefix'])) $this->_versionprefix = $match['prefix'];
		else $this->_versionprefix = null;

		return $this->_versionprefix;
	}

	public function getInstalledVersion()
	{
		if ($this->_installed !== false) {
			return $this->_installed;
		}

		$versionprefix = $this->getVersionPrefix();

		if ($versionprefix)
		{
			// Version table exists, try to get installed version
			$this->db->setQuery("SELECT * FROM ".$this->db->nameQuote($this->db->getPrefix().$versionprefix.'version')." ORDER BY `id` DESC", 0, 1);
			$version = $this->db->loadObject();
			if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());

			if (isset($version->state) && $version->state != '')
			{
				// We have new version of the table and installation process running, so try again
				$this->db->setQuery("SELECT * FROM ".$this->db->nameQuote($this->db->getPrefix().$versionprefix.'version')." WHERE `state`='' ORDER BY `id` DESC", 0, 1);
				$version = $this->db->loadObject();
				if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
			}
			if ($version) {
				$version->version = strtoupper($version->version);
				if (version_compare($version->version, '1.5.999', ">")) $version->prefix = 'kunena_';
				else $version->prefix = 'fb_';
				if (version_compare($version->version, '1.0.5', ">")) $version->component = 'Kunena';
				else $version->component = 'FireBoard';
			}

			// Version table may contain dummy version.. Ignore it
			if (!$version || version_compare($version->version, '0.1.0', "<")) unset($version);
		}

		if (!isset($version))
		{
			// No version found -- try to detect version by searching some missing fields
			$match = $this->detectTable($this->_versionarray);

			// Clean install
			if (empty($match)) return $this->_installed = null;

			// Create version object
			$version = new StdClass();
			$version->id = 0;
			$version->component = $match['component'];
			$version->version = strtoupper($match['version']);
			$version->versiondate = $match['date'];
			$version->installdate = '';
			$version->build = '';
			$version->versionname = '';
			$version->prefix = $match['prefix'];
		}
		return $this->_installed = $version;
	}

	protected function insertVersion($state = 'beginInstall')
	{
		// Insert data from the new version
		$this->insertVersionData(KUNENA_VERSION, KUNENA_VERSION_DATE, KUNENA_VERSION_BUILD, KUNENA_VERSION_NAME, $state);
	}

	protected function updateVersionState($state)
	{
		// Insert data from the new version
		$this->db->setQuery("UPDATE ".$this->db->nameQuote($this->db->getPrefix().'kunena_version')." SET state = ".$this->db->Quote($state)." ORDER BY id DESC LIMIT 1");
		$this->db->query();
		if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
	}

	public function getInstallAction()
	{
		if ($this->_action !== false) {
			return $this->_action;
		}

		$version = $this->getInstalledVersion();
		if ($version->component === null) $this->_action = 'INSTALL';
		else if (version_compare($version->version, '1.9.99', '<=')) $this->_action = 'MIGRATE';
		else if (version_compare(KUNENA_VERSION, $version->version, '>')) $this->_action = 'UPGRADE';
		else if (version_compare(KUNENA_VERSION, $version->version, '<')) $this->_action = 'DOWNGRADE';
		else if (KUNENA_VERSION_BUILD && KUNENA_VERSION_BUILD > $version->build) $this->_action = 'UP_BUILD';
		else if (KUNENA_VERSION_BUILD && KUNENA_VERSION_BUILD < $version->build) $this->_action = 'DOWN_BUILD';
		else $this->_action = 'REINSTALL';

		return $this->_action;
	}

	protected function detectTable($detectlist)
	{
		// Cache
		static $tables = array();
		static $fields = array();

		$found = 0;
		foreach ($detectlist as $detect)
		{
			// If no detection is needed, return current item
			if (!isset($detect['table'])) return $detect;

			$table = $this->db->getPrefix().$detect['table'];

			// Match if table exists
			if (!isset($tables[$table])) // Not cached
			{
				$this->db->setQuery("SHOW TABLES LIKE ".$this->db->quote($table));
				$result = $this->db->loadResult();
				if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
				$tables[$table] = $result;
			}
			if (!empty($tables[$table])) $found = 1;

			// Match if column in a table exists
			if ($found && isset($detect['column']))
			{
				if (!isset($fields[$table])) // Not cached
				{
					$this->db->setQuery("SHOW COLUMNS FROM ".$this->db->nameQuote($table));
					$result = $this->db->loadObjectList('Field');
					if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
					$fields[$table] = $result;
				}
				if (!isset($fields[$table][$detect['column']])) $found = 0; // Sorry, no match
			}
			if ($found) return $detect;
		}
		return array();
	}

	// helper function to migrate table
	protected function migrateTable($oldtable, $newtable)
	{
		$tables = $this->listTables('kunena_');
		if ($oldtable==$newtable || empty($oldtable) || isset($tables[$newtable])) return; // Nothing to migrate

		$sql = "CREATE TABLE ".$this->db->nameQuote($this->db->getPrefix().$newtable)." SELECT * FROM ".$this->db->nameQuote($this->db->getPrefix().$oldtable);
		$this->db->setQuery($sql);
		$this->db->query();
		if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
		if ($this->db->getAffectedRows())
		{
			$this->tables['kunena_'][] = $newtable;
			$this->addStatus("Migrate $newtable", true);
			return array('name'=>$newtable, 'action'=>'migrate', 'sql'=>$sql);
		}
		return array();
	}

	// also insert old version if not in the table
	protected function insertVersionData( $version, $versiondate, $build, $versionname, $state='')
	{
		$this->db->setQuery("INSERT INTO  `#__kunena_version`"
			."SET `version` = ".$this->db->quote($version).","
			."`versiondate` = ".$this->db->quote($versiondate).","
			."`installdate` = CURDATE(),"
			."`build` = ".$this->db->quote($build).","
			."`versionname` = ".$this->db->quote($versionname).","
			."`state` = ".$this->db->quote($state));
		$this->db->query();
		if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
	}

	protected function listTables($prefix, $reload = false)
	{
		if (isset($this->tables[$prefix]) && !$reload) {
			return $this->tables[$prefix];
		}
		$this->db->setQuery("SHOW TABLES LIKE ".$this->db->quote($this->db->getPrefix().$prefix.'%'));
		$list = $this->db->loadResultArray();
		if ($this->db->getErrorNum()) throw new KunenaInstallerException($this->db->getErrorMsg(), $this->db->getErrorNum());
		$this->tables[$prefix] = array();
		foreach ($list as $table) {
			$table = preg_replace('/^'.$this->db->getPrefix().'/', '', $table);
			$this->tables[$prefix][$table] = $table;
		}
		return $this->tables[$prefix];
	}

}
class KunenaInstallerException extends Exception {}