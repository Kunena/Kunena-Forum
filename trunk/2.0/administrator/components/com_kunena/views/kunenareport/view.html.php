<?php
/**
 * @version		$Id$
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
 * Kunenareport view for Kunena backend
 */
class KunenaViewKunenareport extends KunenaView {
	function display() {
		$this->kunenareport = $this->get('SystemReport');

		parent::display ();
	}
}