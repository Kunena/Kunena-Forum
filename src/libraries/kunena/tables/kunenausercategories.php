<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena User Categories Table
 * Provides access to the #__kunena_user_categories table
 */
class TableKunenaUserCategories extends KunenaTable
{
	public $user_id = null;
	public $category_id = null;
	public $role = null;
	public $allreadtime = null;
	public $subscribed = null;
	public $params = null;

	/**
	 * @param   string $db
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_user_categories', array('user_id', 'category_id'), $db);
	}

	/**
	 * @return boolean
	 */
	public function check()
	{
		$user = KunenaUserHelper::get($this->user_id);
		if (!$user->exists())
		{
			$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_USER_INVALID', (int) $user->userid));
		}

		$category = KunenaForumCategoryHelper::get($this->category_id);
		if ($this->category_id && !$category->exists())
		{
			$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_CATEGORY_INVALID', (int) $category->id));
		}

		return ($this->getError() == '');
	}
}
