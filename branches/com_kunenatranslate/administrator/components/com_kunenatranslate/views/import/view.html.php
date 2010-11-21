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
		JToolBarHelper::title( JText::_( 'Kunena Translate' ).': 
			<small><small>'.JText::_('Import .ini').'</small></small>', 'generic.png' );
		if($this->getLayout()== 'exist'){
			$exist = JRequest::getVar('exist', array());
			$this->assignRef('exist', $exist);
		}else{
			$client		= array(
						array('text'=>'Frontend','value'=>'frontend'), 
						array('text'=>'Backend', 'value'=>'backend'),
						array('text'=>'Install', 'value'=>'install'),
						array('text'=>'Template', 'value'=>'template')
						);
			$client		= JHTML::_('select.genericlist', $client, 'client','', 'value','text');
			$lang		= JLanguage::getKnownLanguages();
			foreach ($lang as $v) {
				$langs[] = array('text'=>$v['tag'], 'value'=>$v['tag']);
			}
			$lang		= JHTML::_('select.genericlist', $langs, 'language', '', 'value', 'text');
	fb($langs);
						
			$this->assignRef('client', $client);
			$this->assignRef('lang',$lang);
		}
		parent::display($tpl);
	}
}