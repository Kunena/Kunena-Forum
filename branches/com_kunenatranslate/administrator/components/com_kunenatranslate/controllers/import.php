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

jimport('joomla.application.component.controller');

class KunenaTranslateControllerImport extends KunenaTranslateController
{
	function __construct($config = array()){
		parent::__construct($config);
		
	}
	
	function import(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('import');
		$res = $model->getImport();
		$msg = 'Import success';
		if($res == false){
			$msg = 'Import failed';
			$this->setRedirect('index.php?option=com_kunenatranslate&view=import' , $msg);
		}elseif(is_array($res)){
			$this->setMessage($msg);
			JRequest::setVar('exist', $res);
			JRequest::setVar('layout','exist');
			JRequest::setVar('view', 'import');
			parent::display();
		}else{
			$this->setRedirect('index.php?option=com_kunenatranslate&view=import' , $msg);
		}
	}
	
	function export(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('import');
		if(!$model->export()){
			$msg = 'Export failed';
			$this->setRedirect('index.php?option=com_kunenatranslate&view=import&task=exportview' , $msg);			
		}else{
			$msg = 'Export success';
			$this->setRedirect('index.php?option=com_kunenatranslate&view=import&task=exportview' , $msg);
		}
	}
	
	function update(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid	= JRequest::getVar('cid');
		if(!empty($cid)){
			$model = $this->getModel('import');
			if($model->update()){
				$msg = JText::_('Override sucess');
			}else{
				$msg = JText::_('Override failed');
			}
		}else{
			$msg = JText::_('No override choosed');
		}
		$link = 'index.php?option=com_kunenatranslate&view=import';
		$this->setRedirect($link,$msg);
	}
}