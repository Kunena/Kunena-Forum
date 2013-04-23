<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
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
				array('name'=>'Matias Griese', 'url'=>'http://www.kunena.org/forum/profile/63-matias', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPEMENT')),
				array('name'=>'Florian Dal Fitto', 'url'=>'http://www.kunena.org/forum/profile/1288-xillibit', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPEMENT')),
				array('name'=>'Joshua Weiss', 'url'=>'http://www.kunena.org/forum/profile/10809-coder4life', 'title'=>JText::_('COM_KUNENA_CREDITS_DESIGN_AND_DEV')),
				array('name'=>'Jelle Kok', 'url'=>'http://www.kunena.org/forum/profile/634-810', 'title'=>JText::_('COM_KUNENA_CREDITS_DESIGN_AND_TESTING')),
				array('name'=>'Ron Severdia', 'url'=>'http://www.kunena.org/forum/profile/114-severdia', 'title'=>JText::_('COM_KUNENA_CREDITS_DESIGN')),
				array('name'=>'Janich Rasmussen', 'url'=>'http://www.kunena.org/forum/profile/10133-littlejohn', 'title'=>JText::_('COM_KUNENA_CREDITS_CONTRIBUTION')),
				array('name'=>'Sven Schultschik ', 'url'=>'http://www.kunena.org/forum/profile/2171-lda', 'title'=>JText::_('COM_KUNENA_CREDITS_LANGUAGES')),
				array('name'=>'Michael Russell', 'url'=>'http://www.kunena.org/forum/profile/997-sozzled', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATION_AND_DOC')),
				array('name'=>'Richard Binder', 'url'=>'http://www.kunena.org/forum/profile/2198-rich', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATION_AND_TESTING')),
				array('name'=>'Sami Haaranen', 'url'=>'http://www.kunena.org/forum/profile/151-mortti', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATION_AND_TESTING')),
				array('name'=>'Joe Collins', 'url'=>'http://www.kunena.org/forum/profile/26335-jiminimonka', 'title'=>JText::_('COM_KUNENA_CREDITS_MODERATION_AND_TESTING')),
				array('name'=>'Oliver Ratzesberger', 'url'=>'http://www.kunena.org/forum/profile/64-fxstein', 'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPER_SERVER_MAINTENANCE')),

		);
		$this->thanks = JText::_('COM_KUNENA_CREDITS_THANKS_PART_LONG');

		$this->_prepareDocument();

		parent::display ();
	}

	protected function _prepareDocument(){
		$this->setTitle(JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT'));

		// TODO: set keywords and description
	}

}