<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/version.php');

/**
 * Setup Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelSetup extends JModel
{
	function enableLibraries()
	{
		// Check if the plugin file is present.
		if (!file_exists(JPATH_ROOT.'/plugins/system/jxtended.php')) {
			$this->setError(JText::_('KUNENA_PLUGIN_MISSING'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get the plugin information from the database.
		$db->setQuery(
			'SELECT `id`, `published`' .
			' FROM `#__plugins`' .
			' WHERE `folder` = "system"' .
			' AND `element` = "jxtended"'
		);
		$plugin = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Check to see if the plugin is installed.
		if (empty($plugin)) {
			$this->setError(JText::_('KUNENA_PLUGIN_NOT_INSTALLED'));
			return false;
		}

		// Check to see if the plugin is published.
		if (!$plugin->published)
		{
			// Attempt to publish the plugin.
			$db->setQuery(
				'UPDATE `#__plugins`' .
				' SET `published` = 1' .
				' WHERE `folder` = "system"' .
				' AND `element` = "jxtended"'
			);
			$db->query();

			// Check for a database error.
			if ($db->getErrorNum()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	function initializeAccessControls()
	{
		// Get the database object.
		$db = &$this->_db;

		// Get the number of relevant rows in the components table.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM `#__components`' .
			' WHERE `option` = "com_kunena"'
		);
		$installed = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Require the access helper library.
		jximport('jxtended.access.helper');

		// Register the sections
		JXAccessHelper::registerSection('Kunena', 'com_kunena');

		// Register Actions.
		JXAccessHelper::registerAction(JPERMISSION_ACTION, 'com_kunena', 'KUNENA ACCESS MANAGE', 'KUNENA ACCESS MANAGE DESC', 'manage');

		// Register Asset Actions.
		JXAccessHelper::registerAction(JPERMISSION_ASSET, 'com_kunena', 'KUNENA ACCESS CATEGORY POST', 'KUNENA ACCESS CATEGORY POST DESC', 'category.post');
		JXAccessHelper::registerAction(JPERMISSION_ASSET, 'com_kunena', 'KUNENA ACCESS CATEGORY MANAGE', 'KUNENA ACCESS CATEGORY MANAGE DESC', 'category.manage');

		return true;
	}

	function install()
	{
		// Get the current component version information.
		$version = new KunenaVersion();
		$current = $version->version.'.'.$version->subversion.$version->status;

		// Get the database object.
		$db = &$this->_db;

		// Get the number of relevant rows in the components table.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM `#__components`' .
			' WHERE `option` = "com_kunena"'
		);
		$installed = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Check to see if the component is installed.
		if ($installed > 0) {
			$this->setError(JText::_('KUNENA_ALREADY_INSTALLED'));
			return false;
		}

		// Attempt to add the necessary rows to the components table.
		$db->setQuery(
			'INSERT INTO `#__components` VALUES' .
			' (0, "Kunena", "option=com_kunena", 0, 0, "option=com_kunena", "Kunena", "com_kunena", 0, "components/com_kunena/media/images/icon-16-k.png", 0, "", 1)'
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Verify the schema file.
		$file = JPATH_ADMINISTRATOR.'/components/com_kunena/install/installsql.mysql.utf8.php';
		if (!JFile::exists($file)) {
			$this->setError(JText::_('KUNENA_INSTALL_SCHEMA_FILE_MISSING'));
			return false;
		}

		// Set the SQL from the schema file.
		$db->setQuery(JFile::read($file));

		// Attempt to import the component schema.
		$return = $db->queryBatch(false);

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Attempt to insert the manual install entry into the component version table.
		$db->setQuery(
			'INSERT INTO `#__kunena` (`version`,`log`) VALUES' .
			' ('.$db->Quote($current).', '.$db->Quote(JText::sprintf('KUNENA_MANUAL_INSTALL_VERSION', $current)).')'
		);
		$db->query();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		return true;
	}

	function upgrade()
	{
		// Get the component upgrade information.
		$version	= new KunenaVersion();
		$upgrades	= $version->getUpgrades();

		// If there are upgrades to process, attempt to process them.
		if (is_array($upgrades) && count($upgrades))
		{
			// Sort the upgrades, lowest version first.
			uksort($upgrades, 'version_compare');

			// Get the database object.
			$db = &$this->_db;

			// Get the number of relevant rows in the components table.
			$db->setQuery(
				'SELECT COUNT(id)' .
				' FROM `#__components`' .
				' WHERE `option` = "com_kunena"'
			);
			$installed = $db->loadResult();

			// Check for a database error.
			if ($db->getErrorNum()) {
				$this->setError($db->getErrorMsg());
				return false;
			}

			// Check to see if the component is installed.
			if ($installed < 1) {
				$this->setError(JText::_('KUNENA_NOT_INSTALLED'));
				return false;
			}

			foreach ($upgrades as $upgradeVersion => $file)
			{
				$file = JPATH_COMPONENT.DS.'install'.DS.$file;

				if (JFile::exists($file))
				{
					// Set the upgrade SQL from the file.
					$db->setQuery(JFile::read($file));

					// Execute the upgrade SQL.
					$return = $db->queryBatch(false);

					// Check for a database error.
					if ($db->getErrorNum()) {
						$this->setError(JText::sprintf('KUNENA_DATABASE_UPGRADE_FAILED', $db->getErrorMsg()));
						return false;
					}

					// Upgrade was successful, attempt to log it to the versions table.
					$db->setQuery(
						'INSERT INTO `#__kunena` (`version`,`log`) VALUES' .
						' ('.$db->Quote($upgradeVersion).', '.$db->Quote(JText::sprintf('KUNENA_DATABASE_UPGRADE_VERSION', $upgradeVersion)).')'
					);
					$db->query();

					// Check for a database error.
					if ($db->getErrorNum()) {
						$this->setError(JText::sprintf('KUNENA_DATABASE_UPGRADE_FAILED', $db->getErrorMsg()));
						return false;
					}
				}
			}
		}

		return true;
	}
}