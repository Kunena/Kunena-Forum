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

class KunenaTranslateModelImport extends JModel{
	
	function getImport(){
		jimport('joomla.filesystem.file');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		$lang = JRequest::getVar('language');
		$client = JRequest::getVar('client');
		$clientall = JRequest::getBool('clientall');
		$extension = JRequest::getVar('extension');
		if($clientall == true){
			$clients = KunenaTranslateHelper::getClientList( self::getExtensionFilename() , false, null,null, true);
		}else{
			$clients[] = array('text'=>$client,
								'value' => $client);
		}
		foreach ($clients as $val){
			$client = $val['value'];
			//read ini file
			$inifile = $this->_getPathIni($client, $lang);
			if(!JFile::exists($inifile)){
				JError::raiseWarning('', 'File '.$inifile.' not exist');
				return false;
			}
			//read the ini file
			$ini = KunenaTranslateHelper::readINIfile($inifile);
			if(!$ini){
				JError::raiseWarning('', 'Failed reading: '.$inifile);
				return false;
			}
			//get the labels from DB
			$labels = $this->_loadLabels($client, $extension);
			//look for labels that are missing in DB
			$missing = $ini['nocomments'];
			if(!empty($labels)){
				foreach ( $missing as $kini=>$vini){
					foreach ($labels as $label) {
						if($label->label == $kini && $label->client == $client){
							unset($missing[$kini]);
						}
					}
				}
			}
			//add missing labels
			if(JRequest::getInt('addmissinglabel') == 1 && !empty($missing)){
				$missing = array_keys($missing);
				if(!$this->store($missing,$client, $extension)){
					JError::raiseWarning('','Saving Labels failed');
					return false;
				}
				//get the new labels from DB
				$labels = $this->_loadLabels($client, $extension);
			}else{
				$ini['nocomments'] = array_diff($ini['nocomments'],$missing);
			}
			//are there translations available for some labels?
			$table = $this->getTable('Translation');
			$trans = $table->loadTranslations(null,$lang, $client, $extension);
			$ntrans = null;
			if(empty($labels)){
				JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_LABELS_FOUND_NONE') );
				return false;
			}
			if(empty($trans) && !empty($labels)){
				foreach ($labels as $value) {
					foreach ($ini['nocomments'] as $inik=>$iniv) {
						if( $value->label == $inik){
							$ntrans[$lang][$value->id]['insert'] = $iniv;
						}
					}
				}
			}else{
				foreach ($trans as $value) {
					foreach ($ini['nocomments'] as $inik=>$iniv) {
						if($value->label == $inik){
							$exist[] = array('old' => $value,
											'new' => $iniv);
							unset($ini['nocomments'][$inik]);
						}
					}
				}
				foreach ($trans as $value) {
					foreach ($ini['nocomments'] as $iniv){
						$ntrans[$lang][$value->labelid]['insert'] = $iniv;
					}
				}
			}		
			//store the new translations
			if(!empty($ntrans)){
				if(!$table->store($ntrans, '', $client, $extension)){
					JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_TRANSLATION_SAVE_FAILED') );
					return false;
				}
			}
		}
		
		//give existing translation back, if they exist
		if(isset($exist))	return $exist;
		
		return true;
	}
	
	function getExtensionFilename(){
		$table = $this->getTable('Extension');
		$id = JRequest::getVar('extension');
		$table->load( $id );
		return $table->filename;
	}
	
	function _getPathIni($client,$lang){
		require_once (dirname(__FILE__).DS.'..'.DS.'helper.php');
		$helper = new KunenaTranslateHelper();
		$filename = self::getExtensionFilename();
		$inifile = $helper->loadClientData($client,$lang, $filename);
		return $inifile;
	}
	
	function _loadLabels($client, $extension){
		$row =& $this->getTable('Label');
		$res = $row->loadLabels(null, $client, $extension);
		return $res;
	}
	
	function store($new, $client, $extension){
		$table =& $this->getTable('Label');
		$res = $table->store($new, $client, $extension);
		
		return $res;
	}
	
	function update(){
		$lang	= JRequest::getWord('language');
		$up		= JRequest::getVar('new');
		$cid	= JRequest::getVar('cid');
		if(!empty($up) && !empty($cid)){
			foreach ($cid as $v){
				$trans[$lang][$v]['update'] = $up[$v];
			}
			$table = $this->getTable('Translation');
			if(!$table->store($trans)){
				JError::raiseWarning('','Saving Translations failed');
				return false;
			}
		}else{
			JError::raiseNotice('', JText::_('COM_KUNENATRANSLATE_OVERRIDE_NOTHINGTO'));
		}
		return true;		
	}
	
	function export(){
		jimport('joomla.filesystem.file');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		$client = JRequest::getWord('client', 'backend');
		$lang = JRequest::getVar('language', 'en-GB');
		$clientall = JRequest::getBool('clientall');
		$extension = JRequest::getVar('extension');
		if($clientall == true){
			$clients = KunenaTranslateHelper::getClientList( self::getExtensionFilename() , false, null,null, true);
		}else{
			$clients[] = array('text'=>$client,
								'value' => $client);
		}
		foreach ($clients as $val){
			$client = $val['value'];
			$ini = $this->_getPathIni($client, $lang);
			if(JFile::exists($ini)){
				if(!JFile::copy($ini, $ini.'.bak')){
					//JFile will throw an error
					return false;
				}
				if(!JFile::delete($ini)){
					//JFile will throw an error
					return false;
				}
			}
			$path = JPATH_COMPONENT_ADMINISTRATOR.DS.'dummy.ini'; 
			if(!JFile::copy($path, $ini)){
				//JFile will throw an error
				return false;
			}
			//get the data from DB
			$table = $this->getTable('Translation');
			$trans = $table->loadTranslations('',$lang, $client, $extension);
			if(empty($trans)){
				JError::raiseWarning('', JText::_('COM_KUNENATRANSLATE_DB_NOTRANSLATION'));
				return false;
			}
			$cont = '';
			foreach ($trans as $value) {
				$cont .= "{$value->label}=\"{$value->translation}\"\n";
			}
			if(!JFile::write( $ini, $cont)){
				JError::raiseWarning(21, 'JFile::write: '.JText::_('COM_KUNENATRANSLATE_FILE_WRITE_FAIL') . ": '$ini'");
				return false;
			}
		}
		
		return true;
	}
}