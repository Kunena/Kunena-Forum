<?php

/**
 * Kunena System Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      System
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\System\Kunena\Extension;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Model\AfterSaveEvent as ModelAfterSaveEvent;
use Joomla\CMS\Event\Model\BeforeSaveEvent as ModelBeforeSaveEvent;
use Joomla\CMS\Event\User\AfterDeleteEvent;
use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Event\User\BeforeSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Http\Http;
use Joomla\Registry\Registry;
use Joomla\Utilities\IpHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use RuntimeException;

/**
 * Class Kunena
 *
 * @since   Kunena 6.0
 */
class Kunena extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load language file for front-end translations
     *
     * @var boolean
     */
    protected $autoloadLanguage = \true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  - The method name to call (priority defaults to 0)
     *  - An array composed of the method name to call and the priority
     *
     * @return  array
     * @since   Kunena 7.0.0
     */
    public static function getSubscribedEvents(): array
    {
        $app     = Factory::getApplication();
        $mapping = [];

        if ($app->isClient('site') || $app->isClient('administrator')) {
            $mapping['onUserAfterSave']   = 'onUserAfterSave';
            $mapping['onUserAfterLogin']  = 'onUserAfterLogin';
            $mapping['onUserAfterDelete'] = 'onUserAfterDelete';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
                $mapping['onUserBeforeSave'] = 'onUserBeforeSave';
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
                $mapping['onExtensionBeforeSave'] = 'onExtensionBeforeSave';
                $mapping['onExtensionAfterSave']  = 'onExtensionAfterSave';
            }
        }

        return $mapping;
    }

    /**
     * Method is called before user data is stored in the database
     *
     * @param   BeforeSaveEvent  $event  The event, carrying the old user data, the new user data and whether the user is new.
     *
     * @return  void
     *
     * @since   7.0.0
     * @throws  Exception when listed on stopforumspam
     */
    public function onUserBeforeSave(BeforeSaveEvent $event): void
    {
        $isNew = $event->getIsNew();

        if (!$isNew) {
            // User is already registered, no need to check
            return;
        }

        if (!KunenaConfig::getInstance()->stopForumSpamNewUserCheck) {
            // Checking of new user is disabled
            return;
        }

        // Check that the terms is checked if required ie only in registration from frontend.
        $data              = $event->getData();
        $query['username'] = $data['username'];
        $query['email']    = $data['email'];
        $query['ip']       = IpHelper::getIp();

        if ($query['ip'] === '::1' || $query['ip'] === '127.0.0.1') {
            // Local Host
            unset($query['ip']);
        }

        $http    = new Http();
        $request = 'https://api.stopforumspam.org/api?' . http_build_query($query) . '&json';

        try {
            $response = $http->get($request, [], 5);
        } catch (RuntimeException $e) {
            // The service cannot be reached: do not block the registration because of it.
            return;
        }

        if ($response->getStatusCode() == '200') {
            // The query has worked
            $result = json_decode($response->getBody());

            if ($result && $result->success) {
                if (($result->username->appears ?? false) || ($result->email->appears ?? false) || ($result->ip->appears ?? false)) {
                    $this->loadLanguage('plg_system_kunena.sys');
                    throw new Exception(Text::_('PLG_SYSTEM_KUNENA_STOPFORUMSPAMLISTED'), 400);
                }
            }
        }
    }

    /**
     * Method is called after a succesfull save of the user data
     *
     * @param   AfterSaveEvent  $event  The event instance
     *
     * @return  void
     * @since   2.2.0
     */
    public function onUserAfterSave(AfterSaveEvent $event): void
    {
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('7.1')
            || !KunenaForum::installed()
        ) {
            return;
        }

        $user    = $event->getUser();
        $isNew   = $event->getIsNew();
        $success = $event->getSavingResult();

        // Don't continue if the user wasn't stored successfully
        if (!$success) {
            return;
        }

        if ($isNew && \intval($user['id'])) {
            $kuser = KunenaFactory::getUser(\intval($user['id']));
            $kuser->save();
        }
    }

    /**
     * Save the language of the user in the table users of Kunena
     *
     * @param   AfterLoginEvent  $event  The event instance
     * @return  void
     * @since   Kunena 7.0.0
     */
    public function onUserAfterLogin(AfterLoginEvent $event): void
    {
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('7.1')
            || !KunenaForum::installed()
        ) {
            return;
        }

        if (!($this->getApplication()->isClient('site'))) {
            return;
        }

        $options         = $event->getOptions();
        $kuser           = KunenaFactory::getUser(\intval($options['user']->id));
        $kuser->language = $this->getUserDefaultLanguage($options['user']->params);

        $kuser->save();
    }

    /**
     * A user who is deleted in Joomla loses his data of the forum too: the profile row (signature, personal text, location,
     * birthdate, website), the avatar files and his subscriptions, follows, ignores, read marks and bans. Without this the data
     * stayed behind (only a deletion through the users page of Kunena removed the profile row).
     *
     * @param   AfterDeleteEvent  $event  The event.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public function onUserAfterDelete(AfterDeleteEvent $event): void
    {
        if (
            !$event->getDeletingResult()
            || !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('7.1')
            || !KunenaForum::installed()
        ) {
            return;
        }

        $userid = (int) ($event->getUser()['id'] ?? 0);

        if ($userid < 1) {
            return;
        }

        $db = $this->getDatabase();

        foreach (
            [
                '#__kunena_users'            => ['userid'],
                '#__kunena_user_categories'  => ['user_id'],
                '#__kunena_user_topics'      => ['user_id'],
                '#__kunena_user_read'        => ['user_id'],
                '#__kunena_user_follow'      => ['user_id', 'followed_id'],
                '#__kunena_user_ignore'      => ['user_id', 'ignored_id'],
                '#__kunena_users_banned'     => ['userid'],
                '#__kunena_rate'             => ['userid'],
                '#__kunena_drafts'           => ['user_id'],
                '#__kunena_sessions'         => ['userid'],
                '#__kunena_karma'            => ['userid'],
                '#__kunena_polls_users'      => ['userid'],
                '#__kunena_private_user_map' => ['user_id'],
                '#__kunena_inbox_blocks'     => ['user_id', 'blocked_id'],
            ] as $table => $fields
        ) {
            foreach ($fields as $field) {
                try {
                    $query = $db->createQuery()
                        ->delete($db->quoteName($table))
                        ->where($db->quoteName($field) . ' = ' . $userid);
                    $db->setQuery($query)->execute();
                } catch (Exception $e) {
                    // A table of a plugin which is not installed: nothing to delete there.
                }
            }
        }

        // His inbox (plugin Kunena - Inbox): every message and notification of his side is deleted as if he deleted it himself, so
        // a message stays for the other user until he deletes it too, and the files go with the messages which are removed.
        $inbox = 'Kunena\\Forum\\Plugin\\Kunena\\Inbox\\Helper\\InboxHelper';

        if (class_exists($inbox)) {
            try {
                $query = $db->createQuery()
                    ->select($db->quoteName('id'))
                    ->from($db->quoteName('#__kunena_inbox'))
                    ->where('(' . $db->quoteName('user_id') . ' = ' . $userid . ' OR ' . $db->quoteName('sender_id') . ' = ' . $userid . ')');
                $inbox::delete($userid, array_map('intval', (array) $db->setQuery($query)->loadColumn()));
            } catch (Exception $e) {
                // The inbox is not installed.
            }
        }

        // The avatar files of the user.
        foreach (glob(JPATH_ROOT . '/media/kunena/avatars/users/avatar' . $userid . '.*') ?: [] as $file) {
            @unlink($file);
        }

        foreach (glob(JPATH_ROOT . '/media/kunena/avatars/resized/size*/users/avatar' . $userid . '.*') ?: [] as $file) {
            @unlink($file);
        }
    }

    /**
     * Get the user language defined in his Joomla! profile
     *
     * @param string $params
     * @return string
     * @since   Kunena 7.0.0
     */
    protected function getUserDefaultLanguage($params)
    {
        $userParams = json_decode($params);

        if (!empty($userParams->language)) {
            $language = $userParams->language;
        } else {
            $language = Factory::getApplication()->getLanguage()->getTag();
        }

        return $language;
    }

    /**
     * The configuration form saves only its own fields: the badges (they have their own view) are kept.
     *
     * @param   ModelBeforeSaveEvent  $event  The event, carrying the table of the extension.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public function onExtensionBeforeSave(ModelBeforeSaveEvent $event): void
    {
        $table = $event->getItem();

        if ($event->getContext() !== 'com_config.component' || !isset($table->element, $table->type) || $table->element !== 'com_kunena' || $table->type !== 'component') {
            return;
        }

        $params = new Registry($table->params);

        // The badges and the social websites have their own view: the configuration form does not have them.
        foreach (['badges', 'socials', 'rankgroups'] as $own) {
            if (!$params->exists($own)) {
                $value = ComponentHelper::getParams('com_kunena')->get($own);

                if ($value) {
                    $params->set($own, $value);
                }
            }
        }

        $table->params = $params->toString();
    }

    /**
     * Method is called when an extension is being saved
     *
     * @param   ModelAfterSaveEvent $event  The event instance.
     *
     * @return  void
     *
     * @since   3.9.0
     */
    public function onExtensionAfterSave(ModelAfterSaveEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();
        $isNew   = $event->getIsNew();

        if ($context === 'com_config.component' && $table->type === 'component' && $table->element === 'com_kunena') {
            // We need to clear the cache for com_kunena configuration
            KunenaConfig::getInstance()->reset();
        }
    }
}
