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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

DEFINE('KUNENA_SCHEMA_FILE', KPATH_ADMIN . '/install/install.xml');
DEFINE('KUNENA_UPGRADE_SCHEMA_FILE', KPATH_ADMIN . '/install/upgrade/upgrade.xml');
DEFINE('KUNENA_INSTALL_SCHEMA_EMPTY', '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html><schema></schema>');
DEFINE('KUNENA_INPUT_DATABASE', '_DB_');

jimport('joomla.application.component.model');

/**
 * Install Model for Kunena
 *
 * @since  K1.6
 */
class KunenaModelSchema extends \Joomla\CMS\MVC\Model\BaseDatabaseModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var        boolean
	 * @since    1.6
	 */
	protected $__state_set = false;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $schema = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $xmlschema = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $upgradeschema = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $diffschema = null;

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $db = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $sql = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $version = null;

	/**
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = Factory::getDBO();
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param   string $property Optional parameter name.
	 * @param   mixed  $default  The default value to use if no state property exists by name.
	 *
	 * @return    object    The property where specified, the state object where omitted.
	 *
	 * @since    1.6
	 */
	public function getState($property = null, $default = null)
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false)
		{
			$this->__state_set = true;
		}

		$value = parent::getState($property);

		return (is_null($value) ? $default : $value);
	}

	/**
	 * @param $version
	 *
	 * @since Kunena
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return DOMDocument|null
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function getSchema()
	{
		if ($this->schema == null)
		{
			$this->schema = $this->getSchemaFromDatabase();
		}

		return $this->schema;
	}

	/**
	 * @param   string $input input
	 *
	 * @return null
	 *
	 * @since Kunena
	 */
	public function getXmlSchema($input = KUNENA_SCHEMA_FILE)
	{
		if ($this->xmlschema == null)
		{
			$this->xmlschema = $this->getSchemaFromFile($input);
		}

		return $this->xmlschema;
	}

	/**
	 * @param   string $input input
	 *
	 * @return DOMDocument|null
	 *
	 * @since Kunena
	 */
	public function getUpgradeSchema($input = KUNENA_UPGRADE_SCHEMA_FILE)
	{
		if ($this->upgradeschema == null)
		{
			$this->upgradeschema = $this->createSchema();
		}

		// $this->getSchemaFromFile($input);
		return $this->upgradeschema;
	}

	/**
	 * @param   null $from  from
	 * @param   null $to    to
	 * @param   null $using using
	 *
	 * @return DOMDocument|null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function getDiffSchema($from = null, $to = null, $using = null)
	{
		if ($this->diffschema == null)
		{
			if (!$from)
			{
				$from = $this->getSchema();
			}

			if (!$to)
			{
				$to = $this->getXmlSchema();
			}

			if (!$using)
			{
				$using = $this->getUpgradeSchema();
			}

			$this->fromschema  = $from;
			$this->toschema    = $to;
			$this->usingschema = $using;
			$this->upgradeSchema($from, $using);
			$this->diffschema = $this->getSchemaDiff($from, $to);

			// Echo "<pre>",htmlentities($this->fromschema->saveXML()),"</pre>";

			// Echo "<pre>",htmlentities($this->toschema->saveXML()),"</pre>";
			$this->sql = null;
		}

		return $this->diffschema;
	}

	/**
	 * @return array|null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	protected function getSQL()
	{
		if ($this->sql == null)
		{
			$diff      = $this->getDiffSchema();
			$this->sql = $this->getSchemaSQL($diff);
		}

		return $this->sql;
	}

	/**
	 * @return array|null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function getCreateSQL()
	{
		if ($this->sql == null)
		{
			$from      = $this->createSchema();
			$diff      = $this->getDiffSchema($from);
			$this->sql = $this->getSchemaSQL($diff);
		}

		return $this->sql;
	}

	/**
	 * @param   null $prefix prefix
	 *
	 * @return array
	 *
	 * @since Kunena
	 */
	public function getSchemaTables($prefix = null)
	{
		$schema = $this->getXmlSchema();

		if ($prefix === null)
		{
			$prefix = $this->db->getPrefix();
		}

		$tables = array();

		foreach ($schema->getElementsByTagName('table') as $table)
		{
			$tables[$table->getAttribute('name')] = $prefix . $table->getAttribute('name');
		}

		return $tables;
	}

	/**
	 * @param $table
	 *
	 * @return null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function updateSchemaTable($table)
	{
		$sql = $this->getSQL();

		if (!isset($sql[$table]))
		{
			return null;
		}

		$this->db->setQuery($sql[$table]['sql']);

		try
		{
			$this->db->execute();
		}
		catch (Exception $e)
		{
			throw new KunenaSchemaException($e->getMessage(), $e->getCode());
		}

		$result            = $sql[$table];
		$result['success'] = true;

		return $result;
	}

	/**
	 * @return array
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function updateSchema()
	{
		$sqls    = $this->getSQL();
		$results = array();

		foreach ($sqls as $sql)
		{
			if (!isset($sql['sql']))
			{
				continue;
			}

			$this->db->setQuery($sql['sql']);

			try
			{
				$this->db->execute();
			}
			catch (Exception $e)
			{
				throw new KunenaSchemaException($e->getMessage(), $e->getCode());
			}

			$results[] = $sql;
		}

		return $results;
	}

	/**
	 * @param        $prefix
	 * @param   bool $reload reload
	 *
	 * @return mixed
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	protected function listTables($prefix, $reload = false)
	{
		if (isset($this->tables[$prefix]) && !$reload)
		{
			return $this->tables[$prefix];
		}

		$this->db->setQuery("SHOW TABLES LIKE " . $this->db->quote($this->db->getPrefix() . $prefix . '%'));

		try
		{
			$list = $this->db->loadColumn();
		}
		catch (Exception $e)
		{
			throw new KunenaSchemaException($e->getMessage(), $e->getCode());
		}

		$this->tables[$prefix] = array();

		foreach ($list as $table)
		{
			$table                   = preg_replace('/^' . $this->db->getPrefix() . '/', '', $table);
			$this->tables[$prefix][] = $table;
		}

		return $this->tables[$prefix];
	}

	/**
	 * @return DOMDocument
	 * @since Kunena
	 */
	public function createSchema()
	{
		$schema                     = new DOMDocument('1.0', 'utf-8');
		$schema->formatOutput       = true;
		$schema->preserveWhiteSpace = false;
		$schema->loadXML(KUNENA_INSTALL_SCHEMA_EMPTY);

		return $schema;
	}

	/**
	 * @param        $filename
	 * @param   bool $reload reload
	 *
	 * @return mixed
	 *
	 * @since Kunena
	 */
	public function getSchemaFromFile($filename, $reload = false)
	{
		static $schema = array();

		if (isset($schema[$filename]) && !$reload)
		{
			return $schema[$filename];
		}

		$schema[$filename]                     = new DOMDocument('1.0', 'utf-8');
		$schema[$filename]->formatOutput       = true;
		$schema[$filename]->preserveWhiteSpace = false;
		$schema[$filename]->validateOnParse    = false;
		$schema[$filename]->load($filename);

		return $schema[$filename];
	}

	/**
	 * @param   bool $reload reload
	 *
	 * @return boolean|DOMDocument
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function getSchemaFromDatabase($reload = false)
	{
		static $schema = false;

		if ($schema !== false && !$reload)
		{
			return $schema;
		}

		$tables = $this->listTables('kunena_');

		$schema     = $this->createSchema();
		$schemaNode = $schema->documentElement;

		foreach ($tables as $table)
		{
			if (preg_match('/_(bak|backup)$/', $table))
			{
				continue;
			}

			$tableNode = $schema->createElement("table");
			$schemaNode->appendChild($tableNode);

			$tableNode->setAttribute("name", $table);

			$this->db->setQuery("SHOW COLUMNS FROM " . $this->db->quoteName($this->db->getPrefix() . $table));

			try
			{
				$fields = $this->db->loadObjectList();
			}
			catch (Exception $e)
			{
				throw new KunenaSchemaException($e->getMessage(), $e->getCode());
			}

			foreach ($fields as $row)
			{
				$fieldNode = $schema->createElement("field");
				$tableNode->appendChild($fieldNode);

				if ($row->Key == "PRI")
				{
					$fieldNode->setAttribute("primary_key", "yes");
				}

				$fieldNode->setAttribute("name", $row->Field);
				$fieldNode->setAttribute("type", $row->Type);
				$fieldNode->setAttribute("null", (strtolower($row->Null) == 'yes') ? '1' : '0');

				if ($row->Default !== null)
				{
					$fieldNode->setAttribute("default", $row->Default);
				}

				if ($row->Extra != '')
				{
					$fieldNode->setAttribute("extra", $row->Extra);
				}
			}

			$this->db->setQuery("SHOW KEYS FROM " . $this->db->quoteName($this->db->getPrefix() . $table));

			try
			{
				$keys = $this->db->loadObjectList();
			}
			catch (Exception $e)
			{
				throw new KunenaSchemaException($e->getMessage(), $e->getCode());
			}

			$keyNode = null;

			foreach ($keys as $row)
			{
				if (!isset($keyNode) || $keyNode->getAttribute('name') != $row->Key_name)
				{
					$keyNode = $schema->createElement("key");
					$tableNode->appendChild($keyNode);

					$keyNode->setAttribute("name", $row->Key_name);

					if (!$row->Non_unique)
					{
						$keyNode->setAttribute("unique", (bool) !$row->Non_unique);
					}

					// If ($row->Comment != '') $keyNode->setAttribute("comment", $row->Comment);
				}

				$columns = $keyNode->getAttribute('columns');

				if (!empty($columns))
				{
					$columns .= ',';
				}

				$columns .= $row->Column_name;
				$columns .= ($row->Sub_part) ? '(' . $row->Sub_part . ')' : '';
				$keyNode->setAttribute('columns', $columns);
			}
		}

		return $schema;
	}

	/**
	 * @param $old
	 * @param $new
	 *
	 * @return DOMDocument|null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function getSchemaDiff($old, $new)
	{
		$old = $this->getDOMDocument($old);
		$new = $this->getDOMDocument($new);

		if (!$old || !$new)
		{
			return null;
		}

		// $old->validate();
		// $new->validate();
		$schema     = $this->createSchema();
		$schemaNode = $schema->documentElement;
		$schemaNode->setAttribute('type', 'diff');

		$nodes = $this->listAllNodes(array('new' => $new->documentElement->childNodes, 'old' => $old->documentElement->childNodes));

		foreach ($nodes as $nodeTag => $nodeList)
		{
			foreach ($nodeList as $nodeName => $nodeLoc)
			{
				$newNode = $this->getSchemaNodeDiff($schema, $nodeTag, $nodeName, $nodeLoc);

				if ($newNode)
				{
					$schemaNode->appendChild($newNode);
				}
			}
		}

		return $schema;
	}

	/**
	 * @param $nodeLists
	 *
	 * @return array
	 *
	 * @since Kunena
	 */
	protected function listAllNodes($nodeLists)
	{
		$list = array();

		foreach ($nodeLists as $k => $nl)
		{
			foreach ($nl as $n)
			{
				if ($n instanceof DOMAttr)
				{
					$list[$n->name][$k] = $n;
				}
				else
				{
					if ($n instanceof DOMElement)
					{
						$list[$n->tagName][$n->getAttribute('name')][$k] = $n;
					}
				}
			}
		}

		return $list;
	}

	/**
	 * @param $schema
	 * @param $tag
	 * @param $name
	 * @param $loc
	 *
	 * @return null
	 *
	 * @since Kunena
	 */
	public function getSchemaNodeDiff($schema, $tag, $name, $loc)
	{
		$node = null;

		if (!isset($loc['old']))
		{
			$node   = $schema->importNode($loc['new'], true);
			$action = $loc['new']->getAttribute('action');

			if (!$action)
			{
				$node->setAttribute('action', 'create');
			}

			$prev = $loc['new']->previousSibling;

			while ($prev && !($prev instanceof DOMElement))
			{
				$prev = $prev->previousSibling;
			}

			if ($prev && $tag == 'field' && $prev->tagName == 'field')
			{
				$node->setAttribute('after', $prev->getAttribute('name'));
			}

			return $node;
		}

		// Delete
		if (!isset($loc['new']))
		{
			if ($loc['old']->getAttribute('extra') == 'auto_increment')
			{
				// Only one field can have auto_increment, so give enough info to fix it!
				$node = $schema->importNode($loc['old'], false);
			}
			else
			{
				$node = $schema->createElement($tag);
				$node->setAttribute('name', $name);
			}

			$action = $loc['old']->getAttribute('action');

			if (!$action)
			{
				$action = 'unknown';
			}

			$node->setAttribute('action', $action);

			return $node;
		}

		$action     = $loc['old']->getAttribute('action');
		$childNodes = array();
		$childAll   = $this->listAllNodes(array('new' => $loc['new']->childNodes, 'old' => $loc['old']->childNodes));

		foreach ($childAll as $childTag => $childList)
		{
			foreach ($childList as $childName => $childLoc)
			{
				$childNode = $this->getSchemaNodeDiff($schema, $childTag, $childName, $childLoc);

				if ($childNode)
				{
					$childNodes[] = $childNode;
				}
			}

			if (!$action && count($childNodes))
			{
				$action = 'alter';
			}
		}

		// Primary key is always unique
		if ($loc['new']->tagName == 'key' && $loc['new']->getAttribute('name') == 'PRIMARY')
		{
			$loc['new']->setAttribute('unique', '1');
		}

		// Remove default='' from a field
		if ($loc['new']->tagName == 'field' && $loc['new']->getAttribute('default') === null)
		{
			$loc['new']->removeAttribute('default');
		}

		$attrAll = $this->listAllNodes(array('new' => $loc['new']->attributes, 'old' => $loc['old']->attributes));

		if (!$action)
		{
			foreach ($attrAll as $attrName => $attrLoc)
			{
				if ($attrName == 'primary_key')
				{
					continue;
				}

				if ($attrName == 'action')
				{
					continue;
				}

				if (!isset($attrLoc['old']->value) || !isset($attrLoc['new']->value) || str_replace(' ', '', $attrLoc['old']->value) != str_replace(' ', '', $attrLoc['new']->value))
				{
					$action = 'alter';
				}
			}
		}

		if ($childNodes || $action)
		{
			$node = $schema->importNode($loc['new'], false);

			foreach ($loc['new']->attributes as $attribute)
			{
				$node->setAttribute($attribute->name, $attribute->value);
			}

			if ($loc['old']->hasAttribute('from'))
			{
				$node->setAttribute('from', $loc['old']->getAttribute('from'));
			}

			$node->setAttribute('action', $action);

			$prev = $loc['new']->previousSibling;

			while ($prev && !($prev instanceof DOMElement))
			{
				$prev = $prev->previousSibling;
			}

			if ($prev && $tag == 'field' && $prev->tagName == 'field')
			{
				$node->setAttribute('after', $prev->getAttribute('name'));
			}

			foreach ($childNodes as $newNode)
			{
				$node->appendChild($newNode);
			}
		}

		return $node;
	}

	/**
	 * @param $input
	 *
	 * @return DOMDocument|DOMNode|mixed|null
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	protected function getDOMDocument($input)
	{
		if (($input instanceof DOMNode))
		{
			$schema = $input;
		}
		else
		{
			if ($input === KUNENA_INPUT_DATABASE)
			{
				$schema = $this->getSchemaFromDatabase();
			}
			else
			{
				if (is_string($input) && file_exists($input))
				{
					$schema = $this->getSchemaFromFile($input);
				}
				else
				{
					if (is_string($input))
					{
						$schema = new DOMDocument('1.0', 'utf-8');
						$schema->loadXML($input);
					}
				}
			}
		}

		if (!isset($schema) || $schema == false)
		{
			return null;
		}

		$schema->formatOutput       = true;
		$schema->preserveWhiteSpace = false;

		return $schema;
	}

	/**
	 * @param        $schema
	 * @param   bool $drop drop
	 *
	 * @return array
	 *
	 * @since Kunena
	 */
	public function getSchemaSQL($schema, $drop = false)
	{
		$tables = array();

		foreach ($schema->getElementsByTagName('table') as $table)
		{
			$str       = '';
			$tablename = $this->db->getPrefix() . $table->getAttribute('name');
			$fields    = array();

			switch ($action = $table->getAttribute('action'))
			{
				case 'unknown':
					break;
				case 'drop':
					$str .= 'DROP TABLE ' . $this->db->quoteName($tablename) . ';';
					break;

				//              case 'rename':
				case 'alter':

					if ($action == 'alter')
					{
						$str .= 'ALTER TABLE ' . $this->db->quoteName($tablename) . ' ' . "\n";
					}

					//                  else $str .= 'ALTER TABLE '.$this->db->quoteName($field->getAttribute('from')).' RENAME '.$this->db->quoteName($tablename).' '."\n";
					foreach ($table->childNodes as $field)
					{
						if ($field->hasAttribute('after'))
						{
							$after = ' AFTER ' . $this->db->quoteName($field->getAttribute('after'));
						}
						else
						{
							$after = ' FIRST';
						}

						switch ($action2 = $field->getAttribute('action'))
						{
							case 'unknown':
							case 'drop':
								if ($action2 == 'unknown' && !$drop)
								{
									if ($field->getAttribute('extra') == 'auto_increment')
									{
										// Only one field can have auto_increment, so fix the old field!
										$field->removeAttribute('extra');
										$field->setAttribute('action', 'alter');
									}
								}
								else
								{
									$fields[] = '	DROP ' . $this->getSchemaSQLField($field);
								}
								break;
							case 'rename':
								if ($field->tagName == 'key')
								{
									$fields[] = '	DROP KEY ' . $this->db->quoteName($field->getAttribute('from'));
									$fields[] = '	ADD ' . $this->getSchemaSQLField($field);
								}
								else
								{
									$fields[] = '	CHANGE ' . $this->db->quoteName($field->getAttribute('from')) . ' ' . $this->getSchemaSQLField($field, $after);
								}
								break;
							case 'alter':
								if ($field->tagName == 'key')
								{
									$fields[] = '	DROP KEY ' . $this->db->quoteName($field->getAttribute('name'));
									$fields[] = '	ADD ' . $this->getSchemaSQLField($field);
								}
								else
								{
									$fields[] = '	MODIFY ' . $this->getSchemaSQLField($field, $after);
								}
								break;
							case 'create':
								$fields[] = '	ADD ' . $this->getSchemaSQLField($field, $after);
								break;
							case '':
								break;
							default:
								echo ("Kunena Installer: Unknown action $tablename.$action2 on xml file<br />");
						}
					}

					if ($fields)
					{
						$str .= implode(",\n", $fields) . ';';
					}
					else
					{
						$str = '';
					}
					break;
				case 'create':
				case '':
					$action = 'create';
					$str    .= 'CREATE TABLE ' . $this->db->quoteName($tablename) . ' (' . "\n";

					foreach ($table->childNodes as $field)
					{
						$sqlpart = $this->getSchemaSQLField($field);

						if (!empty($sqlpart))
						{
							$fields[] = '	' . $sqlpart;
						}
					}

					$collation = $this->db->getCollation();

					if (!strstr($collation, 'utf8') && !strstr($collation, 'utf8mb4'))
					{
						$collation = 'utf8_general_ci';
					}

					if (strstr($collation, 'utf8mb4'))
					{
						$str .= implode(",\n", $fields) . " ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE {$collation};";
					}
					else
					{
						$str .= implode(",\n", $fields) . " ) DEFAULT CHARACTER SET utf8 COLLATE {$collation};";
					}
					break;
				default:
					echo ("Kunena Installer: Unknown action $tablename.$action on xml file<br />");
			}

			if (!empty($str))
			{
				$tables[$table->getAttribute('name')] = array('name' => $table->getAttribute('name'), 'action' => $action, 'sql' => $str);
			}
		}

		return $tables;
	}

	/**
	 * @param          $field
	 * @param   string $after after
	 *
	 * @return string
	 *
	 * @since Kunena
	 */
	protected function getSchemaSQLField($field, $after = '')
	{
		if (!($field instanceof DOMElement))
		{
			return '';
		}

		$str = '';

		if ($field->tagName == 'field')
		{
			$str .= $this->db->quoteName($field->getAttribute('name'));

			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ' ' . $field->getAttribute('type');
				$str .= ($field->getAttribute('null') == 1) ? ' NULL' : ' NOT NULL';
				$str .= ($field->hasAttribute('default')) ? ' default ' . $this->db->quote($field->getAttribute('default')) : '';
				$str .= ($field->hasAttribute('extra')) ? ' ' . $field->getAttribute('extra') : '';
				$str .= $after;
			}
		}
		else
		{
			if ($field->tagName == 'key')
			{
				if ($field->getAttribute('name') == 'PRIMARY')
				{
					$str .= 'PRIMARY KEY';
				}
				else
				{
					if ($field->getAttribute('unique') == 1)
					{
						$str .= 'UNIQUE KEY ' . $this->db->quoteName($field->getAttribute('name'));
					}
					else
					{
						$str .= 'KEY ' . $this->db->quoteName($field->getAttribute('name'));
					}
				}

				if ($field->getAttribute('action') != 'drop')
				{
					$str .= ($field->hasAttribute('type')) ? ' USING ' . $field->getAttribute('type') : '';
					$str .= ' (' . $field->getAttribute('columns') . ')';
				}
			}
		}

		return $str;
	}

	/**
	 * @param $dbschema
	 * @param $upgrade
	 *
	 * @throws KunenaSchemaException
	 * @since Kunena
	 */
	public function upgradeSchema($dbschema, $upgrade)
	{
		$dbschema = $this->getDOMDocument($dbschema);
		$upgrade  = $this->getDOMDocument($upgrade);

		if (!$dbschema || !$upgrade)
		{
			return;
		}

		// $dbschema->validate();
		// $upgrade->validate();

		$this->upgradeNewAction($dbschema, $upgrade->documentElement);
	}

	/**
	 * @param          $dbschema
	 * @param          $node
	 * @param   string $table table
	 *
	 * @since Kunena
	 */
	protected function upgradeNewAction($dbschema, $node, $table = '')
	{
		if (!$node)
		{
			return;
		}

		foreach ($node->childNodes as $action)
		{
			if (!($action instanceof DOMElement))
			{
				continue;
			}

			switch ($action->tagName)
			{
				case 'table':
					$this->upgradeNewAction($dbschema, $action, $action->getAttribute('name'));
					break;
				case 'version':
					if (!$this->version)
					{
						break;
					}

					$version = $action->getAttribute('version');
					$date    = $action->getAttribute('date');
					$this->upgradeNewAction($dbschema, $action, $table);
					break;
				case 'if':
					$table = $action->getAttribute('table');
					$field = $action->getAttribute('field');
					$key   = $action->getAttribute('key');

					if (!$field && !$key && !$this->findNode($dbschema, 'table', $table))
					{
						break;
					}

					if ($field && !$this->findNode($dbschema, 'field', $table, $field))
					{
						break;
					}

					if ($key && !$this->findNode($dbschema, 'key', $table, $key))
					{
						break;
					}

					$this->upgradeNewAction($dbschema, $action, $table);
					break;
				default:
					$this->upgradeAction($dbschema, $action, $table);
			}
		}
	}

	/**
	 * @param          $schema
	 * @param          $type
	 * @param          $table
	 * @param   string $field field
	 *
	 * @return null
	 * @since Kunena
	 */
	protected function findNode($schema, $type, $table, $field = '')
	{
		$rootNode = $schema->documentElement;

		foreach ($rootNode->childNodes as $tableNode)
		{
			if (!($tableNode instanceof DOMElement))
			{
				continue;
			}

			if ($tableNode->tagName == 'table' && $table == $tableNode->getAttribute('name'))
			{
				if ($type == 'table')
				{
					return $tableNode;
				}

				foreach ($tableNode->childNodes as $fieldNode)
				{
					if (!($fieldNode instanceof DOMElement))
					{
						continue;
					}

					if ($fieldNode->tagName == $type && $field == $fieldNode->getAttribute('name'))
					{
						return $fieldNode;
					}
				}
			}
		}

		return null;
	}

	/**
	 * @param          $dbschema
	 * @param          $node
	 * @param   string $table table
	 *
	 * @since Kunena
	 */
	protected function upgradeAction($dbschema, $node, $table = '')
	{
		if (!$table)
		{
			$table = $node->getAttribute('table');
		}

		if (!$table)
		{
			return;
		}

		$tag = $node->tagName;

		// Allow both formats: <drop key="id"/> and <key name="id" action="drop"/>
		if ($tag != 'table' && $tag != 'field' && $tag != 'key')
		{
			$action     = $tag;
			$attributes = array('field', 'key', 'table');

			foreach ($attributes as $attribute)
			{
				if ($node->hasAttribute($attribute))
				{
					$tag  = $attribute;
					$name = $node->getAttribute($attribute);
					break;
				}
			}

			if (!isset($name))
			{
				return;
			}
		}
		else
		{
			$action = $node->getAttribute('action');
			$name   = $node->getAttribute('name');
		}

		$to = $node->getAttribute('to');

		$dbnode = $this->findNode($dbschema, $tag, $table, $name);

		if (!$dbnode)
		{
			return;
		}

		if ($action)
		{
			$dbnode->setAttribute('action', $action);
		}

		if ($to)
		{
			if (!$dbnode->hasAttribute('from'))
			{
				$dbnode->setAttribute('from', $dbnode->getAttribute('name'));
			}

			$dbnode->setAttribute('name', $to);
		}
	}
}

/**
 * Class KunenaSchemaException
 * @since Kunena
 */
class KunenaSchemaException extends Exception
{
}
