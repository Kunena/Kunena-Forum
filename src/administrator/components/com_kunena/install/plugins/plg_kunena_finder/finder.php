<?php
/**
 * @copyright      Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Finder Kunena Plugin
 *
 * @package        Joomla.Plugin
 * @subpackage     Kunena.finder
 * @since          2.5
 */
class plgKunenaFinder extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * Finder after save message method
	 * Message is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the Message is saved
	 *
	 * @param   string $context The context of the message passed to the plugin (added in 1.6)
	 * @param   object $table   A Table object containing the message
	 * @param   bool   $isNew   If the message has just been created
	 *
	 * @since    2.5
	 */
	public function onKunenaAfterSave($context, $table, $isNew)
	{
		\Joomla\CMS\Log\Log::add('onKunenaAfterSave context: ' . $context, \Joomla\CMS\Log\Log::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		\Joomla\CMS\Log\Log::add('onKunenaAfterSave table: ' . $table_content, \Joomla\CMS\Log\Log::INFO);
		\Joomla\CMS\Log\Log::add('onKunenaAfterSave isNew: ' . ($isNew) ? 'Yes' : 'No', \Joomla\CMS\Log\Log::INFO);

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');

		// Trigger the onFinderAfterSave event.
		\JFactory::getApplication()->triggerEvent('onFinderAfterSave', array($context, $table, $isNew));

	}

	/**
	 * Finder before save message method
	 * Method is called right before the content is saved
	 *
	 * @param   string $context The context of the content passed to the plugin (added in 1.6)
	 * @param   object $table   A Table object containing the message
	 * @param   bool   $isNew   If the message is just about to be created
	 *
	 * @since   2.5
	 */
	public function onKunenaBeforeSave($context, $table, $isNew)
	{
		\Joomla\CMS\Log\Log::add('onKunenaBeforeSave context: ' . $context, \Joomla\CMS\Log\Log::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		\Joomla\CMS\Log\Log::add('onKunenaBeforeSave table: ' . $table_content, \Joomla\CMS\Log\Log::INFO);
		\Joomla\CMS\Log\Log::add('onKunenaBeforeSave isNew: ' . ($isNew) ? 'Yes' : 'No', \Joomla\CMS\Log\Log::INFO);


		\Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');

		// Trigger the onFinderBeforeSave event.
		\JFactory::getApplication()->triggerEvent('onFinderBeforeSave', array($context, $table, $isNew));

	}

	/**
	 * Finder after delete message method
	 *
	 * @param   string $context The context of the content passed to the plugin (added in 1.6)
	 * @param   object $table   A Table object containing the message
	 *
	 * @since   2.5
	 */
	public function onKunenaAfterDelete($context, $table)
	{
		\Joomla\CMS\Log\Log::add('onKunenaAfterDelete context: ' . $context, \Joomla\CMS\Log\Log::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		\Joomla\CMS\Log\Log::add('onKunenaAfterDelete table: ' . $table_content, \Joomla\CMS\Log\Log::INFO);


		\Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');

		// Trigger the onFinderAfterDelete event.
		\JFactory::getApplication()->triggerEvent('onFinderAfterDelete', array($context, $table));
	}

	/**
	 * Finder after delete message method
	 *
	 * @param   string $context The context of the content passed to the plugin (added in 1.6)
	 * @param   object $table   A Table object containing the message
	 *
	 * @since   2.5
	 */
	public function onKunenaBeforeDelete($context, $table)
	{
		\Joomla\CMS\Log\Log::add('onKunenaBeforeDelete context: ' . $context, \Joomla\CMS\Log\Log::INFO);
		ob_start();
		$table_content = ob_get_contents();
		ob_end_clean();
		\Joomla\CMS\Log\Log::add('onKunenaBeforeDelete table: ' . $table_content, \Joomla\CMS\Log\Log::INFO);


		\Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');

		// Trigger the onFinderAfterDelete event.
		\JFactory::getApplication()->triggerEvent('onFinderBeforeDelete', array($context, $table));
	}
}
