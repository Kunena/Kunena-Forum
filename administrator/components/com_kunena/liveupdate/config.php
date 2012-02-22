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
	var $_updateURL				= 'http://update.kunena.org/kunena/kunena20.ini';
	var $_requiresAuthorization	= false;
	var $_versionStrategy		= 'vcompare';
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array('component' => 'com_kunena', 'key' => 'liveupdate');

	function __construct()
	{
		$this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';

		parent::__construct();
	}
}