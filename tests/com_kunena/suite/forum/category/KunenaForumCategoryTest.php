<?php
/**
 * Kunena Component
 * @package Kunena.UnitTest
 * @subpackage Utilities
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Test class for KunenaForumCategory.
 */
class KunenaForumCategoryTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture.
	 */
	public static function setUpBeforeClass() {
		jimport('joomla.plugin.helper');
	}

	/**
	 * Tears down the fixture.
	 */
	public static function tearDownAfterClass() {
	}

	/**
	 * Test new KunenaForumCategory()
	 *
	 * @return KunenaForumCategory
	 */
	public function testNew() {
		$category = new KunenaForumCategory();
		$this->assertInstanceOf('KunenaForumCategory', $category);
		$this->assertNull($category->id);
		$this->assertFalse($category->exists());
		$this->assertEmpty($category->getChildren());
		$this->assertEmpty($category->getChannels());
		$this->assertTrue($category->isSection());
		$this->assertFalse($category->checkout(JFactory::getUser()));
		$this->assertFalse($category->isCheckedOut(JFactory::getUser()));
		$this->assertTrue($category->checkin());
		$this->assertTrue($category->delete());
		return $category;
	}

	/**
	 * Test category creation
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testNew
	 */
	public function testCreate(KunenaForumCategory $category) {
		$category->name = 'Test Section';
		$category->parent_id = 0;
		$category->save();
		$this->assertTrue($category->exists());
		$this->assertGreaterThan(1, $category->id);
		$category2 = KunenaForumCategory::getInstance($category->id);
		// TODO: Do we want this?
		//$this->assertSame($category, $category2);
		$this->assertSame($category->id, $category2->id);
		$this->assertSame($category->name, $category2->name);
		return $category;
	}

	/**
	 * Test adding moderator to our category
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testCreate
	 */
	public function testAddModerator(KunenaForumCategory $category) {
		$user = KunenaFactory::getUser('admin');
		$access = KunenaFactory::getAccessControl ();

		$this->assertFalse($category->addModerator(0), "Guests cannot become moderators");
		$this->assertFalse($category->addModerator(1, "Non-existing users cannot become moderators"));
		$this->assertTrue($category->addModerator($user), "Administrator can become moderator ({$category->getError()})");
		$this->assertTrue((bool)$user->moderator, "Oops, user didn't become moderator!");
		$mod = $access->getModeratorStatus ($user);
		$this->assertTrue(!empty($mod[$category->id]), "Oops, user didn't become category moderator!");

		$newcategory = new KunenaForumCategory();
		$this->assertFalse($newcategory->addModerator($user), "Non-existing categories cannot have moderators");

		return $category;
	}

	/**
	 * Test removing moderator from our category
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testAddModerator
	 */
	public function testRemoveModerator(KunenaForumCategory $category) {
		$user = KunenaFactory::getUser('admin');
		$access = KunenaFactory::getAccessControl ();

		$this->assertTrue($category->removeModerator($user), "Administrator can loose moderator status ({$category->getError()})");
		$this->assertFalse((bool)$user->moderator, "Oops, user is still moderator!");
		$mod = $access->getModeratorStatus ($user);
		$this->assertTrue(empty($mod[$category->id]), "Oops, user is still category moderator!");

		return $category;
	}

	/**
	 * Test category deletion
	 *
	 * @param KunenaForumCategory $category
	 * @depends testRemoveModerator
	 */
	public function testDelete(KunenaForumCategory $category) {
		$category->delete();
		$this->assertFalse($category->exists());
		$this->assertNull($category->id);
	}
}