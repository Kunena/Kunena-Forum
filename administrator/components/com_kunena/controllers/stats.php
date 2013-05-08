<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Backend Stats Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerStats extends KunenaController {
	protected $baseurl = null;

	public function __construct() {
		parent::__construct();
		$this->baseurl = 'index.php?option=com_kunena&view=stats';
	}
}