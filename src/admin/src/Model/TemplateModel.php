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

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use stdClass;

/**
 * template Model for Kunena
 *
 * @since  6.0
 */
class TemplateModel extends AdminModel
{
	/**
	 * @see     \Joomla\CMS\MVC\Model\FormModel::getForm()
	 *
	 * @param   bool   $loadData  loadData
	 *
	 * @param   array  $data      data
	 *
	 * @return object
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public function getForm($data = [], $loadData = true): object
	{
		$app = Factory::getApplication();

		// Load the configuration definition file.
		$template = $this->getState('template');
		$xml      = KunenaTemplate::getInstance($template)->getConfigXml();

		// Get the form.
		try
		{
			$form = $this->loadForm('com_kunena.template', $xml, ['control' => 'jform', 'load_data' => $loadData, 'file' => false], true, '//config');
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * @return  boolean|stdClass
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getTemplateDetails()
	{
		$app = Factory::getApplication();

		$template = $app->getUserState('kunena.edit.template');
		$details  = KunenaTemplateHelper::parseXmlFile((string) $template);

		if (empty($template))
		{
			$template = $this->getState('template');
			$details  = KunenaTemplateHelper::parseXmlFile((string) $template);
		}

		return $details;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   null  $ordering   ordering
	 * @param   null  $direction  direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		$context = 'com_kunena.admin.templates';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$context .= '.' . $layout;
		}

		// Edit state information
		$value = $this->getUserStateFromRequest($context . '.edit', 'name', '', 'cmd');
		$this->setState('template', $value);

		if (empty($app->getUserState('kunena.edit.templatename')))
		{
			$app->setUserState('kunena.edit.templatename', $value);
		}
	}

	/**
	 * @param   string  $key        key
	 * @param   string  $request    request
	 * @param   null    $default    default
	 * @param   string  $type       type
	 * @param   bool    $resetPage  resetPage
	 *
	 * @return  mixed|null
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getUserStateFromRequest(string $key, string $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app      = Factory::getApplication();
		$input    = $app->input;
		$oldState = $app->getUserState($key);
		$curState = ($oldState !== null) ? $oldState : $default;
		$newState = $input->get($request, null, $type);

		if (($curState != $newState) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($newState !== null)
		{
			$app->setUserState($key, $newState);
		}
		else
		{
			$newState = $curState;
		}

		return $newState;
	}

	/**
	 * Load the data associated with the form
	 *
	 * @return array|mixed
	 * @throws Exception
	 * @since Kunena 3.0
	 * @see   \Joomla\CMS\MVC\Model\FormModel::loadFormData()
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
}
