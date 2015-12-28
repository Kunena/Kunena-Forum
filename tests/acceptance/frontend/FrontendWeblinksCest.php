<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class FrontendWeblinksCest
{
	private $title;

	public function __construct()
	{
		$this->faker = Faker\Factory::create();
		$this->title  = 'Weblink' . $this->faker->randomNumber();
		$this->url  = $this->faker->url();
		$this->menuItem = 'Menu Item' . $this->faker->randomNumber();
	}

	/**
	 * Create a weblink in the backend and confirm it exists and is visible in the Frontend
	 *
	 * @param   \Step\Acceptance\Weblink  $I
	 */

	public function createWeblinkAndConfirmFrontend(\Step\Acceptance\weblink $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Listing a category of Weblinks in frontend');

		$I->doAdministratorLogin();

		$I->createWeblink($this->title, $this->url, "No");

		// Menu link
		$I->createMenuItem($this->menuItem, 'Weblinks', 'List All Web Link Categories', 'Main Menu');

		// Go to the frontend
		$I->comment('I want to check if the menu entry exists in the frontend');
		$I->amOnPage('index.php?option=com_weblinks');
		$I->expectTo('see weblink categories');
		$I->waitForText('Uncategorised','30', ['css' => 'h3']);
		$I->checkForPhpNoticesOrWarnings();
		$I->comment('I open the uncategorised Weblink Category');
		$I->click(['link' => 'Uncategorised']);

		$I->waitForText('Uncategorised','30', ['css' => 'h2']);
		$I->expectTo('see the weblink we created');
		$I->seeElement(['link' => $this->title]);
		$I->seeElement(['xpath' => "//a[@href='$this->url']"]);
	}

	public function hitsAreNotIncrementedIfCountClicksIsOff(\Step\Acceptance\weblink $I)
	{
		$title  = 'Weblink' . $this->faker->randomNumber();
		$url = $I->getConfiguration('counter_test_url');

		$I->am('Administrator');
		$I->wantToTest('Hits are not incremented if Count Clicks is off');

		$I->doAdministratorLogin();

		$I->createWeblink($title, $url, "No");

		// Go to the frontend
		$I->amOnPage('index.php?option=com_weblinks');
		$I->expectTo('see weblink categories');
		$I->waitForText('Uncategorised','30', ['css' => 'h3']);
		$I->checkForPhpNoticesOrWarnings();
		$I->comment('I open the uncategorised Weblink Category');
		$I->click(['link' => 'Uncategorised']);

		// Check that hits is 0
		$I->waitForText('Uncategorised','30', ['css' => 'h2']);
		$I->expectTo('see the weblink we created');
		$I->seeElement(['link' => $title]);
		$I->expectTo('see that hits is 0');
		$I->see('Hits: 0', ['class' => 'list-hits']);

		// Click on the link, go back, and check that hits is still 0
		$I->click(['link' => $title]);
		$I->moveBack();
		$I->expectTo('see that hits is still 0');
		$I->see('Hits: 0', ['class' => 'list-hits']);
	}

	public function hitsAreIncrementedIfCountClicksIsOn(\Step\Acceptance\weblink $I)
	{
		$title  = 'Weblink' . $this->faker->randomNumber();
		$url = $I->getConfiguration('counter_test_url');

		$I->am('Administrator');
		$I->wantToTest('Hits are incremented if Count Clicks is on');

		$I->doAdministratorLogin();

		$I->createWeblink($title, $url, "Yes");

		// Go to the frontend
		$I->amOnPage('index.php?option=com_weblinks');
		$I->expectTo('see weblink categories');
		$I->waitForText('Uncategorised','30', ['css' => 'h3']);
		$I->checkForPhpNoticesOrWarnings();
		$I->comment('I open the uncategorised Weblink Category');
		$I->click(['link' => 'Uncategorised']);

		// Check that hits is 0
		$I->waitForText('Uncategorised','30', ['css' => 'h2']);
		$I->expectTo('see the weblink we created');
		$I->seeElement(['link' => $title]);
		$I->expectTo('see that hits is 0');
		$I->see('Hits: 0', ['class' => 'list-hits']);

		// Click on the link, go back, and check that hits is 1
		$I->click(['link' => $title]);
		$I->moveBack();
		$I->expectTo('see that hits is 1');
		$I->see('Hits: 1', ['class' => 'list-hits']);
	}
}
