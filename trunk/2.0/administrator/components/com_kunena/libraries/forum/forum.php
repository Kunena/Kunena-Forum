<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForum Class
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();

/**
 * Kunena Forum Class
 */
class KunenaForum {
	protected static $version = false;
	protected static $version_date = false;
	protected static $version_name = false;
	protected static $version_build = false;

	const PUBLISHED = 0;
	const UNAPPROVED = 1;
	const DELETED = 2;
	const TOPIC_DELETED = 3;

	private function __construct() {}

	public static function isSvn() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function isCompatible($version, $build=false) {
		// If requested version is smaller than 2.0.0-DEV build 4316, it's not compatible
		if (version_compare($version, '2.0.0-DEV', '<') || ($build && $build < 4316)) {
			return false;
		}
		// Check if future version is needed (remove SVN from the check)
		if (version_compare($version, preg_replace('/-SVN/i', '', self::version()), '>') || $build > self::versionBuild()) {
			return false;
		}
		return true;
	}

	public static function version() {
		if (self::$version === false) {
			self::buildVersion();
		}
		return self::$version;
	}

	public static function versionDate() {
		if (self::$version_date === false) {
			self::buildVersion();
		}
		return self::$version_date;
	}

	public static function versionName() {
		if (self::$version_name === false) {
			self::buildVersion();
		}
		return self::$version_name;
	}

	public static function versionBuild() {
		if (self::$version_build === false) {
			self::buildVersion();
		}
		return self::$version_build;
	}

	public static function getVersionInfo() {
		$version = new stdClass();
		$version->version = self::version();
		$version->date = self::versionDate();
		$version->name = self::versionName();
		$version->build = self::versionBuild();
		return $version;
	}

	public static function enabled() {
		if (!JComponentHelper::isEnabled ( 'com_kunena', true )) {
			return false;
		}
		$config = KunenaFactory::getConfig ();
		return !$config->board_offline;
	}

	// Internal functions

	protected static function buildVersion() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			$changelog = file_get_contents ( KPATH_SITE . '/CHANGELOG.php', NULL, NULL, 0, 1000 );
			preg_match ( '|\$Id\: CHANGELOG.php (\d+) (\S+) (\S+) (\S+) \$|', $changelog, $svn );
			preg_match ( '|~~\s+Kunena\s(\d+\.\d+.\d+\S*)|', $changelog, $version );
		}
		self::$version = ('@kunenaversion@' == '@' . 'kunenaversion' . '@') ? strtoupper ( $version [1] . '-SVN' ) : strtoupper ( '@kunenaversion@' );
		self::$version_date = ('@kunenaversiondate@' == '@' . 'kunenaversiondate' . '@') ? $svn [2] : '@kunenaversiondate@';
		self::$version_name = ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'SVN Revision' : '@kunenaversionname@';
		self::$version_build = ('@kunenaversionbuild@' == '@' . 'kunenaversionbuild' . '@') ? $svn [1] : '@kunenaversionbuild@';
	}

	public function display($viewName, $layout='default', $template=null, $params = array()) {
		$viewName = preg_replace( '/[^A-Z0-9_]/i', '', $viewName );
		$view = "KunenaView{$viewName}";
		$model = "KunenaModel{$viewName}";

		// load Kunena main language file so we can leverage language strings from it
		KunenaFactory::loadLanguage();

		// Load classes
		if ( !class_exists( 'KunenaViewCommon' ) ) {
			$vpath = KPATH_SITE . '/views/common/view.html.php';
			if (!is_file($vpath)) return;
			require_once $vpath;
		}
		if ( !class_exists( $view ) ) {
			$vpath = KPATH_SITE . '/views/'.$viewName.'/view.html.php';
			if (!is_file($vpath)) return;
			require_once $vpath;
		}
		if ( $viewName != 'common' && !class_exists( $model ) ) {
			$mpath = KPATH_SITE . '/models/'.$viewName.'.php';
			if (!is_file($mpath)) return;
			require_once $mpath;
		}

		$view = new $view ( array ('base_path' => KPATH_SITE ) );
		if ($viewName != 'common') {
			if (!($params instanceof JParameter)) {
				$parameters = new JParameter('');
				$parameters->bind($params);
			} else {
				$parameters = $params;
			}
			$parameters->set('layout', $layout);
			// Push the model into the view (as default).
			$model = new $model ();
			$model->initialize($parameters);
			$view->setModel ( $model, true );

			$view->common = new KunenaViewCommon ( array ('base_path' => KPATH_SITE ) );
		}
		// Push document object into the view.
		$view->assignRef ( 'document', JFactory::getDocument() );

		// Render the view.
		if ($parameters->get('templatepath')) $view->addTemplatePath($parameters->get('templatepath'));
		$view->displayLayout ($layout, $template);
	}
}