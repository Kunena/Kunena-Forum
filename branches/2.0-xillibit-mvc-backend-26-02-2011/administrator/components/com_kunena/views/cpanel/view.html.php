<?php
/**
 * @version		$Id: view.html.php 4192 2011-01-15 12:06:53Z mahagr $
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
 * About view for Kunena cpanel
 */
class KunenaAdminViewCpanel extends KunenaView {
	function display() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );

		parent::display ();
	}
}
