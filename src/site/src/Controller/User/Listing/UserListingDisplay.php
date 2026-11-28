<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\User\Listing;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\UserModel;
use RuntimeException;

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
    public mixed $state = null;

    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public mixed $me = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $total = null;

    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public mixed $users = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $pagination = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'User/List';

    /**
     * Load user list.
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

        if (!$this->config->userlistAllowed && $this->app->getIdentity()->guest) {
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
            try {
                $itemid = KunenaRoute::fixMissingItemID();

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'user',
                    'layout' => 'list',
                    'Itemid' => $itemid,
                ];

                // Without a menu item of the forum which the visitor may see (their access, their language) there is no Itemid: no
                // redirect, it went to the same address again, until the browser gave up.
                if (!empty($params['Itemid'])) {
                    return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false), 301);
                }
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        // Exclude super admins.
        if ($this->config->superAdminUserlist) {
            $filter = Access::getUsersByGroup(8);
        } else {
            $filter = [];
        }

        $finder = new KunenaUserFinder();
        $finder
            ->filterByConfiguration($filter)
            // "Search from the beginning of the name" of the settings: it was read by the model, which the list does not use for its
            // query, so the list found the names which have the text anywhere.
            ->filterByName((string) $this->state->get('list.search'), (bool) $this->config->userlistSearchFromStart)
            ->filterByUserType((string) $this->state->get('userlist.usertype'));

        $this->total      = $finder->count();
        $this->pagination = new KunenaPagination($this->total, $start, $limit);

        // The links to the other pages keep the search: it is posted by the search box, and the address of the links keeps only
        // the values of the request made of letters and digits ("ci-" was dropped): page 2 was a page of the whole list.
        if ((string) $this->state->get('list.search') !== '') {
            $this->pagination->setAdditionalUrlParam('search', (string) $this->state->get('list.search'));
        }

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
                // A sentence of its own, not the title again ("User List: Board title").
                $description = Text::sprintf('COM_KUNENA_VIEW_USER_LIST_META_DESCRIPTION', (int) $this->pagination->total, $this->config->boardTitle) . $pagesText;
                $this->setDescription($description);
            }

            // A shared link of the list has a title and a description, as the other lists of the forum.
            $this->setMetaData('og:type', 'website', 'property');
            $this->setMetaData('og:title', $title, 'property');
            $this->setMetaData('og:description', $description, 'property');
        }
    }
}
