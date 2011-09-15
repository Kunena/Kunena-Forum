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
 * Test class for KunenaForumTopicUser.
 */
class KunenaForumTopicUserTest extends PHPUnit_Framework_TestCase {
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
	 * Test new KunenaForumTopicUser()
	 */
	public function testNew() {
		$admin = KunenaFactory::getUser('admin');
		$topicuser = new KunenaForumTopicUser(2, $admin);
		$this->assertInstanceOf('KunenaForumTopicUser', $topicuser);
		$this->assertFalse($topicuser->exists());
		$this->assertEquals(2, $topicuser->topic_id);
		$this->assertEquals($admin->userid, $topicuser->user_id);
	}

	/**
	 * Test topic user creation
	 *
	 * @return KunenaForumTopicUser
	 */
	public function testCreate() {
		$admin = KunenaFactory::getUser('admin');
		$topicuser = KunenaForumTopicUser::getInstance(2, $admin->userid);
		$this->assertEquals(2, $topicuser->topic_id);
		$this->assertEquals($admin->userid, $topicuser->user_id);

		$topicuser->favorite = 1;
		$this->assertTrue($topicuser->save());
		$this->assertTrue($topicuser->exists());
		$this->assertEquals(2, $topicuser->topic_id);
		$this->assertEquals($admin->userid, $topicuser->user_id);
		$this->assertEquals(1, $topicuser->favorite);

		// Check that object was saved to database
		$topicuser2 = new KunenaForumTopicUser();
		$this->assertTrue($topicuser2->load($topicuser->topic_id, $topicuser->user_id));
		$this->assertEquals($topicuser->topic_id, $topicuser2->topic_id);
		$this->assertEquals($topicuser->user_id, $topicuser2->user_id);
		$this->assertEquals($topicuser->favorite, $topicuser2->favorite);

		// Check that instance remains the same
		$topicuser2 = KunenaForumTopicUser::getInstance(2, $admin->userid);
		return $topicuser2;
	}

	/**
	 * Test load()
	 *
	 * @param KunenaForumTopicUser $topicuser
	 * @return KunenaForumTopicUser
	 * @depends testCreate
	 */
	public function testLoad(KunenaForumTopicUser $topicuser) {
		$admin = KunenaFactory::getUser('admin');
		$topicuser = new KunenaForumTopicUser();
		$this->assertFalse($topicuser->load());
		$this->assertFalse($topicuser->load(2,0));
		$this->assertTrue($topicuser->load(2, $admin));
		$this->assertTrue($topicuser->load());
	}

	/**
	 * Test getInstance()
	 *
	 * @param KunenaForumTopicUser $topicuser
	 * @return KunenaForumTopicUser
	 * @depends testCreate
	 */
	public function testGetInstance(KunenaForumTopicUser $topicuser) {
		$admin = KunenaFactory::getUser('admin');

		$topicuser2 = KunenaForumTopicUser::getInstance($topicuser->topic_id, $admin->userid);
		$this->assertSame($topicuser, $topicuser2);
		return $topicuser;
	}

	/**
	 * Test getTopic()
	 *
	 * @param KunenaForumTopicUser $topicuser
	 * @depends testGetInstance
	 */
	public function testGetTopic(KunenaForumTopicUser $topicuser) {
		$topic = $topicuser->getTopic();
		$this->assertTrue($topic->exists());
		$this->assertEquals($topicuser->topic_id, $topic->id);
	}

	/**
	 * Test save()
	 *
	 * @param KunenaForumTopicUser $topicuser
	 * @depends testGetInstance
	 */
	public function testSave(KunenaForumTopicUser $topicuser) {
		$topicuser->favorite = 0;
		$this->assertTrue($topicuser->save());

		$topicuser2 = new KunenaForumTopicUser();
		$this->assertTrue($topicuser2->load($topicuser->topic_id, $topicuser->user_id));
		$this->assertTrue($topicuser2->exists());
		$this->assertEquals($topicuser->topic_id, $topicuser2->topic_id);
		$this->assertEquals($topicuser->user_id, $topicuser2->user_id);
		$this->assertEquals($topicuser->favorite, $topicuser2->favorite);
	}

	/**
	 * Test topic user deletion
	 *
	 * @param KunenaForumTopicUser $topicuser
	 * @depends testGetInstance
	 */
	public function testDelete(KunenaForumTopicUser $topicuser) {
		$this->assertTrue($topicuser->delete());
		$this->assertFalse($topicuser->exists());
	}
}