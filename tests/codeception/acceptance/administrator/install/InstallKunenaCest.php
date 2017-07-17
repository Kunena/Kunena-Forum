<?php
namespace Step\Acceptance\Administrator;
use Page\Acceptance\Administrator\ExtensionManagerPage;
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
class InstallKunenaCest
{
	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCest
	 */
	public function installKunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('/administrator/index.php?option=com_installer');
		$I->comment('I start the install');
		$I->click(ExtensionManagerPage::$installUrlField);
		$I->comment('I enter the url');
		$url = 'http://localhost/tests/codeception/kunena/pkg_kunena_v5.0.zip';
		$I->fillField(['id' => 'install_url'], $url);

		$I->click(['id' => 'installbutton_url']);
		$I->wait(5);

		$I->see('Kunena has been successfully installed!','h2');
		$I->comment('Installation of the package was successful.');
		$I->click(['id' => 'kunena-component']);

		$I->wait(3);
		$I->doAdministratorLogout();
	}
}
