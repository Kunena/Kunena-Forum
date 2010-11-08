<?php
/**
 * @version $Id: kunenatranslate.php 3832 2010-11-01 01:32:24Z svens $
 * KunenaINIMaker Component
 * 
 * @package	Kunena INImaker
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class KunenaTranslateViewUpdate extends JView
{
	function display($tpl = null){
		JToolBarHelper::title( JText::_( 'Kunena Translate' ), 'generic.png' );
		
		$client		= array(
					array('text'=>'Frontend','value'=>'frontend'), 
					array('text'=>'Backend', 'value'=>'backend'),
					array('text'=>'Install', 'value'=>'install'),
					array('text'=>'Template', 'value'=>'template')
					);
		
		if($this->getLayout() == 'labels'){
			$labels =& $this->get('Update');
			$client = JRequest::getWord('client');
			//fb($client);
			$this->assignRef('labels',$labels);
			//fb($labels);
		}
		$this->assignRef('client', $client);

		fb($this);
		parent::display($tpl);
	}
}