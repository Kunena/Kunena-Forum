<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
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
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_CONFIGURATION'), 'config' );
		JToolbarHelper::spacer();
		JToolBarHelper::apply();
		JToolBarHelper::save('save');
		JToolBarHelper::divider();
		if (version_compare(JVERSION, '3.0', '>')) {
			JHtml::_('bootstrap.modal', 'settingModal');
			$title = JText::_('COM_KUNENA_RESET_CONFIG');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#settingModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'restore');
		} else {
			JHtml::_('moobootstrap.modal', 'settingModal');
			$title = JText::_('COM_KUNENA_RESET_CONFIG');
			$dhtml = "<a data-toggle=\"modal\" data-target=\"#settingModal\" class=\"toolbar\">
						<span class=\"icon-32-restore\" title=\"$title\"></span>
							$title</a>";
			$bar->appendButton('Custom', $dhtml, 'restore');
		}
		JToolbarHelper::spacer();
		//$bar->appendButton('Popup', 'restore', 'COM_KUNENA_RESET_CONFIG', 'index.php?option=com_kunena&amp;view=config&amp;layout=modal&amp;tmpl=component', 450, 200);
	}
}
