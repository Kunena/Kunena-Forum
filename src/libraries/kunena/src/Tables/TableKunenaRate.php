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
 * Kunena Rate
 * Provides access to the #__kunena_rate table
 *
 * @since   Kunena 6.0
 */
class TableKunenaRate extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $topic_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public $userid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $rate = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $time = null;

    /**
     * @var     mixed
     * @since   Kunena 8.0
     */
    public $id = null;

    /**
     * @param   DatabaseDriver  $db  database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_rate', 'topic_id', $db);
    }
}
