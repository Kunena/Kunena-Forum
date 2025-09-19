<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      UddeIM
 *
 * @copyright       Copyright (C) 2008 - 2025 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Uddeim\Helper;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Integration\KunenaPrivateAbstract;

/**
 * Class KunenaPrivateUddeIM
 * @since Kunena
 */
class KunenaPrivateUddeim extends KunenaPrivateAbstract
{
    /**
     * @var null|uddeIMAPI
     * @since Kunena
     */
    protected $uddeim = null;

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

        if (!\class_exists('uddeIMAPI')) {
            return;
        }

        $this->uddeim = new \uddeIMAPI;

        if ($this->uddeim->version() < 1) {
            return;
        }
    }

    /**
     * @param $userid
     *
     * @return mixed
     * @since Kunena
     */
    public function getUnreadCount($userid): int
    {
        return $this->uddeim->getInboxUnreadMessages($userid);
    }

    /**
     * @param $text
     *
     * @return string
     * @since Kunena
     */
    public function getInboxLink($text)
    {
        if (!$text) {
            $text = Text::_('COM_KUNENA_PMS_INBOX');
        }

        return '<a href="' . Route::_($this->uddeim->getLinkToBox('inbox', false)) . '" rel="follow">' . $text . '</a>';
    }

    /**
     * @return string
     * @since Kunena
     */
    public function getInboxURL()
    {
        return Route::_($this->uddeim->getLinkToBox('inbox', false));
    }

    /**
     * @param $userid
     *
     * @return string
     * @since Kunena
     */
    protected function getURL($userid): string
    {
        static $itemid = false;

        if ($itemid === false) {
            $itemid = 0;

            if (method_exists($this->uddeim, 'getItemid')) {
                $itemid = $this->uddeim->getItemid();
            }

            if ($itemid) {
                $itemid = '&Itemid=' . (int) $itemid;
            } else {
                $itemid = '';
            }
        }

        return Route::_('index.php?option=com_uddeim&task=new&recip=' . (int) $userid . $itemid);
    }
}
