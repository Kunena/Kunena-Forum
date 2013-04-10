<?php
/**
 * Kunena Component
 * @package Kunena.Plugin
 * @subpackage Example
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Example Kunena Plugin.
 *
 * @since		2.0
 */
class plgKunenaExample extends JPlugin {
	/**
	 * Example prepare content method.
	 *
	 * Method is called by the view.
	 *
	 * @param	string	$context	The context of the content being passed to the plugin.
	 * @param	object	$object		The content object.  Note $object->text is also available.
	 * @param	object	$params		The content params.
	 * @param	int		$limitstart	The 'page' number.
	 */
	public function onKunenaPrepare($context, &$object, &$params, $limitstart) {
	}

	/**
	 * Example after display title method.
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder.
	 *
	 * @param	string		$limitstart	The context for the content passed to the plugin.
	 * @param	object		$params		The content object.  Note $object->text is also available.
	 * @param	object		$object		The content params.
	 * @param	int			$context	The 'page' number.
	 * @return	string
	 */
	public function onKunenaAfterTitle($context, &$object, &$params, $limitstart) {
		return '';
	}

	/**
	 * Example before display content method.
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder.
	 *
	 * @param	string		$context	The context for the content passed to the plugin.
	 * @param	object		$object		The content object.  Note $object->text is also available.
	 * @param	object		$params		The content params.
	 * @param	int			$limitstart	The 'page' number.
	 * @return	string
	 */
	public function onKunenaBeforeDisplay($context, &$object, &$params, $limitstart) {
		return '';
	}

	/**
	 * Example after display content method.
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder.
	 *
	 * @param	string		$context	The context for the content passed to the plugin.
	 * @param	object		$object		The content object.  Note $object->text is also available.
	 * @param	object		$params		The content params.
	 * @param	int			$limitstart	The 'page' number.
	 * @return	string
	 */
	public function onKunenaAfterDisplay($context, &$object, &$params, $limitstart) {
		return '';
	}

	/**
	 * Example change state method.
	 *
	 * @param	string	$context	The context for the content passed to the plugin.
	 * @param	array	$pks		A list of primary key ids of the content that has changed state.
	 * @param	int		$value		The value of the state that the content has been changed to.
	 * @return	boolean
	 */
	public function onKunenaChangeState($context, $pks, $value) {
		return true;
	}

	/**
	 * Example before save content method.
	 *
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * You can set the error by calling $object->setError($message)
	 *
	 * @param	string		$context	The context of the content passed to the plugin.
	 * @param	object		$object		A JTableKunena object.
	 * @param	bool		$isNew		If the content is just about to be created.
	 * @return	bool		If false, abort the save.
	 */
	public function onKunenaBeforeSave($context, &$object, $isNew) {
		return true;
	}

	/**
	 * Example after save content method.
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved.
	 *
	 * @param	string		$context	The context of the content passed to the plugin (added in 1.6).
	 * @param	object		$object		A JTableKunena object.
	 * @param	bool		$isNew		If the content is just about to be created.
	 */
	public function onKunenaAfterSave($context, &$object, $isNew) {
		return true;
	}

	/**
	 * Example before delete method.
	 *
	 * @param	string	$context	The context for the content passed to the plugin.
	 * @param	object	$data		The data relating to the content that is to be deleted.
	 * @return	boolean
	 */
	public function onKunenaBeforeDelete($context, $data) {
		return true;
	}

	/**
	 * Example after delete method.
	 *
	 * @param	string	$context	The context for the content passed to the plugin.
	 * @param	object	$data		The data relating to the content that was deleted.
	 * @return	boolean
	 */
	public function onKunenaAfterDelete($context, $data) {
		return true;
	}
}
