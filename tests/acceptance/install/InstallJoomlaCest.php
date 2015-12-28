<?php
/**
 * Kunena Package
 *
 * @package    Kunena.Package
 *
 * @copyright  (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.kunena.org
 **/

class InstallJoomlaCest
{
	/**
	 *
	 * install Joomla
	 *
	 * @param AcceptanceTester $I
	 */
	public function installJoomla(\AcceptanceTester $I)
	{
		$I->am('Administrator');
		$I->installJoomlaRemovingInstallationFolder();
		$I->doAdministratorLogin();
		$I->setErrorReportingToDevelopment();
		$I->wait(10);
		$I->disablestatistics();
	}

	/**
	 * Install Kunena
	 *
	 * @depends installJoomla
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return InstallKunenaCest
	 */
	public function installKunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('get Kunena repository folder from acceptance.suite.yml (see _support/AcceptanceHelper.php)');

		$I->installExtensionFromUrl('C:\wamp64\www\kunena\src\pkg_kunena_v5.0.0-ALPHA3_2015-12-15.zip');
		$I->doAdministratorLogout();
	}
}
