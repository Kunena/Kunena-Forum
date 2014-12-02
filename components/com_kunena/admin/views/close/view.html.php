<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * This view is displayed when we want to close current modal window.
 */
class KunenaAdminViewClose extends KunenaView {
	/**
	 * Display the view
	 */
	public function displayDefault($tpl = null) {
		// Close modal window.
		JFactory::getDocument()->addScriptDeclaration('
			window.parent.location.href=window.parent.location.href;
			window.parent.SqueezeBox.close();
		');
	}
}
