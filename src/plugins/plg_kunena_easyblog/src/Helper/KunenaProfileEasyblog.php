<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright   (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Easyblog\Helper;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * @package     Kunena
 *
 * @since       Kunena
 */
class KunenaProfileEasyblog extends KunenaProfile
{
    /**
     * @var     Registry
     * @since   Kunena 5.0
     */
    protected ?Registry $params = \null;

    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 5.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }

    /**
     * Get the URL for userlist from Easyblog
     *
     * @param   string  $action  action
     * @param   bool    $xhtml   xhtml
     *
     * @return string
     *
     * @since   Kunena 5.0
     * @throws \Exception
     */
    public function getUserListURL(string $action = '', bool $xhtml = true): string
    {
        $config = KunenaFactory::getConfig();
        $my     = Factory::getApplication()->getIdentity();

        if ($config->userlistAllowed == 0 && $my->id == 0) {
            return false;
        }

        return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
    }

    /**
     * Get the profile URL from CB
     *
     * @param   int     $userid     userid
     * @param   string  $task       task
     * @param   bool    $xhtml      xhtml
     * @param   string  $avatarTab  avatarTab
     *
     * @return  string|false
     * @since   Kunena 5.0
     * @throws  \Exception
     */
    public function getProfileURL(int $userid, $task = '', bool $xhtml = true, string $avatarTab = ''): string|false
    {
        // Make sure that user profile exist.
        if (!$userid) {
            return false;
        }

        return Route::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $userid, false);
    }

    /**
     * @param   KunenaLayout  $view    view
     * @param   Registry      $params  params
     *
     * @return  string
     *
     * @since   Kunena 5.0
     */
    public function showProfile(KunenaLayout $view, Registry $params): string
    {
        return '';
    }

    /**
     * @param   int   $userid  userid
     * @param   bool  $xhtml   xhtml
     *
     * @return  boolean
     *
     * @since   Kunena 5.0
     */
    public function getEditProfileURL(int $userid, bool $xhtml = true): string
    {
        return $this->getProfileURL($userid, 'edit', $xhtml);
    }
}
