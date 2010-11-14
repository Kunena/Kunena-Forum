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
	
	function getUpdate(){
		require_once (dirname(__FILE__).DS.'..'.DS.'helper.php');
		$helper = new KunenaTranslateHelper();
		
		$this->client	= JRequest::getWord('client');
		switch ($this->client){
				case 'frontend':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena';
					$kill		= array('language' , 'svn', 'template');
					break;
				case 'backend':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena';
					$kill		= array('install', 'language', 'images', 'media', 'svn');
					break;
				case 'install':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' .DS. 'install';
					$kill		= array('media', 'svn');
					break;
				case 'template':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena' .DS. 'template' .DS. 'default';
					$kill		= array('language' , 'svn', 'images', 'css', 'media');
					break;
			}
		$helper->scan_dir($dir);
		$helper->killfolder($kill,&$helper->files);
		if($this->client != 'template') $phplist	= $helper->getfiles($helper->files);
		else $phplist = '';
		$xmllist	= $helper->getfiles($helper->files, 'xml');
		$langstrings = $helper->readphpxml($phplist,$xmllist);
		$labels = $this->_loadLabels();
		$res = $helper->getCompared($labels,$langstrings);
		return $res;
	}
	
	function _loadLabels(){
		$row =& $this->getTable('Label');
		$res = $row->loadLabels();
		return $res;
	}
	
	function store($new, $client, $tablename){
		$table =& $this->getTable('Label');
		$res = $table->store($new, $client, $tablename);
		
		return $res;
	}
}