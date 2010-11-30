<?php
/**
 * @version		$Id: install.php 1244 2009-12-02 04:10:31Z mahagr$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.org
 */

//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class KunenaVersion
{
	/**
	 * Contructor
	 *
	 * @since	1.6
	 */
	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	/**
	 * Get warning for unstable releases
	 *
	 * @param	string	Message to be shown containing two %s parameters for version (2.0.0RC) and version type (SVN, RC, BETA etc)
	 * @return	string	Warning message
	 * @since	1.6
	 */
	public function getVersionWarning($msg='COM_KUNENA_VERSION_WARNING')
	{
		if (strpos(Kunena::version(), 'SVN') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_SVN');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_SVN_WARNING');
		} else if (strpos(Kunena::version(), 'RC') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_RC');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_RC_WARNING');
		} else if (strpos(Kunena::version(), 'BETA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_BETA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_BETA_WARNING');
		} else if (strpos(Kunena::version(), 'ALPHA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_ALPHA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_ALPHA_WARNING');
		} else if (strpos(Kunena::version(), 'DEV') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_DEV');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_DEV_WARNING');
		}
		if (!empty($kn_version_warning))
		{
			return JText::sprintf($msg, Kunena::version(), $kn_version_type).' '.$kn_version_warning;
		}
		return '';
	}

	function checkVersion() {
		$version = $this->getDBVersion();
		if (!isset($version->version)) return false;
		if ($version->state) return false;
		if ($version->version != Kunena::version()) return false;
		if ($version->build != Kunena::versionBuild()) return false;
		return true;
	}

	/**
	 * Get version information from database
	 *
	 * @param	string	Kunena table prefix
	 * @return	object	Version table
	 * @since	1.6
	 */
	public function getDBVersion($prefix = 'kunena_')
	{
		if ($prefix)
		{
			// FIXME:
			$this->db->debug(0);
			$this->db->setQuery("SELECT * FROM ".$this->db->nameQuote($this->db->getPrefix().$prefix.'version')." ORDER BY `id` DESC", 0, 1);
			$version = $this->db->loadObject();
		}
		if (!isset($version) || !is_object($version) || !isset($version->state))
		{
			$version->state = '';
		}
		else if (!empty($version->state))
		{
			if ($version->version != Kunena::version() || $version->build != Kunena::versionBuild()) $version->state = '';
		}
		return $version;
	}

	/**
	* Retrieve installed Kunena version as string.
	*
	* @return string "Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname]"
	*/
	function getVersionHTML()
	{
		return 'Kunena '.Kunena::version().' | '.Kunena::versionDate().' | '.Kunena::versionBuild().' [ '.Kunena::versionName().' ]';
	}

	/**
	* Retrieve copyright information as string.
	*
	* @return string "© 2008-2010 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	*/
	function getCopyrightHTML()
	{
		return ': &copy; 2008-2010 '.JText::_('COM_KUNENA_VERSION_COPYRIGHT').': <a href = "http://www.kunena.org" target = "_blank">'
			.JText::_('COM_KUNENA_VERSION_TEAM').'</a>  | '.JText::_('COM_KUNENA_VERSION_LICENSE')
			.': <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">'
			.JText::_('COM_KUNENA_VERSION_GPL').'</a>';
	}

	/**
	* Retrieve installed Kunena version, copyright and license as string.
	*
	* @return string "Installed version: Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname] |
	*		© 2008-2010 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	*/
	function getLongVersionHTML()
	{
		return JText::_('COM_KUNENA_VERSION_INSTALLED').': '.$this->version().' | '.$this->copyright();
	}

}
class KunenaVersionException extends Exception {}