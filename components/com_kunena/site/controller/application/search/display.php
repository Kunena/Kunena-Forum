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

class ComponentKunenaControllerApplicationSearchDisplay extends KunenaControllerApplicationDisplay
{
	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Search');

		return $content;
	}

	protected function before() {
		parent::before();
	}
}
