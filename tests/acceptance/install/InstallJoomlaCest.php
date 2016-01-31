<?php
/**
 * Kunena Package
 *
 * @package    Kunena.Package
 *
 * @copyright  (C) 2008 - 2016 Kunena Team. All rights reserved.
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
		//$I->disablestatistics();
		$I->setErrorReportingToDevelopment();
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
		// URL where the package file to install is located (mostly the same as joomla-cms)
		$url = $I->getConfiguration('url');
		$I->installExtensionFromUrl($url . "/pkg_kunena_v5.0.zip");
		$I->doAdministratorLogout();
	}
}
