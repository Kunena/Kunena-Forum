<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Widget\Menu;

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Menu\Helper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Joomla\Registry\Registry;
use function defined;

/**
 * Class ComponentKunenaControllerWidgetMenuDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerWidgetMenuDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Widget/Menu';

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $basemenu;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $list;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $menu;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $active;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $path;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $active_id;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $showAll;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $class_sfx;

	/**
	 * Prepare menu display.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	protected function before()
	{
		parent::before();

		$this->basemenu = $basemenu = KunenaRoute::getMenu();

		if (!$basemenu)
		{
			return false;
		}

		$parameters = new Registry;
		$template   = KunenaFactory::getTemplate();
		$parameters->set('showAllChildren', $template->params->get('menu_showall', 0));
		$parameters->set('menutype', $basemenu->menutype);
		$parameters->set('startLevel', $basemenu->level + 1);
		$parameters->set('endLevel', $basemenu->level + $template->params->get('menu_levels', 1));

		$this->list      = Helper::getList($parameters);
		$this->menu      = $this->app->getMenu();
		$this->active    = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path      = isset($this->active) ? $this->active->tree : [];
		$this->showAll   = $parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($parameters->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		return true;
	}
}
