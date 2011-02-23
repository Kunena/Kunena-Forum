<?php
/**
* @version $Id$
* MyKunena Plugin
* @package MyKunena
*
* @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*/

/**
 * JFirePHP helper
 *
 * Include this code in your project or copy the entire file into your project.
 * It will allow you to place the FirePHP calls into your code even if FirePHP
 * is not installed on a system. That way you can leverage all of FirePHP's features
 * through the JFirePHP systems plugin, but ship your software without the need for
 * the plugin.
 * The dummy definitions are based on FirePHP 0.3.1
 */

// Only setup dummies if JFirePHP system plugin has not been loaded
if (!defined('JFIREPHP')){
	if (!function_exists('fb')) {
		// Dummy function for older procedural style
		function fb(){}
	}

	if (!class_exists('FB')) {
		// Dummy class for new OO style
		class FB
		{
		  public static function setEnabled($Enabled) {}
		  public static function getEnabled() {}
		  public static function setObjectFilter($Class, $Filter) {}
		  public static function setOptions($Options) {}
		  public static function getOptions() {}
		  public static function send(){}
		  public static function group($Name, $Options=null) {}
		  public static function groupEnd() {}
		  public static function log($Object, $Label=null) {}
		  public static function info($Object, $Label=null) {}
		  public static function warn($Object, $Label=null) {}
		  public static function error($Object, $Label=null) {}
		  public static function dump($Key, $Variable) {}
		  public static function trace($Label) {}
		  public static function table($Label, $Table) {}
		}
	}
}