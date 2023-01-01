<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Misc\Initial;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\Route\KunenaRoute;

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
    public $header = '';

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public $body;

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

        // Display layout with given parameters.
        return KunenaPage::factory('Misc/Default')
            ->set('header', $this->header)
            ->set('body', $this->body);
    }

    /**
     * Prepare custom text output.
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

        $params       = $this->app->getParams('com_kunena');
        $this->header = $params->get('page_title');
        $Itemid       = $this->input->getInt('Itemid');

        if (!$Itemid) {
            if ($this->config->custom_id) {
                $itemidfix = $this->config->custom_id;
            } else {
                $menu      = $this->app->getMenu();
                $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=misc"));
                $itemidfix = $getid->id;
            }

            if (!$itemidfix) {
                $itemidfix = KunenaRoute::fixMissingItemID();
            }

            $controller = BaseController::getInstance("kunena");
            $controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=misc&Itemid={$itemidfix}", false));
            $controller->redirect();
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
            $this->body = function () use ($body) {

                return htmlspecialchars($body, ENT_COMPAT, 'UTF-8');
            };
        } else {
            $this->body = function () use ($body) {

                $cache = Factory::getCache('com_kunena', 'callback');
                $cache->setLifeTime(180);

                return $cache->get(['Kunena\Forum\Libraries\Html\KunenaParser', 'parseBBCode'], [$body]);
            };
        }
    }
}
