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

class KunenaTranslateControllerUpdate extends KunenaTranslateController
{
	function __construct($config = array()){
		parent::__construct($config);
		
	}
	
	function update(){
		JRequest::setVar('layout','labels');
		JRequest::setVar('view', 'update');
		//JRequest::setVar('task','update');
		parent::display();
	}
	
	function save(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//get all labels and build an array again
		$labels = JRequest::getVar('labels');
		$labels = explode(";",$labels);
		//get client
		$client = JRequest::getWord('client');
		//get the array position of the wanted labels
		$cid = JRequest::getVar('cid');
		//select wanted labels
		foreach ($labels as $k=>$v){
			foreach ($cid as $value) {
				if ($value == $k) {
					$new[] = $v;
					break;
				};
			}
		}
		//get the model
		$model = $this->getModel('update');
		//store to database
		$res = $model->store($new, $client, 'label');
		if ($res == true)
			$msg = JText::_('Success');
		else
			$msg = Jtext::_('Fail');
		$this->setRedirect('index.php?option=com_kunenatranslate&view=update', $msg);
	}
}