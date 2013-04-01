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
 * Test class for KunenaForumTopic.
 */
class KunenaForumTopicTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test new KunenaForumTopic()

	 * @return KunenaForumTopic
	 */
	public function testNew() {
		$topic = new KunenaForumTopic();
		$this->assertInstanceOf('KunenaForumTopic', $topic);
		$this->assertNull($topic->id);
		$this->assertFalse($topic->exists());
		$this->assertTrue($topic->delete());
		return $topic;
	}

	/**
	 * Test topic creation
	 *
	 * @param KunenaForumTopic $topic
	 * @return KunenaForumTopic
	 * @depends testNew
	 */
	public function testCreate(KunenaForumTopic $topic) {
		// Creation should fail if there's not enough data
		$this->assertFalse($topic->save());
		$this->assertFalse($topic->exists());
		$this->assertNull($topic->id);

/*		$topic->category_id = 2;
		$topic->subject = 'Topic';
		$this->assertTrue($topic->save());
		$this->assertTrue($topic->exists());
		$this->assertGreaterThan(1, $topic->id);
		$topic2 = KunenaForumTopic::getInstance($topic->id);
		// TODO: Do we want this?
		//$this->assertSame($topic, $topic2);
		$this->assertSame($topic->id, $topic2->id);
		$this->assertSame($topic->name, $topic2->name);
		return $topic2;*/
	}

	/**
	 * Test getInstance()
	 *
	 * @param KunenaForumTopic $topic
	 * @return KunenaForumTopic
	 * @depends testCreate
	 */
/*	public function testGetInstance(KunenaForumTopic $topic) {
		$topic2 = KunenaForumTopic::getInstance($topic->id);
		$this->assertSame($topic, $topic2);
		return $topic;
	}*/

	/**
	 * Test delete()
	 *
	 * @param KunenaForumTopic $topic
	 * @depends testGetInstance
	 */
/*	public function testDelete(KunenaForumTopic $topic) {
		$this->assertTrue($topic->delete(), $topic->getError());
		$this->assertFalse($topic->exists());
		$this->assertNull($topic->id);
	}*/
}