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

class KunenaTranslateModelUpdate extends JModel{
	
	function __construct($config){
		parent::__construct($config);
		$cid = JRequest::getVar('cid', array(0) );
		$this->_id = $cid;
	}
	
	function getUpdate(){
		require_once (dirname(__FILE__).DS.'..'.DS.'helper.php');
		$helper = new KunenaTranslateHelper();
		//get the configdata for the client
		$client	= JRequest::getWord('client');
		$table = $this->getTable('Extension');
		$table->id = JRequest::getInt('extension');
		$helper->loadClientData($client, '', $table->getFilename());
		//scan for files		
		$helper->scan_dir();
		//get the array of folder/files to ignore
		$kill = array(0);
		$kills = $helper->clientdata->getElementByPath('kills');
		if(!empty($kills)){
			foreach ( $kills->children() as $child) {
				if($child->name() == 'kill'){
					$kill[] = $child->data();
				};
			}
			$helper->killfolder($kill);
		}
		$phplist = '';
		$phplist	= $helper->getfiles();
		$xmllist	= $helper->getfiles('xml');
		$langstrings = $helper->readphpxml($phplist,$xmllist);
		$labels = $this->_loadLabels($client);
		$res = $helper->getCompared($labels,$langstrings, JRequest::getVar('task') );
		return $res;
	}
	
	private function _loadLabels($client){
		$row =& $this->getTable('Label');
		$res = $row->loadLabels(null, $client);
		return $res;
	}
	
	function store($new, $client, $extension){
		$table =& $this->getTable('Label');
		$res = $table->store($new, $client, $extension);
		return $res;
	}
	
	function remove(){
		$table =& $this->getTable('Label');
		$res = $table->delete( $this->_id );
		return $res;
	}
}