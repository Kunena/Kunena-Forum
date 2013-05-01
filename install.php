<?php
/**
 * Kunena Package
 * @package Kunena.Package
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
			'0' => '5.4.14' // Preferred version
		),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
		),
		'Joomla!' => array (
			'3.1' => '3.1.1',
			'3.0' => '3.0.3',
			'2.5' => '2.5.9',
			'0' => '2.5.11' // Preferred version
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

		return true;
	}

	public function makeRoute($uri) {
		return JRoute::_($uri, false);
	}

	public function postflight($type, $parent) {
		// Clear Joomla system cache.
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
<div id="kunena-modal" class="modal hide fade"><div class="modal-body"></div></div><script>jQuery('#kunena-modal').remove().prependTo('body').modal({backdrop: 'static', keyboard: false, remote: '{$this->makeRoute('index.php?option=com_kunena&view=install&format=raw')}'})</script>
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
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is higly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');
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
		if (file_exists ( $api )) require_once $api;

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
}
