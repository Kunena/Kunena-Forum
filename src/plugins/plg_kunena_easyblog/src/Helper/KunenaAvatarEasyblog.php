<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Easyblog\Helper;

defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class KunenaAvatarEasyblog
 *
 * @since Kunena
 */
class KunenaAvatarEasyblog extends KunenaAvatar
{
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
     * @return boolean
     * @since Kunena
     * @throws Exception
     * @throws null
     */
    public function getEditURL(): string
    {
        return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
    }

    /**
     * @param   KunenaUser  $user   user
     * @param   int         $sizex  sizex
     * @param   int         $sizey  sizey
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
    {
        if (!$user->userid == 0) {
            $user   = KunenaFactory::getUser($user->userid);
            $user   = EB::user($user->userid);
            $avatar = $user->getAvatar();
        } else {
            $avatar = Uri::root(true) . '/components/com_easyblog/assets/images/default_blogger.png';
        }

        return $avatar;
    }
}
