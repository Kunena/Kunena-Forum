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
use Joomla\CMS\Log\Log;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Plugins Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerPlugins extends KunenaController
{
	/**
	 * @var null|string
	 * @since Kunena
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config config
	 *
	 * @throws Exception
	 *
	 * @since    2.0
	 */
	public function __construct($config = array())
	{
		$this->option = 'com_kunena';
		$this->input  = Factory::getApplication()->input;

		parent::__construct($config);
		$this->baseurl     = 'administrator/index.php?option=com_kunena&view=plugins';
		$this->baseurl2    = 'administrator/index.php?option=com_kunena&view=plugins';
		$this->view_list   = 'plugins';
		$this->text_prefix = 'COM_PLUGINS';

		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		// Value = -3
		$this->registerTask('report', 'publish');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		Factory::getLanguage()->load('com_plugins', JPATH_ADMINISTRATOR);
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   12.2
	 */
	public function publish()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = Factory::getApplication()->input->get('cid', array(), 'array');
		$data  = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			// Retrieve the name of plugin extension from extensions table to check before if the third party extension exist
			$db = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('element', 'extension_id')));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('extension_id') . ' IN (' . $cids . ')');
			$db->setQuery($query);
			$plg_kunena_exts = $db->loadObjectList();

			$cids_exist = array();

			foreach ($plg_kunena_exts as $plg)
			{
				if($plg->element == 'altauserpoints')
				{
					if (file_exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'community')
				{
					if (file_exists(JPATH_ROOT . '/components/com_community/libraries/core.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'comprofiler')
				{
					if ((file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) || (file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'easyblog')
				{
					if (file_exists(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'easyprofile')
				{
					if (file_exists(JPATH_SITE . '/components/com_jsn/helpers/helper.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'easysocial')
				{
					if (file_exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else if ($plg->element == 'uddeim')
				{
					if (file_exists(JPATH_SITE . '/components/com_uddeim/uddeim.api.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				else
				{
					$cids_exist[] = $plg->extension_id;
				}
			}

			$extension    = $this->input->get('extension');
			$extensionURL = ($extension) ? '&extension=' . $extension : '';

			if (empty($cids_exist))
			{
				$this->setMessage(Text::_('COM_KUNENA_PLUGINS_NO_THIRD_PARTIES_EXTENSIONS_FOUND') , 'error');
				$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));

				return false;
			}

			// Publish the items.
			if (!$model->publish($cids_exist, $value))
			{
				Log::add($model->getError(), Log::WARNING, 'jerror');
			}
			else
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}

				$this->setMessage(Text::plural($ntext, count($cid)));
			}
		}

		$editor = KunenaBbcodeEditor::getInstance();
		$editor->initializeHMVC();

		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Getmodel
	 *
	 * @param   string $name   name
	 * @param   string $prefix prefix
	 * @param   array  $config config
	 *
	 * @return object
	 *
	 * @since    2.0
	 */
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if (empty($name))
		{
			$name = 'plugin';
		}

		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 * @since   12.2
	 */
	public function reorder()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = Factory::getApplication()->input->post->get('cid', array(), 'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$model  = $this->getModel();
		$return = $model->reorder($ids, $inc);

		if ($return === false)
		{
			// Reorder failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

			return false;
		}
		else
		{
			// Reorder succeeded.
			$message = Text::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);

			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		$pks   = ArrayHelper::toInteger($pks);
		$order = ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

			return false;
		}
		else
		{
			// Reorder succeeded.
			$this->setMessage(Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

			return true;
		}
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 * @since   12.2
	 */
	public function checkin()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = Factory::getApplication()->input->post->get('cid', array(), 'array');

		$model  = $this->getModel();
		$return = $model->checkin($ids);

		if ($return === false)
		{
			// Checkin failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

			return false;
		}
		else
		{
			$editor = KunenaBbcodeEditor::getInstance();
			$editor->initializeHMVC();

			// Checkin succeeded.
			$message = Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);

			return true;
		}
	}

	/**
	 * Regenerate editor file
	 *
	 * @since 5.0.2
	 * @throws Exception
	 */
	public function resync()
	{
		$editor = KunenaBbcodeEditor::getInstance();
		$editor->initializeHMVC();

		$message = 'Sync done';
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
	}
}
