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

class TableTranslation extends JTable
{
	var $labelid = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_translation', 'labelid', $db);
	}
	
	function loadTranslations($id=null){
		$db = $this->getDBO();
		$where = null;
		if(!empty($id) && is_array($id)){
			$n = count($id);
			$where = ' WHERE ';
			foreach ($id as $k=>$v){
				$where .= 'labelid='.$v;
				if($n>1 && $k<$n-1) $where .= ' OR ';
			}
		}elseif (!empty($id) && is_int($id)){
			$where = ' WHERE labelid='.$id;
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
}