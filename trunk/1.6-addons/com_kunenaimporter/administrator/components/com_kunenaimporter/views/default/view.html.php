<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id: $
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class KunenaimporterViewDefault extends JView {

	function display($tpl = null) {
		$app =& JFactory::getApplication();
		$params =& getKunenaImporterParams();
		$this->assign('params', $params);

		$importer =& $this->getModel('import');
		$exporter =& $this->getModel('export_'.$params->get('extforum'));
		if (is_object($exporter)) {

			$exporter->checkConfig();
			$errormsg = $exporter->getError();
			if (!$errormsg) {
				$options = $exporter->getExportOptions($importer);
				$this->assign('options', $options);
			}
			$messages = $exporter->getMessages();
			$this->assign('messages', $messages);
		} else {
			$errormsg = 'Exporter not found!';
		}
		$this->assign('errormsg', $errormsg);
		if (!$errormsg) {
			JToolBarHelper::custom('import', 'upload', 'upload', JText::_('Import'), false);
			JToolBarHelper::custom('truncate', 'delete', 'delete', JText::_('Truncate'), false);
			JToolBarHelper::divider();
		}
		JToolBarHelper::save('save', JText::_('Save Settings'));
		JToolBarHelper::cancel('cancel', JText::_('Reset'));

		parent::display($tpl);
	}
}
?>
