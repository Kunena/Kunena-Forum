<?php
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class TableLabel extends JTable
{
	/** Primary Key
	 * @var int 
	 */
	var $id = null;
	/** unique key label
	 * @var label
	 */
	var $label = null;
	/** client
	 * @var client
	 */
	var $client = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_label', 'id', $db);
	}
	
	function loadLabels($id=null,$edit=false){
		$db = $this->getDBO();
		$where = null;
		if(!empty($id) && is_array($id)){
			$n = count($id);
			$where = ' WHERE ';
			foreach ($id as $k=>$v){
				$where .= 'id='.$v;
				if($n>1 && $n-1>$k) $where .= ' OR ';
			}
		}elseif (!empty($id) && is_int($id)){
			$where = ' WHERE id='.$id;
		}
		$query = 'SELECT * 
				FROM '. $this->_tbl
				.$where;
		$db->setQuery($query);
		
		$result = $db->loadObjectlist();
		if ($result) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function loadLabelsTrans(){
		$db = $this->getDBO();
		
		$query = 'SELECT l.id, l.label, l.client , t.lang
					FROM '.$this->_tbl.' as l
					LEFT JOIN #__kunenatranslate_translation as t
					ON l.id=t.labelid
					GROUP BY l.label';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function store($data, $client, $table){
		$db = $this->getDBO();
		$cdata = count($data);
		$values = '';
		if(is_array($data)){
			foreach ($data as $k=>$value) {
				$values .= "('', '{$value}', '{$client}')";
				if ($cdata != $k+1) $values .= ",";
			}
		}
		
		$query = "INSERT INTO #__kunenatranslate_{$table} ( id, label, client )
				VALUES {$values}";
		$db->setQuery( $query );
		if(!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}else{
			return true;
		} 
	}
}