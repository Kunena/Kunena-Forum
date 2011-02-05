<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Credits View
 */
class KunenaViewCredits extends KunenaView {
	function displayDefault($tpl = null) {

		$this->setTitle( JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT') );
		parent::display ();
	}
}