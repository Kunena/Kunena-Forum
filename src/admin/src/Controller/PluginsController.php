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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeEditor;

/**
 * Kunena Plugins Controller
 *
 * @since   Kunena 2.0
 */
class PluginsController extends AdminController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * @var string
	 * @since version
	 */
	private $baseurl2;

	/**
	 * @var string
	 * @since version
	 */
	private $textPrefix = 'COM_PLUGINS';

	/**
	 * @var string
	 * @since version
	 */
	private $viewList = 'plugins';

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function __construct($config = [])
	{
		$this->option = 'com_kunena';

		parent::__construct($config);
		$this->baseurl    = 'administrator/index.php?option=com_kunena&view=plugins';
		$this->baseurl2   = 'administrator/index.php?option=com_kunena&view=plugins';
		$this->viewList   = 'plugins';
		$this->textPrefix = 'COM_PLUGINS';

		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		// Value = -3
		$this->registerTask('report', 'publish');
		$this->registerTask('orderup', 'reOrder');
		$this->registerTask('orderdown', 'reOrder');

		Factory::getApplication()->getLanguage()->load('com_plugins', JPATH_ADMINISTRATOR);
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  false
	 *
	 * @throws  Exception
	 * @since   12.2
	 */
	public function publish()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = $this->input->get('cid', [], 'array');
		$cid   = ArrayHelper::toInteger($cid, []);
		$data  = ['publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3];
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_($this->textPrefix . '_NO_ITEM_SELECTED'), 'error');

			return;
		}
		else
		{
			// Get the model from com_plugins.
			$app   = Factory::getApplication();
			$model = $app->bootComponent('com_plugins')->getMVCFactory()->createModel('Plugin', 'Administrator', ['ignore_request' => true]);

			// Make sure the item ids are integers
			$cid  = ArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			// Retrieve the name of plugin extension from extensions table to check before if the third party extension exist
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select($db->quoteName(['element', 'extension_id']));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('extension_id') . ' IN (' . $cids . ')');
			$db->setQuery($query);
			$plg_kunena_exts = $db->loadObjectList();

			$cids_exist = [];

			foreach ($plg_kunena_exts as $plg)
			{
				if ($plg->element == 'altauserpoints')
				{
					if (file_exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				elseif ($plg->element == 'community')
				{
					if (file_exists(JPATH_ROOT . '/components/com_community/libraries/core.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				elseif ($plg->element == 'comprofiler')
				{
					if ((file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) || (file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				elseif ($plg->element == 'easyblog')
				{
					if (file_exists(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				elseif ($plg->element == 'easyprofile')
				{
					if (file_exists(JPATH_SITE . '/components/com_jsn/helpers/helper.php'))
					{
						$cids_exist[] = $plg->extension_id;
					}
				}
				elseif ($plg->element == 'easysocial')
				{
					if (file_exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php'))
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
				$this->setMessage(Text::_('COM_KUNENA_PLUGINS_NO_THIRD_PARTIES_EXTENSIONS_FOUND'), 'error');
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
					$ntext = $this->textPrefix . '_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->textPrefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->textPrefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->textPrefix . '_N_ITEMS_TRASHED';
				}

				$this->setMessage(Text::plural($ntext, \count($cid)));
			}
		}

		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList . $extensionURL, false));
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  Exception
	 * @since   12.2
	 */
	public function reOrder(): bool
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = $this->input->get('cid', [], 'array');
		$ids = ArrayHelper::toInteger($ids, []);

		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$model  = $this->getModel();
		$return = $model->reOrder($ids, $inc);

		if ($return === false)
		{
			// Reorder failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(
				Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false),
				$message,
				'error'
			);

			return false;
		}

		// Reorder succeeded.
		$message = Text::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false), $message);

		return true;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  BaseDatabaseModel|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   Kunena 2.0
	 */
	public function getModel($name = 'Plugin', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function saveOrder(): bool
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get the input
		$pks = $this->input->get('cid', [], 'array');
		$pks = ArrayHelper::toInteger($pks, []);

		$order = $this->input->get('order', [], 'array');
		$order = ArrayHelper::toInteger($order, []);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveOrder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(
				Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false),
				$message,
				'error'
			);

			return false;
		}

		// Reorder succeeded.
		$this->setMessage(Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false));

		return true;
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  Exception
	 * @since   12.2
	 */
	public function checkIn(): bool
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$cid = $this->input->get('cid', [], 'array');
		$ids = ArrayHelper::toInteger($cid, []);

		$model  = $this->getModel();
		$return = $model->checkIn($ids);

		if ($return === false)
		{
			// Checkin failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(
				Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false),
				$message,
				'error'
			);

			return false;
		}

		$editor = KunenaBbcodeEditor::getInstance();
		$editor->initializeHMVC();

		// Checkin succeeded.
		$message = Text::plural($this->textPrefix . '_N_ITEMS_CHECKED_IN', \count($ids));
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false), $message);

		return true;
	}

	/**
	 * Regenerate editor file
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0.2
	 */
	public function resync(): void
	{
		$editor = KunenaBbcodeEditor::getInstance();
		$editor->initializeHMVC();

		$message = 'Sync done';
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->viewList, false), $message);
	}
}
