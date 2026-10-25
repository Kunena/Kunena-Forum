<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Html
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Html;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * The IP address of a message with its place: the place comes from a plugin (the event onKunenaGetIpLocation), when there is one.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaIpLocation
{
    /**
     * @var     array
     * @since   Kunena 8.0
     */
    protected static array $locations = [];

    /**
     * The place of an IP address.
     *
     * @param   string  $ip  The IP address.
     *
     * @return  array|null  label, country (code), latitude and longitude; null when the place is not known.
     *
     * @since   Kunena 8.0
     */
    public static function get(string $ip): ?array
    {
        $ip = trim($ip);

        // The place of a local or reserved address (127.0.0.1, ::1, 192.168.x.x) is of no use.
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        if (\array_key_exists($ip, self::$locations)) {
            return self::$locations[$ip];
        }

        $location = null;

        try {
            PluginHelper::importPlugin('kunena');

            $event = new GenericEvent('onKunenaGetIpLocation', ['ip' => $ip, 'location' => null]);
            Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetIpLocation', $event);

            $answer   = $event->getArgument('location');
            $location = \is_array($answer) && !empty($answer['label']) ? $answer : null;
        } catch (Exception $e) {
            // No place is better than a broken page.
        }

        return self::$locations[$ip] = $location;
    }

    /**
     * The IP address as a link, with the place (a link to the map) when it is known.
     *
     * @param   string  $ip  The IP address.
     *
     * @return  string  Html.
     *
     * @since   Kunena 8.0
     */
    public static function html(string $ip): string
    {
        $html = '<a href="https://search.dnslytics.com/ip/' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8')
            . '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '</a>';

        $location = self::get($ip);

        if ($location) {
            $label = htmlspecialchars($location['label'], ENT_QUOTES, 'UTF-8');

            if (isset($location['latitude'], $location['longitude'])) {
                $html .= ' &middot; <a href="' . htmlspecialchars(KunenaMap::coordinatesUrl((float) $location['latitude'], (float) $location['longitude']), ENT_QUOTES, 'UTF-8')
                    . '" target="_blank" rel="nofollow noopener noreferrer">' . $label . '</a>';
            } else {
                $html .= ' &middot; ' . $label;
            }
        }

        return $html;
    }
}
