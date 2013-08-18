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

class ComponentKunenaControllerPageMenuDisplay extends KunenaControllerDisplay
{
	protected function display() {
		if (!$this->basemenu) return ' ';

		// Display layout with given parameters.
		$content = KunenaLayout::factory('Page/Menubar/Menu')
			->set('list', $this->list)
			->set('path', $this->path)
			->set('active_id', $this->active_id);

		return $content;
	}

	protected function before() {
		$this->basemenu = $basemenu = KunenaRoute::getMenu();
		if (!$basemenu) return;

		$parameters = new JRegistry();
		$ktemplate = KunenaFactory::getTemplate();
		$parameters->set('showAllChildren', $ktemplate->params->get('menu_showall', 0));
		$parameters->set('menutype', $basemenu->menutype);
		$parameters->set('startLevel', $basemenu->level + 1);
		$parameters->set('endLevel', $basemenu->level + $ktemplate->params->get('menu_levels', 1));

		$this->list = KunenaMenuHelper::getList($parameters);
		$this->menu = $this->app->getMenu();
		$this->active = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path = isset($this->active) ? $this->active->tree : array();
		$this->showAll = $parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($parameters->get('class_sfx'));
	}
}
