<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena rank backend
 */
class KunenaAdminViewRank extends KunenaView {
	public function display($tpl = null) {
		$this->setLayout('edit');
		$this->setToolbar();
		$this->state = $this->get('state');
		$this->rank_selected = $this->get('rank');
		$this->rankpath = $this->ktemplate->getRankPath();
		$this->listranks = $this->get('Rankspaths');
		parent::display ($tpl);
	}

	protected function setToolbar() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('ranks');
	}
}