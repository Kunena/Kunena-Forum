<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Joomla
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Joomla\Registry\Registry;

/**
 * class KunenaActivityAbstract 
 *
 * @since   Kunena 7.0.
 */
abstract class KunenaActivityAbstract
{
    /**
     * @var     ?Registry
     * @since   Kunena 7.0.0
     */
    protected ?Registry $params = \null;

    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 7.0.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }
}
