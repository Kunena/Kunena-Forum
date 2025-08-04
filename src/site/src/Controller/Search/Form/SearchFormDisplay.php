<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Search
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Search\Form;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\SearchModel;
use Kunena\Forum\Libraries\Controller\KunenaController;
use RuntimeException;

/**
 * Class ComponentSearchControllerFormDisplay
 *
 * @since   Kunena 4.0
 */
class SearchFormDisplay extends KunenaControllerDisplay
{
    /**
     * @var     SearchModel
     * @since   Kunena 6.0
     */
    public $model;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Search/Form';

    public $state;

    public $me;

    public $isModerator;

    public $error;

    /**
     * Prepare search form display.
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $this->model = new SearchModel([], null, null, $this->input);
        $this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
        $this->state = $this->model->getState();

        $Itemid = $this->input->getCmd('Itemid');

        if (!$Itemid && $this->config->sefRedirect) {
            try {
                if ($this->config->searchId) {
                    $itemid = $this->config->searchId;
                } else {
                    $menu      = $this->app->getMenu();
                    $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=search"));
                    $itemid    = $getid->id;
                }

                if (!$itemid) {
                    $itemid = KunenaRoute::fixMissingItemID();
                }

                $params = [
                    'option' => 'com_kunena',
                    'view' => 'search',
                    'Itemid' => $itemid
                ];

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query($params), false));
            }

            catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        $this->me = KunenaUserHelper::getMyself();

        try {
            $this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $menu_item = $this->app->getMenu()->getActive();

        $robots = $this->app->get('robots');

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
                $this->setTitle(Text::_('COM_KUNENA_SEARCH_ADVSEARCH'));
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $description = Text::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->boardTitle;
                $this->setDescription($description);
            }

            if (!empty($params_robots)) {
                $robots = $params->get('robots');
                $this->setMetaData('robots', $robots);
            }
        }
    }
}
