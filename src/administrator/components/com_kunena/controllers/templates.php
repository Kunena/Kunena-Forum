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

jimport('joomla.filesystem.archive');

use Joomla\Archive\Archive;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

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
	 * @param   array $config config
	 *
	 * @throws Exception
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
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function publish()
	{
		$cid = $this->app->input->get('cid', array(), 'method', 'array');
		$id  = array_shift($cid);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
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

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT_SELECTED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Add
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function add()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
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
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function edit()
	{
		$cid      = $this->app->input->get('cid', array(), 'method', 'array');
		$template = array_shift($cid);

		if (!$template)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));

			return;
		}

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $template))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_FOUND'));

			return;
		}

		$template = KunenaPath::clean($template);
		$this->app->setUserState('kunena.edit.template', $template);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=edit&name={$template}", false));
	}

	/**
	 * Install the new template
	 *
	 * @return boolean|void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function install()
	{
		$tmp        = JPATH_ROOT . '/tmp/';
		$tmp_kunena = JPATH_ROOT . '/tmp/kinstall/';
		$dest       = KPATH_SITE . '/template/';
		$file       = $this->app->input->files->get('install_package', null, 'raw');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error']))
		{
			$this->app->enqueueMessage(
				Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_MISSING', $this->escape($file['name'])),
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
					$archive = new Archive;
					$archive->extract($tmp . $file ['name'], $tmp_kunena);
				}
				catch (Exception $e)
				{
					$this->app->enqueueMessage(
						Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_FAILED', $this->escape($file['name'])),
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

						// Check that the template is comptatible with the actual Kunena version
						if (!KunenaTemplateHelper::templateIsKunenaCompatible($template->targetversion))
						{
							$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_COMPATIBLE_WITH_KUNENA_INSTALLED_VERSION', $template->name, $template->version), 'error');
						}

						if (is_dir($dest . $template->directory))
						{
							if (is_file($dest . $template->directory . '/config/params.ini'))
							{
								if (is_file($tmp_kunena . $template->sourcedir . '/config/params.ini'))
								{
									KunenaFile::delete($tmp_kunena . $template->sourcedir . '/config/params.ini');
								}

								KunenaFile::move($dest . $template->directory . '/config/params.ini', $tmp_kunena . $template->sourcedir . 'config/params.ini');
							}

							if (is_dir($dest . $template->directory . '/assets/images'))
							{
								if (is_dir($tmp_kunena . $template->sourcedir . '/assets/images'))
								{
									KunenaFolder::delete($tmp_kunena . $template->sourcedir . '/assets/images');
								}

								KunenaFolder::move($dest . $template->directory . '/assets/images', $tmp_kunena . $template->sourcedir . '/assets/images');
							}

							if (is_file($dest . $template->directory . '/assets/less/custom.less'))
							{
								KunenaFile::move($dest . $template->directory . '/assets/less/custom.less', $tmp_kunena . $template->sourcedir . '/assets/less/custom.less');
							}

							if (is_file($dest . $template->directory . '/assets/css/custom.css'))
							{
								KunenaFile::move($dest . $template->directory . '/assets/css/custom.css', $tmp_kunena . $template->sourcedir . '/assets/css/custom.css');
							}

							KunenaFolder::delete($dest . $template->directory);
						}

						$success = KunenaFolder::move($tmp_kunena . $template->sourcedir, $dest . $template->directory);

						if ($success !== true)
						{
							$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_FAILED', $template->directory), 'notice');
						}
						else
						{
							$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_SUCCESS', $template->directory));
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
					$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_MISSING_FILE'), 'error');
				}
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'), 'error');
			}
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Uninstall
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function uninstall()
	{
		$cid      = $this->app->input->get('cid', array(), 'method', 'array');
		$id       = array_shift($cid);
		$template = $id;

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Initialize variables
		$otemplate = KunenaTemplateHelper::parseXmlFile($id);

		if (!$otemplate)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (in_array($id, $this->locked))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_SYSTEM_TEMPLATE', $otemplate->name), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (KunenaTemplateHelper::isDefault($template))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_DEFAULT_TEMPLATE', $otemplate->name), 'error');
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
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL_SUCCESS', $id));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'));
			$retval = false;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Choose less
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function chooseless()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$this->app->setUserState('kunena.templatename', $templatename);

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $templatename . '/assets/less'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_LESS'), 'warning');

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseless", false));
	}

	/**
	 * Edit Less
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function editless()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename', '', 'method', 'cmd');

		if (KunenaFile::getExt($filename) !== 'less')
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_LESS'), 'warning');
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
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function choosecss()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$this->app->setUserState('kunena.templatename', $templatename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
	}

	/**
	 * Apply less
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function applyless()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename', '', 'post', 'cmd');
		$filecontent = $this->app->input->post->get('filecontent', '', 'raw');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = KPATH_SITE . '/template/' . $templatename . '/assets/less/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file), 'error'
			);
		}
	}

	/**
	 * Save Less
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function saveless()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename', '', 'post');
		$filecontent = $this->app->input->get('filecontent', '', 'post');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.')
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = KPATH_SITE . '/template/' . $templatename . '/assets/less/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseless&id=' . $templatename, false));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file)
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseless&id=' . $templatename, false));
		}
	}

	/**
	 * Edit Css
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function editcss()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename', '', 'method', 'cmd');

		if (KunenaFile::getExt($filename) !== 'css')
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_CSS'));
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
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function applycss()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$filename     = $this->app->input->get('filename', '', 'post');
		$filecontent  = $this->app->input->get('filecontent', '', 'post');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = KPATH_SITE . '/template/' . $templatename . '/assets/css/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
		}
	}

	/**
	 * Save Css
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function savecss()
	{
		$template     = $this->app->input->getArray(array('cid' => ''));
		$templatename = array_shift($template['cid']);
		$filename     = $this->app->input->get('filename', '', 'post');
		$filecontent  = $this->app->input->get('filecontent', '', 'post');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = KPATH_SITE . '/template/' . $templatename . '/assets/css/' . $filename;
		$return = KunenaFile::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=choosecss", false));
		}
	}

	/**
	 * Apply changes on template parameters
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since    2.0
	 */
	public function apply()
	{
		$template = $this->app->input->get('templatename', '', 'method', 'cmd');
		$menus    = $this->app->input->get('selections', array(), 'post', 'array');
		$menus    = ArrayHelper::toInteger($menus);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=edit&cid[]=' . $template, false));
	}

	/**
	 * Generate the params for CKeditor
	 * 
	 * @param array $params
	 * @since  5.2.6
	 * @return string
	 */
	protected function generateCkeditorParamFile($params)
	{
		$ckeditorSettings = array();

		if (!$params['Bold'])
		{
			$ckeditorSettings[] = 'Bold';
		}

		if (!$params['Italic'])
		{
			$ckeditorSettings[] = 'Italic';
		}

		if (!$params['Underline'])
		{
			$ckeditorSettings[] = 'Underline';
		}

		if (!$params['Strike'])
		{
			$ckeditorSettings[] = 'Strike';
		}

		if (!$params['Superscript'])
		{
			$ckeditorSettings[] = 'Superscript';
		}

		if (!$params['Subscript'])
		{
			$ckeditorSettings[] = 'Subscript';
		}

		if (!$params['JustifyRight'])
		{
			$ckeditorSettings[] = 'JustifyRight';
		}

		if (!$params['JustifyLeft'])
		{
			$ckeditorSettings[] = 'JustifyLeft';
		}

		if (!$params['JustifyBlock'])
		{
			$ckeditorSettings[] = 'JustifyBlock';
		}

		if (!$params['JustifyCenter'])
		{
			$ckeditorSettings[] = 'JustifyCenter';
		}

		if (!$params['RemoveFormat'])
		{
			$ckeditorSettings[] = 'RemoveFormat';
		}

		if (!$params['Confidential'])
		{
			$ckeditorSettings[] = 'Confidential';
		}

		if (!$params['Hidetext'])
		{
			$ckeditorSettings[] = 'Hidetext';
		}

		if (!$params['Spoiler'])
		{
			$ckeditorSettings[] = 'Spoiler';
		}

		if (!$params['Smiley'])
		{
			$ckeditorSettings[] = 'Smiley';
		}

		if (!$params['Ebay'])
		{
			$ckeditorSettings[] = 'Ebay';
		}

		if (!$params['Twitter'])
		{
			$ckeditorSettings[] = 'Twitter';
		}

		if (!$params['Instagram'])
		{
			$ckeditorSettings[] = 'Instagram';
		}

		if (!$params['Soundcloud'])
		{
			$ckeditorSettings[] = 'Soundcloud';
		}

		if (!$params['Map'])
		{
			$ckeditorSettings[] = 'Map';
		}

		if (!$params['FontSize'])
		{
			$ckeditorSettings[] = 'FontSize';
		}

		if (!$params['TextColor'])
		{
			$ckeditorSettings[] = 'TextColor';
		}

		if (!$params['Maximize'])
		{
			$ckeditorSettings[] = 'Maximize';
		}

		if (!$params['Image'])
		{
			$ckeditorSettings[] = 'Image';
		}

		if (!$params['Video'])
		{
			$ckeditorSettings[] = 'Video';
		}

		if (!$params['Link_Unlink'])
		{
			$ckeditorSettings[] = 'Link,Unlink';
		}

		if (!$params['BulletedList'])
		{
			$ckeditorSettings[] = 'BulletedList';
		}

		if (!$params['NumberedList'])
		{
			$ckeditorSettings[] = 'NumberedList';
		}

		if (!$params['Blockquote'])
		{
			$ckeditorSettings[] = 'Blockquote';
		}

		if (!$params['Code'])
		{
			$ckeditorSettings[] = 'Code';
		}

		if (!empty($params['nameskinckeditor']))
		{
			if (!JFolder::exists(KPATH_MEDIA . '/core/js/skins/' . $params['nameskinckeditor']))
			{
				$params['nameskinckeditor'] = '';
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CANNOT_FIND_CKEDITOR_SKIN'),'error');
			}
		}

		if (!empty($params['ckeditorcustomprefixconfigfile']))
		{
			if (!JFile::exists(KPATH_MEDIA . '/core/js/' . $params['ckeditorcustomprefixconfigfile'] . 'ckeditor_config.js'))
			{
				$params['ckeditorcustomprefixconfigfile'] = '';
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CANNOT_FIND_CKEDITOR_CUSTOM_CONFIG_FILE'),'error');
			}
		}

		if (count($params) > 0)
		{
			if (count($ckeditorSettings) > 0)
			{
				$ckeditorSettings = implode(',', $ckeditorSettings);
			}
			else
			{
				$ckeditorSettings = '';
			}

			return $ckeditorSettings;
		}

		return $params['ckeditorParams'] = '';
	}

	/**
	 * Generate the params fr othe legacy editor
	 * 
	 * @param array $params
	 * @since  5.2.6
	 * @return string
	 */
	protected function generateLegacyParamFile($params)
	{
		$wysibbParams = '';

		if ($params['Bold'])
		{
	        $wysibbParams .= 'bold,';
		}

		if ($params['Italic'])
		{
	        $wysibbParams .= 'italic,';
		}

		if ($params['Underline'])
		{
	        $wysibbParams .= 'underline,';
		}

		if ($params['Strike'])
		{
	        $wysibbParams .= 'strike,';
		}

		if ($params['Superscript'])
		{
	        $wysibbParams .= 'sup,';
		}

		if ($params['Subscript'])
		{
	        $wysibbParams .= 'sub';
		}

		if ($params['JustifyLeft'])
		{
	        $wysibbParams .= 'justifyleft,';
		}

		if ($params['JustifyCenter'])
		{
	        $wysibbParams .= 'justifycenter,';
		}

		if ($params['JustifyRight'])
		{
	        $wysibbParams .= 'justifyright,';
	    }
	    
	    /*if ($params['divider'])
	    {
	        $wysibbParams .= '|,';
	    }*/

		if ($params['Image'])
		{
	        $wysibbParams .= 'img,';
		}

		if ($params['Video'])
		{
	        $wysibbParams .= 'video,';
	    }

		if ($params['Link_Unlink'])
	    {
	        $wysibbParams .= 'link,';
	    }
	    
	    /*if ($params['divider'])
	    {
	        $wysibbParams .= '|,';
	    }*/
	    
		if ($params['BulletedList'])
		{
	        $wysibbParams .= 'bullist,';
		}

		if ($params['NumberedList'])
		{
	        $wysibbParams .= 'numlist,';
		}
	    
	    /*if ($params['divider'])
	    {
	        $wysibbParams .= '|,';
	    }*/
	    
	    if ($params['TextColor'])
	    {
	        $params['wysibb'] .= 'fontcolor,';
	    }
	    
	    /*if ($params['wysibb'])
	    {
	        $wysibbParams .= 'fontsize,';
	    }
	    
	    if ($params['wysibb'])
	    {
	        $wysibbParams .= 'fontfamily,';
	    }
	    
	    if ($params['divider'])
	    {
	        $wysibbParams .= '|,';
	    }*/
	    
	    if ($params['Blockquote'])
	    {
	        $wysibbParams .= 'quote,';
	    }

	    if ($params['Code'])
	    {
	        $wysibbParams .= 'code,';
	    }
	    
	    /*if ($params['table'])
	    {
	        $wysibbParams .= 'table,';
	    }*/

		if ($params['RemoveFormat'])
		{
			$wysibbParams .= 'removeFormat';
		}

		return $wysibbParams;
	}

	/**
	 * Method to save param.ini file on filesystem.
	 *
	 * @param   string $template The name of the template.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  3.0.0
	 * @throws null
	 */
	protected function _saveParamFile($template)
	{
		$params = $this->app->input->get('jform', array(), 'post', 'array');

		if ($params['editor'] == 'ckeditor')
		{
			$params['ckeditorParams'] = $this->generateCkeditorParamFile($params);
		}
		else
		{
			$params['wysibbParams'] = $this->generateLegacyParamFile($params);
		}

		$file = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (count($params) > 0)
		{
			$registry = new \Joomla\Registry\Registry;
			$registry->loadArray($params);
			$txt    = $registry->toString('INI');
			$return = KunenaFile::write($file, $txt);

			if (!$return)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE', $file));
				$this->app->redirect(KunenaRoute::_($this->baseurl, false));
			}
		}
	}

	/**
	 * Save template parameters
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 */
	public function save()
	{
		$template = $this->app->input->get('templatename', '', 'method', 'cmd');
		$menus    = $this->app->input->get('selections', array(), 'post', 'array');
		$menus    = ArrayHelper::toInteger($menus);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to restore the default settings of the template selected
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since 5.1
	 */
	public function restore()
	{
		$template = $this->app->input->get('templatename', '', 'method', 'cmd');
		$file     = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (file_exists($file))
		{
			$result = JFile::delete($file);

			if ($result)
			{
				KunenaFile::write($file, '');
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_TEMPLATES_SETTINGS_RESTORED_SUCCESSFULLY'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since 3.0.5
	 * @throws null
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
