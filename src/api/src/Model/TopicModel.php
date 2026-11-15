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
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;

/**
 * A topic, when it is published and the user may read it (else nothing: the API answers not found).
 *
 * @since   Kunena 8.0
 */
class TopicModel extends BaseDatabaseModel
{
    /**
     * @param   int|null  $pk  The id (else the state topic.id).
     *
     * @return  object|null
     *
     * @since   Kunena 8.0
     */
    public function getItem($pk = null)
    {
        $topic = KunenaTopicHelper::get((int) ($pk ?? $this->getState('topic.id')));

        if (!$topic->exists() || (int) $topic->hold !== 0 || (int) $topic->moved_id || !$topic->isAuthorised('read')) {
            return null;
        }

        return KunenaApi::topic($topic);
    }
}
