<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.Poll
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Poll;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use StdClass;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicPoll
 *
 * @property int    $threadid
 * @property string $polltimetolive
 * @property int    $id
 * @property string $title
 * @since   Kunena 6.0
 */
class KunenaPoll extends CMSObject
{
	public $id;

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
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_total = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $options = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $newOptions = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $usercount = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $users = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $myvotes = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $mytime = [];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $mylastvoteId;

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

		parent::__construct($identifier);
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

		if (!$id || \is_array($id) || !$table->load($id))
		{
			return false;
		}

		// Load the KunenaTable object based on id
		$this->_exists = $table->load($id);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
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
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['prefix'], $tabletype ['name']);
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
	 * @return int|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function getTotal(): ?int
	{
		if (\is_null($this->_total))
		{
			$this->_total = 0;
			$options      = $this->getOptions();

			foreach ($options as $option)
			{
				$this->_total += $option->votes;
			}
		}

		return $this->_total;
	}

	/**
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getOptions()
	{
		if ($this->options === false)
		{
			$query = $this->_db->getQuery(true);
			$query->select('*')
				->from($this->_db->quoteName('#__kunena_polls_options'))
				->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
				->order($this->_db->quoteName('id'));
			$this->_db->setQuery($query);

			try
			{
				$this->options = (array) $this->_db->loadObjectList('id');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return $this->options;
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
		if (!\is_array($options))
		{
			return;
		}

		$filter     = InputFilter::getInstance();
		$newOptions = [];

		foreach ($options as $key => &$value)
		{
			$value = trim($filter->clean($value, 'html'));

			if (!empty($value))
			{
				$newOptions[$key] = $value;
			}
		}

		$this->newOptions = $newOptions;
	}

	/**
	 * @return  integer
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getUserCount()
	{
		if ($this->usercount === false)
		{
			$query = $this->_db->getQuery(true);
			$query->select('COUNT(*)')
				->from($this->_db->quoteName('#__kunena_polls_users'))
				->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id));
			$this->_db->setQuery($query);

			try
			{
				$this->usercount = (int) $this->_db->loadResult();
			}
			catch (ExecutionFailureException $e)
			{
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
		if ($this->users === false)
		{
			$query = $this->_db->getQuery(true);
			$query->select('*')
				->from($this->_db->quoteName('#__kunena_polls_users'))
				->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
				->order($this->_db->quoteName('lasttime') . ' DESC');
			$query->setLimit($limit, $start);
			$this->_db->setQuery($query);

			try
			{
				$this->myvotes = $this->users = (array) $this->_db->loadObjectList('userid');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return $this->users;
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  integer
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getMyTime($user = null)
	{
		$user = KunenaFactory::getUser($user);

		if (!isset($this->mytime[$user->userid]))
		{
			$query = $this->_db->getQuery(true);
			$query->select('MAX(lasttime)')
				->from($this->_db->quoteName('#__kunena_polls_users'))
				->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
				->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
			$this->_db->setQuery($query);

			try
			{
				$this->mytime[$user->userid] = $this->_db->loadResult();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return $this->mytime[$user->userid];
	}

	/**
	 * @param   int    $option  option
	 * @param   bool   $change  change
	 * @param   mixed  $user    user
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function vote(int $option, $change = false, $user = null): bool
	{
		if (!$this->exists())
		{
			$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_DOES_NOT_EXIST'));

			return false;
		}

		$options = $this->getOptions();

		if (!isset($options[$option]))
		{
			$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_DOES_NOT_EXIST'));

			return false;
		}

		$user = KunenaFactory::getUser($user);

		if (!$user->exists())
		{
			$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_NOT_EXIST'));

			return false;
		}

		$lastVoteId = $this->getLastVoteId($user);
		$myvotes    = $this->getMyVotes($user);

		if (!$myvotes)
		{
			// First vote
			$votes         = new StdClass;
			$votes->new    = true;
			$votes->pollid = $this->id;
			$votes->votes  = 1;
		}
		elseif ($change && isset($lastVoteId))
		{
			$votes           = new StdClass;
			$votes->new      = false;
			$votes->lasttime = null;
			$votes->lastvote = null;
			$votes->votes    = 1;

			// Change vote: decrease votes in the last option
			if (!$this->changeOptionVotes($lastVoteId, -1))
			{
				// Saving option failed, add a vote to the user
				$votes->votes++;
			}
		}
		else
		{
			$votes      = new StdClass;
			$votes->new = false;

			// Change vote: decrease votes in the last option
			if (!$this->changeOptionVotes($lastVoteId, -1))
			{
				// Add a vote to the user
				$votes->votes++;
			}
		}

		$votes->lasttime = KunenaUserHelper::getMyself()->getTime();
		$votes->lastvote = $option;
		$votes->userid   = (int) $user->userid;

		// Increase vote count from current option
		$this->changeOptionVotes($votes->lastvote, 1);

		if ($votes->new)
		{
			// No votes
			$query = $this->_db->getQuery(true);

			// Insert columns.
			$columns = ['pollid', 'userid', 'votes', 'lastvote', 'lasttime'];

			// Insert values.
			$values = [$this->_db->quote($this->id), $this->_db->quote($votes->userid), $this->_db->quote($votes->votes), $this->_db->quote($votes->lastvote), $this->_db->quote($votes->lasttime)];

			// Prepare the insert query.
			$query
				->insert($this->_db->quoteName('#__kunena_polls_users'))
				->columns($this->_db->quoteName($columns))
				->values(implode(',', $values));
			$this->_db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_INSERT_FAIL'));

				return false;
			}
		}
		else
		{
			// Already voted
			$query = $this->_db->getQuery(true);

			// Insert columns.
			$columns = ['votes', 'lastvote', 'lasttime'];

			// Insert values.
			$values = [$this->_db->quote($votes->votes), $this->_db->quote($votes->lastvote), $this->_db->quote($votes->lasttime)];

			// Prepare the insert query.
			$query
				->insert($this->_db->quoteName('#__kunena_polls_users'))
				->columns($this->_db->quoteName($columns))
				->values(implode(',', $values))
				->where('pollid=' . $this->_db->quote($this->id) . ' AND userid=' . $this->_db->quote($votes->userid));
			$this->_db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_UPDATE_FAIL'));

				return false;
			}
		}

		return true;
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

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
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
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('lastvote'))
			->from($this->_db->quoteName('#__kunena_polls_users'))
			->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
			->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
		$this->_db->setQuery($query);

		try
		{
			$this->mylastvoteId = $this->_db->loadResult();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $this->mylastvoteId;
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

		if (!isset($this->myvotes[$user->userid]))
		{
			$query = $this->_db->getQuery(true);
			$query->select('SUM(' . $this->_db->quoteName('votes') . ')')
				->from($this->_db->quoteName('#__kunena_polls_users'))
				->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->id))
				->andWhere($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid));
			$this->_db->setQuery($query);

			try
			{
				$this->myvotes[$user->userid] = $this->_db->loadResult();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return $this->myvotes[$user->userid];
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
		if (!isset($this->options[$option]->votes))
		{
			// Ignore non-existent options
			return true;
		}

		$this->options[$option]->votes += $delta;

		// Change votes in the option
		$delta = \intval($delta);
		$query = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName('#__kunena_polls_options'))
			->set($this->_db->quoteName('votes') . ' = votes+' . $this->_db->quote($delta))
			->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($option));
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			$this->setError(Text::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_SAVE_FAIL'));

			return false;
		}

		return true;
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
		if (!empty($allow))
		{
			$data = array_intersect_key($data, array_flip($allow));
		}

		$this->setProperties($data);
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
		if (!$this->exists())
		{
			return true;
		}

		// Create the table object
		$table = $this->getTable();

		$success = $table->delete($this->id);

		if (!$success)
		{
			$this->setError($table->getError());
		}

		$this->_exists = false;

		// Delete options
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__kunena_polls_options'))
			->where($db->quoteName('pollid') . ' = ' . $db->quote($this->id));
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// Delete votes
		$query = $db->getQuery();
		$query->delete($db->quoteName('#__kunena_polls_users'))
			->where($db->quoteName('pollid') . ' = ' . $db->quote($this->id));
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// Remove poll from the topic
		$topic = KunenaTopicHelper::get($this->threadid);

		if ($success && $topic->exists() && $topic->poll_id)
		{
			$topic->poll_id = 0;
			$success        = $topic->save();

			if (!$success)
			{
				$this->setError($topic->getError());
			}
		}

		return $success;
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
		return Factory::getDate($this->polltimetolive)->toUnix();
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object to the database.
	 *
	 * @param   bool  $updateOnly  Save the object only if not a new poll.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function save($updateOnly = false): bool
	{
		// Are we creating a new poll
		$isnew = !$this->_exists;

		if ($isnew && empty($this->newOptions))
		{
			$this->setError(Text::_('COM_KUNENA_LIB_POLL_SAVE_ERROR_NEW_AND_NO_OPTIONS'));

			return false;
		}

		// Create the topics table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Store the topic data in the database
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		// Set the id for the \Kunena\Forum\Libraries\Forum\Topic\Topic object in case we created a new topic.
		if ($isnew)
		{
			$this->load($table->id);
			$this->options = [];
		}

		if ($this->newOptions === false)
		{
			// Options have not changed: nothing left to do
			return true;
		}

		// Load old options for comparison
		$options = $this->getOptions();

		// Find deleted options
		foreach ($options as $key => $item)
		{
			if (empty($this->newOptions[$key]))
			{
				$query = $this->_db->getQuery(true);
				$query->delete($this->_db->quoteName('#__kunena_polls_options'))
					->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($key));
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);
				}

				// TODO: Votes in #__kunena_polls_users will be off and there's no way we can fix that
				// Maybe we should allow option to reset votes when option gets removed
				// Or we could prevent users from editing poll..
			}
		}

		// Go though new and changed options
		ksort($this->newOptions);

		foreach ($this->newOptions as $key => $value)
		{
			if (!$value)
			{
				// Ignore empty options
				continue;
			}

			if (!isset($options[$key]))
			{
				// Option doesn't exist: create it
				$query = $this->_db->getQuery(true);
				$query->insert($this->_db->quoteName('#__kunena_polls_options') . '(text, pollid, votes)')
					->values($this->_db->quote($value) . ', ' . $this->_db->quote($this->id) . ' , 0');
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);
				}
			}
			elseif ($options[$key]->text != $value)
			{
				// Option exists and has changed: update text
				$query = $this->_db->getQuery(true);
				$query->update($this->_db->quoteName('#__kunena_polls_options'))
					->set($this->_db->quoteName('text') . ' = ' . $this->_db->quote($value))
					->where($this->_db->quoteName('id') . ' = ' . $this->_db->quote($key));
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);
				}
			}
		}

		// Force reload on options
		$this->options = false;

		return true;
	}
}
