<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationHomeDefaultDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationHomeDefaultDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return bool
	 */
	public function exists()
	{
		return KunenaFactory::getTemplate()->isHmvc();
	}

	/**
	 * Redirect to home page.
	 *
	 * @return KunenaLayout
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	public function execute()
	{
		$menu = $this->app->getMenu();
		$home = $menu->getActive();

		if (!$home)
		{
			$this->input->set('view', 'category');
			$this->input->set('layout', 'list');
			/* throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 500); */
		}
		else
		{
			// Find default menu item.
			$default = $this->getDefaultMenuItem($menu, $home);

			if (!$default || $default->id == $home->id)
			{
				// There is no default menu item, use category view instead.
				$default = $menu->getItem(KunenaRoute::getItemID('index.php?option=com_kunena&view=category&layout=list'));

				if ($default)
				{
					$default = clone $default;
					$defhome = KunenaRoute::getHome($default);

					if (!$defhome || $defhome->id != $home->id)
					{
						$default = clone $home;
					}

					$default->query['view'] = 'category';
					$default->query['layout'] = 'list';
				}
			}

			if (!$default)
			{
				throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 500);
			}

			// Add query variables from shown menu item.
			foreach ($default->query as $var => $value)
			{
				$this->input->set($var, $value);
			}

			// Remove query variables coming from the home menu item.
			$this->input->set('defaultmenu', null);

			// Set active menu item to point the real page.
			$this->input->set('Itemid', $default->id);
			$menu->setActive($default->id);
		}

		// Reset our router.
		KunenaRoute::initialize();

		// Get HMVC controller for the current page.
		$controller = KunenaControllerApplication::getInstance(
			$this->input->getCmd('view'),
			$this->input->getCmd('layout', 'default'),
			$this->input->getCmd('task', 'display'),
			$this->input, $this->app
		);

		if (!$controller)
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		return $controller->execute();
	}

	/**
	 * Get default menu item to be shown up.
	 *
	 * @param   JMenuSite  $menu     Joomla menu.
	 * @param   object     $active   Active menu item.
	 * @param   array      $visited  Already visited menu items.
	 *
	 * @return object|null
	 */
	protected function getDefaultMenuItem(JMenuSite $menu, $active, $visited = array())
	{
		if (empty($active->query['defaultmenu']) || $active->id == $active->query['defaultmenu'])
		{
			// There is no highlighted menu item!
			return null;
		}

		$item = $menu->getItem($active->query['defaultmenu']);

		if (!$item)
		{
			// Menu item points to nowhere, abort!
			KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NOT_EXISTS'), 'menu');

			return null;
		}
		elseif (isset($visited[$item->id]))
		{
			// Menu loop detected, abort!
			KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_LOOP'), 'menu');

			return null;
		}
		elseif (empty($item->component) || $item->component != 'com_kunena' || !isset($item->query['view']))
		{
			// Menu item doesn't point to Kunena, abort!
			KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NOT_KUNENA'), 'menu');

			return null;
		}
		elseif ($item->query['view'] == 'home')
		{
			// Menu item is pointing to another Home Page, try to find default menu item from there.
			$visited[$item->id] = 1;
			$item = $this->getDefaultMenuItem($menu, $item->query['defaultmenu'], $visited);
		}

		return $item;
	}
}
