<?php
/**
 * @version		$Id: install.php 1244 2009-12-02 04:10:31Z mahagr$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Install Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelVersion extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	protected $_installed = false;

	/**
	 * Contructor
	 *
	 * @since	1.6
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = JFactory::getDBO();
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	The default value to use if no state property exists by name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null, $default = null)
	{
		// if the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false)
		{
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
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
		if (strpos(KUNENA_VERSION, 'SVN') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_SVN');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_SVN_WARNING');
		} else if (strpos(KUNENA_VERSION, 'RC') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_RC');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_RC_WARNING');
		} else if (strpos(KUNENA_VERSION, 'BETA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_BETA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_BETA_WARNING');
		} else if (strpos(KUNENA_VERSION, 'ALPHA') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_ALPHA');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_ALPHA_WARNING');
		} else if (strpos(KUNENA_VERSION, 'DEV') !== false) {
			$kn_version_type = JText::_('COM_KUNENA_VERSION_DEV');
			$kn_version_warning = JText::_('COM_KUNENA_VERSION_DEV_WARNING');
		}
		if (!empty($kn_version_warning))
		{
			return JText::sprintf($msg, KUNENA_VERSION, $kn_version_type).' '.$kn_version_warning;
		}
		return '';
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
			$this->db->setQuery("SELECT * FROM ".$this->db->nameQuote($this->db->getPrefix().$prefix.'version')." ORDER BY `id` DESC", 0, 1);
			$version = $this->db->loadObject();
			if ($this->db->getErrorNum()) throw new KunenaVersionException($this->db->getErrorMsg(), $this->db->getErrorNum());
		}
		if (!isset($version) || !is_object($version) || !isset($version->state))
		{
			$version->state = '';
		}
		else if (!empty($version->state))
		{
			if ($version->version != KUNENA_VERSION || $version->build != KUNENA_VERSION_BUILD) $version->state = '';
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
		return 'Kunena '.KUNENA_VERSION.' | '.KUNENA_VERSION_DATE.' | '.KUNENA_VERSION_BUILD.' [ '.KUNENA_VERSION_NAME.' ]';
	}

	/**
	* Retrieve copyright information as string.
	*
	* @return string "© 2008-2010 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	*/
	function getCopyrightHTML()
	{
		return ': &copy; 2008-2010 '.JText::_('COM_KUNENA_VERSION_COPYRIGHT').': <a href = "http://www.kunena.com" target = "_blank">'
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