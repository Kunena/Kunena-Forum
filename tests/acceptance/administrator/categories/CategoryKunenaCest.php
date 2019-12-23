<?php

/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
class CategoryKunenaCest
{
	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function addKunenaCategorySection(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Add new category');
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->amGoingTo('try to save a section');
		$I->click(['xpath' => "//div[@id='toolbar-new']/button"]);
		$I->clickToolbarButton('New Category');
		$I->comment('Add new category title');
		$I->fillField(['name' => 'name'], 'name');
		$I->comment('Add new category alias');
		$I->fillField(['name' => 'alias'], 'alias');
		$I->comment('Set published');
		$I->selectOption(['id' => 'published'], 'Published');
		$I->comment('Add icon');
		$I->fillField(['name' => 'icon'], 'icon-add');
		$I->comment('Add new category description');
		$I->fillField(['id' => 'description'], 'description');
		$I->comment('Add new category header');
		$I->fillField(['id' => 'headerdesc'], 'headerdesc');
		$I->clickToolbarButton('save');
		$I->expectTo('see an error when trying to save a category without title');
		$I->wait(5);
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function addKunenaCategoryCategory(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Add new category');
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->click(['xpath' => "//div[@id='toolbar-new']/button"]);
		$I->comment('Add new category title');
		$I->comment('Set category');
		$I->selectOption(['id' => 'parent_id'], '-  name');
		$I->fillField(['name' => 'name'], 'category4');
		$I->comment('Add new category alias');
		$I->fillField(['name' => 'alias'], 'category4');
		$I->comment('Set published');
		$I->selectOption(['id' => 'published'], 'Published');
		$I->comment('Add icon');
		$I->fillField(['name' => 'icon'], 'icon-add');
		$I->comment('Add new category description');
		$I->fillField(['id' => 'description'], 'description');
		$I->comment('Add new category header');
		$I->fillField(['id' => 'headerdesc'], 'headerdesc');
		$I->clickToolbarButton('save');
		$I->wait(5);
		$I->expectTo('see an error when trying to save a category without title');
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function addKunenaCategorySubCategory(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Add new subcategory');
		$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		$I->click(['xpath' => "//div[@id='toolbar-new']/button"]);
		$I->comment('Add new sub category title');
		$I->comment('Set subcategory');
		$I->selectOption(['id' => 'parent_id'], '-  category');
		$I->fillField(['name' => 'name'], 'subcategory1');
		$I->comment('Add new category alias');
		$I->fillField(['name' => 'alias'], 'subcategory1');
		$I->comment('Set published');
		$I->selectOption(['id' => 'published'], 'Published');
		$I->comment('Add icon');
		$I->fillField(['name' => 'icon'], 'icon-add');
		$I->comment('Add new category description');
		$I->fillField(['id' => 'description'], 'description');
		$I->comment('Add new category header');
		$I->fillField(['id' => 'headerdesc'], 'headerdesc');
		$I->clickToolbarButton('save');
		$I->wait(5);
		$I->expectTo('see an error when trying to save a category without title');
		$I->doAdministratorLogout();
	}

	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCategoryCest
	 */
	public function addKunenaCategoryRemove(\AcceptanceTester $I)
	{
		//$I->doAdministratorLogin();
		//$I->comment('Add new subcategory');
		//$I->amOnPage('administrator/index.php?option=com_kunena&view=categories');
		//$I->comment('select the checkboxes');
		//$I->click(['id' => 'cb5']);
		//$I->click(['id' => 'cb4']);
		//$I->click(['id' => 'cb3']);
		//$I->comment('Delete the items');
		//$I->click(['xpath' => "//div[@id='toolbar-delete']/button"]);
		//$I->wait(5);
		//$I->waitForText('3 categories deleted.', '5', ['id' => 'system-message-container']);
		//$I->doAdministratorLogout();
	}
}
