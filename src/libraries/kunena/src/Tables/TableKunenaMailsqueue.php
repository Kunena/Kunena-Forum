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

use Joomla\Database\DatabaseDriver;

/**
 * Kunena Mails Queue Table
 * Provides access to the #__kunena_notifications_mailsqueue table
 *
 * @since   Kunena 7.1
 */
class TableKunenaMailsqueue extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public $id = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $subject = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $messageId = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $url = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $emailListJson = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $categoryName = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $once = null;

    /**
     * @var     null
     * @since   Kunena 7.1
     */
    public mixed $send = null;

    /**
     * @var     mixed
     * @since   Kunena 8.0
     */
    public mixed $sentusers = null;

    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 7.1
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_notifications_mailsqueue', 'id', $db);
    }
}
