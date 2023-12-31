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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\Tables\TableKunenaKarma;
use RuntimeException;

/**
 * Kunena Forum Topic Karma Class
 *
 * @since 6.2
 */
class KunenaKarma extends CMSObject
{
    /**
     * @var     null
     * @since   Kunena 6.2
     */
    public $userid = null;

    /**
     * @var     null
     * @since   Kunena 6.2
     */
    public $target_userid = null;

    /**
     * @var     null
     * @since   Kunena 6.2
     */
    public $time = null;

    /**
     * @var     boolean
     * @since   Kunena 6.2
     */
    protected $_exists = false;

    /**
     * @var     DatabaseDriver|null
     * @since   Kunena 6.2
     */
    protected $_db = null;

    /**
     * @var     array
     * @since   Kunena 6.2
     */
    protected $users = [];

    /**
     * @param   int  $identifier  identifier
     *
     * @throws  Exception
     * @since   Kunena 6.2
     */
    public function __construct($identifier = 0)
    {
        // Always load the topic -- if rate does not exist: fill empty data
        $this->_db = Factory::getContainer()->get('DatabaseDriver');
        $this->load($identifier);

        parent::__construct($identifier);
    }

    /**
     * Method to load a \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object by id.
     *
     * @param   int  $id  The karma id to be loaded.
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.2
     */
    public function load(int $id): bool
    {
        // Create the table object
        $table = $this->getTable();

        // Load the KunenaTable object based on id
        $this->_exists = $table->load($id);

        // Assuming all is well at this point lets bind the data
        $this->setProperties($table->getProperties());

        return $this->_exists;
    }

    /**
     * Method to get the karma table object.
     *
     * @param   string  $type    Polls table name to be used.
     * @param   string  $prefix  Polls table prefix to be used.
     *
     * @return  boolean|Table
     *
     * @since   Kunena 6.0
     */
    public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'TableKunenaKarma')
    {
        static $tabletype = null;

        // Set a custom table type is defined
        if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
            $tabletype ['name']   = $type;
            $tabletype ['prefix'] = $prefix;
        }

        // Create the TableKunenaKarma table object
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $tableObject = new TableKunenaKarma($db);

        return $tableObject;
    }

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Message\Message object
     *
     * @access  public
     *
     * @param   int   $identifier  The message to load - Can be only an integer.
     * @param   bool  $reload      reload
     *
     * @return  KunenaKarma
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function getInstance($identifier = null, $reload = false)
    {
        return KunenaKarmaHelper::get($identifier, $reload);
    }

    /**
     * Perform insert the karma into table
     *
     * @param   KunenaUser  $user  user
     *
     * @return  JsonResponse
     *
     * @throws Exception
     * @since    Kunena 2.0
     *
     * @internal param int $userid
     */
    public function save()
    {
        $time  = Factory::getDate();
        $query = $this->_db->getQuery(true);
        $values = [
            $this->_db->quote($this->userid),
            $this->_db->quote($this->target_userid),
            $this->_db->quote($time->toUnix()),
        ];

        $query->insert($this->_db->quoteName('#__kunena_karma'))
            ->columns(
                [
                    $this->_db->quoteName('userid'),
                    $this->_db->quoteName('target_userid'),
                    $this->_db->quoteName('time'),
                ]
            )
            ->values(implode(', ', $values));
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }
    }

    /**
     * Get the most recent karma in time gived to an user
     *
     * @param   int  $start  start
     * @param   int  $limit  limit
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getLastTimeKarma()
    {
        $query = $this->_db->getQuery(true);
        $query->select('MAX(time)')
            ->from($this->_db->quoteName('#__kunena_karma'))
            ->where($this->_db->quoteName('target_userid') . ' = ' . $this->_db->quote($this->target_userid));
        $this->_db->setQuery($query);

        try {
            $lastkarma = $this->_db->loadResult();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        return $lastkarma;
    }
}
