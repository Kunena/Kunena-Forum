<?php
/**
 * Kunena Component
 * @package Kunena.Build
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Never use this file in any code - it is only here to provide declarations for external functions
defined('KUNENA_NOT_DEFINED') or die();

// dummy definitions of external classes and functions to avoid zend studio warnings and errors

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class Kunena {
	/**
	 * @return void
	 * @since Kunena
	 */
	static function isSvn() {}
}

// Community Builder dummies
/**
 * @param $var
 *
 *
 * @since version
 */
function cbimport($var) {}

/**
 *
 * @return int
 *
 * @since version
 */
function getCBprofileItemid() { return 0; }

/**
 * @param $var
 *
 * @return string
 *
 * @since version
 */
function cbSef($var) { return ''; }

/**
 * @param $var
 *
 *
 * @since version
 */
function outputCbTemplate($var) {}

/**
 * @param $var
 *
 *
 * @since version
 */
function getLangDefinition($var) {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CBUser {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CBAuthentication {}
function selectTemplate() {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class cbParamsBase {}

// JomSocial dummies

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CFactory {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CRoute {
	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	static function _() { return ''; }
}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CMessaging {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CActivityStream {}

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class CUserPoints {}

/**
 *
 */
define('COMMUNITY_GROUP_ADMIN', 1);

// AlphaUserPoints

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class AlphaUserPointsHelper {}

// UddeIM

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class uddeIMAPI {}

// JUpgrage

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class jUpgrade {};

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class jUpgradeExtensions {};

// GeSHi

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class GeSHi {
	function enable_keyword_links() {}

	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	function parse_code() { return ''; }
};

// XCache
/**
 *
 */
define('XC_TYPE_PHP', 1);
