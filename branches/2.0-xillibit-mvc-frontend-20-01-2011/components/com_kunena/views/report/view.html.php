<?php
/**
 * @version		$Id: view.html.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Report View
 */
class KunenaViewReport extends KunenaView {
	function displayDefault($tpl = null) {
		$this->id = JRequest::getInt('id', 0);
		$this->catid = JRequest::getInt('catid', 0);

		parent::display ();
	}
}