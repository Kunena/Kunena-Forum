<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
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
defined( '_JEXEC' ) or die('');

class KunenaAccess {
	protected static $instance = null;
	protected static $name = 'access';

	static protected function initialize($name) {
			if (!$name) return false;
			$basedir = dirname(__FILE__);
			$file = self::$name;
			$file = "{$basedir}/{$name}/{$file}.php";
			if (is_file($file)) {
				require_once($file);
				$class = __CLASS__ . ucfirst($name);
				if (!class_exists($class)) return false;
				return new $class();
			}
			return false;
	}

	static public function getInstance()
	{
		if (!self::$instance) {
			$config = KunenaFactory::getConfig();
			$name = ''; //$config->integration_access;

			self::$instance = self::initialize($name);
			if (!self::$instance) {
				if (is_dir(JPATH_LIBRARIES.'/joomla/access')) {
					$name = 'joomla16';
				} else {
					$name = 'joomla15';
				}
				self::$instance = self::initialize($name);
			}
		}
		return self::$instance;
	}
}
