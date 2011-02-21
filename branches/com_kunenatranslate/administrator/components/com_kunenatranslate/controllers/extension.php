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

class KunenaTranslateControllerExtension extends KunenaTranslateController
{
	function __construct($config = array()){
		parent::__construct($config);
		
	}
	
	function add(){
		JRequest::setVar('layout', 'new');
		parent::display();
	}
	
	function doinstall(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('extension');
		if( $model->doinstall() ){
			$msg = JText::_('COM_KUNENATRANSLATE_INSTALL_SUCCESS');
		}else{
			$msg = JText::_('COM_KUNENATRANSLATE_INSTALL_FAILED');
		}
		$this->setRedirect('index.php?option=com_kunenatranslate&view=extension' , $msg);
	}
	
	function remove(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('extension');
		if( $model->uninstall() ){
			$msg = JText::_('COM_KUNENATRANSLATE_UNINSTALL_SUCCESS');
		}else{
			$msg = JText::_('COM_KUNENATRANSLATE_UNINSTALL_FAILED');
		}
		$this->setRedirect('index.php?option=com_kunenatranslate&view=extension' , $msg);
	}
}