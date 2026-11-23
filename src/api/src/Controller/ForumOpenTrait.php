<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      Controller
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\Controller;

\defined('_JEXEC') or die();

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The forum offline and "registered users only" apply to the REST API as to the site (KunenaController::execute()): nothing in the
 * API checked them, so with "registered users only" a guest read every public post through the API, where the site shows a login
 * form, and with the forum offline a member with a token still read and posted.
 *
 * @since   Kunena 8.0
 */
trait ForumOpenTrait
{
    /**
     * @param   string  $task  The task.
     *
     * @return  mixed
     *
     * @throws  NotAllowed  The forum is offline (but for the administrators), or for registered users only and this is a guest.
     * @since   Kunena 8.0
     */
    public function execute($task)
    {
        $config = KunenaFactory::getConfig();
        $me     = KunenaUserHelper::getMyself();

        if ($config->boardOffline && !$me->isAdmin()) {
            KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

            throw new NotAllowed(Text::_('COM_KUNENA_FORUM_IS_OFFLINE'), 403);
        }

        if ($config->regOnly && !$me->exists()) {
            KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

            throw new NotAllowed(Text::_('COM_KUNENA_LOGIN_NOTIFICATION'), 403);
        }

        return parent::execute($task);
    }
}
