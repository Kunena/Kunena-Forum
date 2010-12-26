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
		
		$this->client	= JRequest::getWord('client');
		switch ($this->client){
				case 'backend':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena';
					$kill		= array('archive', 'images', 'install', 'language',  'media', 'svn', 'kunena.xml');
					$temp		= false;
					break;	
				case 'install':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' .DS. 'install';
					$kill		= array('media', 'svn');
					$temp		= false;
					break;
				case 'backendmenu':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' .DS. 'kunena.xml';
					$kill		= array();
					$temp		= false;
					break;
				case 'frontend':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena';
					$kill		= array('language' , 'svn', 'template');
					$temp		= false;
					break;
				case 'tpl_default':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena' .DS. 'template' .DS. 'default';
					$kill		= array('language' , 'svn', 'images', 'css', 'media');
					$temp		= true;
					break;
				case 'tpl_example':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena' .DS. 'template' .DS. 'example';
					$kill		= array('language' , 'svn', 'images', 'css', 'media');
					$temp		= true;
					break;
				case 'tpl_skinner':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena' .DS. 'template' .DS. 'skinner';
					$kill		= array('language' , 'svn', 'images', 'css', 'media');
					$temp		= true;
					break;
			}
		$helper->scan_dir($dir);
		$helper->killfolder($kill,&$helper->files);
		$phplist = '';
		$phplist	= $helper->getfiles($helper->files);
		$xmllist	= $helper->getfiles($helper->files, 'xml');
		$langstrings = $helper->readphpxml($phplist,$xmllist);
		$labels = $this->_loadLabels();
		$res = $helper->getCompared($labels,$langstrings, JRequest::getVar('task') );
		return $res;
	}
	
	private function _loadLabels(){
		$row =& $this->getTable('Label');
		$res = $row->loadLabels(null, $this->client);
		return $res;
	}
	
	function store($new, $client){
		$table =& $this->getTable('Label');
		$res = $table->store($new, $client);
		return $res;
	}
	
	function remove(){
		$table =& $this->getTable('Label');
		$res = $table->delete( $this->_id );
		return $res;
	}
}