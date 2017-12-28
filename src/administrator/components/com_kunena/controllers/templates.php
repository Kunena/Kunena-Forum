<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.filesystem.archive');

/**
 * Kunena Backend Templates Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerTemplates extends KunenaController
{
	/**
	 * @var null|string
	 *
	 * @since    2.0
	 */
	protected $baseurl = null;

	/**
	 * @var array
	 *
	 * @since    2.0
	 */
	protected $locked = array('crypsis');

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since    2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
	}

	/**
	 * Publish
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function publish()
	{
		$cid = $this->app->input->get('cid', array(), 'method', 'array');
		$id  = array_shift($cid);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if ($id)
		{
			$this->config->template = $id;
			$this->config->save();
		}

		$template = KunenaFactory::getTemplate($id);
		$template->clearCache();

		$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT_SELECTED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Add
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function add()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=add", false));
	}

	/**
	 * Edit
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function edit()
	{
		$cid      = $this->app->input->get('cid', array(), 'method', 'array');
		$template = array_shift($cid);

		if (!$template)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));

			return;
		}

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $template))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_FOUND'));

			return;
		}

		$template = KunenaPath::clean($template);
		$this->app->setUserState('kunena.edit.template', $template);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=edit&name={$template}", false));
	}

	/**
	 * Install
	 *
	 * @return boolean
	 *
	 * @since    2.0
	 */
	public function install()
	{
		$tmp  = JPATH_ROOT . '/tmp/';
		$tmp_kunena = JPATH_ROOT . '/tmp/kinstall/';
		$dest = KPATH_SITE . '/template/';
		$file = $this->app->input->files->get('install_package', null, 'raw');

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error']))
		{
			$this->app->enqueueMessage(
				JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_MISSING', $this->escape($file['name'])),
				'notice'
			);
		}
		else
		{
			$success = KunenaFile::upload($file ['tmp_name'], $tmp . $file ['name'], false, true);

			if ($success)
			{
				try
				{
					JArchive::extract($tmp . $file ['name'], $tmp_kunena);
				}
				catch (Exception $e)
				{
					$this->app->enqueueMessage(
						JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_FAILED', $this->escape($file['name'])),
						'notice'
					);
				}
			}

			if (is_dir($tmp_kunena))
			{
				$templates = KunenaTemplateHelper::parseXmlFiles($tmp_kunena);

				if (!empty($templates))
				{
					foreach ($templates as $template)
					{
						// Never overwrite locked templates
						if (in_array($template->directory, $this->locked))
						{
							continue;
						}

						if (is_dir($dest . $template->directory))
						{
							if (is_file($dest . $template->directory . '/params.ini'))
							{
								if (is_file($tmp_kunena . $template->sourcedir . '/params.ini'))
								{
									KunenaFile::delete($tmp_kunena . $template->sourcedir . '/params.ini');
								}

								KunenaFile::move($dest . $template->directory . '/config/params.ini', $tmp_kunena . $template->sourcedir . '/params.ini');
							}

							KunenaFolder::delete($dest . $template->directory);
						}

						$success = KunenaFolder::move($tmp_kunena . $template->sourcedir, $dest . $template->directory);

						if ($success !== true)
						{
							$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_FAILED', $template->directory), 'notice');
						}
						else
						{
							$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_SUCCESS', $template->directory));
						}
					}

					// Delete the tmp install directory
					if (is_dir($tmp_kunena))
					{
						KunenaFolder::delete($tmp_kunena);
					}

					// Clear all cache, just in case.
					KunenaCacheHelper::clearAll();
				}
				else
				{
					$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_MISSING_FILE'), 'error');
				}
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'), 'error');
			}
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Uninstall
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function uninstall()
	{
		$cid      = $this->app->input->get('cid', array(), 'method', 'array');
		$id       = array_shift($cid);
		$template = $id;

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Initialize variables
		$otemplate = KunenaTemplateHelper::parseXmlFile($id);

		if (!$otemplate)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (in_array($id, $this->locked))
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_SYSTEM_TEMPLATE', $otemplate->name), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (KunenaTemplateHelper::isDefault($template))
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_DEFAULT_TEMPLATE', $otemplate->name), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$tpl = KPATH_SITE . '/template/' . $template;

		// Delete the template directory
		if (is_dir($tpl))
		{
			$retval = KunenaFolder::delete($tpl);

			// Clear all cache, just in case.
			KunenaCacheHelper::clearAll();
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL_SUCCESS', $id));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'));
			$retval = false;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Choose less
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function chooseless()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$this->app->setUserState('kunena.templatename', $templatename);

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $templatename . '/assets/less'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_LESS'), 'warning');

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseless", false));
	}

	/**
	 * Edit Less
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function editless()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename', '', 'method', 'cmd');

		if (KunenaFile::getExt($filename) !== 'less')
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_LESS'), 'warning');
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseless&id=' . $template, false));
		}

		$this->app->setUserState('kunena.templatename', $templatename);
		$this->app->setUserState('kunena.editless.filename', $filename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=editless", false));
	}

	/**
	 * Choose Css
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function choosecss()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$this->app->setUserState('kunena.templatename', $templatename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
	}

	/**
	 * Apply less
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function applyless()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename', '', 'post', 'cmd');
		$filecontent = $this->app->input->post->get('filecontent', '', 'raw');

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file = KPATH_SITE . '/template/' . $templatename . '/assets/less/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file), 'error');
		}
	}

	/**
	 * Save Less
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function saveless()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename', '', 'post', 'cmd');
		$filecontent = $this->app->input->get('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file = KPATH_SITE . '/template/' . $templatename . '/assets/less/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseless&id=' . $templatename, false));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseless&id=' . $templatename, false));
		}
	}

	/**
	 * Edit Css
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function editcss()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename', '', 'method', 'cmd');

		if (KunenaFile::getExt($filename) !== 'css')
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_CSS'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=choosecss&id=' . $templatename, false));
		}

		$this->app->setUserState('kunena.editcss.tmpl', $templatename);
		$this->app->setUserState('kunena.editcss.filename', $filename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=editcss", false));
	}

	/**
	 * Apply Css
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function applycss()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$filename    = $this->app->input->get('filename', '', 'post', 'cmd');
		$filecontent = $this->app->input->get('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file = KPATH_SITE . '/template/' . $templatename . '/assets/css/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
		}
	}

	/**
	 * Save Css
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function savecss()
	{
		$template = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$filename    = $this->app->input->get('filename', '', 'post', 'cmd');
		$filecontent = $this->app->input->get('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file = KPATH_SITE . '/template/' . $templatename . '/assets/css/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
		}
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @since    2.0
	 */
	public function apply()
	{
		$template = $this->app->input->get('templatename', '', 'method', 'cmd');
		$menus    = $this->app->input->get('selections', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($menus);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=edit&cid[]=' . $template, false));
	}

	/**
	 * Save
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function save()
	{
		$template = $this->app->input->get('templatename', '', 'method', 'cmd');
		$menus    = $this->app->input->get('selections', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($menus);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save param.ini file on filesystem.
	 *
	 * @param   string  $template  The name of the template.
	 *
	 * @return void
	 *
	 * @since  3.0.0
	 */
	protected function _saveParamFile($template)
	{
		$params = $this->app->input->get('jform', array(), 'post', 'array');

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp  = JClientHelper::getCredentials('ftp');
		$file = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (count($params))
		{
			$registry = new JRegistry;
			$registry->loadArray($params);
			$txt    = $registry->toString('INI');
			$return = KunenaFile::write($file, $txt);

			if (!$return)
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE', $file));
				$this->app->redirect(KunenaRoute::_($this->baseurl, false));
			}
		}
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since 3.0.5
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}

