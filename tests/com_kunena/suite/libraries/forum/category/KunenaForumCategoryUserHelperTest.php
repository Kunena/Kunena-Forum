<?php
/**
 * Kunena Component
 * @package Kunena.UnitTest
 * @subpackage Utilities
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Test class for KunenaForumCategoryUserHelper.
 */
class KunenaForumCategoryUserHelperTest extends PHPUnit_Framework_TestCase {
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