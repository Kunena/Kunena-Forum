<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena config backend
 */
class KunenaAdminViewConfig extends KunenaView {
	function displayDefault() {
		$this->lists = $this->get('Configlists');

		// Only set the toolbar if not modal
		if ($this->getLayout() !== 'modal') {
			$this->setToolBarDefault();
		}

		$this->display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_CONFIGURATION'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'restore', 'COM_KUNENA_RESET_CONFIG', 'index.php?option=com_kunena&amp;view=config&amp;layout=modal&amp;tmpl=component', 450, 200);
	}
}
