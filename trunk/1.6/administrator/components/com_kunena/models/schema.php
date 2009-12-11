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

DEFINE('KUNENA_SCHEMA_FILE', KPATH_ADMIN.'/install/install.xml');
DEFINE('KUNENA_UPGRADE_SCHEMA_FILE', KPATH_ADMIN.'/install/upgrade.xml');
DEFINE('KUNENA_INSTALL_SCHEMA_EMPTY', '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE schema SYSTEM "'.KPATH_ADMIN.'/install/kunena16.dtd'.'"><schema></schema>');

jimport('joomla.application.component.model');

/**
 * Install Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelSchema extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	protected $schema = null;
	protected $xmlschema = null;
	protected $diffschema = null;
	protected $db = null;
	protected $sql = null;

	public function __construct()
	{
		parent::__construct();
		$this->db = JFactory::getDBO();
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

	public function getSchema()
	{
		if ($this->schema == null) $this->schema = $this->getSchemaFromDatabase();
		return $this->schema;
	}

	public function getXmlSchema($input=KUNENA_SCHEMA_FILE)
	{
		if ($this->xmlschema == null) $this->xmlschema = $schema = $this->getSchemaFromFile($input);
		return $this->xmlschema;
	}

	public function getDiffSchema($from=null, $to=null, $using=null)
	{
		if ($this->diffschema == null)
		{
			if (!$from) $from = $this->getSchema();
			if (!$to) $to = $this->getXmlSchema();
			$this->fromschema = $from;
			$this->toschema = $to;
			$this->diffschema = $this->getSchemaDiff($from, $to);
			$this->sql = null;
		}
		return $this->diffschema;
	}

	protected function getSQL()
	{
		if ($this->sql == null) {
			$diff = $this->getDiffSchema();
			echo "<pre>",htmlentities($diff->saveXML()),"</pre>";
			$this->sql = $this->getSchemaSQL($diff);
		}
		return $this->sql;
	}

	// helper function to update table schema
	public function updateSchemaTable($table)
	{
		$sql = $this->getSQL();
		if (!isset($sql[$table])) return;
		$this->db->setQuery($sql[$table]['sql']);
		$this->db->query();
		if ($this->db->getErrorNum()) throw new KunenaSchemeException($this->db->getErrorMsg(), $this->db->getErrorNum());
		$result = $sql[$table];
		return $result;
	}

	// helper function to update schema
	public function updateSchema()
	{
		$sqls = $this->getSQL();
		$results = array();
		foreach ($sqls as $sql)
		{
			if (!isset($sql['sql'])) continue;
			$this->db->setQuery($sql['sql']);
			$this->db->query();
			if ($this->db->getErrorNum()) throw new KunenaSchemeException($this->db->getErrorMsg(), $this->db->getErrorNum());
			$results[] = $sql;
		}
		return $results;
	}

	protected function listTables($prefix, $reload = false)
	{
		if (isset($this->tables[$prefix]) && !$reload) {
			return $this->tables[$prefix];
		}
		$this->db->setQuery("SHOW TABLES LIKE ".$this->db->quote($this->db->getPrefix().$prefix.'%'));
		$list = $this->db->loadResultArray();
		if ($this->db->getErrorNum()) throw new KunenaSchemeException($this->db->getErrorMsg(), $this->db->getErrorNum());
		$this->tables[$prefix] = array();
		foreach ($list as $table) {
			$table = preg_replace('/^'.$this->db->getPrefix().'/', '', $table);
			$this->tables[$prefix][] = $table;
		}
		return $this->tables[$prefix];
	}

	public function createSchema()
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

		$tables = $this->listTables('kunena_');

		$schema = $this->createSchema();
		$schemaNode = $schema->documentElement;
		foreach ($tables as $table) {
			if (strstr($table, 'backup')) continue;

			$tableNode = $schema->createElement("table");
			$schemaNode->appendChild($tableNode);

			$tableNode->setAttribute("name", $table);

			$this->db->setQuery( "SHOW COLUMNS FROM ".$this->db->nameQuote($this->db->getPrefix().$table));
			$fields = $this->db->loadObjectList();
			if ($this->db->getErrorNum()) throw new KunenaSchemeException($this->db->getErrorMsg(), $this->db->getErrorNum());
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

			$this->db->setQuery( "SHOW KEYS FROM ".$this->db->nameQuote($this->db->getPrefix().$table));
			$keys = $this->db->loadObjectList();
			if ($this->db->getErrorNum()) throw new KunenaSchemeException($this->db->getErrorMsg(), $this->db->getErrorNum());

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
		$schema = $this->createSchema();
		$schemaNode = $schema->documentElement;
		$schemaNode->setAttribute('type', 'diff');

		$nodes = $this->listAllNodes(array('old'=>$old->documentElement->childNodes, 'new'=>$new->documentElement->childNodes));
		foreach ($nodes as $nodeTag => $nodeList)
		{
			foreach ($nodeList as $nodeName => $nodeLoc)
			{
				$newNode = $this->getSchemaNodeDiff($schema, $nodeTag, $nodeName, $nodeLoc);
				if ($newNode) {
					$schemaNode->appendChild($newNode);
					$dupNode = $this->getDuplicateSibling($newNode);
					if ($dupNode) {
						if ($dupNode->getAttribute('action') == 'leftover') $schemaNode->removeChild($dupNode);
						if ($newNode->getAttribute('action') == 'leftover') $schemaNode->removeChild($newNode);
					}
				}
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

			$renamed = $this->getRenamedFrom($loc['new']);
			if ($renamed === false) $node->setAttribute('action', 'create');
			else if ($renamed == '') $node->setAttribute('action', 'replace');
			else
			{
				$node->setAttribute('from', $renamed);
				$node->setAttribute('action', 'rename');
			}

			$prev = $loc['new']->previousSibling;
			if ($prev && $prev->tagName == 'field') $node->setAttribute('after', $prev->getAttribute('name'));
			return $node;
		}
		// Delete
		if (!isset($loc['new']))
		{
			if($loc['old']->getAttribute('extra') == 'auto_increment')
			{
				// Only one field can have auto_increment, so give enough info to fix it!
				$node = $schema->importNode($loc['old'], false);
			}
			else
			{
				$node = $schema->createElement($tag);
				$node->setAttribute('name', $name);
			}
			$renamed = $this->getRenamedTo($loc['old']);
			if ($renamed === false) $node->setAttribute('action', 'deleted');
			else if ($renamed == '') $node->setAttribute('action', 'drop');
			else
			{
				$node->setAttribute('to', $renamed);
				$node->setAttribute('action', 'leftover');
			}
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

		// Primary key is always unique
		if ($loc['new']->tagName == 'key' && $loc['new']->getAttribute('name') == 'PRIMARY') $loc['new']->setAttribute('unique','1');
		// Remove default='' from a field
		if ($loc['new']->tagName == 'field' && $loc['new']->getAttribute('default') == '') $loc['new']->removeAttribute('default');

		$attributes = array();
		$attrAll = $this->listAllNodes(array('old'=>$loc['old']->attributes, 'new'=>$loc['new']->attributes));
		foreach ($attrAll as $attrName => $attrLoc)
		{
			if ($attrName == 'primary_key') continue;
			if ($attrName == 'action') continue;
			if (!isset($attrLoc['old']->value) || !isset($attrLoc['new']->value) || str_replace(' ', '', $attrLoc['old']->value) != str_replace(' ', '', $attrLoc['new']->value))
				$action = 'alter';
		}

		if (count($childNodes) || $action)
		{
			$node = $schema->importNode($loc['new'], false);
			$node->setAttribute('name', $name);
			$node->setAttribute('action', 'alter');
			$prev = $loc['new']->previousSibling;
			if ($prev && $prev->tagName == 'field') $node->setAttribute('after', $prev->getAttribute('name'));
			foreach ($childNodes as $newNode) {
				$node->appendChild($newNode);
				$dupNode = $this->getDuplicateSibling($newNode);
				if ($dupNode) {
					if ($dupNode->getAttribute('action') == 'leftover') $node->removeChild($dupNode);
					if ($newNode->getAttribute('action') == 'leftover') $node->removeChild($newNode);
				}
			}
		}
		return $node;
	}

	protected function getDuplicateSibling($node)
	{
		$parent = $node->parentNode;
		$name = $node->getAttribute('name');
		$from = $node->getAttribute('from');

		foreach ($parent->getElementsByTagName($node->tagName) as $node2)
		{
			if ($from && $from == $node2->getAttribute('name') && !$node->isSameNode($node2)) return $node2;
			if (!$from && $name == $node2->getAttribute('from') && !$node->isSameNode($node2)) return $node2;
		}
		return null;
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
		$tables = array();
		foreach ($schema->getElementsByTagName('table') as $table)
		{
			$str = '';
			$tablename = $this->db->getPrefix() . $table->getAttribute('name');
			$fields = array();
			switch ($action = $table->getAttribute('action'))
			{
				case 'deleted':
					if (!$drop) break;
				case 'drop':
					$str .= 'DROP TABLE '.$this->db->nameQuote($tablename).';';
					break;
				case 'leftover':
					break;
//				case 'rename':
				case 'alter':
					if ($action == 'alter') $str .= 'ALTER TABLE '.$this->db->nameQuote($tablename).' '."\n";
//					else $str .= 'ALTER TABLE '.$this->db->nameQuote($field->getAttribute('from')).' RENAME '.$this->db->nameQuote($tablename).' '."\n";
					foreach ($table->childNodes as $field)
					{
						if ($field->hasAttribute('after')) $after = ' AFTER '.$field->getAttribute('after');
						else $after = ' FIRST';

						switch ($action2 = $field->getAttribute('action'))
						{
							case 'deleted':
							case 'drop':
								if ($action2 == 'deleted' && !$drop)
								{
									if($field->getAttribute('extra') == 'auto_increment')
									{
										// Only one field can have auto_increment, so fix the old field!
										$field->removeAttribute('extra');
										$field->setAttribute('action', 'alter');
									}
									else break;
								}
								else
								{
									$fields[] = '	DROP '.$this->getSchemaSQLField($field);
									break;
								}
							case 'leftover':
								break;
							case 'rename':
								if ($field->tagName == 'key') break;
								$fields[] = '	CHANGE '.$this->db->nameQuote($field->getAttribute('from')).' '.$this->getSchemaSQLField($field, $after);
								break;
							case 'alter':
								if ($field->tagName == 'key') {
									$fields[] = '	DROP KEY '.$this->db->nameQuote($field->getAttribute('name'));
									$fields[] = '	ADD '.$this->getSchemaSQLField($field);
								} else
									$fields[] = '	MODIFY '.$this->getSchemaSQLField($field, $after);
								break;
							case 'create':
								$fields[] = '	ADD '.$this->getSchemaSQLField($field, $after);
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
					$str .= 'CREATE TABLE '.$this->db->nameQuote($tablename).' ('."\n";
					foreach ($table->childNodes as $field)
					{
						$sqlpart = $this->getSchemaSQLField($field);
						if (!empty($sqlpart)) $fields[] = '	'.$sqlpart;
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

	protected function getSchemaSQLField($field, $after='')
	{
		if (!is_a($field, 'DOMElement')) return '';

		$str = '';
		if ($field->tagName == 'field')
		{
			$str .= $this->db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ' '.$field->getAttribute('type');
				$str .= ($field->getAttribute('null') == 1) ? ' NULL' : ' NOT NULL';
				$str .= ($field->hasAttribute('default')) ? ' default '.$this->db->quote($field->getAttribute('default')) : '';
				$str .= ($field->hasAttribute('extra')) ? ' '.$field->getAttribute('extra') : '';
				$str .= $after;
			}
		}
		else if ($field->tagName == 'key')
		{
			if ($field->getAttribute('name') == 'PRIMARY') $str .= 'PRIMARY KEY';
			else if ($field->getAttribute('unique') == 1) $str .= 'UNIQUE KEY '.$this->db->nameQuote($field->getAttribute('name'));
			else $str .= 'KEY '.$this->db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ($field->hasAttribute('type')) ? ' USING '.$field->getAttribute('type') : '';
				$str .= ' ('.$field->getAttribute('columns').')';
			}
		}
		return $str;
	}

	protected function getRenamedFrom($node)
	{
		$tag = $node->tagName;
		if ($tag == 'table')
		{
			$table = $node->getAttribute('name');
			$field = '';
		}
		else
		{
			$table = $node->parentNode->getAttribute('name');
			$field = $node->getAttribute('name');
		}

		if (!isset($this->_renamed_from[$tag][$table][$field])) $from = false;
		else $from = $this->_renamed_from[$tag][$table][$field];
		return $from;
	}

	protected function getRenamedTo($node)
	{
		$tag = $node->tagName;
		if ($tag == 'table')
		{
			$table = $node->getAttribute('name');
			$field = '';
		}
		else
		{
			$table = $node->parentNode->getAttribute('name');
			$field = $node->getAttribute('name');
		}

		if (!isset($this->_renamed_to[$tag][$table][$field])) $to = false;
		else $to = $this->_renamed_to[$tag][$table][$field];
		return $to;
	}

	public function upgradeSchema($dbschema, $upgrade)
	{
		$dbschema = $this->getDOMDocument($dbschema);
		$upgrade = $this->getDOMDocument($upgrade);
		if (!$dbschema || !$upgrade) return;

		$this->db->validate();
		//$upgrade->validate();

		$schemaNode = $upgrade->documentElement;

		foreach ($schemaNode->childNodes as $action)
		{
			if (!is_a($action, 'DOMElement')) continue;
			if ($action->tagName == 'drop' || $action->tagName == 'rename') $this->upgradeAction($action);
			else if ($action->tagName == 'table') $this->upgradeTableAction($action);
		}
	}

	protected function upgradeTableAction($table)
	{
		foreach ($table->childNodes as $action)
		{
			if (!is_a($action, 'DOMElement')) continue;
			if ($action->tagName == 'drop' || $action->tagName == 'rename') $this->upgradeAction($action, $table->getAttribute('name'));
		}
	}

	protected function upgradeAction($action, $table='')
	{
		$tag = $action->tagName;
		if (!$table) $table = $action->getAttribute('table');
		if (!$table) return;
		$column = $action->getAttribute('field');
		$key = $action->getAttribute('key');
		$to = $action->getAttribute('to');
		if ($tag == 'drop') $to = '';

		if ($column) {
			$this->_renamed_from['field'][$table][$to] = $column;
			$this->_renamed_to['field'][$table][$column] = $to;
		}
		if ($key) {
			$this->_renamed_from['key'][$table][$to] = $key;
			$this->_renamed_to['key'][$table][$key] = $to;
		}
		if (!$column && !$key) {
			$this->_renamed_from['table'][$to][''] = $table;
			$this->_renamed_to['table'][$table][''] = $to;
		}
		//echo "$tag $table: $column$key -> $to<br>";
	}

}
class KunenaSchemeException extends Exception {}