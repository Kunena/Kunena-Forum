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

		$this->intro = JText::_('COM_KUNENA_CREDITS_INTRODUCTION');

		$this->memberList = array(
				array('name'=>'Matias Griese', 'url'=>'http://www.kunena.org/forum/profile/63-matias',
					'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPEMENT')),
				array('name'=>'Florian Dal Fitto', 'url'=>'http://www.kunena.org/forum/profile/1288-xillibit',
					'title'=>JText::_('COM_KUNENA_CREDITS_DEVELOPEMENT')),
				array('name'=>'Joshua Weiss', 'url'=>'http://www.kunena.org/forum/profile/10809-coder4life',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_DESIGN'), JText::_('COM_KUNENA_CREDITS_DEVELOPEMENT'))),
				array('name'=>'Jelle Kok', 'url'=>'http://www.kunena.org/forum/profile/634-810',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_DESIGN'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
				array('name'=>'Ron Severdia', 'url'=>'http://www.kunena.org/forum/profile/114-severdia',
					'title'=>JText::_('COM_KUNENA_CREDITS_DESIGN')),
				array('name'=>'Janich Rasmussen', 'url'=>'http://www.kunena.org/forum/profile/10133-littlejohn',
					'title'=>JText::_('COM_KUNENA_CREDITS_CONTRIBUTION')),
				array('name'=>'Sven Schultschik ', 'url'=>'http://www.kunena.org/forum/profile/2171-svanschu',
					'title'=>JText::_('COM_KUNENA_CREDITS_LANGUAGES')),
				array('name'=>'Michael Russell', 'url'=>'http://www.kunena.org/forum/profile/997-sozzled',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_DOCUMENTATION'))),
				array('name'=>'Richard Binder', 'url'=>'http://www.kunena.org/forum/profile/2198-rich',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
				array('name'=>'Sami Haaranen', 'url'=>'http://www.kunena.org/forum/profile/151-mortti',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
				array('name'=>'Joe Collins', 'url'=>'http://www.kunena.org/forum/profile/26335-jiminimonka',
					'title'=>JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
				array('name'=>'Oliver Ratzesberger', 'url'=>'http://www.kunena.org/forum/profile/64-fxstein',
					'title'=>JText::_('COM_KUNENA_CREDITS_FOUNDER')),

		);
		$this->thanks = JText::sprintf('COM_KUNENA_CREDITS_THANKS_FOR_ALL',
			'https://www.transifex.com/projects/p/Kunena',
			'https://github.com/Kunena/Kunena-2.0/graphs/contributors');

		$this->_prepareDocument();

		parent::display ();
	}

	protected function _prepareDocument(){
		$this->setTitle(JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT'));

		// TODO: set keywords and description
	}

}