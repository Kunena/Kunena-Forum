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

class CompKunenaTranslateHelper
{
	var $x = 0;
	var $files;
	
    public function scan_dir($dir) {
        if(!$temp = @scandir($dir)) {
        	echo "<font color='red'>".JText::_('Error! Files not found!')."</font><br>";
        	return;
        }
        for($i=0; $i < count($temp); $i++) {
        	if($temp[$i] != "." && $temp[$i] != "..") {
        		if(is_file("$dir/$temp[$i]"))
        			$this->files[] = "$dir/$temp[$i]";
        		elseif(is_dir("$dir/$temp[$i]"))
        			$stack[] = "$dir/$temp[$i]";
        	}
        	if((count($temp) - 1) == $i) {
        		if(isset($stack[$this->x])) {
        			$i = -1;
        			$dir = $stack[$this->x];
        			$temp = scandir($stack[$this->x]);
        			$this->x++;
        		}
        	}
        }
    }
    /* Used ti exlude special folders
     * 
     * @param $folderl array of foldernames to kill
     * @param $fulllist array of all files
     * @return $fulllist array
     */
    static public function killfolder($folderl, $fulllist){
    	$isArray	= is_array($folderl);
    	if (!$isArray) $folderl = array($folderl);
    	foreach ($fulllist as $k=>$fulll){
    		foreach ($folderl as $folder){
    			if (strpos($fulll, $folder) !== false) unset($fulllist[$k]);
    		}
    	}
    	return $fulllist;
    }
    
    /*
     * Get files per extension
     * 
     * @param $list array filelist
     * @param $extension string wanted extension Default php
     * @return $list array of wanted filenames
     */
    static public function getfiles($list, $extension='php'){
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
    	if(file_exists($file)){
    		$fres	= file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    		if($fres){
    			foreach ($fres as $k=>$v){
    				if($v{0} == '#' || $v{0} == ';'){
    					$comments[$k]	= $v;
    				}else{
    					list($key,$value) 	= explode('=',$v,2);
    					$res['nocomments'][$key]	= $value;
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
     * Read php and xml file into array
     * @param $php array of php files
     * @param $xml	array of xml files
     * @return $res array with all language strings
     */
    public function readphpxml($php='',$xmlf=''){
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
	            self::parseXmlGroup( &$xmlLinesParams, $params );
	            // get metadata state
	            $params = $xml->document->getElementByPath('state');
	            self::parseXmlGroup( &$xmlLinesParams, $params );
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
	
	/*
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
}