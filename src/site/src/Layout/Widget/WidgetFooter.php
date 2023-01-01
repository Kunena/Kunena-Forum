<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers.Misc
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
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
     * @throws  null
     */
    public function getRSS()
    {
        $config = KunenaFactory::getConfig();

        if ($config->enableRss) {
            $mode = $config->rssType;

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

            if (CMSApplication::getInstance('site')->get('sef_suffix')) {
                $url = KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=default&{$rssType}") . '?format=feed&type=rss';
            } else {
                $url = KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&type=rss&layout=feed&{$rssType}&Itemid={$itemid}", true);
            }

            $doc = Factory::getApplication()->getDocument();
            $doc->addHeadLink($url, 'alternate', 'rel', ['type' => 'application/rss+xml']);

            return '<a rel="alternate" type="application/rss+xml" href="' . $url . '">' . KunenaIcons::rss(true) . '</a>';
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

        return sprintf('%0.3f', $time);
    }
}
