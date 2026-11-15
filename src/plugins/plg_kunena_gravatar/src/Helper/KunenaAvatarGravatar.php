<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Gravatar
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Gravatar\Helper;

\defined('_JEXEC') or die();

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class KunenaAvatarGravatar
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarGravatar extends KunenaAvatar
{
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
     * @return string
     *
     * @since   Kunena 6.0
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
     */
    protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
    {
        $user = KunenaFactory::getUser($user);

        // The hash of the address is in the html of every page with this avatar: not for a member who hides the address (Kunena
        // 7.1). An empty address gives the default image (the hash of zeros, forced).
        return GravatarUrl::build(
            GravatarPrivacy::emailForHash($user->email, $user->hideEmail),
            min($sizex, $sizey),
            (string) $this->params->get('default_image', ''),
            'g'
        );
    }
}
