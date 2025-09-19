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
use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Joomla\CMS\User\User;

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
     * @since   Kunena 6.5
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
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
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
     * @since   Kunena 6.5
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
     * @since   Kunena 6.5
     */
    protected function getUserDefaultLanguage($params)
    {
        $language = '';

        if (!empty($params)) {
            $userParams = \json_decode($params);
            $language = $userParams->language;
        }

        if (empty($language)) {
            $language = Factory::getApplication()->getLanguage()->getTag();
        }

        return $language;
    }
}
