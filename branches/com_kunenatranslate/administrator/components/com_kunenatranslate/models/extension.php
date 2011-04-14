<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class KunenaTranslateModelExtension extends JModel {
	
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
		$cid = JRequest::getVar('cid', array(0) );
		$this->_id = $cid[0];
		$this->_data = NULL;
	}
	
	/**
	 * Returns a list of installed config files
	 * @return array
	 */
	function getList(){
		$table = $this->getTable('Extension');
		return $table->loadList();
	}
	
	/**
	 * Returns a HTML selectlist of installed config files
	 * @return string
	 */
	function getHtmlList($hide_none = false, $special = NULL, $selected = NULL){
		if(!$hide_none)
			$listarray[] = array ('text' => JText::_('COM_KUNENATRANSLATE_EXTENSION_NONESELECTED') ,
							 'value' => '-1');
		$list = self::getList();
		foreach ($list as $value) {
			$listarray[] = array('text' => $value['name'],
							'value' => $value['id']);
		}
		return JHTML::_('select.genericlist', $listarray, 'extension', $special, 'value', 'text', $selected);
	}
	
	/**
	 * returns the filename of the wanted extension
	 * @return string
	 */
	function getFilename(){
		$table = $this->getTable('Extension');
		$table->id = JRequest::getInt('extension');
		return $table->getFilename();
	}
	
	/**
	 * Installs the given xml file
	 * @return bool
	 */
	function doinstall(){
		$file = JRequest::getVar('install_xml', null, 'files', 'array' );
		
		// 	Make sure that file uploads are enabled in php
		if(!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_FILEUPLOAD_RESTRICTED'));
			return false;
		}
		
		if(!is_array($file) ) {
			JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_NOFILE'));
			return false;
		}
		
		if($file['error'] || $file['size'] < 1){
			JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_INSTALLERROR'));
			return false;
		}
		
		if($file['type'] != 'text/xml'){
			JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_NOXML'));
			return false;
		}
				
		jimport('joomla.filesystem.file');
		if( !JFile::upload($file['tmp_name'], JPATH_COMPONENT_ADMINISTRATOR.DS.'conf'.DS.$file['name']) ){
			return false;
		}
		
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		$xml = KunenaTranslateHelper::loadXML( $file['name'] );
		$name = $xml->document->children();
		$name = $name[0]->name() == 'name' ? $name[0]->data() : JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_UNVALID_XML'));
		
		$table = $this->getTable('Extension');
		if($table->exist($name)){
			JError::raiseNotice('', JText::_('COM_KUNENATRANSLATE_EXIST'));
			JError::raiseNotice('', JText::_('COM_KUNENATRANSLATE_FILE_OVERWRITEN'));
			return true;
		}
		$store = array ( 'name'=> $name, 'filename' => $file['name']);
		if( !$table->bind($store) ){
			JError::raiseWarning( '' , $table->getError() );
			return false;
		}
		if( !$table->check() ){
			JError::raiseWarning( '' , $table->getError() );
			return false;
		}
		if( !$table->store() ){
			JError::raiseWarning( '' , $table->getError() );
			return false;
		}
		
		return true;
	}
	/**
	 * Uninstalls one config file
	 * @return bool
	 */
	function uninstall(){
		$table = $this->getTable('Extension');
		//Get the data to delete the file
		if( !$table->load( $this->_id) )
			return false;
		jimport('joomla.filesystem.file');
		$file = JPATH_COMPONENT_ADMINISTRATOR.DS.'conf'.DS.$table->filename;
		if( JFile::exists($file) ){
			if( !JFile::delete($file) ){
				JError::raiseWarning( '', JText::_('COM_KUNENATRANSATE_DELETE_FILE_FAILED') );
				return false;
			}
		}
		//now delete in DB
		if( !$table->delete( $this->_id ) ){
			JError::raiseWarning( '', JText::_('COM_KUNENATRANSATE_DELETE_DB_FAILED') );
			return false;
		}

		return true;
	}
}