<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate\Message;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;

/**
 * Private message mapping to forum message.
 *
 * @property int $message_id
 * @property int $private_id
 * @since   Kunena 6.0
 */
class KunenaPost extends KunenaDatabaseObject
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $_table = 'KunenaPrivatePostMap';
}
