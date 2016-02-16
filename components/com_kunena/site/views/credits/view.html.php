<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Credits View
 */
class KunenaViewCredits extends KunenaView
{
	function displayDefault($tpl = null)
	{

		$this->intro = JText::sprintf('COM_KUNENA_CREDITS_INTRO', 'https://www.kunena.org/team');

		$this->memberList = array(
			array(
				'name' => 'Florian Dal Fitto',
				'url' => 'https://www.kunena.org/forum/user/1288-xillibit',
				'title' => JText::_('COM_KUNENA_CREDITS_DEVELOPMENT')),
			array(
				'name' => 'Jelle Kok',
				'url' => 'https://www.kunena.org/forum/user/634-810',
				'title' => JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_DEVELOPMENT'), JText::_('COM_KUNENA_CREDITS_DESIGN'))),
			array(
				'name' => 'Richard Binder',
				'url' => 'https://www.kunena.org/forum/user/2198-rich',
				'title' => JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
			array(
				'name' => 'Sami Haaranen',
				'url' => 'https://www.kunena.org/forum/user/151-mortti',
				'title' => JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_MODERATION'), JText::_('COM_KUNENA_CREDITS_TESTING'))),
			array(
				'name' => 'Matias Griese',
				'url' => 'https://www.kunena.org/forum/user/63-matias',
				'title' => JText::_('COM_KUNENA_CREDITS_DEVELOPMENT')),
			array(
				'name' => 'Joshua Weiss',
				'url' => 'https://www.kunena.org/forum/user/10809-coder4life',
				'title' => JText::sprintf('COM_KUNENA_CREDITS_X_AND_Y', JText::_('COM_KUNENA_CREDITS_DESIGN'), JText::_('COM_KUNENA_CREDITS_DEVELOPMENT'))),
			array(
				'name' => 'Oliver Ratzesberger',
				'url' => 'https://www.kunena.org/forum/user/64-fxstein',
				'title' => JText::_('COM_KUNENA_CREDITS_FOUNDER')),
		);
		$this->thanks     = JText::sprintf('COM_KUNENA_CREDITS_THANKS', 'https://www.kunena.org/team#special_thanks',
			'https://www.transifex.com/projects/p/Kunena', 'https://www.kunena.org',
			'https://github.com/Kunena/Kunena-Forum/graphs/contributors');

		$this->_prepareDocument();

		$this->render('Credits', $tpl);
	}

	protected function _prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params; // get the params
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT');
				$this->setTitle($title, true);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = 'kunena forum, kunena, forum, joomla, joomla extension, joomla component';
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = 'Kunena is the ideal forum extension for Joomla. It\'s free and fully integrated. "
			. "For more information, please visit www.kunena.org.';
				$this->setDescription($description);
			}
		}
	}
}
