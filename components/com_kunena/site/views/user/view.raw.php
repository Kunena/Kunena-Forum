<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Users View
 */
class KunenaViewUser extends KunenaView {
	function displayList($tpl = null) {
		$response = array();
		if ($this->me->exists()) {
			$users = $this->get ( 'Items' );
			foreach ($users as $user) {
				if ($this->config->username) $response[] = $user->username;
				else $response[] = $user->name;
			}
		}
		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding( 'application/json' );
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$this->getName().'.'.$this->getLayout().'.json"' );

		echo json_encode( $response );
	}
}
