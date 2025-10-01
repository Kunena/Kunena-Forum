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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Model\AfterSaveEvent as ModelAfterSaveEvent;
use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Event\User\BeforeSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Joomla\CMS\User\User;
use Joomla\Http\Http;
use Joomla\Utilities\IpHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;

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
            $mapping['onUserAfterSave'] = 'onUserAfterSave';
            $mapping['onUserAfterLogin'] = 'onUserAfterLogin';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
                $mapping['onUserBeforeSave'] = 'onUserBeforeSave';
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
                $mapping['onExtensionAfterSave'] = 'onExtensionAfterSave';
            }
        }

        return $mapping;
    }

    /**
     * Method is called before user data is stored in the database
     *
     * @param   array    $user   Holds the old user data.
     * @param   boolean  $isNew  True if a new user is stored.
     * @param   array    $data   Holds the new user data.
     *
     * @return  boolean
     *
     * @since   7.0.0
     * @throws  \Exception when listed on stopforumspam
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
        };

        // Check that the terms is checked if required ie only in registration from frontend.
        $data              = $event->getData();
        $query['username'] = $data['username'];
        $query['email']    = $data['email'];
        $query['ip']       = IpHelper::getIp();

        if ($query['ip'] === '::1' || $query['ip'] === '127.0.0.1') {
            // Local Host
            unset($query['ip']);
        }

        $http     = new Http();
        $request  = 'https://api.stopforumspam.org/api?' . \http_build_query($query) . '&json';
        $response = $http->get($request);

        if ($response->getStatusCode() == '200') {
            // The query has worked
            $result = \json_decode($response->getBody());

            if ($result && $result->success) {
                if (($result->username->appears ?? false) || ($result->email->appears ?? false) || ($result->ip->appears ?? false)) {
                    $this->loadLanguage('plg_system_kunena.sys');
                    throw new \Exception(Text::_('PLG_SYSTEM_KUNENA_STOPFORUMSPAMLISTED'), 400);
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
            || !KunenaForum::isCompatible('6.4')
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
     * @return string
     * @since   Kunena 7.0.0
     */
    public function onUserAfterLogin(AfterLoginEvent $event): void
    {
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('6.4')
            || !KunenaForum::installed()
        ) {
            return;
        }

        if (!($this->getApplication()->isClient('site'))) {
            return;
        }

        $options = $event->getOptions();
        $kuser = KunenaFactory::getUser(\intval($options['user']->id));
        $kuser->language = $this->getUserDefaultLanguage($options['user']->params);

        $kuser->save();
    }

    /**
     * Get the user language defined in his Joomla! profile 
     * 
     * @param User $user
     * @return string
     * @since   Kunena 7.0.0
     */
    protected function getUserDefaultLanguage($params)
    {
        if (!empty($params)) {
            $userParams = json_decode($params);
            $language = $userParams->language;
        } else {
            $language = Factory::getApplication()->getLanguage()->getTag();
        }

        return $language;
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
