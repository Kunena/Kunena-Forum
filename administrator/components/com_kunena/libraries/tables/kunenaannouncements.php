<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . '/kunena.php');
/**
 * Kunena Announcements
 * Provides access to the #__kunena_announcements table
 */
class TableKunenaAnnouncements extends KunenaTable
{
	var $id = null;
	var $title = null;
	var $created_by = null;
	var $sdescription = null;
	var $description = null;
	var $created = null;
	var $published = null;
	var $ordering = null;
	var $showdate = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_announcement', 'id', $db );
	}

	function check() {
		if ($this->created_by) {
			$user = KunenaUserHelper::get($this->created_by);
			if (!$user->exists()) {
				$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_USER_INVALID', (int) $user->userid));
			}
		} else {
			$this->created_by = KunenaUserHelper::getMyself()->userid;
		}
		if (!$this->created) $this->created = JFactory::getDate()->toMySQL();
		$this->title = trim($this->title);
		if (!$this->title) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_TITLE' ) );
		}
		$this->sdescription = trim($this->sdescription);
		$this->description = trim($this->description);
		if (!$this->sdescription) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_DESCRIPTION' ) );
		}
		return ($this->getError () == '');
	}
}
