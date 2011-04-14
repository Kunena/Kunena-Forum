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

class KunenaTranslateViewImport extends JView
{
	function display($tpl = null){
		if(JRequest::getVar('task') == 'import' || 
			JRequest::getVar('show') == 'import'){
			$text = JText::_('COM_KUNENATRANSLATE_IMPORT_IMPORT');
		}else{
			$text = JText::_('COM_KUNENATRANSLATE_IMPORT_EXPORT');
		}
		JToolBarHelper::title( JText::_( 'COM_KUNENATRANSLATE' ).': 
			<small><small>'.$text.'</small></small>', 'generic.png' );
		if($this->getLayout()== 'exist'){
			$exist = JRequest::getVar('exist', array());
			$this->assignRef('exist', $exist);
		}else{
			//get extension list
			$ext = $this->getModel('extension');
			$this->assignRef('extensionlist', $ext->getHtmlList() );
			$lang		= JLanguage::getKnownLanguages();
			foreach ($lang as $v) {
				$langs[] = array('text'=>$v['tag'], 'value'=>$v['tag']);
			}
			$lang		= JHTML::_('select.genericlist', $langs, 'language', '', 'value', 'text');
			$this->assignRef('lang',$lang);
		}
		parent::display($tpl);
	}
}