<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaInstaller {
	// Minimum supported versions during downgrade.
	protected static $downgrade = array('3.0' => '3.0.0-DEV', '2.9' => '2.9.90-DEV', '2.0' => '2.0.4');

	public static function canDowngrade($version) {
		if ($version == '@'.'kunenaversion'.'@') return true;
		foreach (self::$downgrade as $major=>$minor) {
			if (version_compare ( $version, $major, "<" )) continue;
			if (version_compare ( $version, $minor, ">=" )) return true;
		}
		return false;
	}
}