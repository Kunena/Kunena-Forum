<?php
/**
 * @package     RedShop
 * @subpackage  Helper Class
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Codeception\Module;

/* Here you can define custom actions
 all public methods declared in helper class will be available in $I */

/**
 * Class AcceptanceHelper
 *
 * @package  Codeception\Module
 *
 * @since    1.4
 */
class AcceptanceHelper extends \Codeception\Module
{
	/**
	 * Function to getConfiguration from the YML and return in the test
	 *
     * @param null $element
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getConfiguration($element = null)
    {
        if (is_null($element)) {
            throw new InvalidArgumentException('empty value or non existing element was requested from configuration');
        }

        return $this->config[$element];
    }

	/**
	 * Function to Verify State of an Object
	 *
	 * @param   String  $expected  Expected State
	 * @param   String  $actual    Actual State
	 *
	 * @return void
	 */
	public function verifyState($expected, $actual)
	{
		$this->assertEquals($expected, $actual, "Assert that the Actual State is equal to the state we Expect");
	}

	/**
	 * Function to VerifyNotices
	 *
	 * @param   string  $expected  Expected Value
	 * @param   string  $actual    Actual Value
	 * @param   string  $page      Page for which we are Verifying
	 *
	 * @return void
	 */
	public function verifyNotices($expected, $actual, $page)
	{
		$this->assertEquals($expected, $actual, "Page " . $page . " Contains PHP Notices and Warnings");
	}
}
