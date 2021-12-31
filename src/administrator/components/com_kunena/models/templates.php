<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

jimport('joomla.application.component.modeladmin');
jimport('joomla.html.pagination');

/**
 * Templates Model for Kunena
 *
 * @since  2.0
 */
class KunenaAdminModelTemplates extends \Joomla\CMS\MVC\Model\AdminModel
{
	/**
	 * @param   array  $config  config
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->app    = Factory::getApplication();
		$this->me     = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();

	}

	/**
	 * @see   \Joomla\CMS\MVC\Model\FormModel::getForm()
	 *
	 * @param   array  $data      data
	 * @param   bool   $loadData  loadData
	 *
	 * @return boolean|mixed
	 * @since Kunena
	 * @throws Exception
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Load the configuration definition file.
		$template = $this->getState('template');
		$xml      = KunenaTemplate::getInstance($template)->getConfigXml();

		// Get the form.
		$form = $this->loadForm('com_kunena_template', $xml, array('control' => 'jform', 'load_data' => $loadData, 'file' => false), true, '//config');

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * @return array
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getTemplates()
	{
		// Get template xml file info
		$rows = KunenaTemplateHelper::parseXmlFiles();

		// Set dynamic template information
		foreach ($rows as $row)
		{
			$row->published = KunenaTemplateHelper::isDefault($row->directory);
		}

		$this->setState('list.total', count($rows));

		if ($this->getState('list.limit'))
		{
			$rows = array_slice($rows, $this->getState('list.start'), $this->getState('list.limit'));
		}

		return $rows;
	}

	/**
	 * @return boolean|stdClass
	 * @since Kunena
	 */
	public function getTemplatedetails()
	{
		$template = $this->app->getUserState('kunena.edit.template');
		$details  = KunenaTemplateHelper::parseXmlFile($template);

		if (empty($template))
		{
			$template = $this->getState('template');
			$details  = KunenaTemplateHelper::parseXmlFile($template);
		}

		return $details;
	}

	/**
	 * @return boolean|null|string
	 * @since Kunena
	 */
	public function getFileLessParsed()
	{
		$template = $this->app->getUserState('kunena.templatename');
		$filename = $this->app->getUserState('kunena.editless.filename');

		$content = file_get_contents(KPATH_SITE . '/template/' . $template . '/assets/less/' . $filename);
		$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

		if ($content === false)
		{
			return;
		}

		return $content;
	}

	/**
	 * @return boolean|null|string
	 * @since Kunena
	 */
	public function getFileContentParsed()
	{
		$template = $this->app->getUserState('kunena.templatename');
		$filename = $this->app->getUserState('kunena.editcss.filename');
		$content  = file_get_contents(KPATH_SITE . '/template/' . $template . '/assets/css/' . $filename);

		if ($content === false)
		{
			return;
		}

		$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

		return $content;
	}

	/**
	 * @return mixed
	 *
	 * @since Kunena
	 */
	public function getFTPcredentials()
	{
		// Set FTP credentials, if given
		$ftp = \Joomla\CMS\Client\ClientHelper::setCredentialsFromRequest('ftp');

		return $ftp;
	}

	/**
	 * @return mixed
	 *
	 * @since Kunena
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create the pagination object.
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page  = new \Joomla\CMS\Pagination\Pagination($this->getTotal(), $this->getStart(), $limit);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}

	/**
	 * @param   string  $id  id
	 *
	 * @return string
	 * @since Kunena
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	/**
	 * @return object
	 * @since Kunena
	 */
	public function getTotal()
	{
		return $this->getState('list.total');
	}

	/**
	 * @return object
	 * @since Kunena
	 */
	public function getStart()
	{
		return $this->getState('list.start');
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.templates';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		// Edit state information
		$value = $this->getUserStateFromRequest($this->context . '.edit', 'name', '', 'cmd');
		$this->setState('template', $value);

		// List state information
		$value = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
		$this->setState('list.limit', $value);

		$value = $this->getUserStateFromRequest($this->context . '.list.ordering', 'filter_order', 'ordering', 'cmd');
		$this->setState('list.ordering', $value);

		$value = $this->getUserStateFromRequest($this->context . '.list.start', 'limitstart', 0, 'int');
		$this->setState('list.start', $value);
	}

	/**
	 * @param           $key
	 * @param           $request
	 * @param   null    $default    default
	 * @param   string  $type       type
	 * @param   bool    $resetPage  resetPage
	 *
	 * @return mixed|null
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app       = Factory::getApplication();
		$input     = $app->input;
		$old_state = $app->getUserState($key);
		$cur_state = ($old_state !== null) ? $old_state : $default;
		$new_state = $input->get($request, null, $type);

		if (($cur_state != $new_state) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null)
		{
			$app->setUserState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * @see   \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 * @return array|mixed
	 * @since Kunena
	 * @throws Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_kunena.edit.template.data', array());

		if (empty($data))
		{
			$template = $this->getState('template');
			$data     = KunenaTemplate::getInstance($template)->params->toArray();
		}

		return $data;
	}

	/**
	 * Method to retrieve the list of premium templates from kunena.org by loading the xml file and create from xml file a stdClass
	 *
	 * @return boolean|stdClass|array
	 *
	 * @since Kunena 5.1
	 */
	protected function loadTemplatesXml()
	{
		$this->template = array();

		$url = 'https://update.kunena.org/templates.xml';

		$options = new Joomla\Registry\Registry;

		try
		{
			$transport = new Joomla\CMS\Http\Transport\StreamTransport($options);
		}
		catch (Exception $e) 
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		if (!$transport->isSupported())
		{
			return false;
		}

		// Create a 'stream' transport.
		$http = new Joomla\CMS\Http\Http($options, $transport);

		try
		{
			$response = $http->get($url);
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		if ($response->code == '200')
		{
			$xml = simplexml_load_string($response->body);

			if ($xml)
			{
				foreach ($xml->templates as $template)
				{
					foreach ($template as $temp)
					{
						$attributes                  = $temp->attributes();
						$temp                        = new stdClass;
						$temp->name                  = (string) $attributes->name;
						$temp->type                  = (string) $attributes->element;
						$temp->created               = (string) $attributes->created;
						$temp->author                = (string) $attributes->author;
						$temp->version               = (string) $attributes->version;
						$temp->description           = (string) $attributes->description;
						$temp->detailsurl            = (string) $attributes->detailsurl;
						$temp->price                 = (string) $attributes->price;
						$temp->thumbnail             = (string) $attributes->thumbnail;
						$temp->authorurl             = (string) $attributes->authorurl;
						$temp->authoremail           = (string) $attributes->authoremail;
						$this->template[$temp->name] = $temp;
					}
				}

				return $this->template;
			}
		}
		else 
		{
			return false;
		}
	}

	/**
	 * Method to call loadTemplatesXml() to return the details of premium templates
	 * 
	 * @return stdClass
	 *
	 * @since Kunena 5.1
	 * @throws Exception
	 */
	public function getTemplatesxml()
	{
		// Get template xml file info
		$rows = self::loadTemplatesXml();

		return $rows;
	}
}
