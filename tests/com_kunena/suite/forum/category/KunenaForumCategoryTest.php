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
	protected function setUp() {
		kimport('kunena.forum.category');
	}

	/**
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
	 * @param KunenaForumCategory $category
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
	 * @param KunenaForumCategory $category
	 * @depends testCreate
	 */
	public function testDelete(KunenaForumCategory $category) {
		$category->delete();
		$this->assertFalse($category->exists());
		$this->assertNull($category->id);
	}
}