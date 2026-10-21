<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      GeoIp
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Geoip\Extension;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * The place of an IP address, from the library GeoIp of Regular Labs (the database of MaxMind GeoLite2, IPv4 and IPv6).
 *
 * @since   Kunena 7.2
 */
class GeoIp extends CMSPlugin implements SubscriberInterface
{
    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     * @since   Kunena 7.2
     */
    public static function getSubscribedEvents(): array
    {
        return ['onKunenaGetIpLocation' => 'onKunenaGetIpLocation'];
    }

    /**
     * @param   array  $config  config
     *
     * @since   Kunena 7.2
     */
    public function __construct(array $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('7.1') && KunenaForum::enabled())) {
            return;
        }

        // Call loadApi method to load Kunena constants
        KunenaForum::loadApi();

        parent::__construct($config);
    }

    /**
     * Give the place of an IP address.
     *
     * @param   GenericEvent  $event  The event instance: the argument ip in, the argument location out.
     *
     * @return  void
     * @since   Kunena 7.2
     */
    public function onKunenaGetIpLocation(GenericEvent $event): void
    {
        $class = 'RegularLabs\\Library\\GeoIp\\GeoIp';

        if (!isset($this->params) || !class_exists($class) || !$class::hasDatabase()) {
            return;
        }

        try {
            $record = (new $class((string) $event->getArgument('ip', '')))->get(['countryCode', 'country', 'region', 'city', 'latitude', 'longitude']);
        } catch (Exception $e) {
            return;
        }

        if (!$record || empty($record->country)) {
            return;
        }

        $detail = (string) $this->params->get('detail', 'city');
        $parts  = [$record->country];

        if ($detail !== 'country' && !empty($record->region)) {
            array_unshift($parts, $record->region);
        }

        if ($detail === 'city' && !empty($record->city)) {
            array_unshift($parts, $record->city);
        }

        $event->setArgument('location', [
            'label'     => implode(', ', array_unique($parts)),
            'country'   => $record->countryCode ?: '',
            'latitude'  => is_numeric($record->latitude) ? (float) $record->latitude : null,
            'longitude' => is_numeric($record->longitude) ? (float) $record->longitude : null,
        ]);
    }
}
