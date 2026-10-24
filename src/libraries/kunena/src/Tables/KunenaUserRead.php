<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Kunena User Read Table
 * Provides access to the #__kunena_user_read table
 *
 * @since   Kunena 6.0
 */
class KunenaUserRead extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $user_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $topic_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $categoryId = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $message_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $time = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $category_id = null;

    /**
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_user_read', ['user_id', 'topic_id'], $db);
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function check(): bool
    {
        $user  = KunenaUserHelper::get($this->user_id);
        $topic = KunenaTopicHelper::get($this->topic_id);

        if (!$user->exists()) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid));
        }

        if (!$topic->exists()) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id));
        }

        $this->category_id = $topic->category_id;

        return true;
    }
}
