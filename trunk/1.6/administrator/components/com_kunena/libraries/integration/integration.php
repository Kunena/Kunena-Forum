<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

// Abstract base class for various 3rd party integration classes
abstract class KunenaIntegration extends JObject {
	protected static $instances = array ();
	protected $loaded = false;

	static public function getInstance($integration) {
		if (! $integration)
			return false;
		if (! isset ( self::$instances [$integration] )) {
			$basedir = dirname ( __FILE__ );
			$file = "{$basedir}/{$integration}/integration.php";
			if (is_file ( $file )) {
				require_once ($file);
				$class = __CLASS__ . ucfirst ( $integration );
				self::$instances [$integration] = new $class ( );
			} else {
				self::$instances [$integration] = false;
			}
		}
		return self::$instances [$integration];
	}

	public function isLoaded() {
		return $this->loaded;
	}

	static public function initialize($name, $integration) {
		if (! $integration)
			$integration = 'none';
		if ($integration == 'auto')
			$integration = self::detectIntegration ( $name, true );
		else if ($integration == 'joomla')
			$integration = self::detectJoomla ();
		$basedir = dirname ( __FILE__ );
		$file = "{$basedir}/{$integration}/{$name}.php";
		if (is_file ( $file )) {
			require_once ($file);
			$class = 'Kunena' . ucfirst ( $name ) . ucfirst ( $integration );
			if (! class_exists ( $class ))
				return null;
			return new $class ( );
		}
		return null;
	}

	static protected function detectJoomla() {
		if (is_dir ( JPATH_LIBRARIES . '/joomla/access' )) {
			return 'joomla16';
		} else {
			return 'joomla15';
		}
	}

	static public function detectIntegration($name, $best = false) {
		jimport ( 'joomla.filesystem.folder' );
		$dir = dirname ( __FILE__ );
		$folders = JFolder::folders ( $dir );
		$list = array ();
		foreach ( $folders as $integration ) {
			$file = "$dir/$integration/$name.php";
			if (is_file ( $file )) {
				$obj = self::initialize ( $name, $integration );
				$priority = 0;
				if ($obj)
					$priority = $obj->priority;
				$list [$integration] = $priority;
				unset ( $obj );
			}
		}
		if ($best) {
			// Return best choice
			arsort ( $list );
			reset ( $list );
			return key ( $list );
		}
		// Return associative list of all options
		return $list;
	}

	// abstract function to be overriden in derived class
	public function load() {
	}
}