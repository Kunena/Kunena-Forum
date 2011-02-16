<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaVersion
{
	/**
	 * Contructor
	 *
	 * @since	1.6
	 */
	public function __construct()
	{
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
		if (strpos(KunenaForum::version(), 'SVN') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_SVN');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_SVN_WARNING');
		} else if (strpos(KunenaForum::version(), 'RC') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_RC');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_RC_WARNING');
		} else if (strpos(KunenaForum::version(), 'BETA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_BETA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_BETA_WARNING');
		} else if (strpos(KunenaForum::version(), 'ALPHA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_ALPHA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_ALPHA_WARNING');
		} else if (strpos(KunenaForum::version(), 'DEV') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_DEV');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_DEV_WARNING');
		}
		if (!empty($kn_version_warning))
		{
			return JText::sprintf($msg, KunenaForum::version(), $kn_version_type).' '.$kn_version_warning;
		}
		return '';
	}

	function checkVersion() {
		$version = $this->getDBVersion();
		if (!isset($version->version)) return false;
		if ($version->state) return false;
		if ($version->version != KunenaForum::version()) return false;
		if ($version->build != KunenaForum::versionBuild()) return false;
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
			$db = JFactory::getDBO();
			// FIXME:
			$db->debug(0);
			$db->setQuery("SELECT * FROM ".$db->nameQuote($db->getPrefix().$prefix.'version')." ORDER BY `id` DESC", 0, 1);
			$version = $db->loadObject();
		}
		if (!isset($version) || !is_object($version) || !isset($version->state))
		{
			$version->state = '';
		}
		else if (!empty($version->state))
		{
			if ($version->version != KunenaForum::version() || $version->build != KunenaForum::versionBuild()) $version->state = '';
		}
		return $version;
	}

	/**
	* Retrieve installed Kunena version as string.
	*
	* @return string "Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname]"
	*/
	static function getVersionHTML()
	{
		return 'Kunena '.KunenaForum::version().' | '.KunenaForum::versionDate().' | '.KunenaForum::versionBuild().' [ '.KunenaForum::versionName().' ]';
	}

	/**
	* Retrieve copyright information as string.
	*
	* @return string "© 2008-2011 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	*/
	static function getCopyrightHTML()
	{
		return ': &copy; 2008-2011 '.JText::_('COM_KUNENA_VERSION_COPYRIGHT').': <a href = "http://www.kunena.org" target = "_blank">'
			.JText::_('COM_KUNENA_VERSION_TEAM').'</a>  | '.JText::_('COM_KUNENA_VERSION_LICENSE')
			.': <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">'
			.JText::_('COM_KUNENA_VERSION_GPL').'</a>';
	}

	/**
	* Retrieve installed Kunena version, copyright and license as string.
	*
	* @return string "Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname] | © 2008-2011 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	*/
	static function getLongVersionHTML()
	{
		return self::getVersionHTML() . ' | ' . self::getCopyrightHTML();
	}

}
class KunenaVersionException extends Exception {}