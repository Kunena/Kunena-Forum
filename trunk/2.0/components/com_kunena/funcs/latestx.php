<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();

class CKunenaLatestX {
	public $allow = 0;
	public $topics = array();
	public $page = 1;
	public $totalpages = 1;
	public $embedded = null;
	public $actionDropdown = array();
	public $actionMove = false;

	public $category = null;
	public $subcategories = null;

	function __construct($func, $page = 0) {
		// My latest is only available to users
		if (! $this->user->id && $func == "mylatest") {
			return;
		}
	}

	function _getCategorySubscriptions() {
		$this->categories = array();
		if (isset($this->total)) return;

		$uname = $this->config->username ? 'name' : 'username';

		$query = "SELECT COUNT(*) FROM #__kunena_user_categories WHERE user_id={$this->db->Quote($this->user->id)} AND subscribed=1";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$query = "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$this->db->Quote($this->user->id)} AND subscribed=1";
		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
		$this->categories = KunenaForumCategoryHelper::getCategories($this->db->loadResultArray ());
		if (KunenaError::checkDatabaseError()) return;
	}

	function getCategoriesSubscriptions() {
		if (isset($this->total)) return;
		$this->columns--;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');
		$this->latestcategory = false;
		$this->_getCategorySubscriptions();
	}

	function displayFlatCats() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if ($this->myprofile->ordering != '0') {
			$this->topic_ordering = $this->myprofile->ordering == '1' ? 'DESC' : 'ASC';
		} else {
			$this->topic_ordering = $this->config->default_sort == 'asc' ? 'ASC' : 'DESC'; // Just to make sure only valid options make it
		}

		CKunenaTools::loadTemplate('/threads/flat_cats.php');
	}
}
