<?php
/**
 * Class category
 *
 * Step Object to interact with a category
 *
 * @todo: this class should grow until being able to execute generic operations over a category: change status, add to category...
 *
 * @package Step\Acceptance
 * @link http://codeception.com/docs/06-ReusingTestCode#StepObjects
 */
namespace Step\Acceptance;
/**
 * Class AdministratorCategoriesStep
 *
 * @package  AcceptanceTester
 *
 * @since    1.4
 */
class category extends \AcceptanceTester
{
	/**
	 * Function to create a Category in Joomla!
	 *
	 * @param  String  $categoryName  Name of the Category which is to be created
	 *
	 * @return void
	 */
	public function createCategory($categoryName)
	{
		$I = $this;
		$I->am('Administrator');
		$I->amOnPage('administrator/index.php?option=com_categories&extension=com_weblinks');
		$I->waitForText('Weblinks: Categories', '30', ['css' => 'h1']);
		$I->expectTo('see categories page');
		$I->checkForPhpNoticesOrWarnings();

		$I->amGoingTo('try to save a category with a filled title');
		$I->clickToolbarButton('New');
		$I->waitForText('Weblinks: New Category', '30', ['css' => 'h1']);
		$I->fillField(['id' => 'jform_title'], $categoryName);
		$I->clickToolbarButton('Save & Close');
		$I->expectTo('see a success message after saving the category');
		$I->see('Category successfully saved', ['id' => 'system-message-container']);
	}

	/**
	 * Function to Trash a Category in Joomla!
	 *
	 * @param  String  $categoryName  Name of the category which is to be trashed
	 *
	 * @return void
	 */
	public function trashCategory($categoryName)
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_categories&extension=com_weblinks');
		$I->waitForText('Weblinks: Categories', '30', ['css' => 'h1']);
		$I->searchForItem($categoryName);
		$I->amGoingTo('Select the weblink result');
		$I->checkAllResults();
		$I->clickToolbarButton("Trash");
		$I->see('category successfully trashed.', ['id' => 'system-message-container']);
	}

	/**
	 * Function to Delete a Category in Joomla!
	 *
	 * @param  String  $categoryName  Name of the category which is to be deleted
	 *
	 * @return void
	 */
	public function deleteCategory($categoryName)
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_categories&extension=com_weblinks');
		$I->waitForText('Weblinks: Categories', '30', ['css' => 'h1']);
		$I->setFilter('select status', 'Trashed');
		$I->searchForItem($categoryName);
		$I->amGoingTo('Select the weblink result');
		$I->checkAllResults();
		$I->clickToolbarButton("empty trash");
		$I->see('category successfully deleted.', ['id' => 'system-message-container']);
	}
}
