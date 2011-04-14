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

class KunenaTranslateModelKunenaTranslate extends JModel {
	function __construct(){
		parent::__construct();
		$cid = JRequest::getVar('cid', array(0) );
		$this->setId($cid);
	}
	
	function setId($id){
		$this->_id = $id;
		$this->_data = NULL;
	}
	
	function getLabels(){
		$this->setId(NULL);
		$res = $this->_getLabels();
		return $res;
	}
	
	function getEdit(){
		$res = $this->_getLabels(true);
		return $res;
	}
	
	function _getLabels($edit=false){
		$extension = JRequest::getInt('extension');
		$client = null;
		if(JRequest::getInt('oldext') == $extension)
			$client = JRequest::getWord('client');
		$table = $this->getTable('Label'); 
		$table->extension = $extension;
		$table->client = $client;
		$labels = $table->loadLabels($this->_id, $edit);
		$table = $this->getTable('Translation');
		$table->extension = $extension;
		$table->client = $client;
		$trans = $table->loadTranslations($this->_id);

		if(!empty($labels)){
			foreach ($labels as $k=>$v){
				$labels[$k]->lang = '';
				if(!empty($trans)){
					foreach ($trans as $value) {
						if($v->id == $value->labelid)
							$labels[$k]->lang[] = $value;
					}
				}
			}
		}
		
		return $labels;
	}
	
	function store(){
		if(JRequest::getWord('add')){
			$label = JRequest::getVar('label');
			$client = JRequest::getVar('client');
			$extension = JRequest::getVar('extension');
		}else{
			$label=$client=$extension=null;
		}
		$languages = JRequest::getVar('knownlanguages', 'en-GB');
		$languages = explode(',',$languages);
		foreach ($languages as $v){
			$data[$v] = JRequest::getVar($v, null);
		}
		$table = $this->getTable('Translation');
		$res = $table->store($data, $label, $client, $extension);
		return $res;
	}
	
	function delete(){
		$table = $this->getTable('Label');
		return $table->delete($this->_id);
	}
}