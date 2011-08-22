<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration.JXtended
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_ADMIN . '/libraries/integration/joomla15/access.php';

/**
 * @deprecated in K2.0
 */
class KunenaAccessJXtended extends KunenaAccessJoomla15 {
	public function __construct() {
		$this->priority = -1;
	}
}