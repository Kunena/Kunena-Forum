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
 * Test class for KunenaForumTopicUserHelper.
 */
class KunenaForumTopicUserHelperTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test get()
	 */
	public function testGet() {
		$admin = KunenaFactory::getUser('admin');

		$topicuser = KunenaForumTopicUserHelper::get();
		$this->assertEquals(null, $topicuser->topic_id);
		$this->assertEquals(0, $topicuser->user_id);
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