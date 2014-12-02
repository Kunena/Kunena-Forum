<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');
/**
 * Kunena Polls
 * Provides access to the #__kunena_polls table
 */
class TableKunenaPolls extends KunenaTable {
	public $id = null;
	public $title = null;
	public $threadid = null;
	public $polltimetolive = null;

	public function __construct($db) {
		parent::__construct ( '#__kunena_polls', 'id', $db );
	}
}
