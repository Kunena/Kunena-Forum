<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message.Thankyou
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
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
 * @since Kunena
 * @property int    $userid
 * @property int    $targetuserid
 * @property string $time
 * @property int    $postid
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
	 * @internal
	 *
	 * @param   int  $id  id
	 *
	 * @since Kunena
	 */
	public function __construct($id)
	{
		$this->id = (int) $id;
	}

	/**
	 * @param   null  $identifier  identifier
	 * @param   bool  $reload      reload
	 *
	 * @return KunenaForumMessageThankyou
	 * @since Kunena
	 * @throws Exception
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumMessageThankyouHelper::get($identifier, $reload);
	}

	/**
	 * @param   int     $userid  userid
	 * @param   string  $time    time
	 *
	 * @return void
	 * @since Kunena
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * Save thank you.
	 *
	 * @param   mixed  $user  user
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
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
		$query = $db->getQuery(true);
		$query->insert($db->quoteName('#__kunena_thankyou'))
			->set('postid = ' . $db->quote($this->id) . ', userid = ' . $db->quote($user->userid) . ', targetuserid = ' . $db->quote($message->userid) . ', time = ' . $db->quote($time->toSql()));
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
	 * @param   int  $userid  userid
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function exists($userid)
	{
		return isset($this->users[(int) $userid]);
	}

	/**
	 * @param   KunenaForumMessage  $message  message
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 */
	protected function _savethankyou(KunenaForumMessage $message)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__kunena_users'))
			->set('thankyou = thankyou+1')
			->where('userid = ' . $db->quote($message->userid));
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
	 *
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
	 * @param   mixed  $user  user
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
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
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__kunena_thankyou'))
			->where('postid = ' . $db->quote($this->id))
			->andWhere('userid = ' . $db->quote($user->userid));
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__kunena_users'))
			->set('thankyou = thankyou-1')
			->where('userid = ' . $db->quote($message->userid));
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
