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
		$lang = JRequest::getVar('language');
		$client = JRequest::getVar('client');
		//read ini file
		$inifile = $this->_getPathIni($client, $lang);
		jimport('joomla.filesystem.file');
		if(!JFile::exists($inifile)){
			JError::raiseWarning('', 'File '.$inifile.' not exist');
			return false;
		}
		//read the ini file
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		$ini = KunenaTranslateHelper::readINIfile($inifile);
		if(!$ini){
			JError::raiseWarning('', 'Failed reading: '.$inifile);
			return false;
		}
		//get the labels from DB
		$labels = $this->_loadLabels();
		//look for labels that are missing in DB
		$missing = $ini['nocomments'];
		foreach ( $missing as $kini=>$vini){
			foreach ($labels as $label) {
				if($label->label == $kini && $label->client == $client){
					unset($missing[$kini]);
				}
			}
		}
		//add missing labels
		if(JRequest::getInt('addmissinglabel') == 1 && !empty($missing)){
			$missing = array_keys($missing);
			if(!$this->store($missing,$client)){
				JError::raiseWarning('','Saving Labels failed');
				return false;
			}
		}else{
			$ini['nocomments'] = array_diff($ini['nocomments'],$missing);
		}
		$labels = $this->_loadLabels();
		//are there translations available for some labels?
		$table = $this->getTable('Translation');
		$trans = $table->loadTranslations(null,$lang);
		$ntrans = null;
		if(empty($trans)){
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
			if(!$table->store($ntrans, '', $client)){
				JError::raiseWarning('','Saving Translations failed');
				return false;
			}
		}
		
		//give existing translation back, if they exist
		if(isset($exist))	return $exist;

		
		return true;
	}
	
	function _getPathIni($client,$lang){
		switch ($client){
				case 'frontend':
					$inifile	= JPATH_SITE .DS. 'language' .DS. $lang .DS. $lang.'.com_kunena.ini';
					break;
				case 'template':
					$inifile	= JPATH_SITE .DS. 'language' .DS. $lang .DS. $lang.'.com_kunena.tpl_default.ini';
					break;
				case 'backend':
					$inifile	= JPATH_ADMINISTRATOR .DS. 'language' .DS. $lang .DS. $lang.'.com_kunena.ini';
					break;
				case 'install':
					$inifile	= JPATH_ADMINISTRATOR .DS. 'language' .DS. $lang .DS. $lang.'.com_kunena.install.ini';
					break;
				case 'adminmenu':
					$inifile	= JPATH_ADMINISTRATOR .DS. 'language' .DS. $lang .DS. $lang.'.com_kunena.menu.ini';
					break;
		}
		return $inifile;
	}
	
	function _loadLabels(){
		$row =& $this->getTable('Label');
		$res = $row->loadLabels();
		return $res;
	}
	
	function store($new, $client){
		$table =& $this->getTable('Label');
		$res = $table->store($new, $client);
		
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
			JError::raiseNotice('', JText::_('Nothing to override'));
		}
		return true;		
	}
	
	function export(){
		$client = JRequest::getWord('client', 'backend');
		$lang = JRequest::getVar('language', 'en-GB');
		$ini = $this->_getPathIni($client, $lang);
		jimport('joomla.filesystem.file');
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
		$trans = $table->loadTranslations('',$lang, $client);
		if(empty($trans)){
			JError::raiseWarning('', JText::_('No translations found in DB'));
			return false;
		}
		$cont = '';
		foreach ($trans as $value) {
			$cont .= "{$value->label}=\"{$value->translation}\"\n";
		}
		if(!JFile::write( $ini, $cont)){
			JError::raiseWarning(21, 'JFile::write: '.JText::_('Unable to write file') . ": '$ini'");
			return false;
		}
		
		return true;
	}
}