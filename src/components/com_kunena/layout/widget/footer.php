<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers.Misc
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * KunenaLayoutWidgetFooter
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutWidgetFooter extends Layout
{
	/**
	 * Method to get the time of page generation
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	protected function getTime()
	{
		$config = KunenaFactory::getConfig();

		if (!$config->time_to_create_page)
		{
			return;
		}

		$profiler = KunenaProfiler::instance('Kunena');
		$time     = $profiler->getTime('Total Time');

		return sprintf('%0.3f', $time);
	}

	/**
	 * Method to get the RSS URL link with image
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	protected function getRSS()
	{
		$config = KunenaFactory::getConfig();

		if ($config->enablerss)
		{
			$mode = $config->rss_type;

			switch ($mode)
			{
				case 'topic' :
					$rss_type = 'mode=topics';
					break;
				case 'recent' :
					$rss_type = 'mode=replies';
					break;
				case 'post' :
					$rss_type = 'layout=posts';
					break;
			}

			$itemid = KunenaRoute::fixMissingItemID();

			if (CMSApplication::getInstance('site')->get('sef_suffix'))
			{
				$url = KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=default&{$rss_type}") . '?format=feed&type=rss';
			}
			else
			{
				$url = KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&type=rss&layout=default&{$rss_type}&Itemid={$itemid}", true);
			}

			$doc = Factory::getApplication()->getDocument();
			$doc->addHeadLink($url, 'alternate', 'rel', ['type' => 'application/rss+xml']);

			return '<a rel="alternate" type="application/rss+xml" href="' . $url . '">' . \Kunena\Forum\Libraries\Icons\Icons::rss($text = true) . '</a>';
		}
		else
		{
			return;
		}
	}
}
