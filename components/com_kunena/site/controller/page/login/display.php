<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerPageLoginDisplay extends KunenaControllerDisplay
{
	protected function display() {
		$me = KunenaUserHelper::getMyself();
		$layout = ($me->exists() ? 'Logout' : 'Login' );

		// Display layout with given parameters.
		$content = KunenaLayout::factory("Page/Login/{$layout}")
			->set('me', $me);

		return $content;
	}
}
