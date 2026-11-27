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

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Error\KunenaError;
use RuntimeException;
use UnexpectedValueException;

/**
 * Kunena Categories
 * Provides access to the #__kunena_categories table
 *
 * @since   Kunena 6.0
 */
class TableKunenaCategories extends KunenaTable
{
    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public $id = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $parentid = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $name = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $alias = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $icon = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $icon_id = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $locked = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $accesstype = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public int $access;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $pubAccess = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $pubRecurse = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $adminAccess = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $adminRecurse = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $ordering = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $published = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $channels = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $checked_out = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $checked_out_time = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $review = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $allowAnonymous = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $postAnonymous = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $hits = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $description = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $headerdesc = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $class_sfx = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $allowPolls = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $topicOrdering = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $iconset = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $numTopics = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $numPosts = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $last_topic_id = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $last_post_id = null;

    /**
     * @var     int|null
     * @since   Kunena 6.0
     */
    public mixed $last_post_time = null;

    /**
     * @var     string|null
     * @since   Kunena 6.0
     */
    public mixed $params = null;

    /**
     * @var     string|null
     * @since   Kunena 5.1.0
     */
    public mixed $topictemplate = null;

    /**
     * @var     mixed
     * @since   Kunena 8.0
     */
    public mixed $allowRatings = null;

    /**
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct($db)
    {
        parent::__construct('#__kunena_categories', 'id', $db);
    }

    /**
     * @param   mixed   $array   array
     * @param   string  $ignore  ignore
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function bind($array, $ignore = ''): bool
    {
        if (\is_object($array)) {
            $array = get_object_vars($array);
        }

        if (isset($array['params']) && !\is_string($array['params'])) {
            if ($array['params'] instanceof Registry) {
                $registry = $array['params'];
            } elseif (\is_array($array['params'])) {
                $registry = new Registry();
                $registry->loadArray($array['params']);
            } else {
                $registry = new Registry();
            }

            $array['params'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function check(): bool
    {
        if ($this->id && $this->parentid) {
            if ($this->id == $this->parentid) {
                throw new RuntimeException(Text::_('COM_KUNENA_FORUM_SAME_ERR'));
            }

            if ($this->isChild($this->parentid)) {
                throw new RuntimeException(Text::_('COM_KUNENA_FORUM_OWNCHILD_ERR'));
            }
        }

        $this->name = trim((string) $this->name);

        // A TINYTEXT column (255 bytes) which the form does not limit: a longer name was an error of the strict database of Joomla
        // and the category was not saved. It is cut at a character.
        if (\strlen($this->name) > 255) {
            $this->name = mb_strcut($this->name, 0, 255, 'UTF-8');
        }

        if (!$this->name) {
            throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_CATEGORIES_ERROR_NO_NAME'));
        }

        if ($this->params instanceof Registry) {
            $this->params = $this->params->toString();
        }

        return true;
    }

    // Check if given forum is one of its own childs

    /**
     * @param   integer  $id  id
     *
     * @return  false|integer
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function isChild($id)
    {
        // The parents as they are in the database, not from the categories of KunenaCategoryHelper: this runs in check(), while
        // categories are saved (a move, a batch), when those objects can be older than the table. Two columns of each category.
        if ($id > 0) {
            $query = $this->_db->createQuery()
                ->select(['id', 'parentid'])
                ->from($this->_db->quoteName('#__kunena_categories'));
            $this->_db->setQuery($query);

            try {
                $list = $this->_db->loadObjectList('id');
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }

            $recurse = [];

            while ($id) {
                if (\in_array($id, $recurse)) {
                    throw new Exception(\get_class($this) . Text::_('COM_KUNENA_RECURSION'));
                }

                $recurse[] = $id;

                if (!isset($list[$id])) {
                    throw new Exception(\get_class($this) . Text::_('COM_KUNENA_LIB_TABLE_CATEGORIES_ERROR_INVALID'));
                }

                $id = $list[$id]->parentid;

                if ($id != 0 && $id == $this->id) {
                    return 1;
                }
            }
        }

        return 0;
    }

    /**
     * @param   mixed  $id     id
     * @param   bool   $reset  reset
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function load($id = null, $reset = true): bool
    {
        $this->_exists = false;
        $k             = $this->_tbl_key;

        // Get the id to load.
        if ($id !== null) {
            $this->$k = $id;
        }

        // Reset the table.
        if ($reset) {
            $this->reset();
        }

        // Check for a valid id to load.
        if ($this->$k === null || \intval($this->$k) < 1) {
            $this->$k = 0;

            return false;
        }

        // Load the data.
        $query = $this->_db->createQuery()
            ->select('*')
            ->from($this->_db->quoteName('#__kunena_categories'))
            ->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($this->$k));
        $this->_db->setQuery($query);

        try {
            $data = $this->_db->loadAssoc();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        if (!$data) {
            $this->$k = 0;

            return false;
        }

        $this->_exists = true;

        // Bind the data to the table.
        $this->bind($data);

        return $this->_exists;
    }

    /**
     * @param   string  $where  where
     *
     * @return bool
     *
     * @since   Kunena 6.0
     */
    public function reOrder($where = ''): bool
    {
        if (!$where) {
            $query = $this->_db->createQuery()
                ->select($this->_db->quoteName('parentid'))
                ->from($this->_db->quoteName('#__kunena_categories'))
                ->group($this->_db->quoteName('parentid'));
            $this->_db->setQuery($query);

            $parents = $this->_db->loadColumn();
            $success = true;

            foreach ($parents as $parentid) {
                $success &= parent::reOrder("parentid={$this->_db->quote($parentid)}");
            }

            return $success;
        }

        return parent::reOrder($where);
    }

    /**
     * @param   bool  $updateNulls  update
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function store($updateNulls = false): bool
    {
        return parent::store($updateNulls);
    }
}
