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
 * Kunena User Categories Table
 * Provides access to the #__kunena_user_categories table
 */
class TableKunenaUserCategories extends KunenaTable {
	public $user_id = null;
	public $category_id = null;
	public $role = null;
	public $allreadtime = null;
	public $subscribed = null;
	public $params = null;

	public function __construct($db) {
		parent::__construct ( '#__kunena_user_categories', array('user_id', 'category_id'), $db );
	}

	public function check() {
		$user = KunenaUserHelper::get($this->user_id);
		if (!$user->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_USER_INVALID', (int) $user->userid ) );
		}
		if ($this->category_id && !KunenaForumCategoryHelper::get($this->category_id)->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_CATEGORY_INVALID', (int) $category->id ) );
		}
		return ($this->getError () == '');
	}
}
