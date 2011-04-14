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

jimport('joomla.application.component.controller');

class KunenaTranslateControllerImport extends KunenaTranslateController
{
	function __construct($config = array()){
		parent::__construct($config);
		
	}
	
	function display(){
		$view = $this->getView('import', 'html', 'KunenaTranslateView');
		$view->setModel( $this->getModel('extension') );
		parent::display();
	}
	
	function import(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('import');
		$res = $model->getImport();
		$msg = 'Import success';
		JRequest::setVar('view', 'import');
		if($res == false){
			$msg = 'Import failed';
			JRequest::setVar('task', 'import');
		}elseif(is_array($res)){
			JRequest::setVar('exist', $res);
			JRequest::setVar('layout','exist');
		}else{
			JRequest::setVar('task', 'import');
		}
		JError::raiseNotice('', $msg);
		self::display();
	}
	
	function getClientList(){
		$mainframe = JFactory::getApplication();
		//get the config file
		$model = $this->getModel('import');
		require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		echo KunenaTranslateHelper::getClientList( $model->getExtensionFilename() , true);
		$mainframe->close();
	}
	
	function export(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('import');
		if(!$model->export()){
			$msg = 'Export failed';
			$this->setRedirect('index.php?option=com_kunenatranslate' , $msg);			
		}else{
			$msg = 'Export success';
			$this->setRedirect('index.php?option=com_kunenatranslate' , $msg);
		}
	}
	
	function update(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid	= JRequest::getVar('cid');
		if(!empty($cid)){
			$model = $this->getModel('import');
			if($model->update()){
				$msg = JText::_('COM_KUNENATRANSLATE_OVERRIDE_SUCCESS');
			}else{
				$msg = JText::_('COM_KUNENATRANSLATE_OVERRIDE_FAILED');
			}
		}else{
			$msg = JText::_('COM_KUNENATRANSLATE_OVERRIDE_NOCHOOSED');
		}
		JError::raiseNotice('', $msg);
		self::display();
	}
}