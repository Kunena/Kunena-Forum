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
 * Test class for KunenaForumTopicUserHelper.
 */
class KunenaForumTopicUserHelperTest extends PHPUnit_Framework_TestCase {
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
	 * Test get()
	 */
	public function testGetInstance() {
		$admin = KunenaFactory::getUser('admin');

		$topicuser = KunenaForumTopicUserHelper::get(0, $admin->userid);
		$this->assertEquals(0, $topicuser->topic_id);
		$this->assertEquals($admin->userid, $topicuser->user_id);
	}

	/**
	 * Test getTopics()
	 */
	public function testGetTopics() {
		list($count, $topics) = KunenaForumTopicHelper::getLatestTopics(false, 0, 20);
		$topicusers = KunenaForumTopicUserHelper::getTopics($topics);
		foreach ($topics as $topic) {
			$this->assertTrue(isset($topicusers[$topic->id]));
			$this->assertEquals($topic->id, $topicusers[$topic->id]->topic_id);
		}
	}
}