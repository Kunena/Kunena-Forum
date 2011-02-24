<?php
/**
 * @version $Id$
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
 * Misc View
 */
class KunenaViewMisc extends KunenaView {
	function displayDefault($tpl = null) {
		$params = JComponentHelper::getParams( 'com_kunena' );
		$this->header = $params->get( 'page_title' );
		$this->body = $params->get( 'body' );
		$this->format = $params->get( 'body_format' );
		$this->setTitle ( $this->header );
		$this->display ();
	}
}