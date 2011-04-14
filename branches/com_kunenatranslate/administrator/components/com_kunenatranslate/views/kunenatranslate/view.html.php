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

class KunenaTranslateViewKunenaTranslate extends JView
{
	function display($tpl = null){
		JToolBarHelper::title( JText::_( 'COM_KUNENATRANSLATE' ), 'generic.png' );
		$layout = $this->getLayout();
		if( $layout == 'form'){
			$labels = $this->get('Edit');
			$languages = JLanguage::getKnownLanguages();
			$this->assignRef('languages', array_keys($languages));
		}elseif ($layout == 'empty'){
			$languages = JLanguage::getKnownLanguages();
			$this->assignRef('languages', array_keys($languages));
			//get extension list
			$ext = $this->getModel('extension');
			$this->assignRef('extensionlist', $ext->getHtmlList() );
		}else{
			$labels = $this->get('Labels');
			$extension = JRequest::getInt('extension');
			$client = null;
			if(JRequest::getInt('oldext') == $extension)
				$client = JRequest::getWord('client');
			$this->assignRef('ext', $extension);
			//Extensionfilter
			$ext = $this->getModel('extension');
			$lists['extension'] = $ext->getHtmlList( false, 'class="inputbox" size="1" 
				onchange="this.form.submit()"' , $extension);
			//Clientfilter
			if(empty($extension)){
				$client[] = array ('text' => JText::_('COM_KUNENATRANSLATE_CHOOSE_EXTENSION') ,
							 'value' => '-1');
				$lists['client'] = JHTML::_('select.genericlist', $client, 'client');
			}else{
				$model = $this->getModel('Import');
				require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
				$lists['client'] = KunenaTranslateHelper::getClientList( $model->getExtensionFilename() , true, 
					'class="inputbox" size="1" onchange="this.form.submit()"', $client);
			}
			$this->assignRef('lists', $lists);
		}
		$this->assignRef('labels',$labels);

		parent::display($tpl);
	}
}