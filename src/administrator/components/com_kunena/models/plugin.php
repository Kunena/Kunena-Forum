<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Form\Form;

jimport('joomla.application.component.modeladmin');

/**
 * Plugin model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       1.6
 */
class KunenaAdminModelPlugin extends \Joomla\CMS\MVC\Model\AdminModel
{
	/**
	 * @var        string    The help screen key for the module.
	 * @since   1.6
	 */
	protected $helpKey = 'JHELP_EXTENSIONS_PLUGIN_MANAGER_EDIT';

	/**
	 * @var        string    The help screen base URL for the module.
	 * @since   1.6
	 */
	protected $helpURL;

	/**
	 * @since   1.6
	 */
	protected $_cache;

	/**
	 * @var        string    The event to trigger after saving the data.
	 * @since   1.6
	 */
	protected $event_after_save = 'onExtensionAfterSave';

	/**
	 * @var        string    The event to trigger after before the data.
	 * @since   1.6
	 */
	protected $event_before_save = 'onExtensionBeforeSave';

	/**
	 * @param   array $config config
	 *
	 * @since Kunena
	 */
	public function __construct($config = array())
	{
		$this->option = 'com_kunena';
		parent::__construct($config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return boolean|JForm
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item    = $this->getItem();
			$folder  = $item->folder;
			$element = $item->element;
		}
		else
		{
			$folder  = ArrayHelper::getValue($data, 'folder', '', 'cmd');
			$element = ArrayHelper::getValue($data, 'element', '', 'cmd');
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.folder', $folder);
		$this->setState('item.element', $element);

		$pluginfile = 'plugin';

		// Get the form.
		$form = $this->loadForm('com_kunena.plugin', $pluginfile, array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer $pk The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 * @throws Exception
	 * @since Kunena
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('plugin.id');

		if (!isset($this->_cache[$pk]))
		{
			$false = false;

			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				Factory::getApplication()->enqueueMessage($table->getError());

				return $false;
			}

			// Convert to the JObject before adding other data.
			$properties        = $table->getProperties(1);
			$this->_cache[$pk] = ArrayHelper::toObject($properties, 'JObject');

			// Convert the params field to an array.
			$registry = new \Joomla\Registry\Registry;
			$registry->loadString($table->params);
			$this->_cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.
			$path = KunenaPath::clean(JPATH_PLUGINS . '/' . $table->folder . '/' . $table->element . '/' . $table->element . '.xml');

			if (is_file($path))
			{
				$this->_cache[$pk]->xml = simplexml_load_file($path);
			}
			else
			{
				$this->_cache[$pk]->xml = null;
			}
		}

		return $this->_cache[$pk];
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string $type   The table type to instantiate
	 * @param   string $prefix A prefix for the table class name. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\Table\Table    A database object
	 * @since Kunena
	 */
	public function getTable($type = 'Extension', $prefix = 'JTable', $config = array())
	{
		return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Override method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	public function save($data)
	{
		// Load the extension plugin group.
		\Joomla\CMS\Plugin\PluginHelper::importPlugin('extension');

		// Setup type
		$data['type'] = 'plugin';

		$context = $this->option . '.' . $this->name;
		$table   = $this->getTable();
		Factory::getApplication()->triggerEvent($this->event_after_save, array($context, &$table, true, $data));

		return parent::save($data);
	}

	/**
	 * Get the necessary data to load an item help screen.
	 *
	 * @return  object  An object with key, url, and local properties for loading the item help screen.
	 *
	 * @since   1.6
	 */
	public function getHelp()
	{
		return (object) array('key' => $this->helpKey, 'url' => $this->helpURL);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_plugins.edit.plugin.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_plugins.plugin', $data);

		return $data;
	}

	/**
	 * Method to allow derived classes to preprocess the data.
	 *
	 * @param   string $context The context identifier.
	 * @param   mixed  &$data   The data to be processed. It gets altered directly.
	 *
	 * @param   string $group   group
	 *
	 * @throws Exception
	 * @since   Joomla 3.1
	 */
	protected function preprocessData($context, &$data, $group = 'kunena')
	{
		// Get the dispatcher and load the users plugins.

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('content');

		// Trigger the data preparation event.
		$results = Factory::getApplication()->triggerEvent('onContentPrepareData', array($context, $data));

		// Check for errors encountered while preparing the data.
		if (count($results) > 0 && in_array(false, $results, true))
		{
			Factory::getApplication()->enqueueMessage($results->getError());
		}
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.6
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.plugin';

		// Execute the parent method.
		parent::populateState();

		$app = Factory::getApplication('administrator');

		// Load the User state.
		$pk = $app->input->getInt('extension_id');
		$this->setState('plugin.id', $pk);
	}

	/**
	 * @param   \JForm $form  A form object.
	 * @param   mixed  $data  The data expected for the form.
	 * @param   string $group Form group.
	 *
	 * @return  mixed  True if successful.
	 * @throws    Exception if there is an error in the form event.
	 * @since   1.6
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		$folder  = $this->getState('item.folder');
		$element = $this->getState('item.element');
		$lang    = Factory::getLanguage();

		// Load the core and/or local language sys file(s) for the ordering field.
		$db    = Factory::getDbo();
		$query = 'SELECT element' .
			' FROM #__extensions' .
			' WHERE (type =' . $db->Quote('plugin') . 'AND folder=' . $db->Quote($folder) . ')';
		$db->setQuery($query);
		$elements = $db->loadColumn();

		foreach ($elements as $elementa)
		{
			$lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_PLUGINS . '/' . $folder . '/' . $elementa, null, false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_PLUGINS . '/' . $folder . '/' . $elementa, $lang->getDefault(), false, false);
		}

		if (empty($folder) || empty($element))
		{
			$app = Factory::getApplication();
			$app->redirect(Route::_('index.php?option=com_kunena&view=plugins', false));
		}

		$formFile = KunenaPath::clean(JPATH_PLUGINS . '/' . $folder . '/' . $element . '/' . $element . '.xml');

		if (!is_file($formFile))
		{
			throw new Exception(Text::sprintf('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
		}

		// Load the core and/or local language file(s).
		$lang->load('plg_' . $folder . '_' . $element, JPATH_ADMINISTRATOR, null, false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_PLUGINS . '/' . $folder . '/' . $element, null, false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_PLUGINS . '/' . $folder . '/' . $element, $lang->getDefault(), false, false);

		if (is_file($formFile))
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(Text::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			throw new Exception(Text::_('JERROR_LOADFILE_FAILED'));
		}

		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');

		if (!empty($help))
		{
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object $table A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition   = array();
		$condition[] = 'type = ' . $this->_db->Quote($table->type);
		$condition[] = 'folder = ' . $this->_db->Quote($table->folder);

		return $condition;
	}

	/**
	 * Custom clean cache method, plugins are cached in 2 places for different clients
	 *
	 * @since   1.6
	 *
	 * @param   null $group     group
	 *
	 * @param   int  $client_id client_id
	 *
	 * @since   Kunena
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_plugins');
	}
}
