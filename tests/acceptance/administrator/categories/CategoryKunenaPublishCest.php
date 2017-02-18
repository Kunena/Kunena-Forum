<?php
/**
 * Kunena Package
 *
 * @package    Kunena.Package
 *
 * @copyright  (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/

class CategoryKunenaPublishCest
{
	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function unpublishCategoryKunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->amGoingTo('try to unpublish a category');
		$I->comment('select the checkboxes');
		$I->click(['xpath' => "//input[@id='cb2']"]);
		$I->click(['xpath' => "//div[@id='toolbar-unpublish']/button"]);
		$I->wait(1);
		$I->waitForText('Category Suggestion Box updated', '5', ['id' => 'system-message-container']);
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function publishCategoryKunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->amGoingTo('try to publish category');
		$I->comment('select the checkboxes');
		$I->click(['xpath' => "//input[@id='cb2']"]);
		$I->click(['xpath' => "//div[@id='toolbar-publish']/button"]);
		$I->expectTo('see an error when trying to save a category without title');
		$I->wait(1);
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function unpublishCategoryKunenaBybButton(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->amGoingTo('try to unpublish a category');
		$I->comment('click on the publish icon');
		$I->click(['xpath' => "//table[@id='categoryList']/tbody/tr[3]/td[3]/a/span"]);
		$I->wait(1);
		//$I->waitForText('Category Suggestion Box updated', '5', ['id' => 'system-message-container']);
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function publishCategoryKunenaBybButton(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->amGoingTo('try to publish a category');
		$I->comment('click on the publish icon');
		$I->click(['xpath' => "//table[@id='categoryList']/tbody/tr[3]/td[3]/a/span"]);
		$I->wait(1);
		//$I->waitForText('Category Suggestion Box updated', '5', ['id' => 'system-message-container']);
		$I->doAdministratorLogout();
	}
}
