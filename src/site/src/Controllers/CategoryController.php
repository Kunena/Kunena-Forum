<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Administrator\Controller\CategoriesController;
use Kunena\Forum\Administrator\Controller\CategoryController as AdminCategoryController;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Kunena Category Controller
 *
 * @since   Kunena 2.0
 */
class CategoryController extends CategoriesController
{
    /**
     * @param   array  $config  config
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl  = 'index.php?option=com_kunena&view=category&layout=manage';
        $this->baseurl2 = 'index.php?option=com_kunena&view=category';
    }

    /**
     * Save category from the frontend category manager.
     * Reuses the backend save logic (token, ACL and field checks).
     *
     * @param   null  $key     key
     * @param   null  $urlVar  url var
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function save($key = null, $urlVar = null)
    {
        $catid = $this->app->getInput()->post->getInt('catid', 0);

        // A list in the request is no category.
        if (\is_array($catid)) {
            $catid = 0;
        }

        $controller = new AdminCategoryController();
        $category   = $controller->saveCategory();

        if ($category instanceof KunenaCategory && $category->exists()) {
            // A new category: the page of the category.
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . (int) $category->id, false));

            return;
        }

        if (!$catid) {
            // A new category which was not saved: back to the form.
            $parentId = $this->app->getInput()->post->getInt('parentid', 0);

            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&layout=create&parentid=' . (\is_array($parentId) ? 0 : $parentId), false));

            return;
        }

        $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . $catid, false));
    }

    /**
     * Delete a category with all its topics and posts (admins of the category; a category with subcategories is kept).
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function delete()
    {
        KunenaFactory::loadLanguage('com_kunena', 'admin');
        KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');

        $list = KunenaRoute::_('index.php?option=com_kunena&view=category&layout=list', false);

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect($list);

            return;
        }

        $category = KunenaCategoryHelper::get($this->app->getInput()->post->getInt('catid', 0));

        if (!$category->exists()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');
            $this->setRedirect($list);

            return;
        }

        if (!$category->isAuthorised('admin')) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8')), 'error');
            $this->setRedirect($list);

            return;
        }

        if ($category->isCheckedOut($this->me->userid)) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8')), 'notice');
            $this->setRedirect($list);

            return;
        }

        // The subcategories are removed first, one by one: a delete on the front would take too much with it.
        if (KunenaCategoryHelper::getChildren($category->id, 0, ['unpublished' => 1, 'action' => 'none'])) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORY_DELETE_HAS_CHILDREN'), 'error');
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . (int) $category->id . '&layout=manage', false));

            return;
        }

        $name   = $category->name;
        $parent = (int) $category->parentid;

        try {
            $category->delete();
        } catch (Exception $e) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_DELETE_FAILED', htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')), 'error');
            $this->setRedirect($list);

            return;
        }

        $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_DELETED', htmlspecialchars($name, ENT_QUOTES, 'UTF-8')), 'success');
        $this->setRedirect($parent ? KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . $parent, false) : $list);
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function jump()
    {
        $catid = $this->app->getInput()->getInt('catid', 0);

        // A list in the request is no category.
        if (\is_array($catid)) {
            $catid = 0;
        }

        if (!$catid) {
            $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&layout=list', false));
        } else {
            $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}", false));
        }
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function markread()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $catid    = $this->app->getInput()->getInt('catid', 0);
        $children = $this->app->getInput()->getBool('children', 0);

        if (!$catid) {
            // All categories
            $session = KunenaFactory::getSession();
            $session->markAllCategoriesRead();

            if (!$session->save()) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_ALL_MARKED'), 'success');
            }
        } else {
            // One category
            $category = KunenaCategoryHelper::get($catid);

            try {
                $category->tryAuthorise('read');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
                $this->setRedirectBack();

                return;
            }

            $session = KunenaFactory::getSession();

            if ($session->userid) {
                $categories = [$category->id => $category];

                if ($children) {
                    // Include all levels of child categories.
                    $categories += $category->getChildren(-1);
                }

                // Mark all unread topics in selected categories as read.
                KunenaCategoryUserHelper::markRead(array_keys($categories));

                if (\count($categories) > 1) {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_ALL_MARKED'), 'success');
                } else {
                    $this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_FORUM_MARKED'), 'success');
                }
            }
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function subscribe()
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $category = KunenaCategoryHelper::get($this->app->getInput()->getInt('catid', 0));

        try {
            // The rules of the action (category subscriptions switched off, banned, a section), not only the right to read.
            $category->tryAuthorise('subscribe');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirectBack();

            return;
        }

        if ($this->me->exists()) {
            $success = $category->subscribe(1);

            if ($success) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_CATEGORY_USER_SUBCRIBED', $category->name), 'success');
            }
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function unsubscribe()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $me = KunenaUserHelper::getMyself();

        // Without a user id the current user is meant (userid 0 would be the guest).
        $userid = $this->app->getInput()->getInt('userid', 0) ?: $me->userid;

        $catid  = $this->app->getInput()->getInt('catid', 0);
        $catids = $catid
            ? [$catid]
            : array_keys($this->app->getInput()->get('categories', [], 'post'));
        $catids = ArrayHelper::toInteger($catids);

        $categories = KunenaCategoryHelper::getCategories($catids);

        foreach ($categories as $category) {
            try {
                $category->tryAuthorise('read');
            } catch (Exception $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                continue;
            }

            // Only moderators of the category may remove the subscription of another user.
            if ($userid != $me->userid && !$me->isModerator($category)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'error');

                continue;
            }

            if ($this->me->exists()) {
                $success = $category->subscribe(0, $userid);

                if ($success && $userid == $me->userid) {
                    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_GEN_CATEGORY_NAME_UNSUBCRIBED', $category->name), 'success');
                } else {
                    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_CATEGORY_NAME_MODERATOR_UNSUBCRIBED_USER', $category->name), 'success');
                }
            }
        }

        $this->setRedirectBack();
    }
}
