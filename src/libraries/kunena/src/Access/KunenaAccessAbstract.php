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

namespace Kunena\Forum\Libraries\Access;

\defined('_JEXEC') or die();

use Joomla\Registry\Registry;

/**
 * class KunenaAccessAbstract 
 *
 * @since   Kunena 7.0
 */
abstract class KunenaAccessAbstract
{
    /**
     * @var     ?Registry
     * @since   Kunena 7.0
     */
    protected ?Registry $params = \null;

    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 7.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }
}
