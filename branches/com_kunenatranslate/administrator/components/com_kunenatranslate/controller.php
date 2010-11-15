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

class KunenaTranslateController extends JController
{
	function __construct($config = array()){
		parent::__construct($config);
	}
	
	function update(){
		JRequest::setVar('view','update');
		parent::display();
	}
	
	function edit(){
		JRequest::setVar('layout','form');
		JRequest::setVar('task', 'edit');
		parent::display();
	}
	
	function save(){
		$model = $this->getModel();
		if($model->store())
			$msg = 'Labels saved';
		else 
			$msg = 'Label saving failed';
		$link = 'index.php?option=com_kunenatranslate';
		$this->setRedirect($link,$msg);
	}
	
}