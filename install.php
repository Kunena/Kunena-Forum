<?php
/**
 * Kunena Package
 * @package Kunena.Package
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena package installer script.
 */
class Pkg_KunenaInstallerScript {
	/**
	 * List of supported versions. Newest version first!
	 * @var array
	 */
	protected $versions = array(
		'PHP' => array (
			'5.3' => '5.3.1',
			'0' => '5.4.15' // Preferred version
		),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
		),
		'Joomla!' => array (
			'3.4' => '3.4.1',
			'3.3' => '3.3.6',
			'2.5' => '2.5.28',
			'0' => '3.4.1' // Preferred version
		)
	);
	/**
	 * List of required PHP extensions.
	 * @var array
	 */
	protected $extensions = array ('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	public function install($parent) {
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		return self::install($parent);
	}

	public function uninstall($parent) {
		return true;
	}

	public function preflight($type, $parent) {
		/** @var JInstallerComponent $parent */
		$manifest = $parent->getParent()->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version)) return false;

		// Remove old log file before installation.
		$logFile = JFactory::getConfig()->get('log_path').'/kunena.php';
		if (is_file($logFile)) {
			@unlink($logFile);
		}

		return true;
	}

	public function makeRoute($uri) {
		return JRoute::_($uri, false);
	}

	public function postflight($type, $parent) {
		$this->fixUpdateSite();

		// Clear Joomla system cache.
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache')) {
			@apc_clear_cache();
		}

		if ($type == 'uninstall') return true;

		$this->enablePlugin('system', 'kunena');
		$this->enablePlugin('quickicon', 'kunena');

		$app = JFactory::getApplication();
		if (version_compare(JVERSION, '3.0', '>')) {
			$modal = <<<EOS
<div id="kunena-modal" class="modal hide fade" style="width:34%;margin-left:-20%;top:25%;"><div class="modal-body"></div></div><script>jQuery('#kunena-modal').remove().prependTo('body').modal({backdrop: 'static', keyboard: false, remote: '{$this->makeRoute('index.php?option=com_kunena&view=install&format=raw')}'})</script>
EOS;

		} else {
			$modal = "<script>window.addEvent('domready',function(){SqueezeBox.open('{$this->makeRoute('index.php?option=com_kunena&view=install&tmpl=component')}',{size:{x:530,y:140},sizeLoading:{x:530,y:140},closable:false,handler:'iframe'});});</script>";
		}
		$app->enqueueMessage('Installing Kunena... '.$modal);

		return true;
	}

	function enablePlugin($group, $element) {
		$plugin = JTable::getInstance('extension');
		if (!$plugin->load(array('type'=>'plugin', 'folder'=>$group, 'element'=>$element))) {
			return false;
		}
		$plugin->enabled = 1;
		return $plugin->store();
	}

	public function checkRequirements($version) {
		$db = JFactory::getDbo();
		$pass  = $this->checkVersion('PHP', phpversion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion ());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli'));
		$pass &= $this->checkExtensions($this->extensions);
		$pass &= $this->checkKunena($version);
		return $pass;
	}

	// Internal functions

	protected function checkVersion($name, $version) {
		$app = JFactory::getApplication();

		$major = $minor = 0;
		foreach ($this->versions[$name] as $major=>$minor) {
			if (!$major || version_compare($version, $major, '<')) continue;
			if (version_compare($version, $minor, '>=')) return true;
			break;
		}
		if (!$major) $minor = reset($this->versions[$name]);
		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');
		return false;
	}

	protected function checkDbo($name, $types) {
		$app = JFactory::getApplication();

		if (in_array($name, $types)) {
			return true;
		}
		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');
		return false;
	}

	protected function checkExtensions($extensions) {
		$app = JFactory::getApplication();

		$pass = 1;
		foreach ($extensions as $name) {
			if (!extension_loaded($name)) {
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}
		return $pass;
	}

	protected function checkKunena($version) {
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (is_file($api)) require_once $api;

		// Do not install over Git repository (K1.6+).
		if ((class_exists('Kunena') && method_exists('Kunena', 'isSvn') && Kunena::isSvn())
				|| (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())) {
			$app->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');
			return false;
		}

		// Check if Kunena can be found from the database.
		$table = $db->getPrefix().'kunena_version';
		$db->setQuery ( "SHOW TABLES LIKE {$db->quote($table)}" );
		if ($db->loadResult () != $table) return true;

		// Get installed Kunena version.
		$db->setQuery("SELECT version FROM {$table} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();
		if (!$installed) return true;

		// Always allow upgrade to the newer version.
		if (version_compare($version, $installed, '>=')) return true;

		// Check if we can downgrade to the current version.
		if (class_exists('KunenaInstaller')) {
			if (KunenaInstaller::canDowngrade($version)) return true;
		} else {
			// Workaround when Kunena files were removed to allow downgrade between bugfix versions.
			$major = preg_replace('/(\d+.\d+)\..*$/', '\\1', $version);
			if (version_compare($installed, $major, '>')) return true;
		}

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.', $installed, $version), 'notice');
		return false;
	}

	protected function fixUpdateSite() {
		$db = JFactory::getDbo();

		// Find all update sites.
		$query = $db->getQuery(true)
			->select($db->quoteName('update_site_id'))->from($db->quoteName('#__update_sites'))
			->where($db->quoteName('location') . ' LIKE '. $db->quote('http://update.kunena.org/%'))
			->order($db->quoteName('update_site_id') . ' ASC');
		$db->setQuery($query);
		$list = (array) $db->loadColumn();

		$query = $db->getQuery(true)
			->set($db->quoteName('name').'='.$db->quote('Kunena 4.0 Update Site'))
			->set($db->quoteName('type').'='.$db->quote('collection'))
			->set($db->quoteName('location').'='.$db->quote('http://update.kunena.org/4.0/list.xml'))
			->set($db->quoteName('enabled').'=1')
			->set($db->quoteName('last_check_timestamp').'=0');

		if (!$list) {
			// Create new update site.
			$query->insert($db->quoteName('#__update_sites'));
			$id = $db->insertid();
		} else {
			// Update last Kunena update site with new information.
			$id = array_pop($list);
			$query->update($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . '=' . $id);
		}
		$db->setQuery($query);
		$db->execute();

		if ($list) {
			$ids = implode(',', $list);

			// Remove old update sites.
			$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . 'IN ('.$ids.')');
			$db->setQuery($query);
			$db->execute();
		}

		// Currently only pkg_kunena gets registered to update site, so remove everything else.
		$list[] = $id;
		$ids = implode(',', $list);

		// Remove old updates.
		$query = $db->getQuery(true)->delete($db->quoteName('#__updates'))->where($db->quoteName('update_site_id') . 'IN ('.$ids.')');
		$db->setQuery($query);
		$db->execute();

		// Remove old update extension bindings.
		$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites_extensions'))->where($db->quoteName('update_site_id') . 'IN ('.$ids.')');
		$db->setQuery($query);
		$db->execute();
	}
}
