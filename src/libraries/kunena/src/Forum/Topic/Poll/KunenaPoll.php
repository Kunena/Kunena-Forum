<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.Poll
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Poll;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Tables\TableKunenaPolls;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicPoll
 *
 * @property int    $threadid
 * @property string $polltimetolive
 * @property int    $id
 * @property string $title
 * @since   Kunena 6.0
 */
class KunenaPoll
{
    public $id;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected mixed $_exists = false;

    /**
     * @var     DatabaseDriver|null
     * @since   Kunena 6.0
     */
    protected mixed $_db = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    protected mixed $_total = null;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected mixed $options = false;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $newOptions = false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected mixed $usercount = false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected mixed $users = false;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $myvotes = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $mytime = [];

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    protected mixed $mylastvoteId = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $threadid = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $typeAlias = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $title = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $polltimetolive = null;

    /**
     * @param   int  $identifier  identifier
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct($identifier = 0)
    {
        // Always load the topic -- if poll does not exist: fill empty data
        $this->_db = Factory::getContainer()->get('DatabaseDriver');
        $this->load($identifier);
    }

    /**
     * @param   array  $data   data
     * @param   array  $allow  allow
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function bind(array $data, array $allow = []): void
    {
        if (!empty($allow)) {
            $data = array_intersect_key($data, array_flip($allow));
        }

        foreach ((array) $data as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * @param   int  $option  option
     * @param   int  $delta   delta
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function changeOptionVotes(int $option, int $delta): bool
    {
        if (!isset($this->options[$option]->votes)) {
            // Ignore non-existent options
            return true;
        }

        $this->options[$option]->votes += $delta;

        // Change votes in the option
        $delta = \intval($delta);
        $query = $this->_db->createQuery();
        $query->update($this->_db->quoteName('#__kunena_polls_options'))
            ->set($this->_db->quoteName('votes') . ' = votes+' . $this->_db->quote($delta))
            ->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($option));
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_SAVE_FAIL'));
        }

        return true;
    }

    /**
     * Method to delete the \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object from the database.
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

        $success = true;

        // Create the table object
        $table = $this->getTable();

        try {
            $table->delete($this->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->_exists = false;

        // Delete options
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_polls_options'))
            ->where($db->quoteName('pollid') . ' = ' . $db->quote($this->id));
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
            $success = false;
        }

        // Delete votes
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_polls_users'))
            ->where($db->quoteName('pollid') . ' = ' . $db->quote($this->id));
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
            $success = false;
        }

        // Remove poll from the topic
        $topic = KunenaTopicHelper::get($this->threadid);

        if ($success && $topic->exists() && $topic->poll_id) {
            $topic->poll_id = 0;

            try {
                $topic->save();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return $success;
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

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Topic\Poll object.
     *
     * @param   mixed  $identifier  Poll to load - Can be only an integer.
     * @param   bool   $reset       reset
     *
     * @return  KunenaPoll
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function getInstance($identifier = null, $reset = false): KunenaPoll
    {
        return KunenaPollHelper::get($identifier, $reset);
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  integer
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getLastVoteId($user = null)
    {
        $user  = KunenaFactory::getUser($user);
        $query = $this->_db->createQuery();
        $query->select($this->_db->quoteName('lastvote'))
            ->from($this->_db->quoteName('#__kunena_polls_users'))
            ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
            ->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
        $this->_db->setQuery($query);

        try {
            $this->mylastvoteId = $this->_db->loadResult();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        return $this->mylastvoteId;
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  string|null  Date and time (UTC) of the last vote of the user.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getMyTime($user = null)
    {
        $user = KunenaFactory::getUser($user);

        // A user who never voted has the value null: it is loaded too, so check the key and not the value.
        if (!\array_key_exists($user->userid, $this->mytime)) {
            $query = $this->_db->createQuery();
            $query->select('MAX(lasttime)')
                ->from($this->_db->quoteName('#__kunena_polls_users'))
                ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
                ->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
            $this->_db->setQuery($query);

            try {
                $this->mytime[$user->userid] = $this->_db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $this->mytime[$user->userid];
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  integer
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getMyVotes($user = null)
    {
        $user = KunenaFactory::getUser($user);

        // A user without votes has the value null: it is loaded too, so check the key and not the value.
        if (!\array_key_exists($user->userid, $this->myvotes)) {
            $query = $this->_db->createQuery();
            $query->select('SUM(' . $this->_db->quoteName('votes') . ')')
                ->from($this->_db->quoteName('#__kunena_polls_users'))
                ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
                ->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
            $this->_db->setQuery($query);

            try {
                $this->myvotes[$user->userid] = $this->_db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $this->myvotes[$user->userid];
    }

    /**
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getOptions()
    {
        if ($this->options === false) {
            $query = $this->_db->createQuery();
            $query->select('*')
                ->from($this->_db->quoteName('#__kunena_polls_options'))
                ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
                ->order($this->_db->quoteName('id'));
            $this->_db->setQuery($query);

            try {
                $this->options = (array) $this->_db->loadObjectList('id');
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $this->options;
    }

    /**
     * Method to get the polls table object.
     *
     * @param   string  $type    Polls table name to be used.
     * @param   string  $prefix  Polls table prefix to be used.
     *
     * @return  Table
     *
     * @since   Kunena 6.0
     */
    public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'TableKunenaPolls')
    {
        static $tabletype = null;

        // Set a custom table type is defined
        if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
            $tabletype ['name']   = $type;
            $tabletype ['prefix'] = $prefix;
        }

        // Create the TableKunenaPolls table object
        $db          = Factory::getContainer()->get(DatabaseInterface::class);
        $tableObject = new TableKunenaPolls($db);

        return $tableObject;
    }

    /**
     * Method to get the poll time to live.
     *
     * @return integer
     *
     * @since 3.0
     */
    public function getTimeToLive(): int
    {
        if (!empty($this->polltimetolive)) {
            return Factory::getDate($this->polltimetolive)->toUnix();
        }

        return 0;

    }

    /**
     * @return int|null
     *
     * @since   Kunena 6.0
     * @throws Exception
     */
    public function getTotal(): ?int
    {
        if (\is_null($this->_total)) {
            $this->_total = 0;
            $options      = $this->getOptions();

            foreach ($options as $option) {
                $this->_total += $option->votes;
            }
        }

        return $this->_total;
    }

    /**
     * @return  integer
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getUserCount()
    {
        if ($this->usercount === false) {
            $query = $this->_db->createQuery();
            $query->select('COUNT(*)')
                ->from($this->_db->quoteName('#__kunena_polls_users'))
                ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id));
            $this->_db->setQuery($query);

            try {
                $this->usercount = (int) $this->_db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $this->usercount;
    }

    /**
     * @param   int  $start  start
     * @param   int  $limit  limit
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getUsers($start = 0, $limit = 0)
    {
        if ($this->users === false) {
            $query = $this->_db->createQuery();
            $query->select('*')
                ->from($this->_db->quoteName('#__kunena_polls_users'))
                ->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
                ->order($this->_db->quoteName('lasttime') . ' DESC');
            $query->setLimit($limit, $start);
            $this->_db->setQuery($query);

            try {
                $this->myvotes = $this->users = (array) $this->_db->loadObjectList('userid');
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return $this->users;
    }

    /**
     * Method to load a \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object by id.
     *
     * @param   int  $id  The poll id to be loaded.
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function load($id)
    {
        // Create the table object
        $table = $this->getTable();

        // Load the KunenaTable object based on id
        if (!$id || \is_array($id) || !($this->_exists = $table->load($id))) {
            return false;
        }

        // Assuming all is well at this point lets bind the data
        $this->bind($table->getProperties());

        return $this->_exists;
    }

    /**
     * Method to save the \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object to the database.
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function save(): bool
    {
        // Are we creating a new poll
        $isnew = !$this->_exists;

        if ($isnew && empty($this->newOptions)) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_SAVE_ERROR_NEW_AND_NO_OPTIONS'));
        }

        // Create the topics table object
        $table = $this->getTable();

        // The columns have 150 (title) and 100 (option) characters: a longer text was an error of a strict database and the poll
        // was not saved.
        $this->title = mb_substr(trim((string) $this->title), 0, 150);

        $properties = [
            'title'          => $this->title,
            'threadid'       => $this->threadid,
            'polltimetolive' => $this->polltimetolive,
        ];

        // The table object is new: without the number the update of an existing poll changed no row (a new title or end date
        // of the poll was lost while the options were saved).
        if (!$isnew) {
            $properties['id'] = (int) $this->id;
        }

        $table->bind($properties);
        $table->exists($this->_exists);

        // Store the topic data in the database
        try {
            $table->store();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // Set the id for the \Kunena\Forum\Libraries\Forum\Topic\Topic object in case we created a new topic.
        if ($isnew) {
            $this->load($table->id);
            $this->options = [];
        }

        if ($this->newOptions === false) {
            // Options have not changed: nothing left to do
            return true;
        }

        // Load old options for comparison
        $options = $this->getOptions();

        // Find deleted options
        foreach ($options as $key => $item) {
            if (empty($this->newOptions[$key])) {
                $query = $this->_db->createQuery();
                $query->delete($this->_db->quoteName('#__kunena_polls_options'))
                    ->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($key));
                $this->_db->setQuery($query);

                try {
                    $this->_db->execute();
                } catch (ExecutionFailureException $e) {
                    KunenaError::displayDatabaseError($e);
                }

                // TODO: Votes in #__kunena_polls_users will be off and there's no way we can fix that
                // Maybe we should allow option to reset votes when option gets removed
                // Or we could prevent users from editing poll..
            }
        }

        // Go though new and changed options
        ksort($this->newOptions);

        foreach ($this->newOptions as $key => $value) {
            if (!$value) {
                // Ignore empty options
                continue;
            }

            if (!isset($options[$key])) {
                // Option doesn't exist: create it
                $query = $this->_db->createQuery();
                $query->insert($this->_db->quoteName('#__kunena_polls_options') . '(text, pollid, votes)')
                    ->values($this->_db->quote($value) . ', ' . $this->_db->quote($this->id) . ' , 0');
                $this->_db->setQuery($query);

                try {
                    $this->_db->execute();
                } catch (ExecutionFailureException $e) {
                    KunenaError::displayDatabaseError($e);
                }
            } elseif ($options[$key]->text != $value) {
                // Option exists and has changed: update text
                $query = $this->_db->createQuery();
                $query->update($this->_db->quoteName('#__kunena_polls_options'))
                    ->set($this->_db->quoteName('text') . ' = ' . $this->_db->quote($value))
                    ->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($key));
                $this->_db->setQuery($query);

                try {
                    $this->_db->execute();
                } catch (ExecutionFailureException $e) {
                    KunenaError::displayDatabaseError($e);
                }
            }
        }

        // Force reload on options
        $this->options = false;

        return true;
    }

    /**
     * Save the vote on the poll option choose by the user, increase the number of votes of the user
     *
     * @param   int            $option   option
     * @param   KunenaTopic    $topic    topic
     * @param   bool           $change   change
     * @param   mixed          $user     user
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 7.0.6
     */
    public function saveVote(int $option, KunenaTopic $topic, $change = false, $user = null): bool
    {
        if (!$this->exists()) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_DOES_NOT_EXIST'));
        }

        $options = $this->getOptions();

        if (!isset($options[$option])) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_DOES_NOT_EXIST'));
        }

        $user = KunenaFactory::getUser($user);

        if (!$user->exists()) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_NOT_EXIST'));
        }

        if (!$topic->isAuthorised('poll.vote', null, true)) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_NOT_AUTHORIZED'));
        }

        $lastVoteId = $this->getLastVoteId($user);
        $myvotes    = (int) $this->getMyVotes($user);
        $userid     = (int) $user->userid;

        // Store the vote time in UTC like the other database dates (the user's own time zone would be stored otherwise).
        $now = Factory::getDate()->toSql();

        // The stored number of votes of the user changes below.
        unset($this->myvotes[$userid], $this->mytime[$userid]);

        // The row of the voter is written first, and the votes of the options change only when that row changed: two requests
        // at the same time found the same votes of the user, and each one counted its vote in the option.
        if (!$myvotes) {
            // First vote: the key of the table (pollid, userid) keeps one row of the user.
            $query = $this->_db->createQuery()
                ->insert($this->_db->quoteName('#__kunena_polls_users'))
                ->columns($this->_db->quoteName(['pollid', 'userid', 'votes', 'lastvote', 'lasttime']))
                ->values(implode(',', [(int) $this->id, $userid, 1, $option, $this->_db->quote($now)]));
            $this->_db->setQuery($query);

            try {
                $this->_db->execute();
            } catch (ExecutionFailureException $e) {
                // The user voted in another request meanwhile.
                if (stripos($e->getMessage(), 'Duplicate') === false) {
                    KunenaError::displayDatabaseError($e);
                }

                throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_INSERT_FAIL'));
            }

            $this->changeOptionVotes($option, 1);

            return true;
        }

        if ($change && isset($lastVoteId)) {
            // Change the vote: only when the last vote is still the one which was read.
            $query = $this->_db->createQuery()
                ->update($this->_db->quoteName('#__kunena_polls_users'))
                ->set($this->_db->quoteName('votes') . ' = 1')
                ->set($this->_db->quoteName('lastvote') . ' = ' . $option)
                ->set($this->_db->quoteName('lasttime') . ' = ' . $this->_db->quote($now))
                ->where($this->_db->quoteName('pollid') . ' = ' . (int) $this->id)
                ->where($this->_db->quoteName('userid') . ' = ' . $userid)
                ->where($this->_db->quoteName('lastvote') . ' = ' . (int) $lastVoteId);
        } else {
            // Additional vote without changing the previous one: only when the number of votes is still the one which was read.
            $query = $this->_db->createQuery()
                ->update($this->_db->quoteName('#__kunena_polls_users'))
                ->set($this->_db->quoteName('votes') . ' = ' . ($myvotes + 1))
                ->set($this->_db->quoteName('lastvote') . ' = ' . $option)
                ->set($this->_db->quoteName('lasttime') . ' = ' . $this->_db->quote($now))
                ->where($this->_db->quoteName('pollid') . ' = ' . (int) $this->id)
                ->where($this->_db->quoteName('userid') . ' = ' . $userid)
                ->where($this->_db->quoteName('votes') . ' = ' . $myvotes);
        }

        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_UPDATE_FAIL'));
        }

        // Another request changed the votes of the user meanwhile.
        if ($this->_db->getAffectedRows() < 1) {
            throw new Exception(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_UPDATE_FAIL'));
        }

        if ($change && isset($lastVoteId)) {
            $this->changeOptionVotes((int) $lastVoteId, -1);
        }

        $this->changeOptionVotes($option, 1);

        return true;
    }

    /**
     * Filters and sets poll options.
     *
     * @param   array  $options  array(id=>name, id=>name)
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function setOptions(array $options): void
    {
        if (!\is_array($options)) {
            return;
        }

        $filter     = InputFilter::getInstance();
        $newOptions = [];

        foreach ($options as $key => &$value) {
            $value = mb_substr(trim($filter->clean($value, 'html')), 0, 100);

            if (!empty($value)) {
                $newOptions[$key] = $value;
            }
        }

        // No more options than the configuration allows (the editor offers no more fields); a poll which has more already keeps
        // its number (the limit may have been lowered after it was made).
        $max = (int) KunenaFactory::getConfig()->pollNbOptions;

        if ($max > 0) {
            $newOptions = \array_slice($newOptions, 0, max($max, \count($this->getOptions())), true);
        }

        $this->newOptions = $newOptions;
    }
}
