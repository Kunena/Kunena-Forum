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

jimport('joomla.application.component.view');

class KunenaTranslateViewUpdate extends JView
{
	function display($tpl = null){
		JToolBarHelper::title( JText::_( 'Kunena Translate' ).': <small><small>'.
			JText::_('Update Labels').'</small></small>', 'generic.png' );
		
		if($this->getLayout() == 'labels'){
			$labels =& $this->get('Update');
			$client = JRequest::getWord('client');
			$extension = JRequest::getInt('extension');
			$this->assignRef('labels',$labels);
			$this->assignRef('client', $client);
			$this->assignRef('extension', $extension);
		}else{
			//get extension list
			$ext = $this->getModel('extension');
			$this->assignRef('extensionlist', $ext->getHtmlList() );
		}

		parent::display($tpl);
	}
}