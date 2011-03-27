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
		$menu = JFactory::getApplication ()->getMenu ();
		$home = $menu->getActive ();
		// TODO: maybe add error
		if (!$home) return;

		// Find default menu item
		$default = $this->_getDefaultMenuItem($menu, $home);
		if (!$default) {
			// There is no default menu item, use category view instead
			$default = clone $menu->getItem ( KunenaRoute::getItemID("index.php?option=com_kunena&view=category&layout=index") );
			$default->query['view'] = 'category';
			$default->query['layout'] = 'index';
		}
		if (!$default) {
			JError::raiseError ( 500, JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
		}

		// Check if menu item was correctly routed
		$active = $menu->getItem ( KunenaRoute::getItemID() );
		if (!$active || ($active->id != $home->id && $active->id != $default->id)) {
			// Routing has been changed, redirect or fail
			if ($active) {
				JRequest::setVar ( 'defaultmenu', null );
				// FIXME: chack possible redirect loops!
				$this->setRedirect (KunenaRoute::_(null, false));
			}
			return;
		}

		// Check if we are using default menu item
		if (!isset($default->query['layout'])) $default->query['layout'] = 'default';
		foreach ( $default->query as $var => $value ) {
			$cmp = JRequest::getVar($var, null);
			if ($var == 'defaultmenu') continue;
			if ($var == 'view' && $cmp == 'home') continue;
			if ($cmp !== null && $value != $cmp) {
				$default = $home;
				break;
			}
		}
		// Add query variables from shown menu item
		if ($default != $home) {
			foreach ( $default->query as $var => $value ) {
				JRequest::setVar ( $var, $value );
			}
		}
		// Set active menu item to point the real page
		$menu->setActive ( $default->id );

		if (JRequest::getVar ( 'view' ) != 'home') {
			// Run display task from our new controller
			$controller = KunenaController::getInstance(true);
			$controller->execute ('display');
			// Set redirect and message
			$this->setRedirect ($controller->getRedirect(), $controller->getMessage(), $controller->getMessageType());
		}
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