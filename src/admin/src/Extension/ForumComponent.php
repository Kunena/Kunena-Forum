<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Extension
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Extension;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use Kunena\Forum\Site\Service\Html\Kunenagrid;
use Kunena\Forum\Site\Service\Html\Kunenaforum;

/**
 * Component class for com_kunena
 *
 * @since   Kunena 6.0
 */
class ForumComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface
{
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('kunenagrid', new Kunenagrid($container->get(SiteApplication::class)));
		$this->getRegistry()->register('kunenaforum', new Kunenaforum($container->get(SiteApplication::class)));
	}
}
