<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tables
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use UnexpectedValueException;

/**
 * Kunena Users Table
 * Provides access to the #__kunena_users table
 *
 * @since   Kunena 4.0
 */
class KunenaUsers extends KunenaTable
{
    /**
     * User ID
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public $userid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $name = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $username = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $email = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $blocked = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $registerDate = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $lastvisitDate = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $view = null;

    /**
     * Signature
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $signature = null;

    /**
     * Is moderator?
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $moderator = null;

    /**
     * Banned until timestamp
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $banned = null;

    /**
     * Ordering of posts
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $ordering = null;

    /**
     * User post count
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $posts = null;

    /**
     * Avatar image file
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $avatar = null;

    /**
     * User karma
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $karma = null;

    /**
     * Kunena Group ID
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $group_id = null;

    /**
     * Kunena Profile hits
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $uhits = null;

    /**
     * Personal text
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $personalText = null;

    /**
     * Gender
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $gender = null;

    /**
     * Birthdate
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $birthdate = null;

    /**
     * User Location
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $location = null;

    /**
     * Name of web site
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $websitename = null;

    /**
     * URL to web site
     *
     * @var     string
     * @since   Kunena 6.0
     **/
    public mixed $websiteurl = null;

    /**
     * User rank
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $rank = null;

    /**
     * Hide Email address
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $hideEmail = null;

    /**
     * Show online
     *
     * @var     integer
     * @since   Kunena 6.0
     **/
    public mixed $showOnline = null;

    /**
     * @var     integer|null
     * @since   Kunena 7.1
     */
    public mixed $displayAgeBirthdate = null;

    /**
     * Thankyou count
     *
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $thankyou = null;

    /**
     * canSubscribe
     *
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $canSubscribe = null;

    /**
     * userListtime
     *
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $userListtime = null;

    /**
     * Status
     *
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $status = null;

    /**
     * Status Text
     *
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $status_text = null;

    /**
     * social
     *
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $socials = null;

    /**
    * social
    *
    * @var     string
    * @since   Kunena 7.0
    */
    public mixed $language = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $timestamp = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $ip = null;

    /**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  4.0.0
     */
    protected $_supportNullValue = true;

    /**
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_users', 'userid', $db);
    }

    /**
     * @param   mixed  $data    data
     * @param   array  $ignore  ignore
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function bind($data, $ignore = [])
    {
        parent::bind($data, $ignore);
        $fields = ['name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate'];

        foreach ($fields as $field) {
            if (isset($data[$field]) && !\in_array($field, $ignore)) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function check(): bool
    {
        $user = KunenaUserHelper::getJoomlaUser((int) $this->userid);

        if (!$this->userid || !$user) {
            // FIXME: find a way to throw exception because it prevent guest to post
            $this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_USERS_ERROR_USER_INVALID', (int) $this->userid));
        }

        if ($this->status < 0 || $this->status > 3) {
            throw new UnexpectedValueException(Text::_('COM_KUNENA_UNKNOWN_STATUS'));
        }

        if (isset($this->status)) {
            if (\strlen($this->status) < 0 || \strlen($this->status) > 255) {
                throw new UnexpectedValueException(Text::_('COM_KUNENA_STATUS_TOOLONG'));
            }
        }

        return true;
    }

    /**
     * @param   null  $userid  userid
     * @param   bool  $reset   reset
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function load($userid = null, $reset = true): bool
    {
        $this->_exists = false;
        $k             = $this->_tbl_key;

        // Get the id to load.
        if ($userid !== null) {
            $this->$k = $userid;
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
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select('u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, u.params AS joomlaParams, ku.*')
            ->from($db->quoteName('#__users', 'u'))
            ->leftJoin($db->quoteName($this->_tbl, 'ku') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'))
            ->where($db->quoteName('u.id') . ' = ' . $db->quote($this->$k));
        $db->setQuery($query);

        try {
            $data = $db->loadAssoc();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        // User does not exist (may exist in #__kunena_users, though)
        if (!$data) {
            $this->$k = 0;

            return false;
        }

        // The Joomla parameters are kept apart, they are no part of the Kunena user (and are not asked for again).
        KunenaUserHelper::cacheJoomlaParams((int) $data['userid'], $data['joomlaParams'] ?? null);
        unset($data['joomlaParams']);

        if ($data['posts'] !== null) {
            $this->_exists = true;
        }

        // Bind the data to the table.
        $this->bind($data);

        return $this->_exists;
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function reset()
    {
        parent::reset();
        $fields = ['name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate'];

        foreach ($fields as $field) {
            $this->$field = null;
        }
    }
}
