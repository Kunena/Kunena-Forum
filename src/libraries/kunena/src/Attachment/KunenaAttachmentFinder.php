<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Attachment
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Collection\KunenaCollection;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Forum\Libraries\Error\KunenaError;
use RuntimeException;

/**
 * Class KunenaAttachmentFinder
 *
 * @since   Kunena 5.0
 */
class KunenaAttachmentFinder extends KunenaFinder
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $table = '#__kunena_attachments';

    /**
     * Filter by attachment private.
     *
     * @return  $this
     *
     * @since   Kunena 6.4
     */
    public function filterByPrivate()
    {
        $this->query->where($this->db->quoteName('protected') . ' = 32');

        return $this;
    }

    /**
     * Get log entries.
     *
     * @return  array|KunenaCollection
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception|void
     */
    public function find()
    {
        if ($this->skip) {
            return [];
        }

        $query = clone $this->query;
        $this->build($query);
        $query->select('a.*');
        $query->setLimit($this->limit, $this->start);

        // A page which prepares its lists twice (the tabs of a profile) searches once.
        $results = self::rememberedAnswer('attachments:' . $query . '|' . (int) $this->limit . '|' . (int) $this->start, function () use ($query) {
            $this->db->setQuery($query);

            $results = [];

            try {
                $results = (array) $this->db->loadObjectList('id');
            } catch (RuntimeException $e) {
                KunenaError::displayDatabaseError($e);
            }

            return $results;
        });

        // The rows have every column of the attachments: they become the attachments (get() would read each one again).
        $instances = KunenaAttachmentHelper::getFromRows($results);

        unset($results);

        return $instances;
    }
}
