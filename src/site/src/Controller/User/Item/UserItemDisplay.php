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

namespace Kunena\Forum\Site\Controller\User\item;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Layout\Widget\WidgetModule;
use Kunena\Forum\Site\Model\UserModel;
use RuntimeException;

/**
 * Class ComponentUserControllerItemDisplay
 *
 * @since   Kunena 4.0
 */
class UserItemDisplay extends KunenaControllerDisplay
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $me = null;

    /**
     * @var     User
     * @since   Kunena 6.0
     */
    public mixed $user = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $profile = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $headerText = null;

    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public mixed $tabs = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'User/Item';

    public mixed $state = null;

    public mixed $candisplaymail = null;

    public mixed $ktemplate = null;

    public mixed $points = null;

    public mixed $medals = null;

    public mixed $private = null;

    public mixed $avatar = null;

    public mixed $banInfo = null;

    public mixed $socials = null;

    /**
     * Load user profile.
     *
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        // The Joomla content plugins of the modules of the summary of the profile (the system plugin of Kunena chooses the targets).
        WidgetModule::prepareWith('kunena_summary', 'profile_summary');

        // If profile integration is disabled, this view doesn't exist.
        $integration = KunenaFactory::getProfile();

        if (!$integration->enabled) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_PROFILE_DISABLED'), 404);
        }

        $userid = $this->input->getInt('userid');

        $model = new UserModel([]);
        $model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
        $this->state = $model->getState();

        $this->me      = KunenaUserHelper::getMyself();
        // Load the viewed user with the loader for several users (the same query as for the users of a topic or a list,
        // it brings the timezone along): the single user load is what the logged in user was loaded with already.
        if ($userid > 0) {
            KunenaUserHelper::loadUsers([$userid]);
        }

        $this->profile = KunenaUserHelper::get($userid);

        // The layouts only need the id of the user, which the Kunena user has too: loading the same user as a Joomla user
        // would take two more queries (the user and its groups). Need to pass directly the userid to the loadUserById() to
        // set 0 when userid is not set else it fails.
        $this->user    = $userid > 0 ? $this->profile : KunenaUserHelper::getJoomlaUser($this->input->getInt('userid', 0));
        $this->profile->tryAuthorise('read');
        $this->candisplaymail = $this->me->canDisplayEmail($this->profile);
        $this->ktemplate      = KunenaFactory::getTemplate();

        $activityIntegration = KunenaFactory::getActivityIntegration();
        $this->points        = $activityIntegration->getUserPoints($this->profile->userid);
        $this->medals        = $activityIntegration->getUserMedals($this->profile->userid);
        $this->private       = KunenaFactory::getPrivateMessaging();
        $this->socials       = $this->profile->getSocialInfoUserProfile();

        $this->avatar  = $this->profile->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post');
        $this->banInfo = $this->config->showBannedReason
            ? KunenaBan::getInstanceByUserid($this->profile->userid)
            : null;

        // Update profile hits.
        if (!$this->profile->exists()) {
            $this->profile->uhits++;
            $this->profile->save();
        } elseif (!$this->profile->isMyself()) {
            // Only the counter changes: no need to store the whole profile and to clear the access cache.
            $this->profile->uhits++;

            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_users'))
                ->set($db->quoteName('uhits') . ' = ' . $db->quoteName('uhits') . ' + 1')
                ->where($db->quoteName('userid') . ' = ' . (int) $this->profile->userid);
            $db->setQuery($query)->execute();
        }

        $Itemid = $this->input->getInt('Itemid');
        $format = $this->input->getCmd('format');

        if (!$Itemid && $format != 'feed' && $this->config->sefRedirect) {
            try {
                if ($this->config->profileId) {
                    $itemid = $this->config->profileId;
                } else {
                    $menu      = $this->app->getMenu();
                    $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=user"));
                    $itemid    = $getid->id;
                }

                if (!$itemid) {
                    $itemid = KunenaRoute::fixMissingItemID();
                }

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'user',
                    'Itemid' => $itemid,
                ];

                if ($userid) {
                    $params['userid'] = $userid;
                }

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false), 301);
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        // The name as text: the breadcrumb escapes it ("Tom & Jerry" was "Tom &amp; Jerry").
        $this->headerText = Text::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName('', false));
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
        // The name as text: the title and the meta tags are escaped by Joomla (the HTML name showed as "Tom &amp; Jerry").
        $name = $this->profile->getName('', false);

        $this->setMetaData('profile:username', $name, 'property');

        if ($this->profile->getGender() == 1) {
            $this->setMetaData('profile:gender', Text::_('COM_KUNENA_MYPROFILE_GENDER_MALE'), 'property');
        } elseif ($this->profile->getGender() == 2) {
            $this->setMetaData('profile:gender', Text::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'), 'property');
        } else {
            $this->setMetaData('profile:gender', Text::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'), 'property');
        }

        $menu_item = $this->app->getMenu()->getActive();
        $image     = '';

        $this->setMetaData('og:url', Uri::current(), 'property');
        $this->setMetaData('og:type', 'profile', 'property');

        if (is_file(JPATH_SITE . '/media/kunena/avatars/' . KunenaFactory::getUser($this->profile->id)->avatar)) {
            $image = Uri::root() . 'media/kunena/avatars/' . KunenaFactory::getUser($this->profile->id)->avatar;
        } elseif ($this->profile->avatar == null || $this->config->avatarType && KunenaFactory::getUser($this->profile->id)->avatar == null) {
            if (is_file(JPATH_SITE . '/' . $this->config->emailHeader)) {
                $image = Uri::base() . $this->config->emailHeader;
            }
        } else {
            $image = $this->profile->getAvatarURL('Profile', '200');
        }

        if (!empty($image)) {
            $this->setMetaData('og:image', $image, 'property');
        }

        $this->setMetaData('robots', 'index, follow');

        // The title, the description and the tags of Open Graph also without an active menu item (a link without an Itemid): the page
        // had none of them then. The settings of the menu item replace the defaults when there is one.
        $params             = $menu_item ? $menu_item->getParams() : null;
        $params_title       = $params ? $params->get('page_title') : '';
        $params_description = $params ? $params->get('menu-meta_description') : '';

        if (!empty($params_title)) {
            $title = $params_title;
        } else {
            $title = Text::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $name);
        }

        $this->setTitle($title);
        $this->setMetaData('og:description', $title, 'property');
        $this->setMetaData('og:title', $name, 'property');

        if (!empty($params_description)) {
            $this->setDescription($params_description);
        } else {
            $description = Text::sprintf(
                'COM_KUNENA_META_PROFILE',
                $name,
                $this->config->boardTitle,
                $name,
                $this->config->boardTitle
            );
            $this->setDescription($description);
        }

        $params_robots = $params ? $params->get('robots') : '';

        if (!empty($params_robots)) {
            $this->setMetaData('robots', $params_robots);
        }

        // A profile without a post is a thin page (a name and a date), one for each user who registered.
        if ((int) $this->profile->posts < 1) {
            $this->keepOutOfIndex();
        }
    }
}
