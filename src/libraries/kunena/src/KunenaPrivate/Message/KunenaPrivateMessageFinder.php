<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate\Message;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Private message finder.
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateMessageFinder extends KunenaFinder
{
    /**
     * The private messages which the searches of this page found, by id.
     *
     * @var     KunenaPrivateMessage[]
     * @since   Kunena 8.0
     */
    protected static array $instances = [];

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $table = '#__kunena_private';

    /**
     * @param   KunenaMessage  $message  message object
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByMessage(KunenaMessage $message): KunenaPrivateMessageFinder
    {
        if (!$message->id) {
            $this->skip = true;
        } else {
            $this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
            $this->query->where("pm.message_id = {$this->db->quote($message->id)}");
        }

        return $this;
    }

    /**
     * @param   array  $ids  ids
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByMessageIds(array $ids): KunenaPrivateMessageFinder
    {
        if (empty($ids)) {
            $this->skip = true;
        } else {
            $this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
            $this->query->where("pm.message_id IN (" . implode(',', array_map('intval', $ids)) . ")");
        }

        return $this;
    }

    /**
     * @param   KunenaUser  $user  user object
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByUser(KunenaUser $user): KunenaPrivateMessageFinder
    {
        if (!$user->userid) {
            $this->skip = true;
        } else {
            $this->query->innerJoin('#__kunena_private_user_map AS um ON a.id=um.private_id');
            $this->query->where("um.user_id = {$this->db->quote($user->userid)}");
        }

        return $this;
    }

    /**
     * Get private messages.
     *
     * @return array
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function find(): array
    {
        return $this->load(parent::find());
    }

    /**
     * @return KunenaPrivateMessage
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function firstOrNew(): KunenaPrivateMessage
    {
        $results = $this->find();
        $first   = array_shift($results);

        return $first ?: new KunenaPrivateMessage();
    }

    /**
     * @param   array  $ids  ids
     *
     * @return  array
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function load(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($ids)) {
            return [];
        }

        // The private messages which a search of this page found before are kept (a topic and the check of its private files
        // look for the same message): only the others are read, and each row becomes its message (a load() would read it again).
        $missing = array_values(array_diff($ids, array_keys(self::$instances)));

        if ($missing) {
            $query = $this->db->createQuery();
            $query->select('*')->from('#__kunena_private')->where('id IN(' . implode(',', $missing) . ')');
            $this->db->setQuery($query);

            foreach ((array) $this->db->loadObjectList('id') as $id => $row) {
                $message = new KunenaPrivateMessage((array) $row);
                $message->exists(true);
                self::$instances[(int) $id] = $message;
            }
        }

        $list = [];

        foreach ($ids as $id) {
            if (isset(self::$instances[$id])) {
                $list[$id] = self::$instances[$id];
            }
        }

        return $list;
    }

    /**
     * Forget the private messages which were found before (after one of them was changed or deleted).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function forget(): void
    {
        self::$instances = [];
    }
}
