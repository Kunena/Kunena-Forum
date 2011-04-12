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

class KunenaTranslateHelper
{
	var $x = 0;
	var $files = null;
	var $clientdata = null;
	var $type = null;
	var $dir = null;
	
    public function scan_dir() {
        if(is_file($this->dir)){
        	$this->files[] = "$this->dir";
        	return;
        }
    	if(!$temp = @scandir($this->dir)) {
        	JError::raiseWarning('', JText::_('Error! Files not found!'));
        	return;
        }
        for($i=0; $i < count($temp); $i++) {
        	if($temp[$i] != "." && $temp[$i] != "..") {
        		if(is_file("$this->dir/$temp[$i]"))
        			$this->files[] = "$this->dir/$temp[$i]";
        		elseif(is_dir("$this->dir/$temp[$i]"))
        			$stack[] = "$this->dir/$temp[$i]";
        	}
        	if((count($temp) - 1) == $i) {
        		if(isset($stack[$this->x])) {
        			$i = -1;
        			$this->dir = $stack[$this->x];
        			$temp = scandir($stack[$this->x]);
        			$this->x++;
        		}
        	}
        }
    }
    /* Used ti exlude special folders
     * 
     * @param $folderl array of foldernames to kill
     * @return true if success
     */
    public function killfolder($folderl){
    	$isArray	= is_array($folderl);
    	if (!$isArray) $folderl = array($folderl);
    	foreach ($this->files as $k=>$fulll){
    		foreach ($folderl as $folder){
    			if (strpos($fulll, $folder) !== false) unset($this->files[$k]);
    		}
    	}
    	return true;
    }
    
    /*
     * Get files per extension
     * 
     * @param $list array filelist
     * @param $extension string wanted extension Default php
     * @return $list array of wanted filenames
     */
    public function getfiles($extension='php'){
    	$list = $this->files;
    	foreach ($list as $k=>$v){
    		$pathinfo = pathinfo($v);
    		if($pathinfo['extension'] != $extension)
    			unset($list[$k]);
    	}
    	return $list;
    }
    
    /*
     * Get all installed languages
     * 
     * @return array Options for JHTMLSelect::genericlist()
     */
    static public function getlanguages(){
    	$languages['frontend']	= JLanguage::getKnownLanguages(JPATH_SITE);
    	$languages['backend']	= JLanguage::getKnownLanguages(JPATH_ADMINISTRATOR);
    	foreach ($languages as $language){
    		foreach ($language as $k=>$lang){
    			$lan[$k]	= $lang['name'];  
    		}
    	}
    	foreach ($lan as $k=>$v){
    		$options[]	= array('value'=>$k, 'text'=>$v);
    	}
    	return $options;
    }
    /*
     * Read ini file into a array
     * @param $file string filepath/name
     * @return $res array with comments and nocomments
     */
    static public function readINIfile($file){
    	$comments= array();
    	$res = NULL;
    	if(file_exists($file)){
    		$fres	= file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    		if($fres){
    			foreach ($fres as $k=>$v){
    				if($v{0} == '#' || $v{0} == ';'){
    					$comments[$k]	= $v;
    				}else{
    					list($key,$value) 	= explode('=',$v,2);
    					$res['nocomments'][$key]	= trim($value, "\"");
    				}
    			}
    			$res['comments']			= $comments;
    		}
    	}
    	return $res;
    }
    
	static public function parseXmlGroup( &$xmlLinesParams, $params ) {
	    if (!empty($params)) {
	        $params = $params->children();
	        // get param
	        foreach($params as $param) {
	            $label = trim($param->attributes('label'));
	            if (!empty($label)) $xmlLinesParams[strtoupper($label)] = $label;
	
	            $descr = trim($param->attributes('description'));
	        	if (!empty($descr)) $xmlLinesParams[strtoupper($descr)] = $descr;
	
	            // get param options
	            if ($param->children()) {
	                foreach($param->children() as $option) {
	                    $data = trim($option->data());
	                    if (!empty($data) && $option->name()=='option') $xmlLinesParams[strtoupper($data)] = $data;
	                }
	            }
	        }
	    }
	}
	
	/*
	 * Get the Labels for the admin Menu
	 * @param &$xmlLinesParams referenz to save all the strings in it
	 * @param $menu administration piece of the XML 
	 */
	static private function parseAdminMenu( &$xmlLinesParams, $menu ){
		if(!empty($menu)){
			//mainmenu item
			$mmenu = $menu->getElementByPath('menu');
			$mmenu = trim($mmenu->data());
			if (!empty($mmenu)) $xmlLinesParams[strtoupper($mmenu)] = $mmenu;
			//submenu
			if($smenu = $menu->getElementByPath('submenu')){
				$smenu = $smenu->children();
				for( $i=0; $i< count($smenu); $i++){
					$label = trim($smenu[$i]->data());
					if (!empty($label)) $xmlLinesParams[strtoupper($label)] = $label;
				}	
			}
		}
	}
	
    /*
     * Read php and xml file into array
     * @param $php array of php files
     * @param $xml	array of xml files
     * @return $res array with all language strings
     */
    public function readphpxml($php='',$xmlf=''){
    	$lines = '';
    	//Pattern to find the translateable string
		$pattern =  "/JText::_[[:space:]]*\(\s*\'(.*)\'\s*\)|JText::_[[:space:]]*\(\s*\"(.*)\"\s*\)".
		            "|JText::sprintf[[:space:]]*\(\s*\"(.*)\"|JText::sprintf[[:space:]]*\(\s*\'(.*)\'".
		            "|JText::printf[[:space:]]*\(\s*\'(.*)\'|JText::printf[[:space:]]*\(\s*\"(.*)\"".
					"|KText::_[[:space:]]*\(\s*\'(.*)\'|KText::_[[:space:]]*\(\s*\"(.*)\"/iU";
		if(!empty($php)){
			foreach ($php as $k=>$v){
				$str		= file_get_contents($v);
				preg_match_all($pattern, $str, $matches , PREG_SET_ORDER );
				foreach ($matches as $match){
					foreach ($match as $k=>$v){
						if( $k!=0 && !empty($match[$k]) ){
							$lines['php'][$match[$k]]	= $match[$k];
						}
					}
				}
			}
		}
		if(!empty($xmlf)){
			foreach ($xmlf as $xmlfi) {
				$xml = JFactory::getXMLParser('Simple');
	            $xml->loadFile($xmlfi);
				// get params
	            $params = $xml->document->getElementByPath('params');
	            self::parseXmlGroup( $xmlLinesParams, $params );
	            // get metadata state
	            $params = $xml->document->getElementByPath('state');
	            self::parseXmlGroup( $xmlLinesParams, $params );
	            //get admin menu
	            $menu = $xml->document->getElementByPath('administration');
	            self::parseAdminMenu( $xmlLinesParams, $menu);
	            // get metadata layout
	            $layout = $xml->document->getElementByPath('layout');
	            if (!empty($layout)) {
	                $title = trim($layout->attributes('title'));
	                if (!empty($title)) $xmlLinesParams[strtoupper($title)] = $title;
	                foreach($layout->children() as $item) {
	                    $data = $item->data();
	                    $data = trim(str_replace( array('<![CDATA[', ']]>'),'!!!',$data));
	                    if (!empty($data)) {
	                       $xmlLinesParams[strtoupper($data)] = $data;
	                    }
	                }
	            }
			}
			$lines['xml']	= $xmlLinesParams;
		}
		return $lines;
	}
	/*
	 * Compare the ini file with the found languagestrings
	 * show which are new, which will be deleted
	 * @param array ini
	 * @param array php/xml
	 * @return array of new and old strings
	 */
	static public function CompareArray($ini,$phpxml){
		$px = $phpxml;
		$ini2 	= $ini['nocomments'];
		//look if there are new strings in php/xml
		$key = array_keys($ini['nocomments']);
		foreach ($phpxml as $pk=>$vk){
			foreach ($key as $v){
				$dkey = array_keys($phpxml[$pk],$v);
				foreach ($dkey as $vkey){
					unset($phpxml[$pk][$vkey]);
				}
			}
			//look if there are old strings in teh ini file
			foreach ($vk as $v){
				if( array_key_exists($v,$ini2) ) unset($ini2[$v]);
			}
		}
		$newfile	= array_diff_key($ini['nocomments'],$ini2);
		if($phpxml['php']) $newfile	= array_diff_key($newfile,$phpxml['php']);
		if($phpxml['xml']) $newfile	= array_diff_key($newfile,$phpxml['xml']);
		$res['newfile']	= $newfile;
		$res['new']		= $phpxml;
		$res['old']		= $ini2;
		return $res;
	}
	
     /**
	 * Compare the DB with the found languagestrings
	 * show which are new, which are old
	 * @param array db
	 * @param array php/xml
	 * @return array of new and old strings
	 */
	static public function getCompared($dbase,$phpxml,$task){
		$res = array( 
						'old' => array(),
						'new' => array()
		);
		$px = $phpxml;
		// TODO find better way to do the compare
		//look if there are new strings in php/xml
		if($task == 'update'){
			if(!empty($dbase)){
				foreach ($phpxml as $pk=>$vk){
					foreach ($dbase as $dk=>$v){
						$dkey = array_keys($phpxml[$pk],$v->label);
						foreach ($dkey as $vkey){
							unset($phpxml[$pk][$vkey]);
						}
					}	
				}
			}
			foreach ($phpxml as $pxv){
				if(!empty($pxv)){
					foreach ($pxv as $value) {
						$res['new'][] = $value;
					}
				}
			}
		}//look if there are old strings in teh ini file
		elseif($task == 'old' && $dbase!=false){
			fb($dbase);
			fb($px);
			foreach ($dbase as $dk=>$dv){
				foreach ($px as $pv) {
					if(is_array($pv)){
						foreach ($pv as $ppv) {
							if($dv->label == $ppv)
								unset($dbase[$dk]);
						}
					}
				}
			}
			foreach ($dbase as $value) {
				$res['old'][$value->id] = $value->label;
			}
		}
		return $res;
	}
	
	/**
	 * Show the files on screen
	 * @param array
	 * @return $res string
	 */
	static public function getRdyArray($arr , $opt=''){
		if($opt =='phpxml'){
			foreach ($arr as $v) {
				foreach ($v as $vv){
					$res[] = $vv.'="'.$vv.'"';
				}
			}
		}else{
			foreach($arr as $k=>$v){
				$res[] = $k.'='.$v;
			}
		}
		return $res;
	}
	
	/**
	 * Loads the config XML file and returns the client list
	 * 
	 * @param boolean $htmllist false for array, true for HTML Selectlist
	 * @return array or html selectlist
	 */
	static public function getClientList($filename, $htmllist=false){
		$client = array();
		$xml = self::loadXML($filename);
		$files = $xml->document->getElementbyPath('files');
		foreach ($files->children() as $child){
			$client[] = array('text' => $child->attributes('name'),
						'value' => $child->name() );
		}
		if($htmllist){
			$client = JHTML::_('select.genericlist', $client, 'client','', 'value','text');
		}
		return $client;
	}
	
	/**
	 * Loads the XML config file and returns a Object
	 * 
	 * @param $filename string Name of the XML-File
	 * @return object JSimpleXML Object of the XML-File 
	 */
	static function loadXML($filename='kunena.xml'){
		$xml = new JSimpleXML();
		$xml->loadFile(JPATH_COMPONENT_ADMINISTRATOR.DS.'conf'.DS.$filename);
		
		return $xml;
	}
	
	/**
	 * Load the specific data for an client
	 * 
	 * @param string $client
	 * @param string language code
	 * @return string inifile path if param $lang is set
	 */
	public function loadClientData($client, $lang=null, $filename){
		$xml = $this->loadXML($filename);
		$this->type = $xml->document->attributes('type');
		$this->clientdata = $xml->document->getElementbyPath('files/'.$client);
		if($lang){
			$ini = self::createIniPath($lang);
			return $ini;
		}
		else self::createPath();
	}
	
	/**
	 * creates the path where to search and inifile location
	 */
	protected function createPath(){
		$area = $this->clientdata->attributes('type');
		$path = $this->clientdata->getElementByPath('dir');
		if($area == 'administrator'){
			switch ($this->type){
				case 'component':
					$dir = JPATH_ADMINISTRATOR.DS.'components'.DS.$path->data();
					break;
			}	
		}elseif ($area == 'site'){
			switch ($this->type){
				case 'component':
					$dir = JPATH_SITE.DS.'components'.DS.$path->data();
					break;
			}
		}else{
			JError::raiseWarning('', JText::sprintf( 'Invalid clienttype %s', $this->type) );
			$this->dir = false;
			return ;
		}
		$this->dir = $dir;
	}
	
	/**
	 * create & give back of the path to the languagefile
	 * @param $lang language code
	 * @return mixed string with path or false
	 */
	protected function createIniPath($lang){
		$area = $this->clientdata->attributes('type');
		$ini = $this->clientdata->getElementByPath('ini');
		if($area == 'administrator'){
			$dir = JPATH_ADMINISTRATOR.DS.'language'.DS.$lang.DS.$lang.'.'.$ini->data();
		}elseif ($area == 'site'){
			$dir = JPATH_SITE.DS.'language'.DS.$lang.DS.$lang.'.'.$ini->data();
		}else{
			JError::raiseWarning('', JText::sprintf( 'Invalid clienttype %s', $this->type) );
			return false;
		}
		return $dir;		
	}
}