<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Fetches the update information from the server or the cache, depending on
 * whether the cache is fresh or not.
 */
class LiveUpdateFetch extends JObject
{
	private $cacheTTL = 24;

	private $storage = null;

	/**
	 * One-stop-shop function which fetches update information and tells you
	 * if there are updates available or not, or if updates are not supported.
	 *
	 * @return int 0 = no updates, 1 = updates available, -1 = updates not supported, -2 = fetching updates crashes the server
	 */
	public function hasUpdates()
	{
		$updateInfo = $this->getUpdateInformation();

		if($updateInfo->stuck) return -2;

		if(!$updateInfo->supported) return -1;

		$config = LiveUpdateConfig::getInstance();
		$extInfo = $config->getExtensionInformation();

		// Filter by stability level
		$minStability = $config->getMinimumStability();
		$stability = strtolower($updateInfo->stability);

		switch($minStability) {
			case 'alpha':
			default:
				// Reports any stability level as an available update
				break;

			case 'beta':
				// Do not report alphas as available updates
				if(in_array($stability, array('alpha'))) return 0;
				break;

			case 'rc':
				// Do not report alphas and betas as available updates
				if(in_array($stability, array('alpha','beta'))) return 0;
				break;

			case 'stable':
				// Do not report alphas, betas and rcs as available updates
				if(in_array($stability, array('alpha','beta','rc'))) return 0;
				break;
		}

		if(empty($updateInfo->version) && empty($updateInfo->date)) return 0;

		// Use the version strategy to determine the availability of an update
		switch($config->getVersionStrategy()) {
			case 'newest':
				jimport('joomla.utilities.date');
				if(empty($extInfo)) {
					$mine = new JDate('2000-01-01 00:00:00');
				} else {
					$mine = new JDate($extInfo['date']);
				}

				$theirs = new JDate($updateInfo->date);

				return ($theirs->toUnix() > $mine->toUnix()) ? 1 : 0;
				break;

			case 'vcompare':
				$mine = $extInfo['version'];
				if(empty($mine)) $mine = '0.0.0';
				$theirs = $updateInfo->version;
				if(empty($theirs)) $theirs = '0.0.0';

				return (version_compare($theirs, $mine, 'gt')) ? 1 : 0;
				break;

			case 'different':
				$mine = $extInfo['version'];
				if(empty($mine)) $mine = '0.0.0';
				$theirs = $updateInfo->version;
				if(empty($theirs)) $theirs = '0.0.0';

				return ($theirs != $mine) ? 1 : 0;
				break;
		}
	}

	/**
	 * Get the latest version (update) information, either from the cache or
	 * from the update server.
	 *
	 * @param $force bool Set to true to force fetching fresh data from the server
	 *
	 * @return stdClass The update information, in object format
	 */
	public function getUpdateInformation($force = false)
	{
		// Get the Live Update configuration
		$config = LiveUpdateConfig::getInstance();

		// Get an instance of the storage class
		$storageOptions = $config->getStorageAdapterPreferences();
		require_once dirname(__FILE__).'/storage/storage.php';
		$this->storage = LiveUpdateStorage::getInstance($storageOptions['adapter'], $storageOptions['config']);
		$storage = $this->storage;

		// If we are requested to forcibly reload the information, clear old data first
		if($force) {
			$this->storage->set('lastcheck', 0);
			$this->storage->set('updatedata', '');
			$this->storage->save();
			$registry = new JRegistry('update');
			$storage->setRegistry($registry);
		}

		// Fetch information from the cache
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$registry = $storage->getRegistry();
			$lastCheck = $registry->get('lastcheck', 0);
			$cachedData = $registry->get('updatedata', null);
		} else {
			$lastCheck = $storage->get('lastcheck', 0);
			$cachedData = $storage->get('updatedata', null);
		}

		if(is_string($cachedData)) {
			$cachedData = trim($cachedData,'"');
			$cachedData = json_decode($cachedData);
		}

		if(empty($cachedData)) {
			$lastCheck = 0;
		}

		// Check if the cache is at most $cacheTTL hours old
		$now = time();
		$maxDifference = $this->cacheTTL * 3600;
		$difference = abs($now - $lastCheck);

		if(!($force) && ($difference <= $maxDifference)) {
			// The cache is fresh enough; return cached data
			return $cachedData;
		} else {
			// The cache is stale; fetch new data, cache it and return it to the caller
			$data = $this->getUpdateData($force);
			$this->storage->set('lastcheck', $now);
			$this->storage->set('updatedata', json_encode($data));
			$this->storage->save();
			return $data;
		}
	}

	/**
	 * Retrieves the update data from the server, unless previous runs indicate
	 * that the download process gets stuck and ends up in a WSOD.
	 *
	 * @param bool $force Set to true to force fetching new data no matter if the process is marked as stuck
	 * @return stdClass
	 */
	private function getUpdateData($force = false)
	{
		$ret = array(
			'supported'		=> false,
			'stuck'			=> true,
			'version'		=> '',
			'date'			=> '',
			'stability'		=> '',
			'downloadURL'	=> '',
			'infoURL'		=> '',
			'releasenotes'	=> ''
		);

		// If the process is marked as "stuck", we won't bother fetching data again; well,
		// unless you really force me to, by setting $force = true.
		if( ($this->storage->get('stuck',0) != 0) && !$force) return (object)$ret;

		$ret['stuck'] = false;

		require_once dirname(__FILE__).'/download.php';

		// First we mark Live Updates as getting stuck. This way, if fetching the update
		// fails with a server error, reloading the page will not result to a White Screen
		// of Death again. Hey, Joomla! core team, are you listening? Some hosts PRETEND to
		// support cURL or URL fopen() wrappers but using them throws an immediate WSOD.
		$this->storage->set('stuck', 1);
		$this->storage->save();

		$config = LiveUpdateConfig::getInstance();
		$extInfo = $config->getExtensionInformation();
		$url = $extInfo['updateurl'];
		$rawData = LiveUpdateDownloadHelper::downloadAndReturn($url);

		// Now that we have some data returned, let's unmark the process as being stuck ;)
		$this->storage->set('stuck', 0);
		$this->storage->save();

		// If we didn't get anything, assume Live Update is not supported (communication error)
		if(empty($rawData) || ($rawData == false)) return (object)$ret;

		// TODO Detect the content type of the returned update stream. For now, I will pretend it's an INI file.

		$data = $this->parseINI($rawData);
		$ret['supported'] = true;

		return (object)array_merge($ret, $data);
	}

	/**
	 * Fetches update information from the server using cURL
	 * @return string The raw server data
	 */
	private function fetchCURL()
	{
		$config = LiveUpdateConfig::getInstance();
		$extInfo = $config->getExtensionInformation();
		$url = $extInfo['updateurl'];

		$process = curl_init($url);
		$config = new LiveUpdateConfig();
		$config->applyCACert($process);
		curl_setopt($process, CURLOPT_HEADER, 0);
		// Pretend we are Firefox, so that webservers play nice with us
		curl_setopt($process, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.14) Gecko/20110105 Firefox/3.6.14');
		curl_setopt($process, CURLOPT_ENCODING, 'gzip');
		curl_setopt($process, CURLOPT_TIMEOUT, 10);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		// The @ sign allows the next line to fail if open_basedir is set or if safe mode is enabled
		@curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($process, CURLOPT_MAXREDIRS, 20);
		$inidata = curl_exec($process);
		curl_close($process);
		return $inidata;
	}

	/**
	 * Fetches update information from the server using file_get_contents, which internally
	 * uses URL fopen() wrappers.
	 * @return string The raw server data
	 */
	private function fetchFOPEN()
	{
		$config = LiveUpdateConfig::getInstance();
		$extInfo = $config->getExtensionInformation();
		$url = $extInfo['updateurl'];

		return @file_get_contents($url);
	}

	/**
	 * Parses the raw INI data into an array of update information
	 * @param string $rawData The raw INI data
	 * @return array The parsed data
	 */
	private function parseINI($rawData)
	{
		$ret = array(
			'version'		=> '',
			'date'			=> '',
			'stability'		=> '',
			'downloadURL'	=> '',
			'infoURL'		=> '',
			'releasenotes'	=> ''
		);

		// Get the magic string
		$magicPos = strpos($rawData, '; Live Update provision file');

		if($magicPos === false) {
			// That's not an INI file :(
			return $ret;
		}

		if($magicPos !== 0) {
			$rawData = substr($rawData, $magicPos);
		}

		require_once dirname(__FILE__).'/inihelper.php';
		$iniData = LiveUpdateINIHelper::parse_ini_file($rawData, false, true);

		// Get the supported platforms
		$supportedPlatform = false;
		$versionParts = explode('.',JVERSION);
		$currentPlatform = $versionParts[0].'.'.$versionParts[1];

		if(array_key_exists('platforms', $iniData)) {
			$rawPlatforms = explode(',', $iniData['platforms']);
			foreach($rawPlatforms as $platform) {
				$platform = trim($platform);
				if(substr($platform,0,7) != 'joomla/') {
					continue;
				}
				$platform = substr($platform, 7);
				if($currentPlatform == $platform) {
					$supportedPlatform = true;
				}
			}
		} else {
			// Lies, damn lies
			$supportedPlatform = true;
		}

		if(!$supportedPlatform) {
			return $ret;
		}

		$ret['version'] = $iniData['version'];
		$ret['date'] = $iniData['date'];
		$config = LiveUpdateConfig::getInstance();
		$auth = $config->getAuthorization();
		$glue = strpos($iniData['link'],'?') === false ? '?' : '&';
		$ret['downloadURL'] = $iniData['link'] . (empty($auth) ? '' : $glue.$auth);
		if(array_key_exists('stability', $iniData)) {
			$stability = $iniData['stability'];
		} else {
			// Stability not defined; guesswork mode enabled
			$version = $ret['version'];
			if( preg_match('#^[0-9\.]*a[0-9\.]*#', $version) == 1 ) {
				$stability = 'alpha';
			} elseif( preg_match('#^[0-9\.]*b[0-9\.]*#', $version) == 1 ) {
				$stability = 'beta';
			} elseif( preg_match('#^[0-9\.]*rc[0-9\.]*#', $version) == 1 ) {
				$stability = 'rc';
			} elseif( preg_match('#^[0-9\.]*$#', $version) == 1 ) {
				$stability = 'stable';
			} else {
				$stability = 'svn';
			}
		}
		$ret['stability'] = $stability;

		if(array_key_exists('releasenotes', $iniData)) {
			$ret['releasenotes'] = $iniData['releasenotes'];
		}

		if(array_key_exists('infourl', $iniData)) {
			$ret['infoURL'] = $iniData['infourl'];
		}

		return $ret;
	}
}