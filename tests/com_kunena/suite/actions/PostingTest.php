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
 * Test class for posting.
 */
class PostingTest extends PHPUnit_Framework_TestCase {
	static public $category = array();
	static public $topic = array();
	static public $message = array();
	/**
	 * Sets up the fixture.
	 */
	public static function setUpBeforeClass() {
		jimport('joomla.plugin.helper');
		$queries[] = "TRUNCATE TABLE #__kunena_categories";
		$queries[] = "TRUNCATE TABLE #__kunena_topics";
		$queries[] = "TRUNCATE TABLE #__kunena_messages";
		$queries[] = "TRUNCATE TABLE #__kunena_messages_text";
		$queries[] = "TRUNCATE TABLE #__kunena_users";
		$queries[] = "TRUNCATE TABLE #__kunena_users_banned";
		$queries[] = "TRUNCATE TABLE #__kunena_user_categories";
		$queries[] = "TRUNCATE TABLE #__kunena_user_read";
		$queries[] = "TRUNCATE TABLE #__kunena_user_topics";
		$queries[] = "TRUNCATE TABLE #__kunena_thankyou";
		$queries[] = "TRUNCATE TABLE #__kunena_keywords";
		$queries[] = "TRUNCATE TABLE #__kunena_keywords_map";
		$queries[] = "TRUNCATE TABLE #__kunena_polls_users";
		$queries[] = "TRUNCATE TABLE #__kunena_polls_options";
		$queries[] = "TRUNCATE TABLE #__kunena_polls";
		$queries[] = "TRUNCATE TABLE #__kunena_sessions";

		$db = JFactory::getDBO ();
		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}
		KunenaFactory::loadLanguage();
		KunenaFactory::getUser(42)->save();
	}

	/**
	 * Tears down the fixture.
	 */
	public static function tearDownAfterClass() {
		self::$category = array();
	}

	/**
	 * Create section and 3 categories
	 */
	public function testCreateCategories() {
		$section = new KunenaForumCategory();
		$section->name = 'Section';
		$section->published = 1;
		$this->assertTrue($section->save(), $section->getError());
		$this->checkCategory($section);
		for ($i=1; $i<=10; $i++) {
			$category = new KunenaForumCategory();
			$category->name = "Category $i";
			$category->parent_id = $section->id;
			$category->published = 1;
			$this->assertTrue($category->save(), $category->getError());
			self::$category[$i] = $category;
			$this->checkCategory($category);
		}
	}

	/**
	 * Create 10 topics with 1-10 replies
	 * @depends testCreateCategories
	 */
	public function testPosting() {
		for ($i=1; $i<=10; $i++) {
			$category = self::$category[$i];
			$fields = array('subject'=>"Topic $i", 'message'=>"Message $i");
			list(self::$topic[$i], self::$message[$i][0]) = $category->newTopic($fields, 42);
			$this->assertTrue(self::$message[$i][0]->save(), self::$message[$i][0]->getError());
			$this->checkCategory($category);
			$this->checkTopic(self::$topic[$i], 42);
			$this->checkUser(42);
			for ($j=1; $j<$i; $j++) {
				$fields = array('message'=>"Message $i:$j");
				self::$message[$i][$j] = self::$topic[$i]->newReply($fields, 42);
				$this->assertTrue(self::$message[$i][$j]->save(), self::$message[$i][$j]->getError());
				$this->checkCategory($category);
				$this->checkTopic(self::$topic[$i], 42);
				$this->checkUser(42);
			}
			echo ".";
		}
	}

	/**
	 * Move topics to another category
	 * @dataProvider providerMovingTopicsToCategory
	 * @depends testPosting
	 */
	public function testMovingTopicsToCategory($topic_id, $target_id) {
		$topic = self::$topic[$topic_id];
		$target = self::$category[$target_id];
		$category = $topic->getCategory();

		$this->assertInstanceOf('KunenaForumTopic', $topic->move($target));
		$this->assertEquals($target->id, $topic->category_id);
		$this->checkUser(42);
		$this->checkCategory($category);
		$this->checkCategory($target);
	}

	public function providerMovingTopicsToCategory() {
		return array (
			array (1,2),
			array (9,2),
			array (4,2),
		);
	}

	/**
	 * Move topics to another category
	 * @dataProvider providerMovingTopicsToTopic
	 * @depends testMovingTopicsToCategory
	 */
	public function testMovingTopicsToTopic($topic_id, $target_id) {
		$topic = self::$topic[$topic_id];
		$target = self::$topic[$target_id];
		$ctopic = clone $topic;
		$ctarget = clone $target;

		$this->assertInstanceOf('KunenaForumTopic', $topic->move($target), 'Check that moving topic succeeds');
		$this->assertEquals($topic->moved_id, $target->id, 'Check that topic becomes moved');
		$this->assertEquals(2, $topic->hold, 'Check that original topic becomes deleted');
		$this->assertEquals($ctopic->posts+$ctarget->posts, $target->posts, 'Check that all messages get moved');

		$this->checkCategory($topic->getCategory());
		$this->checkCategory($target->getCategory());
		$this->checkTopic($topic, 42);
		$this->checkTopic($target, 42);
		$this->checkUser(42);
	}

	public function providerMovingTopicsToTopic() {
		return array (
			array (2,8),
			array (7,3),
			array (4,6),
		);
	}
	protected function checkCategory($category) {
		$db = JFactory::getDBO ();
		// Count topics and posts
		$db->setQuery ( "SELECT COUNT(1) AS topics, SUM(posts) AS posts FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND hold=0 AND moved_id=0" );
		$count = $db->loadObject ();
		// Get last topic and post
		$db->setQuery ( "SELECT id, last_post_id FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND hold=0 AND moved_id=0 ORDER BY last_post_time DESC, last_post_id DESC", 0, 1 );
		$last_topic = $db->loadObject ();

		$this->assertEquals(intval($count->topics), $category->numTopics, 'Check that topic count is right');
		$this->assertEquals(intval($count->posts), $category->numPosts, 'Check that post count is right');
		$this->assertEquals($last_topic ? $last_topic->id : 0, $category->last_topic_id, 'Check that last topic is right');
		$this->assertEquals($last_topic ? $last_topic->last_post_id : 0, $category->last_post_id, 'Check that last post is right');
	}

	protected function checkTopic($topic, $user) {
		if ($topic->moved_id) return;

		$user = KunenaFactory::getUser($user);
		$db = JFactory::getDBO ();
		// Count posts
		$db->setQuery ( "SELECT SUM(hold={$db->quote($topic->hold)}) AS posts, SUM(userid={$db->quote($user->userid)}) AS userposts,
			SUM(catid!={$db->quote($topic->category_id)}) AS checkcat
			FROM #__kunena_messages WHERE thread={$db->quote($topic->id)}" );
		$count = $db->loadObject ();
		// Get first post
		$db->setQuery ( "SELECT id FROM #__kunena_messages WHERE thread={$db->quote($topic->id)} AND hold={$db->quote($topic->hold)} ORDER BY time ASC, id ASC" );
		$first = intval($db->loadResult ());
		// Get last post
		$db->setQuery ( "SELECT id FROM #__kunena_messages WHERE thread={$db->quote($topic->id)} AND hold={$db->quote($topic->hold)} ORDER BY time DESC, id DESC" );
		$last = intval($db->loadResult ());

		$this->assertEquals($first, $topic->first_post_id, 'Check first post in topic');
		$this->assertEquals($last, $topic->last_post_id, 'Check last post in topic');
		$this->assertEquals(intval($count->posts), $topic->posts, 'Check that topic post count is right');
		$this->assertEquals(0, intval($count->checkcat), 'Check that category id is corrent in all messages');
		$this->assertEquals(intval($count->userposts), $topic->getUserInfo($user->userid)->posts, 'Check that user topic post count is right');
	}

	protected function checkUser($user) {
		$user = KunenaFactory::getUser($user);
		if (!$user->exists()) return;

		$db = JFactory::getDBO ();
		// Count posts
		$db->setQuery ( "SELECT SUM(ut.posts) AS posts FROM #__kunena_user_topics AS ut INNER JOIN #__kunena_topics AS t ON ut.topic_id=t.id AND t.hold=0 WHERE ut.user_id={$db->quote($user->userid)}" );
		$posts = intval($db->loadResult ());

		$this->assertEquals($posts, $user->posts);
	}
}