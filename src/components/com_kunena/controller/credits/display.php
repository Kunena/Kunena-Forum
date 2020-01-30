<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Credits
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Credits;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  4.0
 */
class ComponentCreditsControllerDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Credits';

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $logo;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $intro;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $memberList;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $thanks;

	/**
	 * Prepare credits display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		if (PluginHelper::isEnabled('kunena', 'powered'))
		{
			$this->baseurl = 'index.php?option=com_kunena';
			$this->app->redirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_($this->baseurl, false));
		}

		$Itemid = Factory::getApplication()->input->getCmd('Itemid');

		if (!$Itemid && $this->config->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=credits&Itemid={$itemid}", false));
			$controller->redirect();
		}

		$this->logo = KunenaFactory::getTemplate()->getImagePath('icons/kunena-logo-48-white.png');

		$this->intro = Text::sprintf('COM_KUNENA_CREDITS_INTRODUCTION', 'https://www.kunena.org/team');

		$this->memberList = [
			[
				'name'  => 'Florian Dal Fitto',
				'url'   => 'https://www.kunena.org/forum/user/1288-xillibit',
				'title' => Text::_('COM_KUNENA_CREDITS_DEVELOPMENT'),],
			[
				'name'  => 'Jelle Kok',
				'url'   => 'https://www.kunena.org/forum/user/634-810',
				'title' => Text::sprintf('COM_KUNENA_CREDITS_X_AND_Y', Text::_('COM_KUNENA_CREDITS_DEVELOPMENT'), Text::_('COM_KUNENA_CREDITS_DESIGN')),],
			[
				'name'  => 'Richard Binder',
				'url'   => 'https://www.kunena.org/forum/user/2198-rich',
				'title' => Text::sprintf('COM_KUNENA_CREDITS_X_AND_Y', Text::_('COM_KUNENA_CREDITS_MODERATION'), Text::_('COM_KUNENA_CREDITS_TESTING')),],
			[
				'name'  => 'Matias Griese',
				'url'   => 'https://www.kunena.org/forum/user/63-matias',
				'title' => Text::_('COM_KUNENA_CREDITS_DEVELOPMENT'),],
			[
				'name'  => 'Oliver Ratzesberger',
				'url'   => 'https://www.kunena.org/forum/user/64-fxstein',
				'title' => Text::_('COM_KUNENA_CREDITS_FOUNDER'),],
		];
		$this->thanks     = Text::sprintf(
			'COM_KUNENA_CREDITS_THANKS_TO', 'https://www.kunena.org/team#special_thanks',
			'https://www.transifex.com/projects/p/Kunena', 'https://www.kunena.org',
			'https://github.com/Kunena/Kunena-Forum/graphs/contributors'
		);
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
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
				$title = Text::_('COM_KUNENA_VIEW_CREDITS_DEFAULT');
				$this->setTitle($title);
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
				// TODO: translate at some point...
				$description = 'Kunena is the ideal forum extension for Joomla. It\'s free and fully integrated. "
			. "For more information, please visit www.kunena.org.';
				$this->setDescription($description);
			}
		}
	}
}
