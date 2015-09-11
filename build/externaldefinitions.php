<?php
/**
 * Kunena Component
 * @package Kunena.Build
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Never use this file in any code - it is only here to provide declarations for external functions
defined('KUNENA_NOT_DEFINED') or die();

// dummy definitions of external classes and functions to avoid zend studio warnings and errors

/**
 * Class Kunena
 */
class Kunena {
	/**
	 * @return bool
	 */
	static function isSvn() {}
}

// Community Builder dummies
/**
 * @param $var
 */
function cbimport($var) {}

/**
 * @return int
 */
function getCBprofileItemid() { return 0; }

/**
 * @param $var
 *
 * @return string
 */
function cbSef($var) { return ''; }

/**
 * @param $var
 */
function outputCbTemplate($var) {}

/**
 * @param $var
 */
function getLangDefinition($var) {}

/**
 * Class CBUser
 */
class CBUser {}

/**
 * Class CBAuthentication
 */
class CBAuthentication {}
function selectTemplate() {}

/**
 * Class cbParamsBase
 */
class cbParamsBase {}

// JomSocial dummies
/**
 * Class CFactory
 */
class CFactory {}

/**
 * Class CRoute
 */
class CRoute {
	/**
	 * @return string
	 */
	static function _() { return ''; }
}

/**
 * Class CMessaging
 */
class CMessaging {}

/**
 * Class CActivityStream
 */
class CActivityStream {}

/**
 * Class CUserPoints
 */
class CUserPoints {}
define('COMMUNITY_GROUP_ADMIN', 1);

// AlphaUserPoints
/**
 * Class AlphaUserPointsHelper
 */
class AlphaUserPointsHelper {}

// UddeIM
/**
 * Class uddeIMAPI
 */
class uddeIMAPI {}

// JUpgrage
/**
 * Class jUpgrade
 */
class jUpgrade {};

/**
 * Class jUpgradeExtensions
 */
class jUpgradeExtensions {};

// GeSHi
/**
 * Class GeSHi
 */
class GeSHi {
	function enable_keyword_links() {}

	/**
	 * @return string
	 */
	function parse_code() { return ''; }
};

// XCache
define('XC_TYPE_PHP', 1);
