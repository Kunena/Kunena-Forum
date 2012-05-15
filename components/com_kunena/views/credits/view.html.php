<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Credits View
 */
class KunenaViewCredits extends KunenaView {
	function displayDefault($tpl = null) {

		$this->memberList = array(
				array('name'=>'Matias', 'url'=>'http://www.kunena.org/people/63-matias/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPER')),
				array('name'=>'coder4life', 'url'=>'http://www.kunena.org/people/10809-coder4life/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPER')),
				array('name'=>'xillibit', 'url'=>'http://www.kunena.org/people/1288-xillibit/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPER')),
				array('name'=>'fxstein', 'url'=>'http://www.kunena.org/people/64-fxstein/profile', 'title'=>JText::sprintf('COM_KUNENA_CREDITS_DEVELOPER_SPECIAL', 'Yamaha Star VMax').' <a href="http://www.starVmax.com/forum/">www.starVmax.com/forum/</a>'),
				array('name'=>'severdia', 'url'=>'http://www.kunena.org/people/114-severdia/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPER')),
				array('name'=>'810', 'url'=>'http://www.kunena.org/people/634-810/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR')),
				array('name'=>'svanschu', 'url'=>'http://www.kunena.org/people/2171-lda/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR')),
				array('name'=>'LittleJohn', 'url'=>'http://www.kunena.org/people/10133-littlejohn/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR')),
				array('name'=>'sozzled', 'url'=>'http://www.kunena.org/people/997-sozzled/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
				array('name'=>'Mortti', 'url'=>'http://www.kunena.org/people/151-mortti/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
				array('name'=>'Rich', 'url'=>'http://www.kunena.org/people/2198-rich/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
				array('name'=>'GoremanX', 'url'=>'http://www.kunena.org/people/1362-goremanx/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
				array('name'=>'CheechDogg', 'url'=>'http://www.kunena.org/people/9085-cheechdogg/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
				array('name'=>'Jiminimonka', 'url'=>'http://www.kunena.org/people/26335-jiminimonka/profile', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATOR')),
		);
		$this->thanks = JText::sprintf('COM_KUNENA_CREDITS_THANKS_PART_LONG', 'Beat', 'BoardBoss', 'madLyfe', 'infograf768','Joomla!', '<a href="http://www.kunena.org" target="_blank" rel="follow">www.kunena.org</a>').' '.JText::_('COM_KUNENA_CREDITS_THANKS');

		$this->_prepareDocument();

		parent::display ();
	}

	protected function _prepareDocument(){
		$this->setTitle(JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT'));

		// TODO: set keywords and description
	}

}