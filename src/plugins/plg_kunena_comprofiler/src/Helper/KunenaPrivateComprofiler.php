<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Comprofiler\Helper;

\defined('_JEXEC') or die();

use CB\Plugin\PMS\PMSHelper;
use CB\Plugin\PMS\UddeIM;
use CBLib\Application\Application;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaPrivateAbstract;

/**
 * Class KunenaPrivateComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateComprofiler extends KunenaPrivateAbstract
{
    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $loaded = false;

    /**
     * @var     Registry
     * @since   Kunena 6.0
     */
    protected ?Registry $params = \null;

    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 6.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }

    /**
     * @param   integer  $userid  userid
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     */
    public function getUnreadCount(int $userid): int
    {
        global $_CB_PMS;

        if (! $userid) {
            return 0;
        }

        return ($_CB_PMS->getPMSunreadCount($userid)[0] ?? 0);
    }

    /**
     * @return  void|string
     *
     * @since   Kunena 6.0
     */
    public function getInboxURL()
    {
        global $_CB_PMS;

        $userid = Application::MyUser()->getUserId();

        if (! $userid) {
            return '';
        }

        return ($_CB_PMS->getPMSlinks($userid, 0, '', '', 2)[0]['url'] ?? '');
    }

    /**
     * @param   string  $text  text
     *
     * @return  void|string
     *
     * @since   Kunena 6.0
     */
    public function getInboxLink(string $text)
    {
        if (!$text) {
            $text = Text::_('COM_KUNENA_PMS_INBOX');
        }

        if (! Application::MyUser()->getUserId()) {
            return;
        }

        $url = $this->getInboxURL();

        if (! $url) {
            return;
        }

        return '<a href="' . $url . '" rel="follow">' . $text . '</a>';
    }

    /**
     * @param   int  $userid  userid
     *
     * @return string
     *
     * @since   Kunena 6.0
     */
    protected function getURL(int $userid): string
    {
        global $_CB_PMS;

        $user = KunenaFactory::getUser($userid);

        if (! $user->exists()) {
            return '';
        }

        $myid = Application::MyUser()->getUserId();

        if (UddeIM::isUddeIM()) {
            if (! $myid || $myid === $user->userid) {
                return '';
            }
        } elseif (! PMSHelper::canMessage($myid, $user->userid)) {
            return '';
        }

        return ($_CB_PMS->getPMSlinks($user->userid, $myid, '', '', 1)[0]['url'] ?? '');
    }
}
