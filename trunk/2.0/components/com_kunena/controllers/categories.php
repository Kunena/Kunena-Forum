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

jimport ( 'joomla.application.component.controller' );
kimport ( 'kunena.forum.category.helper' );

/**
 * Kunena Categories Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerCategories extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct ( $config );
		$this->baseurl = 'index.php?option=com_kunena&view=categories';
	}

	public function allread() {

	}
}