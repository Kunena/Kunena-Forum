<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.User.Read
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\User\Read;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\Tables\KunenaUserRead;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read
 *
 * @property int $topic_id
 * @property int $category_id
 * @property int $message_id
 * @property int $time
 * @property int $user_id
 * @since   Kunena
 */
class KunenaRead
{
    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $_exists = false;

    /**
     * @var     DatabaseDriver|null
     * @since   Kunena 6.0
     */
    protected $_db = null;

    /**
     * @var     int
     * @since   Kunena 6.4
     */
    public $user_id = null;

    /**
     * @var     int
     * @since   Kunena 6.4
     */
    public $topic_id = null;

    /**
     * @var     int
     * @since   Kunena 6.4
     */
    public $message_id = null;

    /**
     * @var     int
     * @since   Kunena 6.4
     */
    public $time = null;
    
    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public $typeAlias;
    
    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public $categoryId;
    
    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public $category_id;

    /**
     * @param   mixed  $user   user
     *
     * @param   mixed  $topic  topic
     *
     * @throws  Exception
     * @since   Kunena 6.0
     *
     * @internal
     */
    public function __construct($topic = null, $user = null)
    {
        $topic = KunenaTopicHelper::get($topic);

        // Always fill empty data
        $this->_db = Factory::getContainer()->get('DatabaseDriver');

        // Create the table object
        $table = $this->getTable();

        // Lets bind the data
        $this->bind($table->getProperties());
        $this->_exists     = false;
        $this->topic_id    = $topic->exists() ? $topic->id : null;
        $this->category_id = $topic->exists() ? $topic->category_id : null;
        $this->user_id     = KunenaUserHelper::get($user)->userid;
    }

    /**
     * Method to get the topics table object.
     *
     * @param   string  $type    Topics table name to be used.
     * @param   string  $prefix  Topics table prefix to be used.
     *
     * @return  boolean|Table
     *
     * @since   Kunena 6.0
     */
    public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'KunenaUserRead')
    {
        static $tabletype = null;

        // Set a custom table type is defined
        if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
            $tabletype ['name']   = $type;
            $tabletype ['prefix'] = $prefix;
        }

        // Create the KunenaUserRead table object
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $tableObject = new KunenaUserRead($db);

        return $tableObject;
    }

    /**
     * @param   mixed  $id      id
     * @param   mixed  $user    user
     * @param   bool   $reload  reload
     *
     * @return  KunenaRead
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function getInstance($id = null, $user = null, $reload = false): KunenaRead
    {
        return KunenaTopicUserReadHelper::get($id, $user, $reload);
    }

    /**
     * @return  KunenaRead
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getTopic(): KunenaRead
    {
        return KunenaTopicUserReadHelper::get($this->topic_id);
    }

    /**
     * @param   array  $data    data
     * @param   array  $ignore  ignore
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function bind(array $data, array $ignore = []): void
    {
        $data = array_diff_key($data, array_flip($ignore));
        foreach ((array) $data as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function reset(): void
    {
        $this->topic_id = 0;
        $this->load();
    }

    /**
     * Method to load a \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read object by id.
     *
     * @param   int    $topic_id  Topic id to be loaded.
     * @param   mixed  $user      user
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function load($topic_id = null, $user = null): bool
    {
        if ($topic_id === null) {
            $topic_id = $this->topic_id;
        }

        if ($user === null && $this->user_id !== null) {
            $user = $this->user_id;
        }

        $user = KunenaUserHelper::get($user);

        // Create the table object
        $table = $this->getTable();

        // Load the KunenaTable object based on id
        if ($topic_id) {
            $this->_exists = $table->load(['user_id' => $user->userid, 'topic_id' => $topic_id]);
        } else {
            $this->_exists = false;
        }

        // Assuming all is well at this point lets bind the data
        $this->bind($table->getProperties());

        return $this->_exists;
    }

    /**
     * Method to save the \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read object to the database.
     *
     * @param   bool  $updateOnly  Save the object only if not a new entry.
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function save($updateOnly = false): bool
    {
        // Create the topics table object
        $table = $this->getTable();

        $properties = [
            'user_id'     => $this->user_id,
            'topic_id'    => $this->topic_id,
            'category_id' => $this->category_id,
            'message_id'  => $this->message_id,
            'time'        => $this->time
        ];

        $table->bind($properties);
        $table->exists($this->_exists);

        // Check and store the object.
        if (!$table->check()) {
            return false;
        }

        // Are we creating a new topic
        $isnew = !$this->_exists;

        // If we aren't allowed to create new topic return
        if ($isnew && $updateOnly) {
            return true;
        }

        // Store the topic data in the database
        try {
            $result = $table->store();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // Fill up \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read object in case we created a new topic.
        if ($result && $isnew) {
            $this->load();
        }

        return $result;
    }

    /**
     * Method to delete the \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read object from the database.
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function delete(): bool
    {
        if (!$this->exists()) {
            return true;
        }

        // Create the table object
        $table = $this->getTable();

        try {
            $result = $table->delete(['topic_id' => $this->topic_id, 'user_id' => $this->user_id]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->_exists = false;

        return $result;
    }

    /**
     * @param   null|bool  $exists  exists
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function exists($exists = null): bool
    {
        $return = $this->_exists;

        if ($exists !== null) {
            $this->_exists = $exists;
        }

        return $return;
    }
}
