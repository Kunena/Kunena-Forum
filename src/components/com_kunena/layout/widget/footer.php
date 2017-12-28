<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Controllers.Misc
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutWidgetFooter
 *
 * @since  K4.0
 *
 */
class KunenaLayoutWidgetFooter extends KunenaLayout
{
	/**
	 * Method to get the time of page generation
	 *
	 * @return string
	 */
	protected function getTime()
	{
		$config = KunenaFactory::getConfig();

		if (!$config->time_to_create_page)
		{
			return null;
		}

		$profiler = KunenaProfiler::instance('Kunena');
		$time     = $profiler->getTime('Total Time');

		return sprintf('%0.3f', $time);
	}

	/**
	 * Method to get the RSS URL link with image
	 *
	 * @return string
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

			$url = KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&layout=default&{$rss_type}", true);
			$doc = JFactory::getDocument();
			$doc->addHeadLink($url, 'alternate', 'rel', array('type' => 'application/rss+xml'));

			return '<a rel="alternate" type="application/rss+xml" href="' . $url . '">' . KunenaIcons::rss($text = true) . '</a>';
		}
		else
		{
			return null;
		}
	}
}
