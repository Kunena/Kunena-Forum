<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Message.Thankyou
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaForumMessageThankyou
 *
 * @property int    $postid
 * @property int    $userid
 * @property int    $targetuserid
 * @property string $time
 * @since Kunena
 */
class KunenaForumMessageThankyou extends JObject
{
	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $id = 0;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $users = array();

	/**
	 * @param   int $id
	 *
	 * @internal
	 * @since Kunena
	 */
	public function __construct($id)
	{
		$this->id = (int) $id;
	}

	/**
	 * @param   null $identifier
	 * @param   bool $reload
	 *
	 * @return KunenaForumMessageThankyou
	 * @since Kunena
	 */
	static public function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumMessageThankyouHelper::get($identifier, $reload);
	}

	/**
	 * @param   int    $userid
	 * @param   string $time
	 *
	 * @since Kunena
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * Save thank you.
	 *
	 * @param   mixed $user
	 *
	 * @return bool
	 * @throws Exception
	 * @since Kunena
	 */
	public function save($user)
	{
		$user    = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists())
		{
			throw new Exception(JText::_('COM_KUNENA_THANKYOU_LOGIN'));
		}

		if ($user->userid == $message->userid)
		{
			throw new Exception(JText::_('COM_KUNENA_THANKYOU_NOT_YOURSELF'));
		}

		if ($this->exists($user->userid))
		{
			throw new Exception(JText::_('COM_KUNENA_THANKYOU_ALLREADY'));
		}

		if ($user->isBanned())
		{
			throw new Exception(JText::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'));
		}

		$db    = \Joomla\CMS\Factory::getDBO();
		$time  = \Joomla\CMS\Factory::getDate();
		$query = "INSERT INTO #__kunena_thankyou
			SET postid={$db->quote($this->id)} , userid={$db->quote($user->userid)} , targetuserid={$db->quote($message->userid)}, time={$db->quote($time->toSql())}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$this->_savethankyou($message);

		return true;
	}

	/**
	 * Check if the user has already said thank you.
	 *
	 * @param   int $userid
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function exists($userid)
	{
		return isset($this->users[(int) $userid]);
	}

	/**
	 * @param   KunenaForumMessage $message
	 *
	 * @return boolean
	 * @since Kunena
	 */
	protected function _savethankyou(KunenaForumMessage $message)
	{
		$db    = \Joomla\CMS\Factory::getDBO();
		$query = "UPDATE #__kunena_users
				SET thankyou=thankyou+1 WHERE userid={$db->quote($message->userid)}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}

	/**
	 * Get all users who have given thank you to this message.
	 * @return array List of userid=>time.
	 * @since Kunena
	 */
	public function getList()
	{
		return $this->users;
	}

	/**
	 * Detele thank you from the database.
	 *
	 * @param   mixed $user
	 *
	 * @return bool
	 * @throws Exception
	 * @since Kunena
	 */
	public function delete($user)
	{
		$user    = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists())
		{
			throw new Exception(JText::_('COM_KUNENA_THANKYOU_LOGIN'));
		}

		if (!$this->exists($user->userid))
		{
			throw new Exception(JText::_('COM_KUNENA_THANKYOU_NOT_PRESENT'));
		}

		$db    = \Joomla\CMS\Factory::getDBO();
		$query = "DELETE FROM #__kunena_thankyou WHERE postid={$db->quote($this->id)} AND userid={$db->quote($user->userid)}";
		$db->setQuery($query);
		$db->execute();

		$query = "UPDATE #__kunena_users SET thankyou=thankyou-1 WHERE userid={$db->quote($message->userid)}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}
}
