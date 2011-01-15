<?php
/**
 * @version		$Id: kunenareport.php 3965 2010-12-08 13:40:51Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Kunenareport Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerKunenareport extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=kunenareport';
	}
	
	function systemreport() {
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
	
}