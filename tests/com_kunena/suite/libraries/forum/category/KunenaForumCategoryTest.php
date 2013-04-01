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
 * Test class for KunenaForumCategory.
 */
class KunenaForumCategoryTest extends PHPUnit_Framework_TestCase {
	static protected $category = null;
	/**
	 * Test new KunenaForumCategory()
	 *
	 * @return KunenaForumCategory
	 */
	public function testNew() {
		$category = new KunenaForumCategory();
		$this->assertNull($category->id, 'Test that new category id is null');
		$this->assertFalse($category->exists(), 'Test that new category does not exist');
		$this->assertEmpty($category->getChannels(), 'Test that new category has no channels');
		$this->assertTrue($category->isSection(), 'Test that new category is section');
		$this->assertFalse($category->checkout(JFactory::getUser()), 'Test that new category checkout always fails');
		$this->assertFalse($category->isCheckedOut(JFactory::getUser()), 'Test that new category is not checked out after last action');
		$this->assertTrue($category->checkin(), 'Test that new category check in always succeeds');
		$this->assertTrue($category->delete(), 'Test that deleting new category succeeds');
		return $category;
	}

	/**
	 * Test load()
	 *
	 * @depends testNew
	 * @dataProvider providerLoad
	 * @depends PostingTest::testCreateCategories
	 */
	public function testLoad($catid, $exists) {
		$category = new KunenaForumCategory();
		if ($exists) {
			$this->assertTrue($category->load($catid), 'Check that category exists');
		} else {
			$this->assertFalse($category->load($catid), 'Check that category does not exist');
		}
		$this->assertEquals($catid, $category->id, 'Check that category id is correct');
	}

	public function providerLoad() {
		return array (
			array (null, false ),
			array (9999, false ),
			array (0, false ),
			array (1, true ),
			array (2, true ) );
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
		$this->assertTrue($category->save(), 'Check that category save succeeds');
		$this->assertTrue($category->exists(), 'Check that saved category exists');
		$this->assertGreaterThan(0, $category->id, 'Check that saved category has id');
		$this->assertSame($category, KunenaForumCategory::getInstance($category->id), 'Check that saved category instance points to correct object');
		$category2 = clone $category;
		$this->assertTrue($category->load(), 'Check that saved category exists in database');
		$this->assertEquals($category, $category2, 'Check that database is identical to the object');
		self::$category = $category;
		return $category;
	}

	/**
	 * Test getUserInfo()
	 *
	 * @depends testCreate
	 * @depends PostingTest::testCreateCategories
	 */
	public function testGetUserInfo(KunenaForumCategory $category) {
		$catuser = $category->getUserInfo(42);
		$this->assertInstanceOf('KunenaForumCategoryUser', $catuser, 'Check that object is KunenaForumCategoryUser');
		$this->assertEquals($category->id, $catuser->category_id, 'Check that category id is correct');
		$this->assertEquals(42, $catuser->user_id, 'Check that user id is correct');
	}

	/**
	 * Test getInstance()
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testCreate
	 */
	public function testGetInstance(KunenaForumCategory $category) {
		$this->assertSame($category, KunenaForumCategory::getInstance($category->id), 'Check that category instance by id returns correct object');
		$this->assertSame($category, KunenaForumCategory::getInstance($category), 'Check that category instance by KunenaForumCategory returns correct object');
		return $category;
	}

	/**
	 * Test getChildren()
	 *
	 * @dataProvider providerGetChildren
	 * @depends PostingTest::testCreateCategories
	 */
	public function testGetChildren($id, $level, $expected) {
		$category = KunenaForumCategory::getInstance($id);
		$this->assertThat(array_keys($category->getChildren($level)), $this->equalTo($expected), 'Check category children');
	}

	public function providerGetChildren() {
		return array (
			array (null, 0, array() ),
			array (9999, 0, array()),
			array (0, 0, array(1) ),
			array (0, 1, array(1,2,3,4) ),
			array (1, 0, array(2,3,4) ) );
	}

	/**
	 * Test addModerator()
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testCreate
	 */
	public function testAddModerator(KunenaForumCategory $category) {
		$admin = KunenaFactory::getUser('admin');
		$access = KunenaAccess::getInstance();

		$this->assertFalse($category->addModerator(0), "Check that guests cannot become moderators");
		$this->assertFalse($category->addModerator(1, "Check that non-existing users cannot become moderators"));
		$this->assertTrue($category->addModerator($admin), "Check that administrator can become moderator ({$category->getError()})");
		$this->assertTrue((bool)$admin->moderator, "Check that becomes a moderator");
		$mod = $access->getModeratorStatus ($admin);
		$this->assertTrue(!empty($mod[$category->id]), "Check that user becomes category moderator");

		$newcategory = new KunenaForumCategory();
		$this->assertFalse($newcategory->addModerator($admin), "Check that non-existing categories cannot have moderators");

		return $category;
	}

	/**
	 * Test removeModerator()
	 *
	 * @param KunenaForumCategory $category
	 * @return KunenaForumCategory
	 * @depends testAddModerator
	 */
	public function testRemoveModerator(KunenaForumCategory $category) {
		$admin = KunenaFactory::getUser('admin');
		$access = KunenaAccess::getInstance();

		$this->assertTrue($category->removeModerator($admin), "Check that administrator can loose moderator status ({$category->getError()})");
		$this->assertFalse((bool)$admin->moderator, "Check that user is not moderator");
		$mod = $access->getModeratorStatus ($admin);
		$this->assertTrue(empty($mod[$category->id]), "Check that user is not category moderator");

		return $category;
	}

	/**
	 * Test update()
	 *
	 * @param KunenaForumCategory $category
	 * @dataProvider providerUpdate
	 * @return KunenaForumCategory
	 * @depends testCreate
	 */
/*	public function testUpdate($topicdelta, $postdelta, $data) {
		$db = JFactory::getDBO ();
		$category = self::$category;
		$topic = KunenaForumTopicHelper::get($data['id']);
		$topic->bind($data);
		if (!$topic->category_id) $topic->category_id = $category->id;
		//$this->assertTrue($topic->save(), $topic->getError());
		//$this->assertTrue($category->update($topic, $topicdelta, $postdelta), $category->getError());

		$db->setQuery ( "SELECT COUNT(DISTINCT id) AS topics, SUM(posts) AS posts FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND hold=0 AND moved_id=0 GROUP BY id", 0, 1 );
		$count = $db->loadObject ();
		$db->setQuery ( "SELECT id, last_post_id FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND hold=0 AND moved_id=0 ORDER BY last_post_time DESC", 0, 1 );
		$last_topic = $db->loadObject ();

		$this->assertEquals($count->topics, $category->numTopics, 'Check that topic count is right');
		$this->assertEquals($count->posts, $category->numPosts, 'Check that post count is right');
		$this->assertEquals($last_topic ? $last_topic->id : 0, $category->last_topic_id, 'Check that last topic is right');
		$this->assertEquals($last_topic ? $last_topic->last_post_id : 0, $category->last_post_id, 'Check that last post is right');
		return $category;
	}

	public function providerUpdate($test=null) {
		static $results = array (
			// Create topic1
			array (1, 1, array('id'=>100, 'hold'=>0, 'posts'=>1, 'subject'=>'Subject 1', 'last_post_id'=>100, 'last_post_time'=>1, 'last_post_message'=>'Message 1:1') ),
			// Post into topic1
			array (0, 1, array('id'=>100, 'hold'=>0, 'posts'=>2, 'subject'=>'Subject 1', 'last_post_id'=>101, 'last_post_time'=>2, 'last_post_message'=>'Message 1:2') ),
			// Delete post in topic1
			array (0, -1, array('id'=>100, 'hold'=>0, 'posts'=>1, 'subject'=>'Subject 1', 'last_post_id'=>100, 'last_post_time'=>1, 'last_post_message'=>'Message 1:1') ),
			// Delete topic1
			array (-1, -1, array('id'=>100, 'hold'=>2, 'posts'=>1, 'subject'=>'Subject 1', 'last_post_id'=>100, 'last_post_time'=>1, 'last_post_message'=>'Message 1:1') ),
			// Create topic2
			array (1, 1, array('id'=>102, 'hold'=>0, 'posts'=>1, 'subject'=>'Subject 2', 'last_post_id'=>102, 'last_post_time'=>3, 'last_post_message'=>'Message 2:1') ),
			// Post into topic1
			array (1, 1, array('id'=>100, 'hold'=>0, 'posts'=>1, 'subject'=>'Subject 1', 'last_post_id'=>100, 'last_post_time'=>1, 'last_post_message'=>'Message 1:1') ),
			// Move topic2
			array (-1, -1, array('id'=>102, 'category_id'=>1, 'hold'=>0, 'posts'=>1, 'subject'=>'Subject 2', 'last_post_id'=>102, 'last_post_time'=>3, 'last_post_message'=>'Message 2:1') ),
			);
		return $results;
	}*/

	/**
	 * Test delete()
	 *
	 * @param KunenaForumCategory $category
	 * @depends testRemoveModerator
	 */
	public function testDelete(KunenaForumCategory $category) {
		$this->assertTrue($category->delete(), $category->getError());
		$this->assertFalse($category->exists(), 'Check that deleted category does not exist');
		$this->assertNull($category->id, 'Check that deleted category id is null');
	}
}