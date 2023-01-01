<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\User\Listing;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\UserModel;

/**
 * Class ComponentUserControllerListDisplay
 *
 * @since   Kunena 4.0
 */
class UserListingDisplay extends KunenaControllerDisplay
{
    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public $state;

    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public $me;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public $total;

    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public $users;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public $pagination;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'User/List';

    /**
     * Load user list.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  null
     * @throws  Exception
     */
    protected function before()
    {
        parent::before();

        if (!$this->config->userlistAllowed && Factory::getApplication()->getIdentity()->guest) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
        }

        $model = new UserModel([], null);
        $model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
        $this->state = $model->getState();

        $this->me = KunenaUserHelper::getMyself();

        $start = $this->state->get('list.start');
        $limit = $this->state->get('list.limit');

        $Itemid = $this->input->getInt('Itemid');
        $format = $this->input->getCmd('format');

        if (!$Itemid && $format != 'feed' && $this->config->sefRedirect) {
            $itemid     = KunenaRoute::fixMissingItemID();
            $controller = BaseController::getInstance("kunena");
            $controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=user&layout=list&Itemid={$itemid}", false));
            $controller->redirect();
        }

        // Exclude super admins.
        if ($this->config->superAdminUserlist) {
            $filter = Access::getUsersByGroup(8);
        } else {
            $filter = [];
        }

        $finder = new KunenaFinder();
        $finder
            ->filterByConfiguration($filter)
            ->filterByName((string) $this->state->get('list.search'));

        $this->total      = $finder->count();
        $this->pagination = new KunenaPagination($this->total, $start, $limit);

        $alias     = 'ku';
        $aliasList = ['id', 'name', 'username', 'email', 'block', 'registerDate', 'lastvisitDate'];

        if (\in_array($this->state->get('list.ordering'), $aliasList)) {
            $alias = 'a';
        }

        $this->users = $finder
            ->order($this->state->get('list.ordering'), $this->state->get('list.direction') == 'asc' ? 1 : -1, $alias)
            ->start($this->pagination->limitstart)
            ->limit($this->pagination->limit)
            ->find();
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
        $page      = $this->pagination->pagesCurrent;
        $pagesText = ($page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

        $menu_item = $this->app->getMenu()->getActive();

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');

            if (!empty($params_title)) {
                $title = $params->get('page_title') . $pagesText;
                $this->setTitle($title);
            } else {
                $title = Text::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
                $this->setTitle($title);
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description') . $pagesText;
                $this->setDescription($description);
            } else {
                $description = Text::_('COM_KUNENA_VIEW_USER_LIST') . ': ' . $this->config->boardTitle . $pagesText;
                $this->setDescription($description);
            }
        }
    }
}
