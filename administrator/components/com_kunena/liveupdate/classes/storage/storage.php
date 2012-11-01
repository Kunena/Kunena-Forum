<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Abstract class for the update parameters storage
 * @author nicholas
 *
 */
class LiveUpdateStorage
{
	/**
	 * The update data registry
	 * @var JRegistry
	 */
	public static $registry = null;

	/**
	 * 
	 * @param string $type
	 * @param array $config
	 * @return LiveUpdateStorage
	 */
	public static function getInstance($type, $config)
	{
		static $instances = array();
		
		$sig = md5($type, serialize($config));
		if(!array_key_exists($sig, $instances)) {
			require_once dirname(__FILE__).'/'.strtolower($type).'.php';
			$className = 'LiveUpdateStorage'.ucfirst($type);
			$object = new $className($config);
			$object->load($config);
			$newRegistry = clone(self::$registry);
			$object->setRegistry($newRegistry);
			$instances[$sig] = $object;
		}
		return $instances[$sig];
	}
	
	/**
	 * Returns the internally used registry
	 * 
	 * @return JRegistry
	 */
	public function &getRegistry()
	{
		return self::$registry;
	}
	
	/**
	 * Replaces the internally used registry with the one supplied
	 * 
	 * @param JRegistry $registry 
	 */
	public function setRegistry($registry)
	{
		self::$registry = $registry;
	}

	
	public final function set($key, $value)
	{
		if($key == 'updatedata') {
			if(function_exists('json_encode') && function_exists('json_decode')) {
				$value = json_encode($value);
			} elseif(function_exists('base64_encode') && function_exists('base64_decode')) {
				$value = base64_encode(serialize($value));
			} else {
				$value = serialize($value);
			}
		}
		if(version_compare(JVERSION, '3.0.0', 'ge')) {
			self::$registry->set("update.$key", $value);
		} else {
			self::$registry->setValue("update.$key", $value);
		}
	}
	
	public final function get($key, $default)
	{
		if(version_compare(JVERSION, '3.0.0', 'ge')) {
			$value = self::$registry->get("update.$key", $default);
		} else {
			$value = self::$registry->getValue("update.$key", $default);
		}
		if($key == 'updatedata') {
			if(function_exists('json_encode') && function_exists('json_decode')) {
				$value = json_decode($value);
			} elseif(function_exists('base64_encode') && function_exists('base64_decode')) {
				$value = unserialize(base64_decode($value));
			} else {
				$value = unserialize($value);
			}
		}
		return $value;
	}
	
	public function save() {}
	
	public function load($config) {}
}