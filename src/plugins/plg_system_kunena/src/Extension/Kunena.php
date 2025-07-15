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
use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\Application\AfterInitialiseEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\User\KunenaBan;

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

            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * @param   array   $config   Config
     *
     * @since   Kunena 6.0
     * @throws  \Exception
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $app = Factory::getApplication();

        // Do not load when in API client application
        if ($app->isClient('api')) {
            return;
        }

        $kunenaApi = JPATH_ADMINISTRATOR . '/components/com_kunena/api/api.php';

        // Do not load if Kunena is installed, enabled, supported (version) and if api (constants) can be loaded
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('6.4')
            || !KunenaForum::installed()
            || !\is_file($kunenaApi)
        ) {
            return;
        }

        // Load Kunena API and autoload vendor libraries
        require_once $kunenaApi;
        require_once JPATH_LIBRARIES . '/kunena/External/autoload.php';

        // ! Always load language after parent::construct else the name of plugin isn't yet set
        $app->getLanguage()->load('plg_system_kunena.sys');
    }

    /**
     * Routines to run onAfterInitialise
     *
     * @param   AfterInitialiseEvent $event  The event instance.
     * 
     * @return  void
     * @since   Kunena 6.0
     */
    public function onAfterInitialise(AfterInitialiseEvent $event): void
    {
        // Add ban check / only on front-end
        if ($this->getApplication()->isClient('site')) {
            $timestamp = \time();
            $lastCheck = $this->params->get('ban_check_last', 0);

            if ($timestamp - $lastCheck >= 3600) {
                try {
                    $this->cleanExpiredBans();

                    // Update last check time
                    $this->params->set('ban_check_last', $timestamp);

                    // Save the parameters
                    /** @var DatabaseDriver */
                    $db    = $this->getDatabase();
                    $query = $db->createQuery()
                        ->update($db->quoteName('#__extensions'))
                        ->set($db->quoteName('params') . ' = ' . $db->quote($this->params->toString()))
                        ->where([
                            $db->quoteName('type') . ' = ' . $db->quote('plugin'),
                            $db->quoteName('folder') . ' = ' . $db->quote('system'),
                            $db->quoteName('element') . ' = ' . $db->quote('kunena')
                        ]);

                    $db->setQuery($query);
                    $db->execute();
                } catch (\Exception $e) {
                    $this->getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }
    }

    /**
     * Clean expired bans from the system
     *
     * @return  void
     * @since   Kunena 6.0
     */
    private function cleanExpiredBans(): void
    {
        /** @var DatabaseDriver */
        $db  = $this->getDatabase();
        $now = new Date();

        // Find expired site-wide bans
        $query = $db->createQuery()
            ->select('b.*')
            ->from($db->quoteName('#__kunena_users_banned', 'b'))
            ->where($db->quoteName('b.expiration') . ' <= ' . $db->quote($now->toSql()))
            ->where($db->quoteName('b.blocked') . ' = 1')
            ->where($db->quoteName('b.expiration') . ' != ' . $db->quote('9999-12-31 23:59:59'));

        $db->setQuery($query);
        $expiredBans = $db->loadObjectList();

        foreach ($expiredBans as $ban) {
            // Unblock user in Joomla
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($ban->userid);
            if ($user && $user->block) {
                $user->block = 0;
                $user->save();
            }

            // Update Kunena user profile
            $profile = KunenaFactory::getUser($ban->userid);
            $profile->banned = null;
            $profile->save(true);

            // Update ban record
            $banInstance = KunenaBan::getInstance($ban->id);
            if ($banInstance->exists()) {
                $banInstance->addComment('Automatically unbanned by system');
                $banInstance->modified_time = $now->toSql();
                $banInstance->save(true);
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
}
