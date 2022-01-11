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

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

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
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $basecategoryurl = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 * @since   Kunena 2.0
	 *
	 * @see     FormController
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->baseurl         = 'administrator/index.php?option=com_kunena&view=categories';
		$this->basecategoryurl = 'administrator/index.php?option=com_kunena&view=category';
	}

	/**
	 * Save changes on the category
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function save($key = null, $urlVar = null)
	{
		$this->internalSave();
		$postCatid = $this->app->input->post->get('catid', '', 'raw');

		if ($this->app->isClient('administrator'))
		{
			if ($this->task === 'apply')
			{
				$this->setRedirect(KunenaRoute::_($this->basecategoryurl . "&layout=edit&catid={$postCatid}", false));
			}
			else
			{
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		}
		else
		{
			$this->setRedirect(KunenaRoute::_($this->basecategoryurl . '&catid=' . $postCatid));
		}
	}

	/**
	 * Internal method to save category
	 *
	 * @return false|KunenaCategory
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *@throws  Exception
	 * @throws  null
	 */
	protected function internalSave()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');
		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
		$me = KunenaUserHelper::getMyself();

		if ($this->app->isClient('site'))
		{
			KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
		}

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return false;
		}

		$input      = $this->app->input;
		$post       = $input->post->getArray();
		$accesstype = strtr($input->getCmd('accesstype', 'joomla.level'), '.', '-');

		if (empty($post['name']))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORY_MANAGER_PLEASE_SET_A_TITLE'), 'notice');

			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return false;
		}

		if ($post['task'] === 'save2copy')
		{
			$post['title'] = $this->app->getUserState('com_kunena.category_title');
			$post['alias'] = $this->app->getUserState('com_kunena.category_alias');
			$post['catid'] = $this->app->getUserState('com_kunena.category_catid');
		}

		$post['access'] = $input->getInt("access-{$accesstype}", $input->getInt('access'));
		$post['params'] = $input->get("params-{$accesstype}", [], 'array');
		$post['params'] += $input->get("params", [], 'array');
		$success        = false;

		$category = KunenaCategoryHelper::get(\intval($post ['catid']));
		$parent   = KunenaCategoryHelper::get(\intval($post ['parentid']));

		if ($category->exists() && !$category->isAuthorised('admin'))
		{
			// Category exists and user is not admin in category
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($category->name)), 'notice');
		}
		elseif (!$category->exists() && !$me->isAdmin($parent))
		{
			// Category doesn't exist and user is not admin in parent, parentid=0 needs global admin rights
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape($parent->name)), 'notice');
		}
		elseif (!$category->isCheckedOut($me->userid))
		{
			// Nobody can change id or statistics
			$ignore = ['option', 'view', 'task', 'catid', 'id', 'id_last_msg', 'numTopics', 'numPosts', 'time_last_msg', 'aliases', 'aliasesAll'];

			// User needs to be admin in parent (both new and old) in order to move category, parentid=0 needs global admin rights
			if (!$me->isAdmin($parent) || ($category->exists() && !$me->isAdmin($category->getParent())))
			{
				$ignore            = array_merge($ignore, ['parentid', 'ordering']);
				$post ['parentid'] = $category->parentid;
			}

			// Only global admin can change access control and class_sfx (others are inherited from parent)
			if (!$me->isAdmin())
			{
				$access = ['accesstype', 'access', 'pubAccess', 'pubRecurse', 'adminAccess', 'adminRecurse', 'channels', 'class_sfx', 'params'];

				if (!$category->exists() || $parent->id != $category->parentid)
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

			try
			{
				$success = $category->save();
			}
			catch (KunenaException $e)
			{
				$this->app->enqueueMessage(
					Text::sprintf('COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id) . ' ' . $e->getMessage(),
					'error'
				);
			}

			$aliasesAll = explode(',', $input->getString('aliasesAll'));

			$aliases = $input->post->getArray(['aliases' => '']);

			if ($aliasesAll && $aliases)
			{
				$aliases = array_diff($aliasesAll, $aliases);

				foreach ($aliases as $alias)
				{
					$category->deleteAlias($alias);
				}
			}

			// Update read access
			$read                = $this->app->getUserState("com_kunena.user{$me->userid}_read");
			$read[$category->id] = $category->id;
			$this->app->setUserState("com_kunena.user{$me->userid}_read", null);

			$category->checkIn();
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
					$this->app->enqueueMessage(
						Text::sprintf(
							'COM_KUNENA_VIEW_CATEGORY_EDIT_MODERATOR_REMOVED',
							$this->escape($user->getName()),
							$this->escape($category->name)
						)
					);
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
	protected function escape(string $var): string
	{
		return htmlspecialchars($var, ENT_COMPAT);
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function apply(): void
	{
		$category = $this->internalSave();

		if ($category->exists())
		{
			$this->setRedirect(KunenaRoute::_($this->basecategoryurl . "&layout=edit&catid={$category->id}", false));
		}
		else
		{
			$this->setRedirect(KunenaRoute::_($this->basecategoryurl . "&layout=create", false));
		}
	}

	/**
	 * Cancel
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function cancel($key = null, $urlVar = null)
	{
		$postCatid = $this->app->input->post->get('catid', '', 'raw');
		$category  = KunenaCategoryHelper::get($postCatid);
		$category->checkIn();

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save a category like a copy of existing one.
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function save2copy(): void
	{
		$postCatid = $this->app->input->post->get('catid', '', 'raw');
		$postAlias = $this->app->input->post->get('alias', '', 'raw');
		$postName  = $this->app->input->post->get('name', '', 'raw');

		list($title, $alias) = $this->internalGenerateNewTitle($postCatid, $postAlias, $postName);

		$this->app->setUserState('com_kunena.category_title', $title);
		$this->app->setUserState('com_kunena.category_alias', $alias);
		$this->app->setUserState('com_kunena.category_catid', 0);

		$this->internalSave();
		$this->setRedirect(KunenaRoute::_($this->basecategoryurl, false));
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $categoryId  The id of the category.
	 * @param   string   $alias       The alias.
	 * @param   string   $name        The name.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @throws Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected function internalGenerateNewTitle(int $categoryId, string $alias, string $name): array
	{
		while (KunenaCategoryHelper::getAlias($categoryId, $alias))
		{
			$name  = StringHelper::increment($name);
			$alias = StringHelper::increment($alias, 'dash');
		}

		return [$name, $alias];
	}

	/**
	 * Save2new
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function save2new(): void
	{
		$this->internalSave();
		$this->setRedirect(KunenaRoute::_($this->basecategoryurl . "&layout=create", false));
	}
}
