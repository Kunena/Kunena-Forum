<?php
/**
 * @version $Id: api.php 3682 2010-10-10 11:37:46Z mahagr $
 * Kunena Component - KunenaForum Class
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
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

	private function __construct() {}

	public static function isSvn() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function isCompatible($version, $build=false) {
		if (version_compare($version, '1.7.0-DEV', '<')) {
			return false;
		}
		if (version_compare($version, self::version(), '>')) {
			return false;
		}
		if ($build && $build < self::versionBuild()) {
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
}