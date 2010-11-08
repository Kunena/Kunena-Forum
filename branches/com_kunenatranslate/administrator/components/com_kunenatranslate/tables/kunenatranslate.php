<?php
/**
 * @version $Id:  $
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class TableKunenaTranslate extends JTable
{
	/** Primary Key
	 * @var int
	 */
	var $id = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_label', 'id', $db);
	}
	
	function loadLabels(){
		$db = $this->getDBO();
		
		$query = 'SELECT * FROM '. $this->_tbl;
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