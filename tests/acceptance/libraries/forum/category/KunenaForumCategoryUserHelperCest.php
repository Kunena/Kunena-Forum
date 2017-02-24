<?php
/**
 * Kunena Component
 * @package Kunena.UnitTest
 * @subpackage Utilities
 *
 * @copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Test class for KunenaForumCategoryUserHelper.
 */
class KunenaForumCategoryUserHelperCest extends PHPUnit_Framework_TestCase {
	/**
	 * Test get()
	 */
	public function testGet() {
		$admin = KunenaFactory::getUser('admin');

		$categoryuser = KunenaForumCategoryUserHelper::get(0, $admin->userid);
		$this->assertEquals(0, $categoryuser->category_id);
		$this->assertEquals($admin->userid, $categoryuser->user_id);
	}

	/**
	 * Test getCategories()
	 */
	public function testGetCategories() {
		$categories = KunenaForumCategoryHelper::getCategories();
		$categoryusers = KunenaForumCategoryUserHelper::getCategories();
		foreach ($categories as $category) {
			$this->assertTrue(isset($categoryusers[$category->id]));
			$this->assertEquals($category->id, $categoryusers[$category->id]->category_id);
		}
	}
}
