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
		JToolBarHelper::title( JText::_( 'Kunena Translate' ), 'generic.png' );
		$layout = $this->getLayout();
		if( $layout == 'form'){
			$labels = $this->get('Edit');
			$languages = JLanguage::getKnownLanguages();
			$this->assignRef('languages', array_keys($languages));
		}elseif ($layout == 'empty'){
			$languages = JLanguage::getKnownLanguages();
			$this->assignRef('languages', array_keys($languages));
			require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
			$client = KunenaTranslateHelper::getClientList(true);
			$this->assignRef('client',$client);
		}else{
			$labels = $this->get('Labels');
		}
		$this->assignRef('labels',$labels);

		parent::display($tpl);
	}
}