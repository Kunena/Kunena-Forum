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
		if($this->getLayout() == 'form'){			
			$labels = $this->get('Edit');
			$languages = JLanguage::getKnownLanguages();
			$this->assignRef('languages', array_keys($languages));
fb($languages);
		}else{
			$labels = $this->get('Labels');
		}
		$this->assignRef('labels',$labels);
		
		fb($labels);
		//($this);
		parent::display($tpl);
	}
}