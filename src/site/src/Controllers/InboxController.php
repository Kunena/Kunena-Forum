<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\KunenaController;

/**
 * Kunena Inbox Controller
 *
 * @since   Kunena 8.0
 */
class InboxController extends KunenaController
{
    /**
     * @param   array  $config  config
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}
