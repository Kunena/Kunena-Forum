<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Live Update Component Storage Class
 * Allows to store the update data to a component's parameters. This is the most reliable method. 
 * Its configuration options are:
 * component	string	The name of the component which will store our data. If not specified the extension name will be used.
 * key			string	The name of the component parameter where the serialized data will be stored. If not specified "liveupdate" will be used.
 */
class LiveUpdateStorageComponent extends LiveUpdateStorage
{
	private static $component = null;
	private static $key = null;
	
	public function load($config)
	{
		if(!array_key_exists('component', $config)) {
			self::$component = $config['extensionName'];
		} else {
			self::$component = $config['component'];
		}

		if(!array_key_exists('key', $config)) {
			self::$key = 'liveupdate';
		} else {
			self::$key = $config['key'];
		}
		
		jimport('joomla.html.parameter');
		jimport('joomla.application.component.helper');
		// Not using JComponentHelper to avoid conflicts ;)
		$db = JFactory::getDbo();
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$sql = $db->getQuery(true)
				->select($db->nq('params'))
				->from($db->nq('#__extensions'))
				->where($db->nq('type').' = '.$db->q('component'))
				->where($db->nq('element').' = '.$db->q(self::$component));
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote(self::$component).
				" AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new JRegistry();
			$params->loadJSON($rawparams);
		} else {
			$params = new JParameter($rawparams);
		}
		$data = $params->getValue(self::$key, '');
				
		jimport('joomla.registry.registry');
		self::$registry = new JRegistry('update');
		
		self::$registry->loadINI($data);
	}
	
	public function save()
	{
		$data = self::$registry->toString('INI');
		
		$db = JFactory::getDBO();
		
		// An interesting discovery: if your component is manually updating its
		// component parameters before Live Update is called, then calling Live
		// Update will reset the modified component parameters because
		// JComponentHelper::getComponent() returns the old, cached version of
		// them. So, we have to forget the following code and shoot ourselves in
		// the feet. Dammit!!!
		/*
		jimport('joomla.html.parameter');
		jimport('joomla.application.component.helper');
		$component = JComponentHelper::getComponent(self::$component);
		$params = new JParameter($component->params);
		$params->setValue(self::$key, $data);
		*/

		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$sql = $db->getQuery(true)
				->select($db->nq('params'))
				->from($db->nq('#__extensions'))
				->where($db->nq('type').' = '.$db->q('component'))
				->where($db->nq('element').' = '.$db->q(self::$component));
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote(self::$component).
				" AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		$params = new JRegistry();
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$params->loadJSON($rawparams);
		} else {
			$params->loadINI($rawparams);
		}
		
		$params->setValue(self::$key, $data);
		
		if( version_compare(JVERSION,'1.6.0','ge') )
		{
			// Joomla! 1.6
			$data = $params->toString('JSON');
			$sql = $db->getQuery(true)
				->update($db->nq('#__extensions'))
				->set($db->nq('params').' = '.$db->q($data))
				->where($db->nq('type').' = '.$db->q('component'))
				->where($db->nq('element').' = '.$db->q(self::$component));
		}
		else
		{
			// Joomla! 1.5
			$data = $params->toString('INI');
			$sql = 'UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
				"`option` = ".$db->Quote(self::$component)." AND `parent` = 0 AND `menuid` = 0";
		}

		$db->setQuery($sql);
		$db->query();
	}
} 