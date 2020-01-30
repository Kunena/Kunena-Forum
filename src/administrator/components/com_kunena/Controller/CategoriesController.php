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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\Helper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use RuntimeException;
use function defined;

/**
 * Kunena Categories Controller
 *
 * @since   Kunena 2.0
 */
class CategoriesController extends FormController
{
	/**
	 * @var     string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $baseurl = null;

	/**
	 * @var     string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $baseurl2 = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=categories';
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Categories', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Lock
	 *
	 * @return  void
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function lock()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Set variable
	 *
	 * @param   integer  $cid       id
	 * @param   string   $variable  variable
	 * @param   string   $value     value
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
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

		$categories = Helper::getCategories($cid);

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
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unlock()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Review
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function review()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Unreview
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unreview()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Allow Anonymous
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function allow_anonymous()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Deny Anonymous
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function deny_anonymous()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Allow Polls
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function allow_polls()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Deny Polls
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function deny_polls()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Publish
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function publish()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Unpublish
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unpublish()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Add
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function add()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$id = array_shift($cid);
		$this->setRedirect(KunenaRoute::_("administrator/index.php?option=com_kunena&view=category&layout=create&catid={$id}", false));
	}

	/**
	 * Edit
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function edit($key = null, $urlVar = null)
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$id = array_shift($cid);

		if (!$id)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}
		else
		{
			$this->setRedirect(KunenaRoute::_("administrator/index.php?option=com_kunena&view=category&layout=edit&catid={$id}", false));
		}
	}

	/**
	 * Remove
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function remove()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$count = 0;
		$name  = null;

		$categories = Helper::getCategories($cid);

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

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Cancel
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function cancel($key = null)
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$id = $this->app->input->getInt('catid', 0);

		$category = Helper::get($id);

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
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function saveorder()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid   = $this->app->input->get('cid', [], 'array');
		$cid   = ArrayHelper::toInteger($cid);
		$order = $this->app->input->get('order', [], 'array');
		$order = ArrayHelper::toInteger($order);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_CATEGORIES_SELECTED'), 'notice');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$success = false;

		$categories = Helper::getCategories($cid);

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

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function saveOrderAjax()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

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
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function orderup()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->orderUpDown(array_shift($cid), -1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Order Up Down
	 *
	 * @param   integer  $id         id
	 * @param   integer  $direction  direction
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  null
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

		$category = Helper::get($id);

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
	 * @since   Kunena 3.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function orderdown()
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid);

		$this->orderUpDown(array_shift($cid), 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to archive one or multiples categories
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function archive()
	{
		$cid = $this->app->input->get('cid', [], 'array');

		if (!empty($cid))
		{
			$this->setVariable($cid, 'published', 2);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Method to put in trash one or multiple categories
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function trash()
	{
		$cid = $this->app->input->get('cid', [], 'array');

		if (!empty($cid))
		{
			$this->setVariable($cid, 'published', -2);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Method to do batch process on selected categories, to move or copy them.
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 5.1.0
	 *
	 * @throws  Exception
	 * @throws  null
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
					$query->update($db->quoteName('#__kunena_categories'))
						->set($db->quoteName('parent_id') . " = " . $db->quote(intval($cat_parent)))
						->where($db->quoteName('id') . " = " . $db->quote($cat));
					$db->setQuery($query);

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
