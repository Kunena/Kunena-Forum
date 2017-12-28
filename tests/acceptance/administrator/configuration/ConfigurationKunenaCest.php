<?php
/**
 * Kunena Package
 *
 * @package    Kunena.Package
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/

class ConfigurationKunenaCest
{
	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaSetupCest
	 */
	public function ConfigurationKunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Improve basic setup to max settings');
		$I->amOnPage('administrator/index.php?option=com_kunena&view=config');
		$I->click(['xpath' => '//a[contains(@href, \'#tab_basics\')]']);
		$I->comment('I enter the email');
		$I->fillField(['name' => 'cfg_email'], 'demo@test.com');
		//$I->comment('Turn debug on');
		//$I->selectOption(['id' => 'cfg_debug'], 'Yes');
		//$I->comment('Turn off cache');
		//$I->selectOption(['xpath' => '//select[@id=\'cfg_cache\']'], 'No');
		$I->comment('I open the Frontend Tab');
		$I->click(['xpath' => '//a[contains(@href, \'#tab_frontend\')]']);
		//$I->comment('Enable the avatars on index');
		//$I->selectOption(['xpath' => '//select[@id=\'cfg_avataroncat\']'], 'Yes');
		//$I->comment('Enable pickup category new topic');
		//$I->selectOption(['xpath' => '//select[@id=\'cfg_pickup_category\']'], 'Yes');
		$I->comment('I open the Users Tab');
		$I->click(['xpath' => '//a[contains(@href, \'#tab_users\')]']);
		//$I->comment('Ask email');
		//$I->selectOption(['xpath' => '//select[@id=\'cfg_askemail\']'], 'Yes');
		//$I->comment('Turn off user can report himself');
		//$I->selectOption(['xpath' => '//select[@id=\'cfg_user_report\']'], 'No');
		$I->comment('I open the Server Tab');
		$I->click(['link' => 'Uploads']);
		$I->comment('I fill Database User');
		$I->fillField(['name' => 'cfg_imagesize'], '5000');
		$I->comment('I fill Database User');
		$I->fillField(['name' => 'cfg_filesize'], '5000');
		$I->comment('I click on save');
		$I->click(['xpath' => '//div[@id=\'toolbar-save\']//button']);
		$I->wait(5);
		$I->doAdministratorLogout();
	}
}
