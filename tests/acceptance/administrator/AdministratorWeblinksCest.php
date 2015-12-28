<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class AdministratorWeblinksCest
{
	public function __construct()
	{
		$this->faker = Faker\Factory::create();
		$this->title  = 'Weblink' . $this->faker->randomNumber();
		$this->url  = $this->faker->url();
	}

	public function administratorVerifyAvailableTabs(\Step\Acceptance\weblink $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Weblinks Edit View Tabs');

		$I->doAdministratorLogin();

		$I->amGoingTo('Navigate to Weblinks page in /administrator/ and verify the Tabs');
		$I->amOnPage('administrator/index.php?option=com_weblinks&view=weblinks');
		$I->waitForText('Web Links', '30', ['css' => 'h1']);
		$I->clickToolbarButton('New');
		$I->waitForText('Web Link: New', '30', ['css' => 'h1']);
		$I->verifyAvailableTabs(['New Web Link', 'Images', 'Publishing', 'Options', 'Metadata']);
	}

	public function administratorCreateWeblink(\Step\Acceptance\weblink $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Weblink creation in /administrator/');

		$I->doAdministratorLogin();

		// Get the weblink StepObject
		$I->amGoingTo('Navigate to Weblinks page in /administrator/');
		$I->amOnPage('administrator/index.php?option=com_weblinks');
		$I->waitForText('Web Links', '30', ['css' => 'h1']);
		$I->expectTo('see weblinks page');
		$I->checkForPhpNoticesOrWarnings();

		$I->amGoingTo('try to save a weblink with a filled title and URL');
		$I->clickToolbarButton('New');
		$I->waitForText('Web Link: New', '30', ['css' => 'h1']);
		$I->fillField(['id' => 'jform_title'], $this->title);
		$I->fillField(['id' => 'jform_url'], $this->url);
		$I->clickToolbarButton('Save & Close');
		$I->waitForText('Web Links', '30', ['css' => 'h1']);
		$I->expectTo('see a success message and the weblink added after saving the weblink');
		$I->see('Web link successfully saved', ['id' => 'system-message-container']);
		$I->see($this->title, ['id' => 'weblinkList']);
	}

	/**
	 * @depends administratorCreateWeblink
	 */
	public function administratorTrashWeblink(AcceptanceTester $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Weblink removal in /administrator/');

		$I->doAdministratorLogin();

		$I->amGoingTo('Navigate to Weblinks page in /administrator/');
		$I->amOnPage('administrator/index.php?option=com_weblinks');
		$I->waitForText('Web Links','30',['css' => 'h1']);
		$I->expectTo('see weblinks page');

		$I->amGoingTo('Search the just saved weblink');
		$I->searchForItem($this->title);
		$I->waitForText('Web Links','30',['css' => 'h1']);

		$I->amGoingTo('Delete the just saved weblink');
		$I->checkAllResults();
		$I->clickToolbarButton('Trash');
		$I->waitForText('Web Links','30',['css' => 'h1']);
		$I->expectTo('see a success message and the weblink removed from the list');
		$I->see('Web link successfully trashed',['id' => 'system-message-container']);
		$I->cantSee($this->title,['id' => 'weblinkList']);
	}

	/**
	 * @depends administratorTrashWeblink
	 */
	public function administratorDeleteWeblink(AcceptanceTester $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Weblink removal in /administrator/');

		$I->doAdministratorLogin();

		$I->amGoingTo('Navigate to Weblinks page in /administrator/');
		$I->amOnPage('administrator/index.php?option=com_weblinks');
		$I->waitForText('Web Links','30',['css' => 'h1']);
		$I->expectTo('see weblinks page');
		$I->selectOptionInChosen('- Select Status -', 'Trashed');
		$I->amGoingTo('Search the just saved weblink');
		$I->searchForItem($this->title);
		$I->waitForText('Web Links','30',['css' => 'h1']);

		$I->amGoingTo('Delete the just saved weblink');
		$I->checkAllResults();
		$I->click(['xpath'=> '//div[@id="toolbar-delete"]/button']);
		$I->waitForText('Web Links','30',['css' => 'h1']);
		$I->expectTo('see a success message and the weblink removed from the list');
		$I->see('1 web link successfully deleted.',['id' => 'system-message-container']);
		$I->cantSee($this->title,['id' => 'weblinkList']);
	}

	public function administratorCreateWeblinkWithoutTitleFails(AcceptanceTester $I)
	{
		$I->am('Administrator');
		$I->wantToTest('Weblink creation without title fails in /administrator/');

		$I->doAdministratorLogin();

		$I->amGoingTo('Navigate to Weblinks page in /administrator/');
		$I->amOnPage('administrator/index.php?option=com_weblinks');
		$I->waitForText('Web Links','30',['css' => 'h1']);
		$I->expectTo('see weblinks page');
		$I->checkForPhpNoticesOrWarnings();

		$I->amGoingTo('try to save a weblink with empty title and it should fail');
		$I->click(['xpath'=> "//button[@onclick=\"Joomla.submitbutton('weblink.add')\"]"]);
		$I->waitForText('Web Link: New','30',['css' => 'h1']);
		$I->click(['xpath'=> "//button[@onclick=\"Joomla.submitbutton('weblink.apply')\"]"]);
		$I->expectTo('see an error when trying to save a weblink without title and without URL');
		$I->see('Invalid field:  Title',['id' => 'system-message-container']);
		$I->see('Invalid field:  URL',['id' => 'system-message-container']);
	}
}