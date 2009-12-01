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
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'1.0.4', 'date'=>'2007-12-23', 'table'=>'fb_sessions',   'column'=>'currvisit'),
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'1.0.3', 'date'=>'2007-09-04', 'table'=>'fb_categories', 'column'=>'headerdesc'),
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'1.0.2', 'date'=>'2007-08-03', 'table'=>'fb_users',      'column'=>'rank'),
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'1.0.1', 'date'=>'2007-05-20', 'table'=>'fb_users',      'column'=>'uhits'),
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'1.0.0', 'date'=>'2007-04-15', 'table'=>'fb_categories', 'column'=>'image'),
			array('component'=>'FireBoard', 'prefix'=>'fb_', 'version'=>'0.9.x', 'date'=>'0000-00-00', 'table'=>'fb_messages'),
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

	public function initialize()
	{
		// Create or migrate version table if it does not exist
		$versionprefix = $this->getVersionPrefix();
		if ($versionprefix != 'kunena_')
		{
			if ($versionprefix === null)
				$this->createVersionTable();
			else
				$this->migrateVersionTable();
		}
		// Insert data from the old version, if it does not exist in the version table
		$version = $this->getInstalledVersion();
		if ($version && $version->id == 0)
			$this->insertVersionData($version->version, $version->versiondate, $version->build, $version->versionname, null);
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

	public function getInstalledVersion()
	{
		if ($this->_installed !== false) {
			return $this->_installed;
		}

		$db = JFactory::getDBO();
		$versionprefix = $this->getVersionPrefix();

		if ($versionprefix == 'kunena_')
		{
			// Version table exists, try to get installed version
			$db->setQuery("SELECT * FROM ".$db->nameQuote($db->getPrefix().'kunena_version')." WHERE `status`='' ORDER BY `id` DESC", 0, 1);
	    	$version = $db->loadObject();
	    	if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());

	    	$version->version = strtoupper($version->version);
	    	if (version_compare($version->version, '1.0.5', ">")) $version->component = 'Kunena';
	    	else $version->component = 'FireBoard';

	    	// Version table may contain dummy version.. Ignore it
			if (version_compare($version->version, '0.1.0', "<")) unset($version);
		}

		if (!isset($version))
		{
			// No version found -- try to detect version by searching some missing fields
			$match = $this->detectTable($this->versionarray);

			// Clean install
			if (!isset($match['component'])) return $this->_installed = null;

			// Create version object
			$version = StdClass();
			$version->id = 0;
			$version->component = $match['component'];
			$version->version = strtoupper($match['version']);
			$version->versiondate = $match['date'];
			$version->installdate = '';
			$version->build = '';
			$version->versionname = '';
		}
		return $this->_installed = $version;
	}

	public function insertVersion()
	{
 		// Insert data from the new version
		$this->insertVersionData(KUNENA_VERSION, KUNENA_VERSION_DATE, KUNENA_VERSION_BUILD, KUNENA_VERSION_NAME, 'install.insertVersion');
	}

	public function getInstallAction()
	{
		if ($this->_action !== false) {
			return $this->_action;
		}

		$version = $this->getInstalledVersion();
		if ($version === null) $this->_action = 'INSTALL';
		else if (version_compare($version->version, '1.0.5', '<')) $this->_action = 'MIGRATE_FB';
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
					$db->setQuery("SHOW COLUMNS FROM ".$db->quote($table));
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

	// helper function to create new version table
	protected function createVersionTable()
	{
		$db =& JFactory::getDBO();
	    $db->setQuery("CREATE TABLE `#__kunena_version`"
			." (`id` INTEGER NOT NULL AUTO_INCREMENT,"
			." `version` VARCHAR(20) NOT NULL,"
			." `versiondate` DATE NOT NULL,"
			." `installdate` DATE NOT NULL,"
			." `build` VARCHAR(20) NOT NULL,"
			." `versionname` VARCHAR(40) NULL,"
			." `status` VARCHAR(32) NULL,"
			." PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8;");
		// Let the install handle the error
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
	}

	// helper function to create new version table
	protected function migrateVersionTable()
	{
		$versionprefix = $this->getVersionPrefix();
		if ($versionprefix === null || $versionprefix == 'kunena_') return; // Nothing to migrate

		$this->createVersionTable();
		$db =& JFactory::getDBO();
	    $db->setQuery("INSERT INTO `#__kunena_version` SELECT *, status=null FROM ".$this->nameQuote($db->getPrefix().$versionprefix.'version'));
		// Let the install handle the error
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
	}

	// also insert old version if not in the table
	protected function insertVersionData( $version, $versiondate, $build, $versionname, $status=null)
	{
		$versionprefix = $this->getVersionPrefix();
		if ($versionprefix != 'kunena_') {
			$this->createVersionTable();
			if ($versionprefix !== null) $this->migrateVersionTable();
		}

		$db =& JFactory::getDBO();
		if ($status !== null) $status = $db->quote(md5($status));
	    $db->setQuery("INSERT INTO  `#__kunena_version`"
			."SET `version` = ".$db->quote($version).","
			."`versiondate` = ".$db->quote($versiondate).","
			."`installdate` = CURDATE(),"
			."`build` = ".$db->quote($build).","
			."`versionname` = ".$db->quote($versionname).","
			."`status` = $status;");
		$db->query();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
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
		static $schema = false;
		if (isset($schema[$filename]) && !$reload) {
			return $schema[$filename];
		}
		$schema = new DOMDocument('1.0', 'utf-8');
		$schema->formatOutput = true;
		$schema->preserveWhiteSpace = false;
		$dom->validateOnParse = true;
		$schema->load($filename);
		return $schema;
	}

	public function getSchemaFromDatabase($reload = false)
	{
		static $schema = false;
		if ($schema !== false && !$reload) {
			return $schema;
		}

		$db =& JFactory::getDBO();
		$db->setQuery("SHOW TABLES LIKE ".$db->quote($db->getPrefix().'kunena_%'));
		$tables = $db->loadResultArray();
		if ($db->getErrorNum()) throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());

		$schema = $this->getSchemaNew();
		$schemaNode = $schema->documentElement;
		foreach ($tables as $table) {
			if (strstr($table, 'backup')) continue;

			$tableNode = $schema->createElement("table");
			$schemaNode->appendChild($tableNode);

			$tableNode->setAttribute("name", str_replace($db->getPrefix(), '', $table));

			$db->setQuery( "SHOW FIELDS FROM ".$db->nameQuote($table));
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

			$db->setQuery( "SHOW KEYS FROM ".$db->nameQuote($table));
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

	public function getSchemaSQL($schema)
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
					$str .= 'DROP TABLE '.$db->nameQuote($tablename).';';
					break;
				case 'alter':
					$str .= 'ALTER TABLE '.$db->nameQuote($tablename).' '."\n";
					foreach ($table->childNodes as $field)
					{
						switch ($action = $field->getAttribute('action'))
						{
							case 'drop':
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
								echo("Kunena Installer: Unknown action $tablename.$action on xml file<br />");
						}
					}
					$str .= implode(",\n", $fields) . ';';
					break;
				case 'create':
				case '':
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
			$tables[$table->getAttribute('name')] = $str;
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