<?php
/**
 * @package    JoomlaBrowser
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Codeception\Module;

use Codeception\Module\WebDriver;

/**
 * Joomla Browser class to perform test suits for Joomla.
 *
 * @since  1.0
 */
class JoomlaBrowser extends WebDriver
{
	/**
	 * The module required fields, to be set in the suite .yml configuration file.
	 *
	 * @var array
	 */
	protected $requiredFields = array(
		'url',
		'browser',
		'username',
		'password',
		'database type',
		'database host',
		'database user',
		'database password',
		'database name',
		'database type',
		'database prefix',
		'admin email',
		'language'
	);

	/**
	 * Function to Do Admin Login In Joomla!
	 *
	 * @param   string|null  $user      Optional Username. If not passed the one in acceptance.suite.yml will be used
	 * @param   string|null  $password  Optional password. If not passed the one in acceptance.suite.yml will be used
	 *
	 * @return  void
	 */
	public function doAdministratorLogin($user = null, $password = null)
	{
		$I = $this;

		if (is_null($user))
		{
			$user = $this->config['username'];
		}

		if (is_null($password))
		{
			$password = $this->config['password'];
		}

		$this->debug('I open Joomla Administrator Login Page');
		$I->amOnPage('/administrator/index.php');
		$I->waitForElement(['id' => 'mod-login-username'], 60);
		$this->debug('Fill Username Text Field');
		$I->fillField(['id' => 'mod-login-username'], $user);
		$this->debug('Fill Password Text Field');
		$I->fillField(['id' => 'mod-login-password'], $password);

		// @todo: update login button in joomla login screen to make this xPath more friendly
		$this->debug('I click Login button');
		$I->click(['xpath' => "//button[contains(normalize-space(), 'Log in')]"]);
		$this->debug('I wait to see Administrator Control Panel');
		$I->waitForText('Control Panel', 4, ['css' => 'h1.page-title']);
	}

	/**
	 * Function to Do Frontend Login In Joomla!
	 *
	 * @param   string|null  $user      Optional username. If not passed the one in acceptance.suite.yml will be used
	 * @param   string|null  $password  Optional password. If not passed the one in acceptance.suite.yml will be used
	 *
	 * @return  void
	 */
	public function doFrontEndLogin($user = null, $password = null)
	{
		$I = $this;

		if (is_null($user))
		{
			$user = $this->config['username'];
		}

		if (is_null($password))
		{
			$password = $this->config['password'];
		}

		$this->debug('I open Joomla Frontend Login Page');
		$I->amOnPage('/index.php?option=com_users&view=login');
		$this->debug('Fill Username Text Field');
		$I->fillField(['id' => 'username'], $user);
		$this->debug('Fill Password Text Field');
		$I->fillField(['id' => 'password'], $password);

		// @todo: update login button in joomla login screen to make this xPath more friendly
		$this->debug('I click Login button');
		$I->click(['xpath' => "//div[@class='login']/form/fieldset/div[4]/div/button"]);
		$this->debug('I wait to see Frontend Member Profile Form with the Logout button in the module');
		$I->waitForElement(['xpath' => "//form[@id='login-form']/div[@class='logout-button']"], 60);
	}

	/**
	 * Function to Do frontend Logout in Joomla!
	 *
	 * @return  void
	 */
	public function doFrontendLogout()
	{
		$I = $this;
		$this->debug('I open Joomla Frontend Login Page');
		$I->amOnPage('/index.php?option=com_users&view=login');
		$this->debug('I click Logout button');
		$I->click(['xpath' => "//div[@class='logout']//button[contains(text(), 'Log out')]"]);
		$this->debug('I wait to see Login form');
		$I->waitForElement(['xpath' => "//div[@class='login']//button[contains(text(), 'Log in')]"], 30);
		$I->seeElement(['xpath' => "//div[@class='login']//button[contains(text(), 'Log in')]"]);
	}

	/**
	 * Installs Joomla
	 *
	 * @return  void
	 */
	public function installJoomla()
	{
		$I = $this;

		// Install Joomla CMS');

		$this->debug('I open Joomla Installation Configuration Page');
		$I->amOnPage('/installation/index.php');
		$this->debug('I check that FTP tab is not present in installation. Otherwise it means that I have not enough '
			. 'permissions to install joomla and execution will be stoped');
		$I->dontSeeElement(['id' => 'ftp']);

		// I Wait for the text Main Configuration, meaning that the page is loaded
		$this->debug('I wait for Main Configuration');
		$I->waitForElement('#jform_language', 60);
		$I->debug('Wait for chosen to render the Languages list field');
		$I->wait(2);
		$I->debug('I select dk-DK as installation language');

		// Select a random language to force reloading of the lang strings after selecting English
		$I->selectOptionInChosen('#jform_language', 'Danish (DK)');
		$I->waitForText('Generel konfiguration', 60, 'h3');

		// Wait for chosen to render the field
		$I->debug('I select en-GB as installation language');
		$I->debug('Wait for chosen to render the Languages list field');
		$I->wait(2);
		$I->selectOptionInChosen('#jform_language', 'English (United Kingdom)');
		$I->waitForText('Main Configuration', 60, 'h3');
		$this->debug('I fill Site Name');
		$I->fillField(['id' => 'jform_site_name'], 'Joomla CMS test');
		$this->debug('I fill Site Description');
		$I->fillField(['id' => 'jform_site_metadesc'], 'Site for testing Joomla CMS');

		// I get the configuration from acceptance.suite.yml (see: tests/_support/acceptancehelper.php)
		$this->debug('I fill Admin Email');
		$I->fillField(['id' => 'jform_admin_email'], $this->config['admin email']);
		$this->debug('I fill Admin Username');
		$I->fillField(['id' => 'jform_admin_user'], $this->config['username']);
		$this->debug('I fill Admin Password');
		$I->fillField(['id' => 'jform_admin_password'], $this->config['password']);
		$this->debug('I fill Admin Password Confirmation');
		$I->fillField(['id' => 'jform_admin_password2'], $this->config['password']);
		$this->debug('I click Site Offline: no');

		// ['No Site Offline']
		$I->click(['xpath' => "//fieldset[@id='jform_site_offline']/label[@for='jform_site_offline1']"]);
		$this->debug('I click Next');
		$I->click(['link' => 'Next']);

		$this->debug('I Fill the form for creating the Joomla site Database');
		$I->waitForText('Database Configuration', 60, ['css' => 'h3']);

		$this->debug('I select MySQLi');
		$I->selectOption(['id' => 'jform_db_type'], $this->config['database type']);
		$this->debug('I fill Database Host');
		$I->fillField(['id' => 'jform_db_host'], $this->config['database host']);
		$this->debug('I fill Database User');
		$I->fillField(['id' => 'jform_db_user'], $this->config['database user']);
		$this->debug('I fill Database Password');
		$I->fillField(['id' => 'jform_db_pass'], $this->config['database password']);
		$this->debug('I fill Database Name');
		$I->fillField(['id' => 'jform_db_name'], $this->config['database name']);
		$this->debug('I fill Database Prefix');
		$I->fillField(['id' => 'jform_db_prefix'], $this->config['database prefix']);
		$this->debug('I click Remove Old Database ');
		$I->selectOptionInRadioField('Old Database Process', 'Remove');
		$this->debug('I click Next');
		$I->click(['link' => 'Next']);
		$this->debug('I wait Joomla to remove the old database if exist');
		$I->wait(1);
		$I->waitForElementVisible(['id' => 'jform_sample_file-lbl'], 30);

		$this->debug('I install joomla with or without sample data');
		$I->waitForText('Finalisation', 60, ['xpath' => '//h3']);

		// @todo: installation of sample data needs to be created

		// No sample data
		$I->selectOption(['id' => 'jform_sample_file'], ['id' => 'jform_sample_file0']);
		$I->click(['link' => 'Install']);

		// Wait while Joomla gets installed
		$this->debug('I wait for Joomla being installed');
		$I->waitForText('Congratulations! Joomla! is now installed.', 60, ['xpath' => '//h3']);
	}

	/**
	 * Install Joomla removing the Installation folder at the end of the execution
	 *
	 * @return  void
	 */
	public function installJoomlaRemovingInstallationFolder()
	{
		$I = $this;

		$I->installJoomla();

		$this->debug('Removing Installation Folder');
		$I->click(['xpath' => "//input[@value='Remove installation folder']"]);

		$I->debug('I wait for Removing Installation Folder button to become disabled');
		$I->waitForJS("return jQuery('form#adminForm input[name=instDefault]').attr('disabled') == 'disabled';", 60);

		$I->debug('Joomla is now installed');
		$I->see('Congratulations! Joomla! is now installed.', ['xpath' => '//h3']);
	}

	/**
	 * Installs Joomla with Multilingual Feature active
	 *
	 * @param   array  $languages  Array containing the language names to be installed
	 *
	 * @example: $I->installJoomlaMultilingualSite(['Spanish', 'French']);
	 *
	 * @return  void
	 */
	public function installJoomlaMultilingualSite($languages = array())
	{
		if (!$languages)
		{
			// If no language is passed French will be installed by default
			$languages[] = 'French';
		}

		$I = $this;

		$I->installJoomla();

		$this->debug('I go to Install Languages page');
		$I->click(['id' => 'instLangs']);
		$I->waitForText('Install Language packages', 60, ['xpath' => '//h3']);

		foreach ($languages as $language)
		{
			$I->debug('I mark the checkbox of the language: ' . $language);
			$I->click(['xpath' => "//label[contains(text()[normalize-space()], '$language')]"]);
		}

		$I->click(['link' => 'Next']);
		$I->waitForText('Multilingual', 60, ['xpath' => '//h3']);
		$I->selectOptionInRadioField('Activate the multilingual feature', 'Yes');
		$I->waitForElementVisible(['id' => 'jform_activatePluginLanguageCode-lbl']);
		$I->selectOptionInRadioField('Install localised content', 'Yes');
		$I->selectOptionInRadioField('Enable the language code plugin', 'Yes');
		$I->click(['link' => 'Next']);

		$I->waitForText('Congratulations! Joomla! is now installed.', 60, ['xpath' => '//h3']);
		$this->debug('Removing Installation Folder');
		$I->click(['xpath' => "//input[@value='Remove installation folder']"]);

		// @todo https://github.com/joomla-projects/joomla-browser/issues/45
		$I->wait(2);

		$this->debug('Joomla is now installed');
		$I->see('Congratulations! Joomla! is now installed.', ['xpath' => '//h3']);
	}

	/**
	 * Sets in Adminitrator->Global Configuration the Error reporting to Development
	 * {@internal doAdminLogin() before}
	 *
	 * @return  void
	 */
	public function setErrorReportingToDevelopment()
	{
		$I = $this;
		$this->debug('I open Joomla Global Configuration Page');
		$I->amOnPage('/administrator/index.php?option=com_config');
		$this->debug('I wait for Global Configuration title');
		$I->waitForText('Global Configuration', 60, ['css' => '.page-title']);
		$this->debug('I open the Server Tab');
		$I->click(['link' => 'Server']);
		$this->debug('I wait for error reporting dropdown');
		$I->selectOptionInChosen('Error Reporting', 'Development');
		$this->debug('I click on save');
		$this->clickToolbarButton('save');
		$this->debug('I wait for global configuration being saved');
		$I->waitForText('Global Configuration', 60, ['css' => '.page-title']);
		$I->see('Configuration successfully saved.', ['id' => 'system-message-container']);
	}

	/**
	 * Installs a Extension in Joomla that is located in a folder inside the server
	 *
	 * @param   String  $path  Path for the Extension
	 * @param   string  $type  Type of Extension
	 *
	 * @note: doAdminLogin() before
	 *
	 * @deprecated  since Joomla 3.4.4-dev. Use installExtensionFromFolder($path, $type = 'Extension') instead.
	 *
	 * @return      void
	 */
	public function installExtensionFromDirectory($path, $type = 'Extension')
	{
		$this->debug('Suggested to use installExtensionFromFolder instead of installExtensionFromDirectory');
		$this->installExtensionFromFolder($path, $type);
	}

	/**
	 * Installs a Extension in Joomla that is located in a folder inside the server
	 *
	 * @param   String  $path  Path for the Extension
	 * @param   string  $type  Type of Extension
	 *
	 * {@internal doAdminLogin() before}
	 *
	 * @return    void
	 */
	public function installExtensionFromFolder($path, $type = 'Extension')
	{
		$I = $this;
		$I->amOnPage('/administrator/index.php?option=com_installer');
		$I->waitForText('Extensions: Install', '30', ['css' => 'H1']);
		$I->click(['link' => 'Install from Folder']);
		$this->debug('I enter the Path');
		$I->fillField(['id' => 'install_directory'], $path);
		$I->click(['id' => 'installbutton_directory']);
		$I->waitForText('was successful', '60', ['id' => 'system-message-container']);
		$this->debug("$type successfully installed from $path");
	}

	/**
	 * Installs a Extension in Joomla that is located in a url
	 *
	 * @param   String  $url   Url address to the .zip file
	 * @param   string  $type  Type of Extension
	 *
	 * {@internal doAdminLogin() before}
	 *
	 * @return    void
	 */
	public function installExtensionFromUrl($url, $type = 'Extension')
	{
		$I = $this;
		$I->amOnPage('/administrator/index.php?option=com_installer');
		$I->waitForText('Extensions: Install', '30', ['css' => 'H1']);
		$I->click(['link' => 'Install from URL']);
		$this->debug('I enter the url');
		$I->fillField(['id' => 'install_url'], $url);
		$I->click(['id' => 'installbutton_url']);
		$I->waitForText('was successful', '30', ['id' => 'system-message-container']);

		if ($type == 'Extension')
		{
			$this->debug('Extension successfully installed from ' . $url);
		}

		if ($type == 'Plugin')
		{
			$this->debug('Installing plugin was successful.' . $url);
		}

		if ($type == 'Package')
		{
			$this->debug('Installation of the package was successful.' . $url);
		}
	}

	/**
	 * Function to check for PHP Notices or Warnings
	 *
	 * @param   string  $page  Optional, if not given checks will be done in the current page
	 *
	 * {@internal doAdminLogin() before}
	 *
	 * @return    void
	 */
	public function checkForPhpNoticesOrWarnings($page = null)
	{
		$I = $this;

		if ($page)
		{
			$I->amOnPage($page);
		}

		$I->dontSeeInPageSource('Notice:');
		$I->dontSeeInPageSource('<b>Notice</b>:');
		$I->dontSeeInPageSource('Warning:');
		$I->dontSeeInPageSource('<b>Warning</b>:');
		$I->dontSeeInPageSource('Strict standards:');
		$I->dontSeeInPageSource('<b>Strict standards</b>:');
		$I->dontSeeInPageSource('The requested page can\'t be found');
	}

	/**
	 * Selects an option in a Joomla Radio Field based on its label
	 *
	 * @param   string  $label   The text in the <label> with for attribute that links to the radio element
	 * @param   string  $option  The text in the <option> to be selected in the chosen radio button
	 *
	 * @return  void
	 */
	public function selectOptionInRadioField($label, $option)
	{
		$I = $this;
		$I->debug("Trying to select the $option from the $label");
		$label = $I->findField(['xpath' => "//label[contains(normalize-space(string(.)), '$label')]"]);
		$radioId = $label->getAttribute('for');

		$I->click("//fieldset[@id='$radioId']/label[contains(normalize-space(string(.)), '$option')]");
	}

	/**
	 * Selects an option in a Chosen Selector based on its label
	 *
	 * @param   string  $label   The text in the <label> with for attribute that links to the <select> element
	 * @param   string  $option  The text in the <option> to be selected in the chosen selector
	 *
	 * @return void
	 */
	public function selectOptionInChosen($label, $option)
	{
		$select = $this->findField($label);
		$selectID = $select->getAttribute('id');
		$chosenSelectID = $selectID . '_chzn';
		$I = $this;
		$this->debug("I open the $label chosen selector");
		$I->click(['xpath' => "//div[@id='$chosenSelectID']/a/div/b"]);
		$this->debug("I select $option");
		$I->click(['xpath' => "//div[@id='$chosenSelectID']//li[text()='$option']"]);

		// Gives time to chosen to close
		$I->wait(1);
	}

	/**
	 * Selects an option in a Chosen Selector based on its id
	 *
	 * @param   string  $selectId  The id of the <select> element
	 * @param   string  $option    The text in the <option> to be selected in the chosen selector
	 *
	 * @return void
	 */
	public function selectOptionInChosenById($selectId, $option)
	{
		$chosenSelectID = $selectId . '_chzn';
		$I = $this;
		$this->debug("I open the $chosenSelectID chosen selector");
		$I->click(['xpath' => "//div[@id='$chosenSelectID']/a/div/b"]);
		$this->debug("I select $option");
		$I->click(['xpath' => "//div[@id='$chosenSelectID']//li[text()='$option']"]);

		// Gives time to chosen to close
		$I->wait(1);
	}

	/**
	 * Selects an option in a Chosen Selector based on its id
	 *
	 * @param   string  $selectId  The id of the <select> element
	 * @param   string  $option    The text in the <option> to be selected in the chosen selector
	 *
	 * @return void
	 */
	public function selectOptionInChosenByIdUsingJs($selectId, $option)
	{
		$I = $this;

		$option = trim($option);
		$I->executeJS("jQuery('#$selectId option').filter(function(){ return this.text.trim() === \"$option\" }).prop('selected', true);");
		$I->executeJS("jQuery('#$selectId').trigger('liszt:updated').trigger('chosen:updated');");
		$I->executeJS("jQuery('#$selectId').trigger('change');");

		// Give time to Chosen to update
		$I->wait(1);
	}

	/**
	 * Selects one or more options in a Chosen Multiple Select based on its label
	 *
	 * @param   string  $label    Label of the select field
	 * @param   array   $options  Array of options to be selected
	 *
	 * @return void
	 */
	public function selectMultipleOptionsInChosen($label, $options)
	{
		$select = $this->findField($label);
		$selectID = $select->getAttribute('id');
		$chosenSelectID = $selectID . '_chzn';
		$I = $this;

		foreach ($options as $option)
		{
			$this->debug("I open the $label chosen selector");
			$I->click(['xpath' => "//div[@id='$chosenSelectID']/ul"]);
			$this->debug("I select $option");
			$I->click(['xpath' => "//div[@id='$chosenSelectID']//li[contains(text()[normalize-space()], '$option')]"]);

			// Gives time to chosen to close
			$I->wait(1);
		}
	}

	/**
	 * Function to Logout from Administrator Panel in Joomla!
	 *
	 * @return void
	 */
	public function doAdministratorLogout()
	{
		$I = $this;
		$I->click(['xpath' => "//ul[@class='nav nav-user pull-right']//li//a[@class='dropdown-toggle']"]);
		$this->debug("I click on Top Right corner toggle to Logout from Admin");
		$I->waitForElement(['xpath' => "//li[@class='dropdown open']/ul[@class='dropdown-menu']//a[text() = 'Logout']"], 60);
		$I->click(['xpath' => "//li[@class='dropdown open']/ul[@class='dropdown-menu']//a[text() = 'Logout']"]);
		$I->waitForElement(['id' => 'mod-login-username'], 60);
		$I->waitForText('Log in', 60, ['xpath' => "//fieldset[@class='loginform']//button"]);
	}

	/**
	 * Function to Enable a Plugin
	 *
	 * @param   String  $pluginName  Name of the Plugin
	 *
	 * @return void
	 */
	public function enablePlugin($pluginName)
	{
		$I = $this;
		$I->amOnPage('/administrator/index.php?option=com_plugins');
		$this->debug('I check for Notices and Warnings');
		$this->checkForPhpNoticesOrWarnings();
		$I->searchForItem($pluginName);
		$I->waitForElement($this->searchResultPluginName($pluginName), 30);
		$I->checkExistenceOf($pluginName);
		$I->click(['xpath' => "//input[@id='cb0']"]);
		$I->click(['xpath' => "//div[@id='toolbar-publish']/button"]);
		$I->see('successfully enabled', ['id' => 'system-message-container']);
	}

	/**
	 * Function to return Path for the Plugin Name to be searched for
	 *
	 * @param   String  $pluginName  Name of the Plugin
	 *
	 * @return string
	 */
	private function searchResultPluginName($pluginName)
	{
		$path = "//form[@id='adminForm']/div/table/tbody/tr[1]/td[4]/a[contains(text(), '" . $pluginName . "')]";

		return $path;
	}

	/**
	 * Uninstall Extension based on a name
	 *
	 * @param   string  $extensionName  Is important to use a specific
	 *
	 * @return  void
	 */
	public function uninstallExtension($extensionName)
	{
		$I = $this;
		$I->amOnPage('/administrator/index.php?option=com_installer&view=manage');
		$I->waitForText('Extensions: Manage', '30', ['css' => 'H1']);
		$I->searchForItem($extensionName);
		$I->waitForElement(['id' => 'manageList'], '30');
		$I->click(['xpath' => "//input[@id='cb0']"]);
		$I->click(['xpath' => "//div[@id='toolbar-delete']/button"]);
		$I->acceptPopup();
		$I->waitForText('was successful', '30', ['id' => 'system-message-container']);
		$I->see('was successful', ['id' => 'system-message-container']);
		$I->searchForItem($extensionName);
		$I->waitForText(
			'There are no extensions installed matching your query.',
			60,
			['class' => 'alert-no-items']
		);
		$I->see('There are no extensions installed matching your query.', ['class' => 'alert-no-items']);
		$this->debug('Extension successfully uninstalled');
	}

	/**
	 * Function to Search For an Item in Joomla! Administrator Lists views
	 *
	 * @param   String  $name  Name of the Item which we need to Search
	 *
	 * @return void
	 */
	public function searchForItem($name = null)
	{
		$I = $this;

		if ($name)
		{
			$I->debug("Searching for $name");
			$I->fillField(['id' => "filter_search"], $name);
			$I->click(['xpath' => "//button[@type='submit' and @data-original-title='Search']"]);
		}
		else
		{
			$I->debug('clearing search filter');
			$I->click(['xpath' => "//button[@type='button' and @data-original-title='Clear']"]);
		}
	}

	/**
	 * Function to Check of the Item Exist in Search Results in Administrator List.
	 *
	 * note: on long lists of items the item that your are looking for may not appear in the first page. We recommend
	 * the usage of searchForItem method before using the current method.
	 *
	 * @param   String  $name  Name of the Item which we are Searching For
	 *
	 * @return void
	 */
	public function checkExistenceOf($name)
	{
		$I = $this;
		$I->debug("Verifying if $name exist in search result");
		$I->seeElement(['xpath' => "//form[@id='adminForm']/div/table/tbody"]);
		$I->see($name, ['xpath' => "//form[@id='adminForm']/div/table/tbody"]);
	}

	/**
	 * Function to select all the item in the Search results in Administrator List
	 *
	 * Note: We recommend use of checkAllResults function only after searchForItem to be sure you are selecting only the desired result set
	 *
	 * @return void
	 */
	public function checkAllResults()
	{
		$I = $this;
		$this->debug("Selecting Checkall button");
		$I->click(['xpath' => "//thead//input[@name='checkall-toggle' or @name='toggle']"]);
	}

	/**
	 * Function to install a language through the interface
	 *
	 * @param   string  $languageName  Name of the language you want to install
	 *
	 * @return void
	 */
	public function installLanguage($languageName)
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_installer&view=languages');
		$this->debug('I check for Notices and Warnings');
		$this->checkForPhpNoticesOrWarnings();
		$this->debug('Refreshing languages');
		$I->click(['xpath' => "//div[@id='toolbar-refresh']/button"]);
		$I->waitForElement(['id' => 'j-main-container'], 30);
		$I->searchForItem($languageName);
		$I->waitForElement($this->searchResultLanguageName($languageName), 30);
		$I->click(['id' => "cb0"]);
		$I->click(['xpath' => "//div[@id='toolbar-upload']/button"]);
		$I->waitForText('was successful.', 60, ['id' => 'system-message-container']);
		$I->see('No Matching Results', ['class' => 'alert-no-items']);
		$this->debug($languageName . ' successfully installed');
	}

	/**
	 * Function to return Path for the Language Name to be searched for
	 *
	 * @param   String  $languageName  Name of the language
	 *
	 * @return string
	 */
	private function searchResultLanguageName($languageName)
	{
		$xpath = "//form[@id='adminForm']/div/table/tbody/tr[1]/td[2]/label[contains(text(),'" . $languageName . "')]";

		return $xpath;
	}

	/**
	 * Publishes a module on frontend in given position
	 *
	 * @param   string  $module    The full name of the module
	 * @param   string  $position  The template position of a module. Right position by default
	 *
	 * @return void
	 */
	public function setModulePosition($module, $position = 'position-7')
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_modules');
		$I->searchForItem($module);
		$I->click(['link' => $module]);
		$I->waitForText("Modules: $module", 30, ['css' => 'h1.page-title']);
		$I->click(['link' => 'Module']);
		$I->waitForElement(['id' => 'general'], 30);
		$I->selectOptionInChosen('Position', $position);
		$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
		$I->waitForText('Module successfully saved', 30, ['id' => 'system-message-container']);
	}

	/**
	 * Publishes a module on all frontend pages
	 *
	 * @param   string  $module  The full name of the module
	 *
	 * @return  void
	 */
	public function publishModule($module)
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_modules');
		$I->searchForItem($module);
		$I->checkAllResults();
		$I->click(['xpath' => "//div[@id='toolbar-publish']/button"]);
		$I->waitForText('1 module successfully published.', 30, ['id' => 'system-message-container']);
	}

	/**
	 * Changes the module Menu assignment to be shown on all the pages of the website
	 *
	 * @param   string  $module  The full name of the module
	 *
	 * @return  void
	 */
	public function displayModuleOnAllPages($module)
	{
		$I = $this;
		$I->amOnPage('administrator/index.php?option=com_modules');
		$I->searchForItem($module);
		$I->click(['link' => $module]);
		$I->waitForElement(['link' => 'Menu Assignment'], 30);
		$I->click(['link' => 'Menu Assignment']);
		$I->waitForElement(['id' => 'jform_menus-lbl'], 30);
		$I->click(['id' => 'jform_assignment_chzn']);
		$I->click(['xpath' => "//li[@data-option-array-index='0']"]);
		$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
		$I->waitForText('Module successfully saved', 30, ['id' => 'system-message-container']);
	}

	/**
	 * Function to select Toolbar buttons in Joomla! Admin Toolbar Panel
	 *
	 * @param   string  $button  The full name of the button
	 *
	 * @return  void
	 */
	public function clickToolbarButton($button)
	{
		$I = $this;
		$input = strtolower($button);

		$screenSize = explode("x", $this->config['window_size']);

		if ($screenSize[0] <= 480)
		{
			$I->click('Toolbar');
		}

		switch ($input)
		{
			case "new":
				$I->click(['xpath' => "//div[@id='toolbar-new']//button"]);
				break;
			case "edit":
				$I->click(['xpath' => "//div[@id='toolbar-edit']//button"]);
				break;
			case "publish":
				$I->click(['xpath' => "//div[@id='toolbar-publish']//button"]);
				break;
			case "unpublish":
				$I->click(['xpath' => "//div[@id='toolbar-unpublish']//button"]);
				break;
			case "archive":
				$I->click(['xpath' => "//div[@id='toolbar-archive']//button"]);
				break;
			case "check-in":
				$I->click(['xpath' => "//div[@id='toolbar-checkin']//button"]);
				break;
			case "batch":
				$I->click(['xpath' => "//div[@id='toolbar-batch']//button"]);
				break;
			case "rebuild":
				$I->click(['xpath' => "//div[@id='toolbar-refresh']//button"]);
				break;
			case "trash":
				$I->click(['xpath' => "//div[@id='toolbar-trash']//button"]);
				break;
			case "save":
				$I->click(['xpath' => "//div[@id='toolbar-apply']//button"]);
				break;
			case "save & close":
				$I->click(['xpath' => "//div[@id='toolbar-save']//button"]);
				break;
			case "save & new":
				$I->click(['xpath' => "//div[@id='toolbar-save-new']//button"]);
				break;
			case "cancel":
				$I->click(['xpath' => "//div[@id='toolbar-cancel']//button"]);
				break;
			case "options":
				$I->click(['xpath' => "//div[@id='toolbar-options']//button"]);
				break;
			case "empty trash":
				$I->click(['xpath' => "//div[@id='toolbar-delete']//button"]);
				break;
		}
	}

	/**
	 * Creates a menu item with the Joomla menu manager, only working for menu items without additional required fields
	 *
	 * @param   string  $menuTitle     The menu item title
	 * @param   string  $menuCategory  The category of the menu type (for example Weblinks)
	 * @param   string  $menuItem      The menu item type / link text (for example List all Web Link Categories)
	 * @param   string  $menu          The menu where the item should be created
	 * @param   string  $language      If you are using Multilingual feature, the language for the menu
	 *
	 * @return  void
	 */
	public function createMenuItem($menuTitle, $menuCategory, $menuItem, $menu = 'Main Menu', $language = 'All')
	{
		$I = $this;

		$I->debug("I open the menus page");
		$I->amOnPage('administrator/index.php?option=com_menus&view=menus');
		$I->waitForText('Menus', '60', ['css' => 'H1']);
		$this->checkForPhpNoticesOrWarnings();

		$I->debug("I click in the menu: $menu");
		$I->click(['link' => $menu]);
		$I->waitForText('Menus: Items', '60', ['css' => 'H1']);
		$this->checkForPhpNoticesOrWarnings();

		$I->debug("I click new");
		$I->click("New");
		$I->waitForText('Menus: New Item', '60', ['css' => 'h1']);
		$this->checkForPhpNoticesOrWarnings();
		$I->fillField(['id' => 'jform_title'], $menuTitle);

		$I->debug("Open the menu types iframe");
		$I->click(['link' => "Select"]);
		$I->waitForElement(['id' => 'menuTypeModal'], '60');
		$I->wait(1);
		$I->switchToIFrame("Menu Item Type");

		$I->debug("Open the menu category: $menuCategory");

		// Open the category
		$I->wait(1);
		$I->waitForElement(['link' => $menuCategory], '60');
		$I->click(['link' => $menuCategory]);

		$I->debug("Choose the menu item type: $menuItem");
		$I->wait(1);
		$I->waitForElement(['xpath' => "//a[contains(text()[normalize-space()], '$menuItem')]"], '60');
		$I->click(['xpath' => "//div[@id='collapseTypes']//a[contains(text()[normalize-space()], '$menuItem')]"]);
		$I->debug('I switch back to the main window');
		$I->switchToIFrame();
		$I->debug('I leave time to the iframe to close');
		$I->wait(2);
		$I->selectOptionInChosen('Language', $language);
		$I->waitForText('Menus: New Item', '30', ['css' => 'h1']);
		$I->debug('I save the menu');
		$I->click("Save");

		$I->waitForText('Menu item successfully saved', '60', ['id' => 'system-message-container']);
	}

	/**
	 * Function to filter results in Joomla! Administrator.
	 *
	 * @param   string  $label  Label of the Filter you want to use.
	 * @param   string  $value  Value you want to set in the filters.
	 *
	 * @return void
	 */
	public function setFilter($label, $value)
	{
		$label = strtolower($label);
		$filters = array(
			"select status" 	=> "filter_published",
			"select access"		=> "filter_access",
			"select language" 	=> "filter_language",
			"select tag"		=> "filter_tag",
			"select max levels"	=> "filter_level"
		);

		$I = $this;
		$I->click(['xpath' => "//button[@data-original-title='Filter the list items.']"]);
		$I->debug('I try to select the filters');

		foreach ($filters as $fieldName => $id)
		{
			if ($fieldName == $label)
			{
				$I->selectOptionInChosenByIdUsingJs($id, $value);
			}
		}

		$I->debug('Applied filters');
	}

	/**
	 * Function to Verify the Tabs on a Joomla! screen
	 *
	 * @param   Array  $expectedTabs  Expected Tabs on the Page
	 * @param   Mixed  $tabsLocator   Locator for the Tabs in Edit View
	 *
	 * @return void
	 */
	public function verifyAvailableTabs($expectedTabs, $tabsLocator = ['xpath' => "//ul[@id='myTabTabs']/li/a"])
	{
		$I = $this;
		$actualArrayOfTabs = $I->grabMultiple($tabsLocator);
		$I->debug("Fetch the current list of Tabs in the edit view which is: " . implode(", ", $actualArrayOfTabs));
		$url = $I->grabFromCurrentUrl();
		$I->assertEquals($expectedTabs, $actualArrayOfTabs, "Tab Labels do not match on edit view of" . $url);
		$I->debug('Verify the Tabs');
	}

	/**
	 * Hide the statistics info message
	 *
	 * {@internal doAdminLogin() before}
	 *
	 * @return void
	 */
	public function disableStatistics()
	{
		$I = $this;
		$this->debug('I click on never');
		$I->waitForElement(['link' => 'Never'], 60);
		$I->click(['link' => 'Never']);
	}
}
