<?php
/**
 * @version		$Id: recent.php 1088 2009-09-26 19:40:21Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
kimport('database.query');
kimport('user.user');

/**
 * Post model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelPost extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	/**
	 * The model context for caching.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $_context = 'com_kunena.post';

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null)
	{
		if (!$this->__state_set)
		{
			// Get the application object and component options.
			$app	= JFactory::getApplication();
			$params	= $app->getParams('com_kunena');

			// If recent request is for a category, we also get a category id
			$this->setState('category.id', JRequest::getInt('category', 0));

			// Load the user parameters.
			$user = JFactory::getUser();
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));
			$this->setState('user.name', $user->get('username'));
			$this->setState('user.email', $user->get('email'));

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to create new thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function post($subject, $message, $categoryid, $emoticon=0)
	{
		if (empty($message)) return false;
		if (empty($subject)) return false;
		if (intval($categoryid) < 1) return false;

		$userid = $this->getState('user.id');
		$user = KUser::getInstance($userid);
		$allowed = explode(',', $user->getAllowedCategories());
		if (!in_array($categoryid, $allowed)) return false;

		$this->_db->setQuery("SELECT id, locked, published FROM #__kunena_categories WHERE id=".intval($categoryid));
		$category = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($category == null) return false;
		if ($category->locked) return false;
		if (!$category->published) return false;

		$this->_db->setQuery("INSERT INTO #__kunena_threads (catid, topic_subject, topic_emoticon)
			VALUES(".intval($categoryid).",".$this->_db->quote($subject).",".intval($emoticon).")");
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		$threadid = $this->_db->insertId();

		$messageid = $this->reply($subject, $message, $threadid);
		if (!$messageid) return;

		// Update thread
		$this->_db->setQuery("UPDATE #__kunena_threads SET first_post_id=last_post_id, first_post_time=last_post_time, "
			."first_post_userid=last_post_userid, first_post_name=last_post_name, first_post_message=last_post_message "
			."WHERE threadid=".intval($threadid));
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $messageid;
	}

	/**
	 * Method to post a message into a thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function reply($subject, $message, $threadid, $parentid=0)
	{
		if (empty($message)) return false;
		if (intval($threadid) < 1) return false;

		$this->_db->setQuery("SELECT id, catid, locked, hold, moved_id FROM #__kunena_threads WHERE id=".intval($threadid));
		$thread = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($thread == null) return false;
		if ($thread->moved_id) return false;
		if ($thread->locked) return false;

		$userid = $this->getState('user.id');
		$user = KUser::getInstance($userid);
		$allowed = explode(',', $user->getAllowedCategories());
		if (!in_array($thread->catid, $allowed)) return false;

		$this->_db->setQuery("SELECT id, locked, published FROM #__kunena_categories WHERE id=".intval($thread->catid));
		$category = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($category == null) return false;
		if ($category->locked) return false;
		if (!$category->published) return false;

		$username = $this->getState('user.name');
		$useremail = $this->getState('user.email');

		$ip = $_SERVER["REMOTE_ADDR"];

		$this->_db->setQuery("INSERT INTO #__kunena_messages (parent, thread, name, userid, email, subject, time, ip, hold, message)
			VALUES(".intval($parentid).",".intval($threadid).","
			.$this->_db->quote($username).",".intval($userid).",".$this->_db->quote($useremail).",".$this->_db->quote($subject)
			.",".intval($posttime).",".$this->_db->quote($ip).",".intval($hold).",".$this->_db->quote($message).")");
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		$messageid = $this->_db->insertId();

		if ($user->userid)
		{
			// Update post count from a user
			$this->_db->setQuery("UPDATE #__kunena_users SET posts=posts+1 WHERE userid=".intval($userid));
			$this->_db->query();
			if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		}

		// Update thread
		$this->_db->setQuery("UPDATE #__kunena_threads SET posts=posts+1, last_post_id=".intval($messageid).", last_post_time=".intval($posttime)
		.", last_post_userid=".intval($userid).", last_post_name=".$this->_db->quote($username).", last_post_email=".$this->_db->quote($useremail)
		.", last_post_message=".$this->_db->quote($message)." WHERE threadid=".intval($threadid));
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		// Update category
		$this->_db->setQuery("UPDATE #__kunena_categories SET numPosts=numPosts+1, id_last_msg=".intval($messageid).", time_last_msg=".intval($posttime)
		." WHERE catid=".intval($thread->catid));
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $messageid;
	}

	/**
	 * Method to edit a message
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function edit($subject, $message, $reason, $messageid)
	{
		if (empty($message)) return false;
		if (intval($messageid) < 1) return false;

		$this->_db->setQuery("SELECT id, thread, userid, hold FROM #__kunena_messages WHERE id=".intval($messageid));
		$msg = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($msg == null) return false;
		if ($msg->hold) return false;

		$userid = $this->getState('user.id');
		if ($userid==0 || $msg->userid != $userid) return false;

		$this->_db->setQuery("SELECT id, catid, locked, hold, moved_id FROM #__kunena_threads WHERE id=".intval($msg->thread));
		$thread = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($thread == null) return false;
		if ($thread->moved_id) return false;
		if ($thread->locked) return false;

		$user = KUser::getInstance($userid);
		$allowed = explode(',', $user->getAllowedCategories());
		if (!in_array($thread->catid, $allowed)) return false;

		$ip = $_SERVER["REMOTE_ADDR"];

		$this->_db->setQuery("UPDATE #__kunena_messages subject=".$this->_db->quote($subject).", message=".$this->_db->quote($message)
		.", modified_by=".intval($userid).", modified_time=".intval($posttime).", modified_reason=".$this->_db->quote($reason));
		$kunena_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
	}

	/**
	 * Method to subscribe a thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function subscribe($threadid)
	{
		if (intval($threadid) < 1) return false;

		$this->_db->setQuery("SELECT id, catid, locked, hold, moved_id FROM #__kunena_threads WHERE id=".intval($threadid));
		$thread = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($thread == null) return false;
		if ($thread->moved_id) return false;

		$userid = $this->getState('user.id');
		if (!$userid) return false;

		$user = KUser::getInstance($userid);
		$allowed = explode(',', $user->getAllowedCategories());
		if (!in_array($thread->catid, $allowed)) return false;

		$this->_db->setQuery("INSERT IGNORE INTO #__kunena_subscriptions (thread,userid) VALUES (".intval($threadid).",".intval($userid).")");
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $this->_db->getAffectedRows();
	}

	/**
	 * Method to unsubscribe a thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function unsubscribe($threadid)
	{
		if (intval($threadid) < 1) return false;

		$userid = $this->getState('user.id');
		if (!$userid) return false;

		$kunena_db->setQuery("DELETE FROM #__kunena_subscriptions WHERE thread=".intval($threadid)." AND userid=".intval($userid));
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $this->_db->getAffectedRows();
	}

	/**
	 * Method to favorite a thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function favorite($threadid)
	{
		if (intval($threadid) < 1) return false;

		$this->_db->setQuery("SELECT id, catid, locked, hold, moved_id FROM #__kunena_threads WHERE id=".intval($threadid));
		$thread = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		if ($thread == null) return false;
		if ($thread->moved_id) return false;

		$userid = $this->getState('user.id');
		if (!$userid) return false;

		$user = KUser::getInstance($userid);
		$allowed = explode(',', $user->getAllowedCategories());
		if (!in_array($thread->catid, $allowed)) return false;

		$this->_db->setQuery("INSERT IGNORE INTO #__kunena_favorites (thread,userid) VALUES (".intval($threadid).",".intval($userid).")");
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $this->_db->getAffectedRows();
	}

	/**
	 * Method to unfavorite a thread
	 *
	 * @return	?
	 * @since	1.6
	 */
	public function unfavorite($threadid)
	{
		if (intval($threadid) < 1) return false;

		$userid = $this->getState('user.id');
		if (!$userid) return false;

		$kunena_db->setQuery("DELETE FROM #__kunena_favorites WHERE thread=".intval($threadid)." AND userid=".intval($userid));
		$this->_db->query();
		if ($this->_db->getErrorNum()) throw new KunenaPostException($this->_db->getErrorMsg(), $this->_db->getErrorNum());

		return $this->_db->getAffectedRows();
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 * @return	string	A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('user.aid');

		return md5($id);
	}
}

class KunenaPostException extends Exception {}