<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Joomla\Database\DatabaseDriver;

/**
 * Kunena Karma
 * Provides access to the #__kunena_karma table
 *
 * @since   Kunena 6.2
 */
class TableKunenaKarma extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 6.2
     */
    public $target_userid = null;

    /**
     * @var     null
     * @since   Kunena 6.2
     */
    public $time = null;

    /**
     * @param   DatabaseDriver  $db  database driver
     *
     * @since   Kunena 6.2
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_karma', 'target_userid', $db);
    }
}
