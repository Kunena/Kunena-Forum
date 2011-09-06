<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Category.User
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.user.helper');
kimport ('kunena.forum.category.user');
kimport ('kunena.forum.category.helper');

/**
 * Kunena Forum Category User Helper Class
 */
class KunenaForumCategoryUserHelper {
	// Global for every instance
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumCategoryUser object
	 *
	 * @access	public
	 * @param	identifier		The user category to load - Can be only an integer.
	 * @return	KunenaForumCategoryUser		The user category object.
	 * @since	1.7
	 */
	static public function get($category = null, $user = null, $reload = false) {
		if ($category instanceof KunenaForumCategory) {
			$category = $category->id;
		}
		$category = intval ( $category );
		$user = KunenaUserHelper::get($user);

		if ($category === null)
			return new KunenaForumCategoryUser (null, $user);

		if ($reload || empty ( self::$_instances [$user->userid][$category] )) {
			self::$_instances [$user->userid][$category] = array_pop(KunenaForumCategoryUserHelper::getCategories ( $category, $user ));
		}

		return self::$_instances [$user->userid][$category];
	}

	static public function getCategories($ids = false, $user=null) {
		$user = KunenaUserHelper::get($user);
		if ($ids === false) {
			return isset(self::$_instances[$user->userid]) ? self::$_instances[$user->userid] : self::getCategories(array_keys(KunenaForumCategoryHelper::getCategories()), $user);
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadCategories($ids, $user);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$user->userid][$id])) {
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	// Internal functions

	static protected function loadCategories($ids, $user) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$user->userid][$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_user_categories WHERE user_id={$db->quote($user->userid)} AND category_id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('category_id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumCategoryUser ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = $instance;
			} else {
				self::$_instances [$user->userid][$id] = new KunenaForumCategoryUser ($id, $user->userid);
			}
		}
		unset ($results);
	}
}