<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Route\KunenaFeedLink;
use Kunena\Forum\Libraries\Route\KunenaRoute;

// Use function defined;

/**
 * KunenaLayoutWidgetFooter
 *
 * @since   Kunena 4.0
 */
class WidgetFooter extends KunenaLayout
{
    /**
     * Method to get the RSS URL link with image
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getRSS()
    {
        $config = KunenaFactory::getConfig();

        if ($config->enableRss) {
            $mode    = $config->rssType;
            $rssType = 'mode=topics';

            switch ($mode) {
                case 'topic':
                    $rssType = 'mode=topics';
                    break;
                case 'recent':
                    $rssType = 'mode=replies';
                    break;
                case 'post':
                    $rssType = 'mode=posts';
                    break;
            }

            $itemid = KunenaRoute::fixMissingItemID();

            if (Factory::getContainer()->get(SiteApplication::class)->get('sef_suffix')) {
                $url = KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=default&{$rssType}") . '?format=feed&type=' . KunenaFeedLink::type();
            } else {
                $url = KunenaRoute::_('index.php?option=com_kunena&view=topics&format=feed&type=' . KunenaFeedLink::type() . "&layout=feed&{$rssType}&Itemid={$itemid}", true);
            }

            // The feed of the forum at FeedBurner, when the option has its address (the option was read by nothing).
            $burner = KunenaFeedLink::burner();

            if ($burner !== null) {
                $url = htmlspecialchars($burner, ENT_QUOTES, 'UTF-8');
            }

            $doc = Factory::getApplication()->getDocument();
            $doc->addHeadLink($url, 'alternate', 'rel', ['type' => KunenaFeedLink::mime()]);

            // Only an icon: its name for the screen readers (aria-label) and for the mouse (the tooltip of the title).
            $label = htmlspecialchars(Text::_('COM_KUNENA_RSS_FEED_LABEL'), ENT_QUOTES, 'UTF-8');

            return '<a rel="alternate" type="' . KunenaFeedLink::mime() . '" href="' . $url . '" class="' . KunenaFactory::getTemplate()->tooltips()
                . '" data-bs-toggle="tooltip" title="' . $label . '" aria-label="' . $label . '">' . KunenaIcons::rss(true) . '</a>';
        }
    }

    /**
     * Method to get the time of page generation
     *
     * @return  string|void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getTime()
    {
        $config = KunenaFactory::getConfig();

        if (!$config->timeToCreatePage) {
            return;
        }

        $profiler = KunenaProfiler::instance('Kunena');
        $time     = $profiler->getTime('Total Time');

        return \sprintf('%0.3f', $time);
    }
}
