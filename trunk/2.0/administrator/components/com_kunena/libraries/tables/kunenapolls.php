<?php
/**
 * @version $Id$
 * Kunena Component - TableKunenaPolls class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');
/**
 * Kunena Polls
 * Provides access to the #__kunena_polls table
 */
class TableKunenaPolls extends KunenaTable
{
	var $id = null;
	var $title = null;
	var $threadid = null;
	var $polltimetolive = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_polls', 'id', $db );
	}
}