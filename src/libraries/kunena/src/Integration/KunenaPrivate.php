<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

/**
 * Class KunenaPrivate
 *
 * @since   Kunena 6.0
 */
class KunenaPrivate
{
    /**
     * @var     ?KunenaPrivate
     * @since   Kunena 6.0
     */
    protected static ?KunenaPrivate $instance = \null;

    /**
     * Function to get the active KunenaPrivate instance from the integration
     * 
     * @return  KunenaPrivate
     * @throws  \Exception
     * @since   Kunena 6.0
     */
    public static function getInstance(): KunenaPrivate
    {
        if (\null === self::$instance) {
            PluginHelper::importPlugin('kunena');

            $privateEvent = new KunenaGetPrivateEvent('onKunenaGetPrivate');
            Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetPrivate', $privateEvent);

            $private = $privateEvent->getPrivate();

            if ($private instanceof KunenaPrivate) {
                self::$instance = $private;
            } elseif (\is_array($private) && !empty($private) && $private[0] instanceof KunenaPrivate) {
                self::$instance = $private[0];
            } else {
                self::$instance = new self();
            }
        }

        return self::$instance;
    }

    /**
     * Show the icon of the link for private
     * 
     * @param   integer  $userid  userid
     *
     * @return  string
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function showIcon(int $userid): string
    {
        $my = Factory::getApplication()->getIdentity();

        // Don't send messages from/to anonymous and to yourself
        if ($my->id == 0 || $userid == 0 || $userid == $my->id) {
            return '';
        }

        $ktemplate     = KunenaFactory::getTemplate();
        $topicicontype = $ktemplate->params->get('topicicontype');

        if ($topicicontype == 'fa') {
            $class = 'btn btn-small';
        } elseif ($topicicontype == 'svg') {
            $class = 'btn btn-outline-primary btn-sm';
        } else {
            $class = 'btn btn-small';
        }

        $url = $this->getURL($userid);

        $onclick = $this->getOnClick($userid);

        // No PMS enabled or PM not allowed
        if (empty($url)) {
            return '';
        }

        // We should offer the user a PM link
        return '<a class="' . $class . '" href="' . $url . '""' . $onclick . '">' . KunenaIcons::pm() . '</a>';
    }

    /**
     * @param   integer  $userid  userid
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected function getURL(int $userid): string
    {
        return '';
    }

    /**
     * @param   integer  $userid  userid
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected function getOnClick(int $userid): string
    {
        return '';
    }

    /**
     * Show a new icon when there are new items in inbox
     * 
     * @param   integer  $userid  userid
     * @param   string   $class   class
     * @param   string   $icon    icon
     *
     * @return  string
     *
     * @throws Exception
     * @since    Kunena 6.0
     *
     * @internal param $text
     */
    public function showNewIcon(int $userid, $class = '', $icon = ''): string
    {
        $my      = Factory::getApplication()->getIdentity();
        $url     = $this->getURL($userid);
        $onclick = $this->getOnClick($userid);

        // No PMS enabled or PM not allowed
        if (empty($url) || $my->id == 0 || $userid == 0) {
            return '';
        }

        $ktemplate     = KunenaFactory::getTemplate();
        $topicicontype = $ktemplate->params->get('topicicontype');

        if (empty($class)) {
            if ($topicicontype == 'fa') {
                $class = 'btn btn-small';
            } elseif ($topicicontype == 'svg') {
                $class = 'btn btn-outline-primary btn-sm';
            } else {
                $class = 'btn btn-small';
            }
        }

        // Don't send messages from/to anonymous and to yourself
        if ($userid == $my->id) {
            $pmCount = $this->getUnreadCount($my->id);
            $text    = $pmCount ? Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $pmCount) : Text::_('COM_KUNENA_PMS_INBOX');
            $url     = $this->getInboxURL();

            return '<a class="' . $class . '" href="' . $url . '">' . KunenaIcons::pm() . ' ' . $text . '</a>';
        }

        // We should offer the user a PM link
        return '<a class="' . $class . '" href="' . $url . '"' . $onclick . '>' . KunenaIcons::pm() . ' ' . Text::_('COM_KUNENA_PM_WRITE') . '</a>';
    }

    /**
     * Get the number of unread items
     * 
     * @param   integer  $userid  userid
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     */
    public function getUnreadCount(int $userid): int
    {
        return 0;
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getInboxURL()
    {
        return '';
    }

    /**
     * @param   string  $text  text
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getInboxLink(string $text)
    {
        return '';
    }
}
