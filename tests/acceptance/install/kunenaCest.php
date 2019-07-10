<?php

/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
class KunenaCest
{
	/**
	 * Install Kunena
	 *
	 * @param AcceptanceTester $I
	 *
	 * @return KunenaCest
	 */
	public function Kunena(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->amOnPage('/administrator/index.php?option=com_installer');
		$I->waitForText('Extensions: Install', '30', ['css' => 'H1']);
		$I->wait(3);
		$I->click(['link' => 'Install from URL']);
		$I->fillField(['id' => 'install_url'], 'https://update.kunena.org/nightlybuilds/pkg_kunena_v6.0.0-ALPHA4-DEV_2019-07-10.zip');
		$I->click(['id' => 'installbutton_url']); // Install button// Install button
		$I->comment('Close the installer');
		$I->amOnPage('administrator/index.php?option=com_kunena');
		$I->wait(10);
		$I->doAdministratorLogout();
	}
}
