<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message\Karma;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;

/**
 * Kunena Forum Topic Karma Helper Class
 *
 * @since 6.2
 */
abstract class KunenaKarmaHelper
{
    /**
     * @var     array
     * @since   Kunena 6.2
     */
    protected static $_instances = [];

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Topic\TopicRate object
     *
     * @access    public
     *
     * @internal  param The $identifier rate object to load - Can be only an integer.
     *
     * @param   bool  $reload      reload
     *
     * @param   null  $identifier  identifier
     *
     * @return  KunenaKarma The rate object.
     *
     * @since     Kunena 6.2
     *
     * @throws  Exception
     */
    public static function get($identifier = null, $reload = false): KunenaKarma
    {
        if ($identifier instanceof KunenaKarma) {
            return $identifier;
        }

        $id = \intval($identifier);

        if ($id < 1) {
            return new KunenaKarma();
        }

        if ($reload || empty(self::$_instances [$id])) {
            self::$_instances [$id] = new KunenaKarma($id);
        }

        return self::$_instances [$id];
    }
}
