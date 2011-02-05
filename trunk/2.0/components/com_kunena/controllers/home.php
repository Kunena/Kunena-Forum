<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.forum.category.helper' );

/**
 * Kunena Home Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerHome extends KunenaController {
	public $home = 1;

	public function display() {
		$app = JFactory::getApplication ();
		$menu = $app->getMenu ();
		$home = $menu->getActive ();
		// TODO: maybe add error
		if (!$home) return;
		$view = '';

		// We need to highlight default menu item and show it instead of home page -- there's nothing to see in this view
		$active = $this->_getDefaultMenuItem($menu, $home);
		if (!$active) {
			// There is no default menu item, use categories view instead
			$view = 'categories';
			$active = $menu->getItem ( KunenaRoute::getItemID("index.php?option=com_kunena&view={$view}") );
		}
		if (!$active) {
			// FIXME:
			JError::raiseError ( 500, JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
		}

		// Remove query variables set by home menu item
		foreach ( $home->query as $var => $value ) {
			if (isset($_REQUEST[$var]) && $_REQUEST[$var] == $value) {
				JRequest::setVar ( $var, null );
			}
		}
		// Add query variables from shown menu item
		foreach ( $active->query as $var => $value ) {
			JRequest::setVar ( $var, $value );
		}
		// Set view if we are using our backup view
		if ($view) {
			JRequest::setVar ( 'view', $view );
		}

		// Set active menu item to point the real page
		$menu->setActive ( $active->id );

		// Run display task from our new controller
		$controller = KunenaController::getInstance();
		$controller->execute ('display');

		// Set redirect and message
		$this->setRedirect ($controller->getRedirect(), $controller->getMessage(), $controller->getMessageType());
	}

	protected function _getDefaultMenuItem($menu, $active, $visited=array()) {

		if (empty ( $active->query ['defaultmenu'] ) || $active->id == $active->query ['defaultmenu']) {
			// There is no highlighted menu item
			return null;
		}

		$item = $menu->getItem ( $active->query ['defaultmenu'] );
		if (! $item) {
			// Menu item points to nowhere, abort
			KunenaError::warning ( JText::sprintf ( 'COM_KUNENA_WARNING_MENU_NOT_EXISTS' ), 'menu' );
			return null;

		} elseif (isset($visited[$item->id])) {
			// Menu loop detected, abort
			// TODO: add translation
			KunenaError::warning ( JText::sprintf ( 'COM_KUNENA_WARNING_MENU_LOOP' ), 'menu' );
			return null;

		} elseif (empty ( $item->component ) || $item->component != 'com_kunena' || !isset($item->query ['view'])) {
			// Menu item doesn't point to Kunena, abort
			KunenaError::warning ( JText::sprintf ( 'COM_KUNENA_WARNING_MENU_NOT_KUNENA' ), 'menu' );
			return null;

		} elseif ( $item->query ['view'] == 'home' ) {
			// Menu item is pointing to another Home Page, try to find default menu item from there
			$visited[$item->id] = 1;
			$item = $this->_getDefaultMenuItem($menu, $item->query ['defaultmenu'], $visited);

		}
		return $item;
	}
}