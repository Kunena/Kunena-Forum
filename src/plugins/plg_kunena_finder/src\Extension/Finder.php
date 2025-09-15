<?php

/**
 * @copyright      Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Plugin\Kunena\Finder;

\defined('_JEXEC') or die;

use Joomla\CMS\Event\Finder\AfterDeleteEvent;
use Joomla\CMS\Event\Finder\AfterSaveEvent;
use Joomla\CMS\Event\Finder\BeforeSaveEvent;
use Joomla\CMS\Event\Model\AfterDeleteEvent as ModelAfterDeleteEvent;
use Joomla\CMS\Event\Model\AfterSaveEvent as ModelAfterSaveEvent;
use Joomla\CMS\Event\Model\BeforeDeleteEvent as ModelBeforeDeleteEvent;
use Joomla\CMS\Event\Model\BeforeSaveEvent as ModelBeforeSaveEvent;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaBeforeDeleteEvent;

/**
 * Finder Kunena Plugin
 *
 * @package        Joomla.Plugin
 * @subpackage     Kunena.finder
 * @since          2.5
 */
final class Finder extends CMSPlugin implements SubscriberInterface, DispatcherAwareInterface
{
    use DispatcherAwareTrait;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return array
     *
     * @since   5.3.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onKunenaAfterDelete'  => 'onKunenaAfterDelete',
            'onKunenaAfterSave'    => 'onKunenaAfterSave',
            'onKunenaBeforeDelete' => 'onKunenaBeforeDelete',
            'onKunenaBeforeSave'   => 'onKunenaBeforeSave',
        ];
    }

    /**
     * Finder after save message method
     * Message is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the Message is saved
     *
     * @param   ModelAfterSaveEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   2.5
     *
     * @throws  \Exception
     */
    public function onKunenaAfterSave(ModelAfterSaveEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();
        $isNew   = $event->getIsNew();

        Log::add('onKunenaAfterSave context: ' . $context, Log::INFO);
        \ob_start();
        $table_content = \ob_get_contents();
        \ob_end_clean();
        Log::add('onKunenaAfterSave table: ' . $table_content, Log::INFO);
        Log::add('onKunenaAfterSave isNew: ' . ($isNew) ? 'Yes' : 'No', Log::INFO);

        PluginHelper::importPlugin('finder');

        // Trigger the onFinderAfterSave event.
        $this->getDispatcher()->dispatch('onFinderAfterSave', new AfterSaveEvent('onFinderAfterSave', [
            'context' => $context,
            'subject' => $table,
            'isNew'   => $isNew,
        ]));
    }

    /**
     * Finder before save message method
     * Method is called right before the content is saved
     *
     * @param   ModelBeforeSaveEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   2.5
     *
     * @throws  \Exception
     */
    public function onKunenaBeforeSave(ModelBeforeSaveEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();
        $isNew   = $event->getIsNew();

        Log::add('onKunenaBeforeSave context: ' . $context, Log::INFO);
        \ob_start();
        $table_content = \ob_get_contents();
        \ob_end_clean();
        Log::add('onKunenaBeforeSave table: ' . $table_content, Log::INFO);
        Log::add('onKunenaBeforeSave isNew: ' . ($isNew) ? 'Yes' : 'No', Log::INFO);

        PluginHelper::importPlugin('finder');

        // Trigger the onFinderBeforeSave event.
        $this->getDispatcher()->dispatch('onFinderBeforeSave', new BeforeSaveEvent('onFinderBeforeSave', [
            'context' => $context,
            'subject' => $table,
            'isNew'   => $isNew,
        ]));
    }

    /**
     * Finder after delete message method
     *
     * @param   ModelAfterDeleteEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   2.5
     *
     * @throws  \Exception
     */
    public function onKunenaAfterDelete(ModelAfterDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        Log::add('onKunenaAfterDelete context: ' . $context, Log::INFO);
        \ob_start();
        $table_content = \ob_get_contents();
        \ob_end_clean();
        Log::add('onKunenaAfterDelete table: ' . $table_content, Log::INFO);

        PluginHelper::importPlugin('finder');

        // Trigger the onFinderAfterDelete event.
        $this->getDispatcher()->dispatch('onFinderAfterDelete', new AfterDeleteEvent('onFinderAfterDelete', [
            'context' => $context,
            'subject' => $table,
        ]));
    }

    /**
     * Finder before delete message method
     *
     * @param   ModelBeforeDeleteEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   2.5
     *
     * @throws  \Exception
     */
    public function onKunenaBeforeDelete(ModelBeforeDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        Log::add('onKunenaBeforeDelete context: ' . $context, Log::INFO);
        \ob_start();
        $table_content = \ob_get_contents();
        \ob_end_clean();
        Log::add('onKunenaBeforeDelete table: ' . $table_content, Log::INFO);

        PluginHelper::importPlugin('finder');

        // Trigger the onFinderAfterDelete event.
        $this->getDispatcher()->dispatch('onFinderBeforeDelete', new KunenaBeforeDeleteEvent('onFinderBeforeDelete', [
            'context' => $context,
            'subject' => $table,
        ]));
    }
}
