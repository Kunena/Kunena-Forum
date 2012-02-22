<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * This is the base class inherited by the config.php file in LiveUpdate's root.
 * You may override it non-final members to customise its behaviour. 
 * @author Nicholas K. Dionysopoulos <nicholas@akeebabackup.com>
 *
 */
abstract class LiveUpdateAbstractConfig extends JObject
{
	/** @var string The extension name, e.g. com_foobar, plg_foobar, mod_foobar, tpl_foobar etc */
	protected $_extensionName = 'com_foobar';
	/** @var string The human-readable name of your extension */
	protected $_extensionTitle = 'Foobar Component for Joomla!';
	/** 
	 * The filename of the XML manifest of your extension. Leave blank to use extensionname.xml. For example,
	 * if the extension is com_foobar, it will look for com_foobar.xml and foobar.xml in the component's
	 * directory.
	 * @var string
	 * */
	protected $_xmlFilename = '';
	
	/** @var string The information storage adapter to use. Can be 'file' or 'component' */
	protected $_storageAdapter = 'file';
	/** @var array The configuration options for the storage adapter used */
	protected $_storageConfig = array('path' => JPATH_CACHE);
	/**
	 * How to determine if a new version is available. 'different' = if the version number is different,
	 * the remote version is newer, 'vcompare' = use version compare between the two versions, 'newest' =
	 * compare the release dates to find the newest. I suggest using 'different' on most cases.
	 * @var string
	 */
	protected $_versionStrategy = 'different';
	
	/** @var The current version of your extension. Populated automatically from the XML manifest. */ 
	protected $_currentVersion = '';
	/** @var The current release date of your extension. Populated automatically from the XML manifest. */
	protected $_currentReleaseDate = '';
	
	/** @var string The URL to the INI update stream of this extension */
	protected $_updateURL = '';
	/** @var bool Does the download URL require authorization to download the package? */
	protected $_requiresAuthorization = false;
	
	/** @var string The username to authorize a download on your site */
	protected $_username = '';
	/** @var string The password to authorize a download on your site */
	protected $_password = '';
	/** @var string The Download ID to authorize a download on your site; use it instead of the username/password pair */
	protected $_downloadID = '';
	
	/** @var string The path to a local copy of cacert.pem, required if you plan on using HTTPS URLs to fetch live udpate information or download files from */
	protected $_cacerts = null;
	
	/** @var string The minimum stability level to report as available update. One of alpha, beta, rc and stable. */
	protected $_minStability = 'alpha';
	
	/**
	 * Singleton implementation
	 * @return LiveUpdateConfig An instance of the Live Update configuration class
	 */
	public static function &getInstance()
	{
		static $instance = null;
		
		if(!is_object($instance)) {
			$instance = new LiveUpdateConfig();
		}
		
		return $instance;
	}
	
	/**
	 * Public constructor. It populates all extension-specific fields. Override to your liking if necessary.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->populateExtensionInfo();
		$this->populateAuthorization();
	}
	
	/**
	 * Returns the URL to the update INI stream. By default it returns the value to
	 * the protected $_updateURL property of the class. Override with your implementation
	 * if you want to modify its logic.
	 */
	public function getUpdateURL()
	{
		return $this->_updateURL;
	}
	
	/**
	 * Override this ethod to load customized CSS and media files instead of the stock
	 * CSS and media provided by Live Update. If you override this class it MUST return
	 * true, otherwise LiveUpdate's CSS will be loaded after yours and will override your
	 * settings.
	 * 
	 * @return bool Return true to stop Live Update from loading its own CSS files.
	 */
	public function addMedia()
	{
		return false;
	}
	
	/**
	 * Gets the authorization string to append to the download URL. It returns either the
	 * download ID or username/password pair. Please override the class constructor, not
	 * this method, if you want to fetch these values.
	 */
	public final function getAuthorization()
	{
		if(!empty($this->_downloadID)) {
			return "dlid=".urlencode($this->_downloadID);
		}
		if(!empty($this->_username) && !empty($this->_password)) {
			return "username=".urlencode($this->_username)."&password=".urlencode($this->_password);
		}
		return "";
	}
	
	public final function requiresAuthorization()
	{
		return $this->_requiresAuthorization;
	}
	
	/**
	 * Returns all the information we have about the extension and its update preferences
	 * @return array The extension information
	 */
	public final function getExtensionInformation()
	{
		return array(
			'name'			=> $this->_extensionName,
			'title'			=> $this->_extensionTitle,
			'version'		=> $this->_currentVersion,
			'date'			=> $this->_currentReleaseDate,
			'updateurl'		=> $this->_updateURL,
			'requireauth'	=> $this->_requiresAuthorization
		);
	}
	
	/**
	 * Returns the information regarding the storage adapter
	 * @return array
	 */
	public final function getStorageAdapterPreferences()
	{
		$config = $this->_storageConfig;
		$config['extensionName'] = $this->_extensionName;
		
		return array(
			'adapter'		=> $this->_storageAdapter,
			'config'		=> $config
		);
	}
	
	public final function getVersionStrategy()
	{
		return $this->_versionStrategy;
	}
	
	/**
	 * Get the current version from the XML manifest of the extension and
	 * populate the class' properties.
	 */
	private function populateExtensionInfo()
	{
		require_once dirname(__FILE__).'/xmlslurp.php';
		$xmlslurp = new LiveUpdateXMLSlurp();
		$data = $xmlslurp->getInfo($this->_extensionName, $this->_xmlFilename);
		if(empty($this->_currentVersion)) $this->_currentVersion = $data['version'];
		if(empty($this->_currentReleaseDate)) $this->_currentReleaseDate = $data['date'];
	}
	
	/**
	 * Fetch username/password and Download ID from the component's configuration.
	 */
	private function populateAuthorization()
	{
		if(!$this->_requiresAuthorization) return;
		
		if(substr($this->_extensionName,0,3) != 'com') return;
		
		jimport('joomla.html.parameter');
		jimport('joomla.application.component.helper');
		
		// Not using JComponentHelper to avoid conflicts ;)
		$db = JFactory::getDbo();
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$sql = $db->getQuery(true)
				->select($db->nq('params'))
				->from($db->nq('#__extensions'))
				->where($db->nq('type').' = '.$db->q('component'))
				->where($db->nq('element').' = '.$db->q($this->_extensionName));
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote($this->_extensionName).
				" AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new JRegistry();
			$params->loadJSON($rawparams);
		} else {
			$params = new JParameter($rawparams);
		}
		
		$this->_username	= $params->getValue('username','');
		$this->_password	= $params->getValue('password','');
		$this->_downloadID	= $params->getValue('downloadid','');
	}
	
	public function applyCACert(&$ch)
	{
		if(!empty($this->_cacerts)) {
			if(file_exists($this->_cacerts)) {
				@curl_setopt($ch, CURLOPT_CAINFO, $this->_cacerts);
			}
		}
	}
	
	public function getMinimumStability()
	{
		return $this->_minStability;
	}
}