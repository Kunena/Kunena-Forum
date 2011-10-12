<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Class
 */
class KunenaForum {
	protected static $version = false;
	protected static $version_major = false;
	protected static $version_date = false;
	protected static $version_name = false;

	const PUBLISHED = 0;
	const UNAPPROVED = 1;
	const DELETED = 2;
	const TOPIC_DELETED = 3;

	const MODERATOR = 1;
	const ADMINISTRATOR = 2;

	private function __construct() {}

	public static function isSvn() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function isCompatible($version) {
		// If requested version is smaller than 2.0.0-DEV, it's not compatible
		if (version_compare($version, '2.0', '<')) {
			return false;
		}
		// Check if future version is needed (remove SVN from the check)
		if (version_compare($version, preg_replace('/-SVN/i', '', self::version()), '>')) {
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

	public static function versionMajor() {
		if (self::$version_major === false) {
			self::buildVersion();
		}
		return self::$version_major;
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

	public static function getVersionInfo() {
		$version = new stdClass();
		$version->version = self::version();
		$version->date = self::versionDate();
		$version->name = self::versionName();
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
			$xml = KPATH_ADMIN . '/kunena.xml';
			$parser = JFactory::getXMLParser ( 'Simple' );
			$parser->loadFile ( $xml );
			self::$version = $parser->document->getElementByPath ( 'version' )->data () . '-SVN';
		} else {
			self::$version = strtoupper ( '@kunenaversion@' );
		}
		self::$version_major = substr(self::$version, 0, 3);
		self::$version_date = ('@kunenaversiondate@' == '@' . 'kunenaversiondate' . '@') ? JFactory::getDate()->toMySQL() : '@kunenaversiondate@';
		self::$version_name = ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'SVN Revision' : '@kunenaversionname@';
	}

	public function display($viewName, $layout='default', $template=null, $params = array()) {
		$viewName = preg_replace( '/[^A-Z0-9_]/i', '', $viewName );
		$view = "KunenaView{$viewName}";
		$model = "KunenaModel{$viewName}";

		// load Kunena main language file so we can leverage language strings from it
		KunenaFactory::loadLanguage();

		require_once KPATH_SITE . '/views/common/view.html.php';
		require_once KPATH_SITE . '/models/common.php';

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

		// Add template path
		$ktemplate = KunenaFactory::getTemplate();
		$templatepath = KPATH_SITE."/{$ktemplate->getPath()}/html/";
		$view->addTemplatePath($templatepath.$viewName);
		if ($parameters->get('templatepath')) $view->addTemplatePath($parameters->get('templatepath'));

		if ($viewName != 'common') {
			$view->common = new KunenaViewCommon ( array ('base_path' => KPATH_SITE ) );
			$view->common->addTemplatePath($templatepath.'common');
		}
		// Push document object into the view.
		$view->assignRef ( 'document', JFactory::getDocument() );

		// Render the view.
		$view->displayLayout ($layout, $template);
	}
}