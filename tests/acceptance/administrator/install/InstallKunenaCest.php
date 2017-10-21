<?php

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
		$I->installExtensionFromFileUpload('pkg_kunena_v5.1-dev.zip');
		$I->wait(10);
		$I->comment('Close the installer');
		$I->amOnPage('administrator/index.php?option=com_kunena');
		$I->wait(1);
		$I->doAdministratorLogout();
	}
}
