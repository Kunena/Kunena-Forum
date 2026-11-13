<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Misc\Initial;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class MiscDisplay extends Display
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $header = '';

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $body = null;

    /**
     * Return custom display layout.
     *
     * @return  KunenaLayout
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function display()
    {
        $menu_item = $this->app->getMenu()->getActive();

        $componentParams = ComponentHelper::getParams('com_config');
        $robots          = $componentParams->get('robots');

        if ($robots == 'noindex, follow') {
            $this->setMetaData('robots', 'noindex, follow');
        } elseif ($robots == 'index, nofollow') {
            $this->setMetaData('robots', 'index, nofollow');
        } elseif ($robots == 'noindex, nofollow') {
            $this->setMetaData('robots', 'noindex, nofollow');
        } else {
            $this->setMetaData('robots', 'index, follow');
        }

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');
            $params_robots      = $params->get('robots');

            if (!empty($params_title)) {
                $title = $params->get('page_title');
                $this->setTitle($title);
            } else {
                $title = $this->config->boardTitle;
                $this->setTitle($title);
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $description = $this->config->boardTitle;
                $this->setDescription($description);
            }

            if (!empty($params_robots)) {
                $robots = $params->get('robots');
                $this->setMetaData('robots', $robots);
            }
        }

        $pageTitle       = $menu_item ? (string) $menu_item->getParams()->get('page_title') : '';
        $metaDescription = $menu_item ? (string) $menu_item->getParams()->get('menu-meta_description') : '';
        $image           = '';

        if (is_file(JPATH_SITE . '/' . $this->config->emailHeader)) {
            $image = Uri::base() . $this->config->emailHeader;
        }

        foreach (static::getSocialMetaData($pageTitle, $metaDescription, (string) $this->config->boardTitle, Uri::current(), $image) as [$name, $content, $attribute]) {
            $this->setMetaData($name, $content, $attribute);
        }

        // Display layout with given parameters.
        return KunenaPage::factory('Misc/Default')
            ->set('header', $this->header)
            ->set('body', $this->body);
    }

    /**
     * The Open Graph and Twitter Card tags of the page, in plain text: Joomla escapes them when it renders the head.
     * The title and the description fall back to the board title, and a tag without a value is left out.
     *
     * @param   string  $pageTitle        The page title of the menu item.
     * @param   string  $metaDescription  The meta description of the menu item.
     * @param   string  $boardTitle       The title of the board.
     * @param   string  $url              The URL of the page.
     * @param   string  $image            The URL of the image, or an empty string.
     *
     * @return  array  A list of [name, content, attribute].
     *
     * @since   Kunena 8.0
     */
    protected static function getSocialMetaData(string $pageTitle, string $metaDescription, string $boardTitle, string $url, string $image): array
    {
        $clean = static function (string $text): string {
            $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return trim(preg_replace('/\s+/u', ' ', $text));
        };

        $board       = $clean($boardTitle);
        $title       = $clean($pageTitle);
        $description = $clean($metaDescription);

        if ($title === '') {
            $title = $board;
        }

        if ($description === '') {
            $description = $board;
        }

        $tags = [
            ['og:url', $url, 'property'],
            ['og:type', 'website', 'property'],
            ['og:title', $title, 'property'],
            ['og:description', $description, 'property'],
            ['og:image', $image, 'property'],
            ['twitter:card', 'summary', 'name'],
            ['twitter:title', $title, 'name'],
            ['twitter:description', $description, 'name'],
            ['twitter:image', $image, 'name'],
        ];

        return array_values(array_filter($tags, static fn (array $tag): bool => $tag[1] !== ''));
    }

    /**
     * Prepare custom text output.
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function before()
    {
        parent::before();

        $params       = $this->app->getParams('com_kunena');
        $this->header = $params->get('page_title');
        $Itemid       = $this->input->getInt('Itemid');

        if (!$Itemid) {
            try {
                if (isset($this->config->customId) && $this->config->customId) {
                    $itemid = $this->config->customId;
                } else {
                    $menu      = $this->app->getMenu();
                    $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=misc"));
                    $itemid    = $getid->id;
                }

                if (!$itemid) {
                    $itemid = KunenaRoute::fixMissingItemID();
                }

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'misc',
                    'Itemid' => $itemid,
                ];

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false));
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        $body   = $params->get('body');
        $format = $params->get('body_format');

        if ($this->header === null) {
            $this->header = '';
        }

        $this->header = htmlspecialchars($this->header, ENT_COMPAT, 'UTF-8');

        if ($format == 'html') {
            $this->body = trim($body);
        } elseif ($format == 'text') {
            $this->body = htmlspecialchars($body, ENT_COMPAT, 'UTF-8');
        } else {
            $options = ['defaultgroup' => 'com_kunena'];
            $cache   = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('callback', $options);
            $cache->setLifeTime(180);

            $this->body = $cache->get(['Kunena\Forum\Libraries\Html\KunenaParser', 'parseBBCode'], [$body]);
        }
    }
}
