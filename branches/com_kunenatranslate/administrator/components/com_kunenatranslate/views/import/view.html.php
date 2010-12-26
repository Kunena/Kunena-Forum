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
		if(JRequest::getVar('task') == 'importview'){
			$text = JText::_('Import');
		}else{
			$text = JText::_('Export');
		}
		JToolBarHelper::title( JText::_( 'Kunena Translate' ).': 
			<small><small>'.$text.'</small></small>', 'generic.png' );
		if($this->getLayout()== 'exist'){
			$exist = JRequest::getVar('exist', array());
			$this->assignRef('exist', $exist);
		}else{
			require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
			$client = KunenaTranslateHelper::getClientList(true);
			$lang		= JLanguage::getKnownLanguages();
			foreach ($lang as $v) {
				$langs[] = array('text'=>$v['tag'], 'value'=>$v['tag']);
			}
			$lang		= JHTML::_('select.genericlist', $langs, 'language', '', 'value', 'text');
			$this->assignRef('lang',$lang);
			$this->assignRef('client', $client);
		}
		parent::display($tpl);
	}
}