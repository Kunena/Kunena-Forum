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
 * Test class for KunenaFactory.
 */
class KunenaFactoryTest extends PHPUnit_Framework_TestCase {
	public function testGetConfig() {
		$instance = KunenaFactory::getConfig();
		$this->assertInstanceOf('KunenaConfig', $instance);
	}

	public function testGetTemplate() {
		$instance = KunenaFactory::getTemplate();
		$this->assertInstanceOf('KunenaTemplate', $instance);
	}

	public function testGetUser() {
		$instance = KunenaFactory::getUser();
		$this->assertInstanceOf('KunenaUser', $instance);
	}

	public function testGetSession() {
		$instance = KunenaFactory::getSession();
		$this->assertInstanceOf('KunenaSession', $instance);
	}
}