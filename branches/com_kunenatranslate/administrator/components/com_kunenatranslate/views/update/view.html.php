<?php
/**
 * @version $Id$
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
		JToolBarHelper::title( JText::_( 'Kunena Translate' ).': <small><small>'.
			JText::_('Update Labels').'</small></small>', 'generic.png' );
		
		require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
		$client = KunenaTranslateHelper::getClientList(true);
		
		if($this->getLayout() == 'labels'){
			$labels =& $this->get('Update');
			$client = JRequest::getWord('client');
			$this->assignRef('labels',$labels);
		}
		$this->assignRef('client', $client);

		parent::display($tpl);
	}
}