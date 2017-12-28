<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic.Poll
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaForumTopicPoll
 *
 * @property int $id
 * @property string $title
 * @property int $threadid
 * @property string $polltimetolive
 */
class KunenaForumTopicPoll extends JObject
{
	protected $_exists = false;
	protected $_db = null;
	protected $_total = null;
	protected $options = false;
	protected $newOptions = false;
	protected $usercount = false;
	protected $users = false;
	protected $myvotes = array();
	protected $mytime = array();

	/**
	 * @param   int $identifier
	 */
	public function __construct($identifier = 0)
	{
		// Always load the topic -- if poll does not exist: fill empty data
		$this->_db = JFactory::getDBO();
		$this->load($identifier);
	}

	/**
	 * Returns KunenaForumTopicPoll object.
	 *
	 * @param   mixed $identifier	Poll to load - Can be only an integer.
	 * @param   bool  $reset
	 *
	 * @return KunenaForumTopicPoll
	 */
	static public function getInstance($identifier = null, $reset = false)
	{
		return KunenaForumTopicPollHelper::get($identifier, $reset);
	}

	/**
	 * @param   null|bool $exists
	 *
	 * @return boolean
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}

	/**
	 * Filters and sets poll options.
	 *
	 * @param   array $options	array(id=>name, id=>name)
	 */
	public function setOptions($options)
	{
		if (!is_array($options))
		{
			return;
		}

		$filter = JFilterInput::getInstance();
		$newOptions = array();

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
	 * @return array
	 */
	public function getOptions()
	{
		if ($this->options === false)
		{
			$query = "SELECT *
				FROM #__kunena_polls_options
				WHERE pollid={$this->_db->Quote($this->id)}
				ORDER BY id";
			$this->_db->setQuery($query);

			try
			{
				$this->options = (array) $this->_db->loadObjectList('id');
			}
			catch (JDatabaseExceptionExecuting $e)
	 		{
	 			KunenaError::displayDatabaseError($e);
	 		}
		}

		return $this->options;
	}

	/**
	 * @return integer
	 */
	public function getTotal()
	{
		if (is_null($this->_total))
		{
			$this->_total = 0;
			$options = $this->getOptions();

			foreach ($options as $option)
			{
				$this->_total += $option->votes;
			}
		}

		return $this->_total;
	}

	/**
	 * @return integer
	 */
	public function getUserCount()
	{
		if ($this->usercount === false)
		{
			$query = "SELECT COUNT(*)
				FROM #__kunena_polls_users
				WHERE pollid={$this->_db->Quote($this->id)}";
			$this->_db->setQuery($query);

			try
			{
				$this->usercount = (int) $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
	 		{
	 			KunenaError::displayDatabaseError($e);
	 		}
		}

		return $this->usercount;
	}

	/**
	 * @param   int $start
	 * @param   int $limit
	 *
	 * @return array
	 */
	public function getUsers($start = 0, $limit = 0)
	{
		if ($this->users === false)
		{
			$query = "SELECT *
				FROM #__kunena_polls_users
				WHERE pollid={$this->_db->Quote($this->id)} ORDER BY lasttime DESC";
			$this->_db->setQuery($query, $start, $limit);

			try
			{
				$this->myvotes = $this->users = (array) $this->_db->loadObjectList('userid');
			}
			catch (JDatabaseExceptionExecuting $e)
	 		{
	 			KunenaError::displayDatabaseError($e);
	 		}
		}

		return $this->users;
	}

	/**
	 * @param   mixed $user
	 *
	 * @return integer
	 */
	public function getMyVotes($user = null)
	{
		$user = KunenaFactory::getUser($user);

		if (!isset($this->myvotes[$user->userid]))
		{
			$query = "SELECT SUM(votes)
				FROM #__kunena_polls_users
				WHERE pollid={$this->_db->Quote($this->id)} AND userid={$this->_db->Quote($user->userid)}";
			$this->_db->setQuery($query);

			try
			{
				$this->myvotes[$user->userid] = $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
	 		{
	 			KunenaError::displayDatabaseError($e);
	 		}
		}

		return $this->myvotes[$user->userid];
	}

	/**
	 * @param   mixed $user
	 *
	 * @return integer
	 */
	public function getLastVoteId($user = null)
	{
		$user = KunenaFactory::getUser($user);
		$query = "SELECT lastvote
				FROM #__kunena_polls_users
				WHERE pollid={$this->_db->Quote($this->id)} AND userid={$this->_db->Quote($user->userid)}";
		$this->_db->setQuery($query);

		try
		{
			$this->mylastvoteId = $this->_db->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
	 	{
	 		KunenaError::displayDatabaseError($e);
	 	}

		return $this->mylastvoteId;
	}

	/**
	 * @param   mixed $user
	 *
	 * @return integer
	 */
	public function getMyTime($user = null)
	{
		$user = KunenaFactory::getUser($user);

		if (!isset($this->mytime[$user->userid]))
		{
			$query = "SELECT MAX(lasttime)
				FROM #__kunena_polls_users
				WHERE pollid={$this->_db->Quote($this->id)} AND userid={$this->_db->Quote($user->userid)}";
			$this->_db->setQuery($query);

			try
			{
				$this->mytime[$user->userid] = $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
		 	{
		 		KunenaError::displayDatabaseError($e);
		 	}
		}

		return (int) $this->mytime[$user->userid];
	}

	/**
	 * @param   int   $option
	 * @param   bool  $change
	 * @param   mixed $user
	 *
	 * @return boolean
	 */
	public function vote($option, $change = false, $user = null)
	{
		if (!$this->exists())
		{
			$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_DOES_NOT_EXIST'));
			return false;
		}

		$options = $this->getOptions();

		if (!isset($options[$option]))
		{
			$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_DOES_NOT_EXIST'));
			return false;
		}

		$user = KunenaFactory::getUser($user);

		if (!$user->exists())
		{
			$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_NOT_EXIST'));
			return false;
		}

		$lastVoteId = $this->getLastVoteId($user->userid);
		$myvotes = $this->getMyVotes($user);

		if (!$myvotes)
		{
			// First vote
			$votes = new StdClass();
			$votes->new = true;
			$votes->pollid = $this->id;
			$votes->votes = 1;
		}
		elseif ($change && isset($lastVoteId))
		{
			$votes = new StdClass();
			$votes->new = false;
			$votes->lasttime = null;
			$votes->lastvote = null;
			$votes->votes = 1;

			// Change vote: decrease votes in the last option
			if (!$this->changeOptionVotes($lastVoteId, -1))
			{
				// Saving option failed, add a vote to the user
				$votes->votes++;
			}
		}
		else
		{
			$votes = new StdClass();
			$votes->new = false;

			// Add a vote to the user
			$votes->votes = ++$myvotes;
		}

		$votes->lasttime = JFactory::getDate()->toSql();
		$votes->lastvote = $option;
		$votes->userid = (int) $user->userid;

		// Increase vote count from current option
		$this->changeOptionVotes($votes->lastvote, 1);

		if ($votes->new)
		{
			// No votes
			$query = "INSERT INTO #__kunena_polls_users (pollid,userid,votes,lastvote,lasttime)
				VALUES({$this->_db->Quote($this->id)},{$this->_db->Quote($votes->userid)},{$this->_db->Quote($votes->votes)},{$this->_db->Quote($votes->lastvote)},{$this->_db->Quote($votes->lasttime)});";
			$this->_db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_INSERT_FAIL'));
				return false;
			}
		}
		else
		{
			// Already voted
			$query = "UPDATE #__kunena_polls_users
				SET votes={$this->_db->Quote($votes->votes)},lastvote={$this->_db->Quote($votes->lastvote)},lasttime={$this->_db->Quote($votes->lasttime)}
				WHERE pollid={$this->_db->Quote($this->id)} AND userid={$this->_db->Quote($votes->userid)};";
			$this->_db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_USER_UPDATE_FAIL'));
				return false;
			}
		}

		return true;
	}

	/**
	 * @param   int  $option
	 * @param   int  $delta
	 *
	 * @return boolean
	 */
	protected function changeOptionVotes($option, $delta)
	{
		if (!isset($this->options[$option]->votes))
		{
			// Ignore non-existent options
			return true;
		}

		$this->options[$option]->votes += $delta;

		// Change votes in the option
		$delta = intval($delta);
		$query = "UPDATE #__kunena_polls_options SET votes=votes+{$delta} WHERE id={$this->_db->Quote($option)}";

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			$this->setError(JText::_('COM_KUNENA_LIB_POLL_VOTE_ERROR_OPTION_SAVE_FAIL'));
			return false;
		}

		return true;
	}

	/**
	 * Method to get the polls table object.
	 *
	 * @param   string $type		Polls table name to be used.
	 * @param   string $prefix	Polls table prefix to be used.
	 *
	 * @return KunenaTable|TableKunenaPolls
	 */
	public function getTable($type = 'KunenaPolls', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance($tabletype ['name'], $tabletype ['prefix']);
	}

	/**
	 * @param   array $data
	 * @param   array $allow
	 */
	public function bind(array $data, array $allow = array())
	{
		if (!empty($allow)) { $data = array_intersect_key($data, array_flip($allow)); }

		$this->setProperties($data);
	}

	/**
	 * Method to load a KunenaForumTopicPoll object by id.
	 *
	 * @param   int $id	The poll id to be loaded.
	 *
	 * @return boolean
	 */
	public function load($id)
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
	 * Method to delete the KunenaForumTopicPoll object from the database.
	 *
	 * @return bool	True on success.
	 */
	public function delete()
	{
		if (!$this->exists())
		{
			return true;
		}

		// Create the table object
		$table = $this->getTable();

		$success = $table->delete($this->id);

		if (! $success)
		{
			$this->setError($table->getError());
		}

		$this->_exists = false;

		// Delete options
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__kunena_polls_options WHERE pollid={$db->Quote($this->id)}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// Delete votes
		$query = "DELETE FROM #__kunena_polls_users WHERE pollid={$db->Quote($this->id)}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// Remove poll from the topic
		$topic = KunenaForumTopicHelper::get($this->threadid);

		if ($success && $topic->exists() && $topic->poll_id)
		{
			$topic->poll_id = 0;
			$success = $topic->save();

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
	public function getTimeToLive()
	{
		return JFactory::getDate($this->polltimetolive)->toUnix();
	}

	/**
	 * Method to save the KunenaForumTopicPoll object to the database.
	 *
	 * @param   bool $updateOnly	Save the object only if not a new poll.
	 *
	 * @return bool	True on success.
	 */
	public function save($updateOnly = false)
	{
		//are we creating a new poll
		$isnew = ! $this->_exists;

		if ($isnew && empty($this->newOptions))
		{
			$this->setError(JText::_('COM_KUNENA_LIB_POLL_SAVE_ERROR_NEW_AND_NO_OPTIONS'));
			return false;
		}

		// Create the topics table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		//Store the topic data in the database
		if (! $table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		// Set the id for the KunenaForumTopic object in case we created a new topic.
		if ($isnew)
		{
			$this->load($table->id);
			$this->options = array();
		}

		if ($this->newOptions === false)
		{
			// Options have not changed: nothing left to do
			return true;
		}

		// Load old options for comparision
		$options = $this->getOptions();

		// Find deleted options
		foreach ($options as $key => $item)
		{
			if (empty($this->newOptions[$key]))
			{
				$query = "DELETE FROM #__kunena_polls_options WHERE id={$this->_db->Quote($key)}";
				$this->_db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
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
				$query = "INSERT INTO #__kunena_polls_options (text, pollid, votes)
					VALUES({$this->_db->quote($value)}, {$this->_db->Quote($this->id)}, 0)";
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
				{
					KunenaError::displayDatabaseError($e);
				}

			}
			elseif ($options[$key]->text != $value)
			{
				// Option exists and has changed: update text
				$query = "UPDATE #__kunena_polls_options
					SET text={$this->_db->quote($value)}
					WHERE id={$this->_db->Quote($key)}";
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
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
