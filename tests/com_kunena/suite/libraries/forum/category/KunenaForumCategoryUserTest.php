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
 * Test class for KunenaForumCategoryUser.
 */
class KunenaForumCategoryUserTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test new KunenaForumCategoryUser()
	 */
	public function testNew() {
		$admin = KunenaFactory::getUser('admin');
		$categoryuser = new KunenaForumCategoryUser(0, $admin);
		$this->assertInstanceOf('KunenaForumCategoryUser', $categoryuser);
		$this->assertFalse($categoryuser->exists());
		$this->assertEquals(0, $categoryuser->category_id);
		$this->assertEquals($admin->userid, $categoryuser->user_id);
	}

	/**
	 * Test category user creation
	 *
	 * @return KunenaForumCategoryUser
	 */
	public function testCreate() {
		$admin = KunenaFactory::getUser('admin');
		$categoryuser = KunenaForumCategoryUser::getInstance(0, $admin->userid);
		$this->assertEquals(0, $categoryuser->category_id);
		$this->assertEquals($admin->userid, $categoryuser->user_id);

		$categoryuser->role = 1;
		$this->assertTrue($categoryuser->save());
		$this->assertTrue($categoryuser->exists());
		$this->assertEquals(0, $categoryuser->category_id);
		$this->assertEquals($admin->userid, $categoryuser->user_id);
		$this->assertEquals(1, $categoryuser->role);

		// Check that object was saved to database
		$categoryuser2 = new KunenaForumCategoryUser();
		$this->assertTrue($categoryuser2->load($categoryuser->category_id, $categoryuser->user_id));
		$this->assertEquals($categoryuser, $categoryuser2);

		// Check that instance remains the same
		$categoryuser2 = KunenaForumCategoryUser::getInstance(0, $admin->userid);
		return $categoryuser2;
	}

	/**
	 * Test load()
	 *
	 * @param KunenaForumCategoryUser $categoryuser
	 * @return KunenaForumCategoryUser
	 * @depends testCreate
	 */
	public function testLoad(KunenaForumCategoryUser $categoryuser) {
		$admin = KunenaFactory::getUser('admin');
		$categoryuser = new KunenaForumCategoryUser(0, 0);
		$this->assertFalse($categoryuser->load());
		$this->assertFalse($categoryuser->load(10,0));
		$this->assertTrue($categoryuser->load(0, $admin));
		$this->assertTrue($categoryuser->load());
	}

	/**
	 * Test getInstance()
	 *
	 * @param KunenaForumCategoryUser $categoryuser
	 * @return KunenaForumCategoryUser
	 * @depends testCreate
	 */
	public function testGetInstance(KunenaForumCategoryUser $categoryuser) {
		$admin = KunenaFactory::getUser('admin');

		$categoryuser2 = KunenaForumCategoryUser::getInstance($categoryuser->category_id, $admin->userid);
		$this->assertSame($categoryuser, $categoryuser2);
		return $categoryuser;
	}

	/**
	 * Test getCategory()
	 *
	 * @param KunenaForumCategoryUser $categoryuser
	 * @depends testGetInstance
	 */
	public function testGetCategory(KunenaForumCategoryUser $categoryuser) {
		$category = $categoryuser->getCategory();
		$this->assertFalse($category->exists());
		//$this->assertEquals($categoryuser->category_id, $category->id);
	}

	/**
	 * Test save()
	 *
	 * @param KunenaForumCategoryUser $categoryuser
	 * @depends testGetInstance
	 */
	public function testSave(KunenaForumCategoryUser $categoryuser) {
		$categoryuser->role = 0;
		$categoryuser->subscribed = 1;
		$this->assertTrue($categoryuser->save());

		$categoryuser2 = new KunenaForumCategoryUser();
		$this->assertTrue($categoryuser2->load($categoryuser->category_id, $categoryuser->user_id));
		$this->assertTrue($categoryuser2->exists());
		$this->assertEquals($categoryuser, $categoryuser2);
	}

	/**
	 * Test delete()
	 *
	 * @param KunenaForumCategoryUser $categoryuser
	 * @depends testGetInstance
	 */
	public function testDelete(KunenaForumCategoryUser $categoryuser) {
		$this->assertTrue($categoryuser->delete());
		$this->assertFalse($categoryuser->exists());
	}
}