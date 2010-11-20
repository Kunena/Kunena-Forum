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
		$model = $this->getModel('import');
		fb($model);
		$res = $model->getImport();
		fb($res);
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
}