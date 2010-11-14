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
	
	function edit(){
		JRequest::setVar('layout','form');
		parent::display();
	}
	
	
}