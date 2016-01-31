<?php
/**
 * Kunena Component
 * @package Kunena.Build
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Never use this file in any code - it is only here to provide declarations for external functions
defined('KUNENA_NOT_DEFINED') or die();

// dummy definitions of external classes and functions to avoid zend studio warnings and errors

class Kunena {
	/**
	 * @return bool
	 */
	static function isSvn() {}
}

// Community Builder dummies
function cbimport($var) {}
function getCBprofileItemid() { return 0; }
function cbSef($var) { return ''; }
function outputCbTemplate($var) {}
function getLangDefinition($var) {}
class CBUser {}
class CBAuthentication {}
function selectTemplate() {}
class cbParamsBase {}

// JomSocial dummies
class CFactory {}
class CRoute {
	static function _() { return ''; }
}
class CMessaging {}
class CActivityStream {}
class CUserPoints {}
define('COMMUNITY_GROUP_ADMIN', 1);

// AlphaUserPoints
class AlphaUserPointsHelper {}

// UddeIM
class uddeIMAPI {}

// JUpgrage
class jUpgrade {};
class jUpgradeExtensions {};

// GeSHi
class GeSHi {
	function enable_keyword_links() {}
	function parse_code() { return ''; }
};

// XCache
define('XC_TYPE_PHP', 1);
