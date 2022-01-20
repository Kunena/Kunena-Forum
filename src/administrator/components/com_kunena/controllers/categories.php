<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Categories Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerCategories extends KunenaController
{
	/**
	 * @since    2.0.0-BETA2
	 * @var null|string
	 */
	protected $baseurl = null;

	/**
	 * @since    2.0.0-BETA2
	 *
	 * @var null|string
	 */
	protected $baseurl2 = null;

	/**
	 * Constructor
	 *
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl  = 'administrator/index.php?option=com_kunena&view=categories';
		$this->baseurl2 = 'administrator/index.php?option=com_kunena&view=categories';
	}

	/**
	 * Lock
	 *
	 * @since    2.0.0-BETA2
	 *
	 * @return  void
	 * @throws Exception
	 * @throws null
	 * @since    Kunena
	 */
	public function lock()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 1);
		$this->setRedirectBack();
	}

	/**
	 * Set variable
	 *
	 * @param   integer $cid      id
	 * @param   string  $variable variable
	 * @param   string  $value    value
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	protected function setVariable($cid, $variable, $value)
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');

			return;
		}

		$count = 0;
		$name  = null;

		$categories = KunenaForumCategoryHelper::getCategories($cid);

		foreach ($categories as $category)
		{
			if ($category->get($variable) == $value)
			{
				continue;
			}

			if (!$category->isAuthorised('admin'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
			}
			elseif (!$category->isCheckedOut($this->me->userid))
			{
				$category->set($variable, $value);

				if ($category->save())
				{
					$count++;
					$name = $category->name;
				}
				else
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape($category->getError())), 'notice');
				}
			}
			else
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');
			}
		}

		if ($count == 1 && $name)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_SAVED', $this->escape($name)));
		}

		if ($count > 1)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORIES_SAVED', $count));
		}
	}

	/**
	 * Unlock
	 *
	 * @since    2.0.0-BETA2
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 */
	public function unlock()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 0);
		$this->setRedirectBack();
	}

	/**
	 * Review
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function review()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 1);
		$this->setRedirectBack();
	}

	/**
	 * Unreview
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function unreview()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 0);
		$this->setRedirectBack();
	}

	/**
	 * Allow Anonymous
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function allow_anonymous()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 1);
		$this->setRedirectBack();
	}

	/**
	 * Deny Anonymous
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function deny_anonymous()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 0);
		$this->setRedirectBack();
	}

	/**
	 * Allow Polls
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function allow_polls()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 1);
		$this->setRedirectBack();
	}

	/**
	 * Deny Polls
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function deny_polls()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 0);
		$this->setRedirectBack();
	}

	/**
	 * Publish
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function publish()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 1);
		$this->setRedirectBack();
	}

	/**
	 * Unpublish
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 *
	 * @since    Kunena
	 */
	public function unpublish()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 0);
		$this->setRedirectBack();
	}

	/**
	 * Add
	 *
	 * @return void
	 *
	 * @since    2.0.0-BETA2
	 *
	 * @throws Exception
	 * @since    Kunena
	 * @throws null
	 */
	public function add()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$id = array_shift($cid);
		$this->setRedirect(KunenaRoute::_($this->baseurl2 . "&layout=create&catid={$id}", false));
	}

	/**
	 * Edit
	 *
	 * @return void
	 *
	 * @since    2.0.0-BETA2
	 *
	 * @throws Exception
	 * @since    Kunena
	 * @throws null
	 */
	public function edit()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$id = array_shift($cid);

		if (!$id)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirectBack();

			return;
		}
		else
		{
			$this->setRedirect(KunenaRoute::_($this->baseurl2 . "&layout=edit&catid={$id}", false));
		}
	}

	/**
	 * Apply
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 */
	public function apply()
	{
		$category = $this->_save();

		if ($category->exists())
		{
			$this->setRedirect(KunenaRoute::_($this->baseurl2 . "&layout=edit&catid={$category->id}", false));
		}
		else
		{
			$this->setRedirect(KunenaRoute::_($this->baseurl2 . "&layout=create", false));
		}
	}

	/**
	 * Save
	 *
	 * @return KunenaForumCategory|void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 */
	protected function _save()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$app        = Factory::getApplication();
		$input      = $app->input;
		$post       = $app->input->post->getArray();
		$accesstype = strtr($input->getCmd('accesstype', 'joomla.level'), '.', '-');

		if ($post['task'] == 'save2copy')
		{
			$post['title'] = $this->app->getUserState('com_kunena.category_title');
			$post['alias'] = $this->app->getUserState('com_kunena.category_alias');
			$post['catid'] = $this->app->getUserState('com_kunena.category_catid');
		}

		$post['access'] = $input->getInt("access-{$accesstype}", $input->getInt('access', null));
		$post['params'] = $input->get("params-{$accesstype}", array(), 'post', 'array');
		$post['params'] += $input->get("params", array(), 'post', 'array');
		$success        = false;

		$category = KunenaForumCategoryHelper::get(intval($post ['catid']));
		$parent   = KunenaForumCategoryHelper::get(intval($post ['parent_id']));

		if ($category->exists() && !$category->isAuthorised('admin'))
		{
			// Category exists and user is not admin in category
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
		}
		elseif (!$category->exists() && !$this->me->isAdmin($parent))
		{
			// Category doesn't exist and user is not admin in parent, parent_id=0 needs global admin rights
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($parent->name)), 'notice');
		}
		elseif (!$category->isCheckedOut($this->me->userid))
		{
			// Nobody can change id or statistics
			$ignore = array('option', 'view', 'task', 'catid', 'id', 'id_last_msg', 'numTopics', 'numPosts', 'time_last_msg', 'aliases', 'aliases_all');

			// User needs to be admin in parent (both new and old) in order to move category, parent_id=0 needs global admin rights

			if (!$this->me->isAdmin($parent) || ($category->exists() && !$this->me->isAdmin($category->getParent())))
			{
				$ignore             = array_merge($ignore, array('parent_id', 'ordering'));
				$post ['parent_id'] = $category->parent_id;
			}

			// Only global admin can change access control and class_sfx (others are inherited from parent)
			if (!$this->me->isAdmin())
			{
				$access = array('accesstype', 'access', 'pub_access', 'pub_recurse', 'admin_access', 'admin_recurse', 'channels', 'class_sfx', 'params');

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
			$aliasesInput = $app->input->getString('aliases_all');

			if (!empty($aliasesInput))
			{
				$aliases_all = explode(',', $aliasesInput);

				$aliases = $app->input->post->getArray(array('aliases' => ''));

				if ($aliases_all && $aliases)
				{
					$aliases = array_diff($aliases_all, $aliases['aliases']);

					foreach ($aliases_all as $alias)
					{
						$category->deleteAlias($alias);
					}
				}
			}

			// Update read access
			$read                = $this->app->getUserState("com_kunena.user{$this->me->userid}_read");
			$read[$category->id] = $category->id;
			$this->app->setUserState("com_kunena.user{$this->me->userid}_read", null);

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
				$user = KunenaFactory::getUser($userid);

				if ($category->tryAuthorise('admin', null, false) && $category->removeModerator($user))
				{
				    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_VIEW_CATEGORY_EDIT_MODERATOR_REMOVED', $this->escape($user->getName()), $this->escape($category->name)));
				}
			}
		}

		return $category;
	}

	/**
	 * Save2new
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 */
	public function save2new()
	{
		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl2 . "&layout=create", false));
	}

	/**
	 * Save
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 */
	public function save()
	{
		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save a category like a copy of existing one.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since    2.0.0-BETA2
	 */
	public function save2copy()
	{
		$post_catid = $this->app->input->post->get('catid', '', 'raw');
		$post_alias = $this->app->input->post->get('alias', '', 'raw');
		$post_name  = $this->app->input->post->get('name', '', 'raw');

		list($title, $alias) = $this->_generateNewTitle($post_catid, $post_alias, $post_name);

		$this->app->setUserState('com_kunena.category_title', $title);
		$this->app->setUserState('com_kunena.category_alias', $alias);
		$this->app->setUserState('com_kunena.category_catid', 0);

		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer $category_id The id of the category.
	 * @param   string  $alias       The alias.
	 * @param   string  $name        The name.
	 *
	 * @return    array  Contains the modified title and alias.
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 */
	protected function _generateNewTitle($category_id, $alias, $name)
	{
		while (KunenaForumCategoryHelper::getAlias($category_id, $alias))
		{
			$name  = Joomla\String\StringHelper::increment($name);
			$alias = Joomla\String\StringHelper::increment($alias, 'dash');
		}

		return array($name, $alias);
	}

	/**
	 * Remove
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	public function remove()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirectBack();

			return;
		}

		$count = 0;
		$name  = null;

		$categories = KunenaForumCategoryHelper::getCategories($cid);

		foreach ($categories as $category)
		{
			if (!$category->isAuthorised('admin'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
			}
			elseif (!$category->isCheckedOut($this->me->userid))
			{
				if ($category->delete())
				{
					$count++;
					$name = $category->name;
				}
				else
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_DELETE_FAILED', $this->escape($category->getError())), 'notice');
				}
			}
			else
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');
			}
		}

		if ($count == 1 && $name)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_DELETED', $this->escape($name)));
		}

		if ($count > 1)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORIES_DELETED', $count));
		}

		$this->setRedirectBack();
	}

	/**
	 * Cancel
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	public function cancel()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$id = $this->app->input->getInt('catid', 0);

		$category = KunenaForumCategoryHelper::get($id);

		if (!$category->isAuthorised('admin'))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
		}
		elseif (!$category->isCheckedOut($this->me->userid))
		{
			$category->checkin();
		}
		else
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Save order
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	public function saveorder()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);
		$order = $this->app->input->get('order', array(), 'post', 'array');
		$order = ArrayHelper::toInteger($order);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirectBack();

			return;
		}

		$success = false;

		$categories = KunenaForumCategoryHelper::getCategories($cid);

		foreach ($categories as $category)
		{
			if (!isset($order [$category->id]) || $category->get('ordering') == $order [$category->id])
			{
				continue;
			}

			if (!$category->getParent()->tryAuthorise('admin'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->getParent()->name)), 'notice');
			}
			elseif (!$category->isCheckedOut($this->me->userid))
			{
				$category->set('ordering', $order [$category->id]);
				$success = $category->save();

				if (!$success)
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape($category->getError())), 'notice');
				}
			}
			else
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_NEW_ORDERING_SAVED'));
		}

		$this->setRedirectBack();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   3.0
	 * @throws null
	 */
	public function saveOrderAjax()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');

		// Get the model
		$model = $this->getModel('categories');

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		$this->app->close();
	}

	/**
	 * Order Up
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	public function orderup()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->orderUpDown(array_shift($cid), -1);
		$this->setRedirectBack();
	}

	/**
	 * Order Up Down
	 *
	 * @param   integer $id        id
	 * @param   integer $direction direction
	 *
	 * @throws null
	 *
	 * @return  void
	 *
	 * @since  K3.0
	 */
	protected function orderUpDown($id, $direction)
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!$id)
		{
			return;
		}

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$category = KunenaForumCategoryHelper::get($id);

		if (!$category->getParent()->tryAuthorise('admin'))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->getParent()->name)), 'notice');

			return;
		}

		if ($category->isCheckedOut($this->me->userid))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape($category->name)), 'notice');

			return;
		}

		$db  = Factory::getDbo();
		$row = new TableKunenaCategories($db);
		$row->load($id);

		// Ensure that we have the right ordering
		$where = 'parent_id=' . $db->quote($row->parent_id);
		$row->reorder();
		$row->move($direction, $where);
	}

	/**
	 * Order Down
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K3.0
	 */
	public function orderdown()
	{
		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->orderUpDown(array_shift($cid), 1);
		$this->setRedirectBack();
	}

	/**
	 * Method to archive one or multiples categories
	 *
	 * @since K4.0
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since 2.0
	 */
	public function archive()
	{
		$cid = $this->app->input->get('cid', array(), 'array');

		if (!empty($cid))
		{
			$this->setVariable($cid, 'published', 2);
			$this->setRedirectBack();
		}
	}

	/**
	 * Method to put in trash one or multiple categories
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since K4.0
	 *
	 * @since Kunena
	 */
	public function trash()
	{
		$cid = $this->app->input->get('cid', array(), 'array');

		if (!empty($cid))
		{
			$this->setVariable($cid, 'published', -2);
			$this->setRedirectBack();
		}
	}

	/**
	 * Method to do batch process on selected categories, to move or copy them.
	 *
	 * @return boolean|void
	 *
	 * @throws Exception
	 * @since  5.1.0
	 * @throws null
	 */
	public function batch_categories()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$cid        = $this->app->input->get('cid', '', 'array');
		$cat_parent = $this->app->input->getInt('batch_catid_target', 0);
		$task       = $this->app->input->getString('move_copy');

		if ($cat_parent == 0 || empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORIES_LABEL_BATCH_NOT_SELECTED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return false;
		}

		if ($task == 'move')
		{
			$db = Factory::getDBO();

			foreach ($cid as $cat)
			{
				if ($cat_parent != $cat)
				{
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__kunena_categories'));
					$query->set($db->quoteName('parent_id') . " = " . $db->quote(intval($cat_parent)));
					$query->where($db->quoteName('id') . " = " . $db->quote($cat));
					$db->setQuery((string) $query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->app->enqueueMessage($e->getMessage());

						return;
					}
				}
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORIES_LABEL_BATCH_MOVE_SUCCESS'));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));

		return true;
	}
}
