<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_kunena';
	var $_extensionTitle		= 'Kunena Forum';
	var $_updateURL				= 'http://update.kunena.org/com_kunena.ini';
	var $_requiresAuthorization	= false;
	var $_versionStrategy		= 'vcompare';
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array('component' => 'com_kunena', 'key' => 'liveupdate');
	var $_minStability			= 'stable';

	function __construct()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.sys',KPATH_ADMIN, 'en-GB');
		$lang->load('com_kunena.sys') || $lang->load('com_kunena.sys',KPATH_ADMIN);

		$this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';
		$this->_extensionTitle = JText::_('COM_KUNENA');

		parent::__construct();
	}
}
