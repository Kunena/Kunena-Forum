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

/**
 * The dashboard model
 *
 * @package		Kunena
 * @subpackage	com_kunena
 */
class KunenaModelDashboard extends JModel
{
	/**
	 * Determines if your site needs ACL upgrades
	 *
	 * @return	int		Positive int if initialised, zero if not
	 */
	function getAclInitialised()
	{
		$db	= &$this->getDBO();

		// Check that control is installed.
		$db->setQuery(
			'SHOW TABLES LIKE '.$db->Quote($db->replacePrefix('#__core_acl_acl_sections'))
		);

		// If the table is present, get the number of items.
		if ($db->loadResult())
		{
			$db->setQuery(
				'SELECT COUNT(*)' .
				' FROM #__core_acl_acl_sections' .
				' WHERE `value`='.$db->Quote('com_kunena')
			);
			return $db->loadResult();
		}
		else {
			$this->setError('FB Error JXtended Control not installed');
			return 0;
		}
	}

	/**
	 * Gets the version log
	 *
	 * @return	array
	 */
	function getVersions()
	{
		static $instance;

		if ($instance == null) {
			$db		= &$this->getDBO();
			$db->setQuery(
				'SELECT *' .
				' FROM #__kunena' .
				' ORDER BY installed_date DESC'
			);
			$instance	= $db->loadObjectList();
		}
		return $instance;
	}

	function getUpgrades()
	{
		$versions	= $this->getVersions();
		$result		= array();

		if ($lastVer = array_shift($versions)) {
			$thisVer		= new KunenaVersion;
			$thisVersion	= $thisVer->version.'.'.$thisVer->subversion;
			$lastVersion	= $lastVer->version;
			if (version_compare($thisVersion, $lastVersion) == 1) {
				$files = JFolder::files(JPATH_COMPONENT.DS.'install', '^upgradesql');
				foreach ($files as $file) {
					$parts	= explode('.', $file);
					$fileVersion	= str_replace('_', '.', $parts[1]);
					if (version_compare($fileVersion, $lastVersion) > 0) {
						$result[$fileVersion]	= $file;
					}
				}
			}
		}
		return	$result;
	}
}