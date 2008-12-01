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

/**
 * Version Object for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaVersion extends JObject
{
	var $version	= '1.0';

	var $subversion	= '0';

	var $date		= '2008-11-25 00:00:00';

	var $status		= 'alpha';

	var $revision	= '$Revision$';

	/**
	 * Container for version information.
	 *
	 * @access	pirvate
	 * @var		array
	 */
	var $_versions = null;

	/**
	 * Container for upgrade information.
	 *
	 * @access	pirvate
	 * @var		array
	 */
	var $_upgrades = null;


	/**
	 * Method to get the SVN build number.
	 *
	 * @access	public
	 * @return	int		SVN build number
	 * @since	1.0
	 */
	function getBuild()
	{
		return intval(substr($this->revision, 11));
	}

	/**
	 * Method to get version history information.
	 *
	 * @access	public
	 * @return	mixed	False on failure, array otherwise.
	 * @since	1.0
	 */
	function getVersions()
	{
		if (!empty($this->_versions)) {
			return $this->_versions;
		}

		// Load the version information.
		$db	= &JFactory::getDBO();
		$db->setQuery('SELECT * FROM `#__kunena` ORDER BY `id` DESC');
		$this->_versions = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		return $this->_versions;
	}

	/**
	 * Method to get version upgrade information.
	 *
	 * @access	public
	 * @return	mixed	False on failure, array otherwise.
	 * @since	1.0
	 */
	function getUpgrades()
	{
		if (!empty($this->_upgrades)) {
			return $this->_upgrades;
		}

		$this->upgrades = array();

		// Get the version log data.
		$versions = (array)$this->getVersions();

		if ($lastVer = array_shift($versions))
		{
			$thisVer = new KunenaVersion();
			$thisVersion = $thisVer->version.'.'.$thisVer->subversion.$thisVer->status;
			$lastVersion = $lastVer->version;

			// Is the current version newer than the last version recorded?
			if (version_compare($thisVersion, $lastVersion) == 1)
			{
				// Yes, so look for upgrade SQL files.
				$files = JFolder::files(JPATH_COMPONENT.DS.'install', '^upgradesql');

				// Grab only the upgrade SQL files that are newer than the current version.
				foreach ($files as $file)
				{
					$parts = explode('.', $file);
					$fileVersion = str_replace('_', '.', $parts[1]);

					if (version_compare($fileVersion, $lastVersion) > 0) {
						$this->_upgrades[$fileVersion] = $file;
					}
				}
			}
		}

		return $this->_upgrades;
	}

	/**
	 * Method to show upgrades.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function showUpgrades()
	{
		if (count($this->getUpgrades())) {
			$url = JRoute::_('index.php?option=com_kunena&task=setup.upgrade&'.JUtility::getToken().'=1');
			JError::raiseWarning(500, JText::sprintf('KUNENA_DATABASE_UPGRADE_REQUIRED', $url));
		}
	}
}