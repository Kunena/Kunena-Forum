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

class TableExtension extends JTable
{
	/** Primary Key
	 * @var int 
	 */
	var $id = null;
	/** key name
	 * @var name
	 */
	var $name = null;
	/** filename
	 * @var filename
	 */
	var $filename = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_extension', 'id', $db);
	}
	
	function loadList(){
		$db =& $this->getDBO();

		$query = 'SELECT *'
		. ' FROM '.$this->_tbl;
		$db->setQuery( $query );

		if ($result = $db->loadAssocList( )) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function exist($name){
		if($this->loadByName($name)){
			return true;
		}
		return false;		
	}
	
	function getFilename(){
		$db= & $this->getDBO();
		$query = 'SELECT filename'
				.' FROM '.$this->_tbl
				.' WHERE '.$this->_tbl_key.'='.$this->_db->Quote($this->id);
		$db->setQuery($query);
		
		if ($result = $db->loadResult( )) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function loadByName($name){
		$this->reset();
		$db=& $this->getDBO();
		
		$query = 'SELECT * '
				.' FROM '.$this->_tbl
				.' WHERE name='.$this->_db->Quote($name);
		$db->setQuery($query);
		
		if ($result = $db->loadAssoc( )) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
}