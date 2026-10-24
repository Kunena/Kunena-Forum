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
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Kunena Topics
 * Provides access to the #__kunena_topics table
 *
 * @since   Kunena 6.0
 */
class TableKunenaTopics extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public $id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $categoryId = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $subject = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $icon_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $locked = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $hold = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $ordering = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $posts = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $hits = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $attachments = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $poll_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $moved_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $first_post_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $first_post_time = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $first_post_userid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $first_post_message = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $first_post_guest_name = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $last_post_id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $last_post_time = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $last_post_userid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $last_post_message = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $last_post_guest_name = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $params = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $category_id = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $label_id = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $rating = null;

    /**
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_topics', 'id', $db);
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
        $category = KunenaCategoryHelper::get($this->category_id);

        if (!$category->exists()) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $category->id));
        }

        $this->category_id = $category->id;
        $this->subject     = trim((string) $this->subject);

        // TINYTEXT columns: 255 bytes (see the check of the messages).
        foreach (['subject', 'first_post_guest_name', 'last_post_guest_name'] as $field) {
            if (\is_string($this->$field) && \strlen($this->$field) > 255) {
                $this->$field = mb_strcut($this->$field, 0, 255, 'UTF-8');
            }
        }

        if (!$this->subject) {
            throw new UnexpectedValueException(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_NO_SUBJECT'));
        }

        return true;
    }

    /**
     * @param   null  $id     id
     * @param   bool  $reset  reset
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

        // Load the user data.
        $query = $this->_db->createQuery()
            ->select('*')
            ->from($this->_db->quoteName('#__kunena_topics'))
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
}
