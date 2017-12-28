<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetMenuDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetMenuDisplay extends KunenaControllerDisplay
{
	protected $name = 'Widget/Menu';

	public $basemenu;

	public $list;

	public $menu;

	public $active;

	public $path;

	public $active_id;

	public $showAll;

	public $class_sfx;

	/**
	 * Prepare menu display.
	 *
	 * @return boolean
	 */
	protected function before()
	{
		parent::before();

		$this->basemenu = $basemenu = KunenaRoute::getMenu();

		if (!$basemenu)
		{
			return false;
		}

		$parameters = new JRegistry;
		$template = KunenaFactory::getTemplate();
		$parameters->set('showAllChildren', $template->params->get('menu_showall', 0));
		$parameters->set('menutype', $basemenu->menutype);
		$parameters->set('startLevel', $basemenu->level + 1);
		$parameters->set('endLevel', $basemenu->level + $template->params->get('menu_levels', 1));

		$this->list = KunenaMenuHelper::getList($parameters);
		$this->menu = $this->app->getMenu();
		$this->active = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path = isset($this->active) ? $this->active->tree : array();
		$this->showAll = $parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($parameters->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		return true;
	}
}
