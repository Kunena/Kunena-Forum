<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      Model
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\Model;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Kunena\Forum\Libraries\Api\KunenaApi;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * A post, when it is published and the user may read it (else nothing: the API answers not found).
 *
 * @since   Kunena 8.0
 */
class PostModel extends BaseDatabaseModel
{
    /**
     * @param   int|null  $pk  The id (else the state post.id).
     *
     * @return  object|null
     *
     * @since   Kunena 8.0
     */
    public function getItem($pk = null)
    {
        $message = KunenaMessageHelper::get((int) ($pk ?? $this->getState('post.id')));

        if (!$message->exists() || (int) $message->hold !== 0 || !$message->isAuthorised('read')) {
            return null;
        }

        return KunenaApi::post($message, KunenaUserHelper::getMyself());
    }
}
