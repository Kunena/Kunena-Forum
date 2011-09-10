<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . '/kunena.php');
kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');

/**
 * Kunena User Categories Table
 * Provides access to the #__kunena_user_categories table
 */
class TableKunenaUserCategories extends KunenaTable
{
	var $user_id = null;
	var $category_id = null;
	var $role = null;
	var $allreadtime = null;
	var $subscribed = null;
	var $params = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_user_categories', array('user_id', 'category_id'), $db );
	}

	function check() {
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