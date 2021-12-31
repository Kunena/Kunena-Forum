<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetMenuDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetMenuDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Widget/Menu';

	/**
	 * @var
	 * @since Kunena
	 */
	public $basemenu;

	/**
	 * @var
	 * @since Kunena
	 */
	public $list;

	/**
	 * @var
	 * @since Kunena
	 */
	public $menu;

	/**
	 * @var
	 * @since Kunena
	 */
	public $active;

	/**
	 * @var
	 * @since Kunena
	 */
	public $path;

	/**
	 * @var
	 * @since Kunena
	 */
	public $active_id;

	/**
	 * @var
	 * @since Kunena
	 */
	public $showAll;

	/**
	 * @var
	 * @since Kunena
	 */
	public $class_sfx;

	/**
	 * Prepare menu display.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$this->basemenu = $basemenu = KunenaRoute::getMenu();

		if (!$basemenu)
		{
			return false;
		}

		$parameters = new \Joomla\Registry\Registry;
		$template   = KunenaFactory::getTemplate();
		$parameters->set('showAllChildren', $template->params->get('menu_showall', 0));
		$parameters->set('menutype', $basemenu->menutype);
		$parameters->set('startLevel', $basemenu->level + 1);
		$parameters->set('endLevel', $basemenu->level + $template->params->get('menu_levels', 1));

		$this->list      = KunenaMenuHelper::getList($parameters);
		$this->menu      = $this->app->getMenu();
		$this->active    = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path      = isset($this->active) ? $this->active->tree : array();
		$this->showAll   = $parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($parameters->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		return true;
	}
}
