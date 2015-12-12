<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Framework
 * @subpackage  Forum.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Forum Topic Rate Class
 */
class KunenaForumTopicRate extends JObject
{
	protected $id = 0;

	protected $stars = 0;

	protected $users = array();

	/**
	 *
	 * @access    protected
	 *
	 * @param   mixed|null  $id
	 *
	 * @throws Exception
	 */
	public function __construct($id)
	{
		$this->id    = (int) $id;
		$this->stars = JFactory::getApplication()->input->get('stars', 0, 'int');
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access    public
	 *
	 * @param int  $identifier The message to load - Can be only an integer.
	 * @param bool $reload
	 *
	 * @return    KunenaForumMessage        The message object.
	 */
	static public function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumTopicRateHelper::get($identifier, $reload);
	}

	/**
	 * @param int $pid
	 * @param int $userid
	 *
	 * @return int userid if hes in table else empty
	 * @since 2.0
	 */
	public function exists($userid)
	{
		return isset($this->users[$userid]);
	}

	/**
	 * @param $userid
	 * @param $time
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * Perform insert the rate into table
	 *
	 * @param $user
	 *
	 * @return bool true if success
	 * @internal param int $userid
	 *
	 * @since    2.0
	 */
	public function save($user)
	{
		$user  = KunenaFactory::getUser($user);
		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$user->exists())
		{
			$this->setError(JText::_('COM_KUNENA_RATE_LOGIN'));

			return false;
		}

		if ($user->userid == $topic->first_post_userid)
		{
			$this->setError(JText::_('COM_KUNENA_RATE_NOT_YOURSELF'));

			return false;
		}

		if ($this->exists($user->userid))
		{
			$this->setError(JText::_('COM_KUNENA_RATE_ALLREADY'));

			return false;
		}

		$db    = JFactory::getDBO();
		$time  = JFactory::getDate();
		$query = $db->getQuery(true);
		$query->insert('#__kunena_rate')
			->set('topicid=' . $db->quote($this->id))
			->set("userid={$db->quote($user->userid)}")
			->set("rate={$db->quote($this->stars)}")
			->set("time={$db->quote($time->toSQL())}");
		$db->setQuery($query);
		$db->query();

		// Check for an error message.
		if ($db->getErrorNum())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		return true;
	}
}
