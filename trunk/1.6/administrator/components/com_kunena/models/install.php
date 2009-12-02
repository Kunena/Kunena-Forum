<?php
/**
 * @version		$Id$
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

DEFINE('KUNENA_INPUT_DATABASE', 912357); // just contains random number

DEFINE('KUNENA_INSTALL_SCHEMA_FILE', KPATH_ADMIN.'/install/install.xml');
DEFINE('KUNENA_INSTALL_SCHEMA_EMPTY', '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE schema SYSTEM "'.KPATH_ADMIN.'/install/kunena16.dtd'.'"><schema></schema>');

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

	protected $_sql = null;
	protected $tables = array();

	public function __construct()
	{
		parent::__construct();
		$db = JFactory::getDBO();

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
			array('component'=>'FireBoard',	'prefix'=>'fb_', 'version'=>'0.9.x', 'date'=>'0000-00-00', 'table'=>'fb_messages'),
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
	 */	public function cleanup()
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
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	public function beginInstall()
	{
		$results = array();

		// Migrate version table from old installation (if available)
		$result = $this->migrateTable($this->getVersionPrefix().'version', 'kunena_version');
		if ($result) $results[] = $result;

		// Get changes in database
		$schema = $this->getSchemaFromDatabase();
		$diff = $this->getSchemaDiff($schema, KUNENA_INSTALL_SCHEMA_FILE);
		$this->_sql = $this->getSchemaSQL($diff);

		//echo "<pre>",htmlentities($schema->saveXML()),"</pre>";
		//echo "<pre>",htmlentities($diff->saveXML()),"</pre>";
		//echo "<pre>",print_r($this->_sql),"</pre>";

		$result = $this->updateTable('kunena_version');
		if ($result) $results[] = $result;

		// Insert data from the old version, if it does not exist in the version table
		$version = $this->getInstalledVersion();
		if ($version->id == 0 && $version->component)
			$this->insertVersionData($version->version, $version->versiondate, $version->build, $version->versionname, null);

		$this->insertVersion('migrateDatabase');
		return $results;
	}

	public function migrateDatabase()
	{
		$results = array();

		// Migrate rest of the tables from old installation (if available)
		$version = $this->getInstalledVersion();
		$tables = $this->listTables($version->prefix);
		foreach ($tables as $oldtable)
		{
			$newtable = preg_replace('/^'.$version->prefix.'/', 'kunena_', $oldtable);
			$result = $this->migrateTable($oldtable, $newtable);
			if ($result) $results[] = $result;
		}
		$this->resetTables('kunena_');

		$this->updateVersionState('upgradeDatabase');
		return $results;
	}

	public function upgradeDatabase()
	{
		$results = array();

		$schema = $this->getSchemaFromDatabase(true);
		$diff = $this->getSchemaDiff($schema, KUNENA_INSTALL_SCHEMA_FILE);
		$this->_sql = $this->getSchemaSQL($diff);

		//echo "<pre>",htmlentities($schema->saveXML()),"</pre>";
		//echo "<pre>",htmlentities($diff->saveXML()),"</pre>";
		//echo "<pre>",print_r($this->_sql),"</pre>";

		foreach ($this->_sql as $table)
		{
			$result = $this->updateTable($table['name']);
			if ($result) $results[] = $result;
		}

		$this->updateVersionState('');
		return $results;
	}

	public function getVersionWarning()
	{
		if (strpos(KUNENA_VERSION, 'SVN') !== false) {
			$kn_version_name = 'SVN Revision';
			$kn_version_warning = 'Never use an SVN revision for anything else other than software development!';
		} else if (strpos(KUNENA_VERSION, 'RC') !== false) {
			$kn_version_name = 'Release Candidate';
			$kn_version_warning = 'This release may contain bugs, which will be fixed in the final version.';
		} else if (strpos(KUNENA_VERSION, 'BETA') !== false) {
			$kn_version_name = 'Beta Release';
			$kn_version_warning = 'This release is not recommended to be used on live production sites.';
		} else if (strpos(KUNENA_VERSION, 'ALPHA') !== false) {
			$kn_version_name = 'Alpha Release';
			$kn_version_warning = 'This is a public preview and should never be used on live production sites.';
		} else if (strpos(KUNENA_VERSION, 'DEV') !== false) {
			$kn_version_name = 'Development Snapshot';
			$kn_version_warning = 'This is an internal release which should be used only by developers and testers!';
		}
		if (!empty($kn_version_warning))
		{
			return sprintf('You are about to install Kunena %s (%s).', KUNENA_VERSION, KUNENA_VERSION_NAME).' '.$kn_version_warning;
		}
	}

	public function getRequirements()
	{
		if ($this->_req !== false) {
			return $this->_req;
		}

		$req = new StdClass();
		$db = JFactory::getDBO();
		$req->mysql = $db->getVersion();
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

	public function getLastVersion()
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM ".$db->nameQuote($db->getPrefix().$this->getVersionPrefix().'version')." ORDER BY `id` DESC", 0, 1);
		$version = $db->loadObject();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		if (!$version || !isset($version->state))
		{
			$version->state = '';
		}
		else if (!empty($version->state))
		{
			if ($version->version != KUNENA_VERSION || $version->build != KUNENA_VERSION_BUILD) $version->state = '';
		}
		return $version;
	}

	public function getInstalledVersion()
	{
		if ($this->_installed !== false) {
			return $this->_installed;
		}

		$db = JFactory::getDBO();
		$versionprefix = $this->getVersionPrefix();

		if ($versionprefix)
		{
			// Version table exists, try to get installed version
			$db->setQuery("SELECT * FROM ".$db->nameQuote($db->getPrefix().$versionprefix.'version')." ORDER BY `id` DESC", 0, 1);
			$version = $db->loadObject();
			if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());

			if (isset($version->state) && $version->state != '')
			{
				// We have new version of the table and installation process running, so try again
				$db->setQuery("SELECT * FROM ".$db->nameQuote($db->getPrefix().$versionprefix.'version')." WHERE `state`='' ORDER BY `id` DESC", 0, 1);
				$version = $db->loadObject();
				if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
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
		$db = JFactory::getDBO();
		// Insert data from the new version
		$db->setQuery("UPDATE ".$db->nameQuote($db->getPrefix().'kunena_version')." SET state = ".$db->Quote($state)." ORDER BY id DESC LIMIT 1");
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
	}

	public function getInstallAction()
	{
		if ($this->_action !== false) {
			return $this->_action;
		}

		$version = $this->getInstalledVersion();
		if ($version->component === null) $this->_action = 'INSTALL';
		else if (version_compare($version->version, '1.0.5', '<')) $this->_action = 'MIGRATE';
		else if (version_compare($version->version, '1.5.999', '<')) $this->_action = 'MIGRATE';
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

		$db = JFactory::getDBO();

		$found = 0;
		foreach ($detectlist as $detect)
		{
			// If no detection is needed, return current item
			if (!isset($detect['table'])) return $detect;

			$table = $db->getPrefix().$detect['table'];

			// Match if table exists
			if (!isset($tables[$table])) // Not cached
			{
				$db->setQuery("SHOW TABLES LIKE ".$db->quote($table));
				$result = $db->loadResult();
				if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
				$tables[$table] = $result;
			}
			if (!empty($tables[$table])) $found = 1;

			// Match if column in a table exists
			if ($found && isset($detect['column']))
			{
				if (!isset($fields[$table])) // Not cached
				{
					$db->setQuery("SHOW COLUMNS FROM ".$db->nameQuote($table));
					$result = $db->loadObjectList('Field');
					if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
					$fields[$table] = $result;
				}
				if (!isset($fields[$table][$detect['column']])) $found = 0; // Sorry, no match
			}
			if ($found) return $detect;
		}
		return array();
	}

	// helper function to update table
	protected function updateTable($table)
	{
		if (!isset($this->_sql[$table])) return;

		$db =& JFactory::getDBO();
		$db->setQuery($this->_sql[$table]['sql']);
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		$result = $this->_sql[$table];
		if ($this->_sql[$table]['action'] == 'create') $this->addToTables('kunena_', $table);
		else if ($this->_sql[$table]['action'] == 'drop') $this->removeFromTables('kunena_', $table);
		unset($this->_sql[$table]);
		return $result;
	}

	// helper function to migrate table
	protected function migrateTable($oldtable, $newtable)
	{
		$tables = $this->listTables('kunena_');
		if ($oldtable==$newtable || empty($oldtable) || isset($tables[$newtable])) return; // Nothing to migrate

		$db =& JFactory::getDBO();
		$sql = "CREATE TABLE ".$db->nameQuote($db->getPrefix().$newtable)." SELECT * FROM ".$db->nameQuote($db->getPrefix().$oldtable);
		$db->setQuery($sql);
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		if ($db->getAffectedRows())
		{
			$this->addToTables('kunena_', $newtable);
			return array('name'=>$newtable, 'action'=>'migrate', 'sql'=>$sql);
		}
		return array();
	}

	// also insert old version if not in the table
	protected function insertVersionData( $version, $versiondate, $build, $versionname, $state='')
	{
		$db =& JFactory::getDBO();
		$db->setQuery("INSERT INTO  `#__kunena_version`"
			."SET `version` = ".$db->quote($version).","
			."`versiondate` = ".$db->quote($versiondate).","
			."`installdate` = CURDATE(),"
			."`build` = ".$db->quote($build).","
			."`versionname` = ".$db->quote($versionname).","
			."`state` = ".$db->quote($state));
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
	}

	protected function resetTables($prefix)
	{
		unset($this->tables[$prefix]);
	}

	protected function addToTables($prefix, $table)
	{
		$this->tables[$prefix][$table] = $table;
	}

	protected function removeFromTables($prefix, $table)
	{
		unset($this->tables[$prefix][$table]);
	}

	protected function listTables($prefix, $reload = false)
	{
		if (isset($this->tables[$prefix]) && !$reload) {
			return $this->tables[$prefix];
		}
		$db =& JFactory::getDBO();
		$db->setQuery("SHOW TABLES LIKE ".$db->quote($db->getPrefix().$prefix.'%'));
		$list = $db->loadResultArray();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		$this->tables[$prefix] = array();
		foreach ($list as $table) {
			$table = preg_replace('/^'.$db->getPrefix().'/', '', $table);
			$this->addToTables($prefix, $table);
		}
		return $this->tables[$prefix];
	}

	public function getSchemaNew()
	{
		$schema = new DOMDocument('1.0', 'utf-8');
		$schema->formatOutput = true;
		$schema->preserveWhiteSpace = false;
		$schema->loadXML(KUNENA_INSTALL_SCHEMA_EMPTY);
		return $schema;
	}

	public function getSchemaFromFile($filename, $reload = false)
	{
		static $schema = array();
		if (isset($schema[$filename]) && !$reload) {
			return $schema[$filename];
		}
		$schema[$filename] = new DOMDocument('1.0', 'utf-8');
		$schema[$filename]->formatOutput = true;
		$schema[$filename]->preserveWhiteSpace = false;
		$dom->validateOnParse = true;
		$schema[$filename]->load($filename);
		return $schema[$filename];
	}

	public function getSchemaFromDatabase($reload = false)
	{
		static $schema = false;
		if ($schema !== false && !$reload) {
			return $schema;
		}

		$db =& JFactory::getDBO();
		$tables = $this->listTables('kunena_');

		$schema = $this->getSchemaNew();
		$schemaNode = $schema->documentElement;
		foreach ($tables as $table) {
			if (strstr($table, 'backup')) continue;

			$tableNode = $schema->createElement("table");
			$schemaNode->appendChild($tableNode);

			$tableNode->setAttribute("name", $table);

			$db->setQuery( "SHOW FIELDS FROM ".$db->nameQuote($db->getPrefix().$table));
			$fields = $db->loadObjectList();
			if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
			foreach ($fields as $row) {
				$fieldNode = $schema->createElement("field");
				$tableNode->appendChild($fieldNode);

				if ($row->Key == "PRI") $fieldNode->setAttribute("primary_key", "yes");
				$fieldNode->setAttribute("name", $row->Field);
				$fieldNode->setAttribute("type", $row->Type);
				$fieldNode->setAttribute("null", (strtolower($row->Null)=='yes') ? '1' : '0');
				if ($row->Default != '') $fieldNode->setAttribute("default", $row->Default);
				if ($row->Extra != '') $fieldNode->setAttribute("extra", $row->Extra);
			}

			$db->setQuery( "SHOW KEYS FROM ".$db->nameQuote($db->getPrefix().$table));
			$keys = $db->loadObjectList();
			if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());

			unset($keyNode);
			foreach ($keys as $row) {
				if (!isset($keyNode) || $keyNode->getAttribute('name') != $row->Key_name) {
					$keyNode = $schema->createElement("key");
					$tableNode->appendChild($keyNode);

					$keyNode->setAttribute("name", $row->Key_name);
					if (!$row->Non_unique) $keyNode->setAttribute("unique", (bool)!$row->Non_unique);
					//if ($row->Comment != '') $keyNode->setAttribute("comment", $row->Comment);
				}

				$columns = $keyNode->getAttribute('columns');
				if (!empty($columns)) $columns .= ',';
				$columns .= $row->Column_name;
				$columns .= ($row->Sub_part) ? '('.$row->Sub_part.')' : '';
				$keyNode->setAttribute('columns', $columns);
			}
		}
		return $schema;
	}

	public function getSchemaDiff($old, $new)
	{
		$old = $this->getDOMDocument($old);
		$new = $this->getDOMDocument($new);
		if (!$old || !$new) return;

		$old->validate();
		$new->validate();
		$schema = $this->getSchemaNew();
		$schemaNode = $schema->documentElement;
		$schemaNode->setAttribute('type', 'diff');

		$nodes = $this->listAllNodes(array('old'=>$old->documentElement->childNodes, 'new'=>$new->documentElement->childNodes));
		foreach ($nodes as $nodeTag => $nodeList)
		{
			foreach ($nodeList as $nodeName => $nodeLoc)
			{
				$newNode = $this->getSchemaNodeDiff($schema, $nodeTag, $nodeName, $nodeLoc);
				if ($newNode) $schemaNode->appendChild($newNode);
			}
		}
		return $schema;
	}

	protected function listAllNodes($nodeLists)
	{
		$list = array();
		foreach ($nodeLists as $k=>$nl) foreach ($nl as $n)
		{
			if (is_a($n, 'DOMAttr')) $list[$n->name][$k] = $n;
			else if (is_a($n, 'DOMElement')) $list[$n->tagName][$n->getAttribute('name')][$k] = $n;
		}
		return $list;
	}

	public function getSchemaNodeDiff($schema, $tag, $name, $loc)
	{
		$node = null;
		// Add
		if (!isset($loc['old']))
		{
			$node = $schema->importNode($loc['new'], true);
			$node->setAttribute('action', 'create');
			return $node;
		}
		// Delete
		if (!isset($loc['new']))
		{
			$node = $schema->createElement($tag);
			$node->setAttribute('name', $name);
			$node->setAttribute('action', 'drop');
			return $node;
		}

		$action = false;
		$childNodes = array();
		$childAll = $this->listAllNodes(array('old'=>$loc['old']->childNodes, 'new'=>$loc['new']->childNodes));
		foreach ($childAll as $childTag => $childList)
		{
			foreach ($childList as $childName => $childLoc)
			{
				$childNode = $this->getSchemaNodeDiff($schema, $childTag, $childName, $childLoc);
				if ($childNode) $childNodes[] = $childNode;
			}
		}
		$attributes = array();
		$attrAll = $this->listAllNodes(array('old'=>$loc['old']->attributes, 'new'=>$loc['new']->attributes));
		foreach ($attrAll as $attrName => $attrLoc)
		{
			if ($attrName == 'primary_key' || $attrName == 'action') continue;
			if (!isset($attrLoc['old']->value) || !isset($attrLoc['new']->value) || str_replace(' ', '', $attrLoc['old']->value) != str_replace(' ', '', $attrLoc['new']->value))
				$action = 'alter';
		}

		if (count($childNodes) || $action)
		{
			$node = $schema->importNode($loc['new'], false);
			$node->setAttribute('name', $name);
			$node->setAttribute('action', 'alter');
			foreach ($childNodes as $childNode) $node->appendChild($childNode);
		}
		return $node;
	}

	protected function getDOMDocument($input)
	{
		if (is_a($input, 'DOMNode')) $schema = $input;
		else if ($input === KUNENA_INPUT_DATABASE) $schema = $this->getSchemaFromDatabase();
		else if (is_string($input) && file_exists($input)) $schema = $this->getSchemaFromFile($input);
		else if (is_string($input)) { $schema = new DOMDocument('1.0', 'utf-8'); $schema->loadXML($input); }
		if (!isset($schema)  || $schema == false) return;
		$schema->formatOutput = true;
		$schema->preserveWhiteSpace = false;

		return $schema;
	}

	public function getSchemaSQL($schema, $drop=false)
	{
		$db =& JFactory::getDBO();
		$tables = array();
		foreach ($schema->getElementsByTagName('table') as $table)
		{
			$str = '';
			$tablename = $db->getPrefix() . $table->getAttribute('name');
			$fields = array();
			switch ($action = $table->getAttribute('action'))
			{
				case 'drop':
					if (!$drop) break;
					$str .= 'DROP TABLE '.$db->nameQuote($tablename).';';
					break;
				case 'alter':
					$str .= 'ALTER TABLE '.$db->nameQuote($tablename).' '."\n";
					foreach ($table->childNodes as $field)
					{
						switch ($action2 = $field->getAttribute('action'))
						{
							case 'drop':
								if (!$drop) break;
								$fields[] = '	DROP '.$this->getSchemaSQLField($field);
								break;
							case 'alter':
								if ($field->tagName == 'key') {
									$fields[] = '	DROP KEY '.$db->nameQuote($field->getAttribute('name'));
									$fields[] = '	ADD '.$this->getSchemaSQLField($field);
								} else
									$fields[] = '	MODIFY '.$this->getSchemaSQLField($field);
								break;
							case 'create':
								$fields[] = '	ADD '.$this->getSchemaSQLField($field);
							case '':
								break;
							default:
								echo("Kunena Installer: Unknown action $tablename.$action2 on xml file<br />");
						}
					}
					if (count($fields)) $str .= implode(",\n", $fields) . ';';
					else $str = '';
					break;
				case 'create':
				case '':
					$action = 'create';
					$str .= 'CREATE TABLE '.$db->nameQuote($tablename).' ('."\n";
					foreach ($table->childNodes as $field)
					{
						$fields[] = '	'.$this->getSchemaSQLField($field);
					}
					$str .= implode(",\n", $fields) . ' ) DEFAULT CHARSET=utf8;';
					break;
				default:
					echo("Kunena Installer: Unknown action $tablename.$action on xml file<br />");
			}
			if (!empty($str))
				$tables[$table->getAttribute('name')] = array('name'=>$table->getAttribute('name'), 'action'=>$action, 'sql'=>$str);
		}
		return $tables;
	}

	protected function getSchemaSQLField($field)
	{
		$db =& JFactory::getDBO();

		$str = '';
		if ($field->tagName == 'field')
		{
			$str .= $db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ' '.$field->getAttribute('type');
				$str .= ($field->getAttribute('null') == 1) ? ' NULL' : ' NOT NULL';
				$str .= ($field->hasAttribute('default')) ? ' default '.$db->quote($field->getAttribute('default')) : '';
				$str .= ($field->hasAttribute('extra')) ? ' '.$field->getAttribute('extra') : '';
			}
		}
		else if ($field->tagName == 'key')
		{
			if ($field->getAttribute('name') == 'PRIMARY') $str .= 'PRIMARY KEY';
			else if ($field->getAttribute('unique') == 1) $str .= 'UNIQUE KEY '.$db->nameQuote($field->getAttribute('name'));
			else $str .= 'KEY '.$db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ($field->hasAttribute('type')) ? ' USING '.$field->getAttribute('type') : '';
				$str .= ' ('.$field->getAttribute('columns').')';
			}
		}
		return $str;
	}

}
class KunenaInstallerException extends Exception {}