<?php
/**
 * @version		$Id: categories.php 3965 2010-12-08 13:40:51Z mahagr $
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
 * Kunena Credits Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerCredits extends KunenaController {
  public function __construct($config = array()) {
		parent::__construct($config);
			$app = JFactory::getApplication ();

	}
}