<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate\Message;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;

/**
 * Private message mapping for user.
 *
 * @property int    $user_id
 * @property string $read_at
 * @property string $replied_at
 * @property string $deleted_at
 * @property int    $private_id
 * @since   Kunena 6.0
 */
class KunenaUser extends KunenaDatabaseObject
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $_table = 'KunenaPrivateUserMap';

    /**
     * Get the table relevant properties. Override for your specific Object
     * 
     * @return array    Assocative array with the propertie values of table
     * 
     * @since   Kunena 6.4
     */
    protected function getTableProperties(): array
    {
        $properties = [
            'private_id' => $this->private_id,
            'user_id'    => $this->user_id,
            'read_at'    => $this->read_at,
            'replied_at' => $this->replied_at,
            'deleted_at' => $this->deleted_at
        ];

        return $properties;
    }
}
