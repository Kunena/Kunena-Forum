<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Message.Thankyou
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

/**
 * Class KunenaForumMessageThankyou
 *
 * @property int    $postid
 * @property int    $userid
 * @property int    $targetuserid
 * @property string $time
 * @since Kunena
 */
class KunenaForumMessageThankyou extends CMSObject
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
	 * @param   int $id id
	 *
	 * @internal
	 * @since Kunena
	 */
	public function __construct($id)
	{
		$this->id = (int) $id;
	}

	/**
	 * @param   null $identifier identifier
	 * @param   bool $reload     reload
	 *
	 * @return KunenaForumMessageThankyou
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumMessageThankyouHelper::get($identifier, $reload);
	}

	/**
	 * @param   int    $userid userid
	 * @param   string $time   time
	 *
	 * @since Kunena
	 * @return void
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * Save thank you.
	 *
	 * @param   mixed $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function save($user)
	{
		$user    = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists())
		{
			throw new Exception(Text::_('COM_KUNENA_THANKYOU_LOGIN'));
		}

		if ($user->userid == $message->userid)
		{
			throw new Exception(Text::_('COM_KUNENA_THANKYOU_NOT_YOURSELF'));
		}

		if ($this->exists($user->userid))
		{
			throw new Exception(Text::_('COM_KUNENA_THANKYOU_ALLREADY'));
		}

		if ($user->isBanned())
		{
			throw new Exception(Text::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'));
		}

		$db    = Factory::getDBO();
		$time  = Factory::getDate();
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
	 * @param   int $userid userid
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function exists($userid)
	{
		return isset($this->users[(int) $userid]);
	}

	/**
	 * @param   KunenaForumMessage $message message
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function _savethankyou(KunenaForumMessage $message)
	{
		$db    = Factory::getDBO();
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
	 * Delete thank you from the database.
	 *
	 * @param   mixed $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function delete($user)
	{
		$user    = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists())
		{
			throw new Exception(Text::_('COM_KUNENA_THANKYOU_LOGIN'));
		}

		if (!$this->exists($user->userid))
		{
			throw new Exception(Text::_('COM_KUNENA_THANKYOU_NOT_PRESENT'));
		}

		$db    = Factory::getDBO();
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
