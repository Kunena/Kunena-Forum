<?php
/**
 * @package     Mywalks.Site
 * @subpackage  com_mywalks
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Kunena\Forum\Site\Service\KunenaNomenuRules as NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * Routing class of com_kunena
 *
 * @since  3.3
 */
class Router extends RouterView
{
	/**
	 * The category factory
	 *
	 * @var CategoryFactoryInterface
	 *
	 * @since  4.0.0
	 */
	private $categoryFactory;

	/**
	 * The db
	 *
	 * @var DatabaseInterface
	 *
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * Mywalks Component router constructor
	 *
	 * @param   SiteApplication           $app              The application object
	 * @param   AbstractMenu              $menu             The menu object to work with
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}
}
