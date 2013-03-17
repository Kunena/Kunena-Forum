<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAdminTemplate30 {

	public function initialize() {
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla30/layout.css' );
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla30/styles.css' );
	}
}