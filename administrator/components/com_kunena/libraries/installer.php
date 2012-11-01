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
	protected static $downgrade = array('2.0' => '2.0.0-BETA1');

	public static function canDowngrade($version) {
		if ($version == '@'.'kunenaversion'.'@') return true;
		foreach (self::$downgrade as $major=>$minor) {
			if (version_compare ( $version, $major, "<" )) continue;
			if (version_compare ( $version, $minor, ">=" )) return true;
			break;
		}
		return false;
	}
}