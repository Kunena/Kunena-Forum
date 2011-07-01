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

require_once (dirname ( __FILE__ ) . '/kunena.php');
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