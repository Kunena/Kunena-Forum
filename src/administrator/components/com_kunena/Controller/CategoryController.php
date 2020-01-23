<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;

/**
 * Kunena Category Controller
 *
 * @since   Kunena 6.0
 */
class CategoryController extends FormController
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=categories';
	}

	/**
	 * Save changes on the category
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		$this->_save();

		if ($this->app->isClient('administrator'))
		{
			$this->setRedirect(\KunenaRoute::_($this->baseurl, false));
		}
		else
		{
			$post_catid = $this->app->input->post->get('catid', '', 'raw');
			$this->setRedirect(\KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . $post_catid));
		}
	}

	/**
	 * Internal method to save category
	 *
	 * @return  \KunenaForumCategory|void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function _save()
	{
		\KunenaFactory::loadLanguage('com_kunena', 'admin');
		$me = \KunenaUserHelper::getMyself();

		if ($this->app->isClient('site'))
		{
			\KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
		}

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$input      = $this->app->input;
		$post       = $input->post->getArray();
		$accesstype = strtr($input->getCmd('accesstype', 'joomla.level'), '.', '-');

		if ($post['task'] == 'save2copy')
		{
			$post['title'] = $this->app->getUserState('com_kunena.category_title');
			$post['alias'] = $this->app->getUserState('com_kunena.category_alias');
			$post['catid'] = $this->app->getUserState('com_kunena.category_catid');
		}

		$post['access'] = $input->getInt("access-{$accesstype}", $input->getInt('access', null));
		$post['params'] = $input->get("params-{$accesstype}", [], 'array');
		$post['params'] += $input->get("params", [], 'array');
		$success        = false;

		$category = \KunenaForumCategoryHelper::get(intval($post ['catid']));
		$parent   = \KunenaForumCategoryHelper::get(intval($post ['parent_id']));

		if ($category->exists() && !$category->isAuthorised('admin'))
		{
			// Category exists and user is not admin in category
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
		}
		elseif (!$category->exists() && !$me->isAdmin($parent))
		{
			// Category doesn't exist and user is not admin in parent, parent_id=0 needs global admin rights
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($parent->name)), 'notice');
		}
		elseif (!$category->isCheckedOut($this->me->userid))
		{
			// Nobody can change id or statistics
			$ignore = ['option', 'view', 'task', 'catid', 'id', 'id_last_msg', 'numTopics', 'numPosts', 'time_last_msg', 'aliases', 'aliases_all'];

			// User needs to be admin in parent (both new and old) in order to move category, parent_id=0 needs global admin rights
			if (!$me->isAdmin($parent) || ($category->exists() && !$me->isAdmin($category->getParent())))
			{
				$ignore             = array_merge($ignore, ['parent_id', 'ordering']);
				$post ['parent_id'] = $category->parent_id;
			}

			// Only global admin can change access control and class_sfx (others are inherited from parent)
			if (!$me->isAdmin())
			{
				$access = ['accesstype', 'access', 'pub_access', 'pub_recurse', 'admin_access', 'admin_recurse', 'channels', 'class_sfx', 'params'];

				if (!$category->exists() || $parent->id != $category->parent_id)
				{
					// If category didn't exist or is moved, copy access and class_sfx from parent
					$category->bind($parent->getProperties(), $access, true);
				}

				$ignore = array_merge($ignore, $access);
			}

			$category->bind($post, $ignore);

			if (!$category->exists())
			{
				$category->ordering = 99999;
			}

			$success     = $category->save();
			$aliases_all = explode(',', $input->getString('aliases_all'));

			$aliases = $input->post->getArray(['aliases' => '']);

			if ($aliases_all)
			{
				$aliases = array_diff($aliases_all, $aliases['aliases']);

				foreach ($aliases_all as $alias)
				{
					$category->deleteAlias($alias);
				}
			}

			// Update read access
			$read                = $this->app->getUserState("com_kunena.user{$me->userid}_read");
			$read[$category->id] = $category->id;
			$this->app->setUserState("com_kunena.user{$me->userid}_read", null);

			if (!$success)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape($category->getError())), 'notice');
			}

			$category->checkin();
		}
		else
		{
			// Category was checked out by someone else.
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');
		}

 		if ($success)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_SAVED', $this->escape($category->name)));
		}

		if (!empty($post['rmmod']))
		{
			foreach ((array) $post['rmmod'] as $userid => $value)
			{
				$user = \KunenaFactory::getUser($userid);

				if ($category->tryAuthorise('admin', null, false) && $category->removeModerator($user))
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_VIEW_CATEGORY_EDIT_MODERATOR_REMOVED', $this->escape($user->getName()), $this->escape($category->name)));
				}
			}
		}

		return $category;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   string  $var  The output to escape.
	 *
	 * @return  string The escaped value.
	 *
	 * @since   Kunena 6.0
	 */
	protected function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}
}
