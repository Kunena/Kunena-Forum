<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Finder Kunena Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Kunena.finder
 * @since   2.5
 */
class plgKunenaFinder extends JPlugin
{
	/**
	 * Finder after save message method
	 * Message is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the Message is saved
	 *
	 * @param	string		$context	The context of the message passed to the plugin (added in 1.6)
	 * @param	object		$table		A Table object containing the message
	 * @param	bool		$isNew		If the message has just been created
	 * @since	2.5
	 */
	public function onKunenaAfterSave($context, $table, $isNew)
	{
		JLog::add('onKunenaAfterSave context: '.$context, JLog::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		JLog::add('onKunenaAfterSave table: '.$table_content, JLog::INFO);
		JLog::add('onKunenaAfterSave isNew: '.($isNew)?'Yes':'No', JLog::INFO);
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $table, $isNew));

	}
	/**
	 * Finder before save message method
	 * Method is called right before the content is saved
	 *
	 * @param	string		$context	The context of the content passed to the plugin (added in 1.6)
	 * @param	object		$table		A Table object containing the message
	 * @param	bool		$isNew		If the message is just about to be created
	 * @since   2.5
	 */
	public function onKunenaBeforeSave($context, $table, $isNew)
	{
		JLog::add('onKunenaBeforeSave context: '.$context, JLog::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		JLog::add('onKunenaBeforeSave table: '.$table_content, JLog::INFO);
		JLog::add('onKunenaBeforeSave isNew: '.($isNew)?'Yes':'No', JLog::INFO);

		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $table, $isNew));

	}
	/**
	 * Finder after delete message method
	 *
	 * @param	string		$context	The context of the content passed to the plugin (added in 1.6)
	 * @param	object		$table		A Table object containing the message
	 * @since   2.5
	 */
	public function onKunenaAfterDelete($context, $table)
	{
		JLog::add('onKunenaAfterDelete context: '.$context, JLog::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		JLog::add('onKunenaAfterDelete table: '.$table_content, JLog::INFO);

		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $table));
	}
	/**
	 * Finder after delete message method
	 *
	 * @param	string		$context	The context of the content passed to the plugin (added in 1.6)
	 * @param	object		$table		A Table object containing the message
	 * @since   2.5
	 */
	public function onKunenaBeforeDelete($context, $table)
	{
		JLog::add('onKunenaBeforeDelete context: '.$context, JLog::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		JLog::add('onKunenaBeforeDelete table: '.$table_content, JLog::INFO);
	
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');
	
		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderBeforeDelete', array($context, $table));
	}

}
