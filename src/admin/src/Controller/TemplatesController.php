<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\Archive\Archive;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use function defined;

/**
 * Kunena Backend Templates Controller
 *
 * @since   Kunena 2.0
 */
class TemplatesController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 2.0
	 */
	protected $baseurl = null;

	/**
	 * @var     array
	 * @since   Kunena 2.0
	 */
	protected $locked = ['aurelia'];

	/**
	 * @var     object
	 * @since   Kunena 2.0
	 */
	private $config;

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
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
	}

	/**
	 * Publish
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function publish(): void
	{
		$cid = $this->app->input->get('cid', [], 'array');
		$id  = array_shift($cid);

		if (!Session::checkToken())
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
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function add()
	{
		if (!Session::checkToken())
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
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function edit($key = null, $urlVar = null)
	{
		$cid      = $this->app->input->get('cid', [], 'array');
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
		$this->app->setUserState('kunena.edit.templatename', $template);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=edit&name={$template}", false));
	}

	/**
	 * Install the new template
	 *
	 * @return  boolean|void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function install(): bool
	{
		$tmpKunena = KunenaPath::tmpdir() . '/kinstall/';
		$dest      = KPATH_SITE . '/template/';
		$file      = $this->app->input->files->get('install_package', null, 'raw');

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return false;
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
			$success = File::upload($file ['tmp_name'], $dest . $file ['name'], false, true);

			if ($success)
			{
				try
				{
					$archive = new Archive;
					$archive->extract($dest . $file ['name'], $tmpKunena);
				}
				catch (Exception $e)
				{
					$this->app->enqueueMessage(
						Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_FAILED', $this->escape($file['name'])),
						'notice'
					);
				}
			}

			if (is_dir($tmpKunena))
			{
				$templates = KunenaTemplateHelper::parseXmlFiles($tmpKunena);

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
							// Check that the template is comptatible with the actual Kunena version
							if (!KunenaTemplateHelper::templateIsKunenaCompatible($template->targetversion))
							{
								$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_COMPATIBLE_WITH_KUNENA_INSTALLED_VERSION', $template->name, $template->version), 'error');
							}

							if (is_file($dest . $template->directory . '/config/params.ini'))
							{
								if (is_file($tmpKunena . $template->sourcedir . '/config/params.ini'))
								{
									File::delete($tmpKunena . $template->sourcedir . '/config/params.ini');
								}

								File::move($dest . $template->directory . '/config/params.ini', $tmpKunena . $template->sourcedir . 'config/params.ini');
							}

							if (is_dir($dest . $template->directory . '/assets/images'))
							{
								if (is_dir($tmpKunena . $template->sourcedir . '/assets/images'))
								{
									Folder::delete($tmpKunena . $template->sourcedir . '/assets/images');
								}

								Folder::move($dest . $template->directory . '/assets/images', $tmpKunena . $template->sourcedir . '/assets/images');
							}

							if (is_file($dest . $template->directory . '/assets/less/custom.less'))
							{
								File::move($dest . $template->directory . '/assets/less/custom.less', $tmpKunena . $template->sourcedir . '/assets/less/custom.less');
							}

							if (is_file($dest . $template->directory . '/assets/css/custom.css'))
							{
								File::move($dest . $template->directory . '/assets/css/custom.css', $tmpKunena . $template->sourcedir . '/assets/css/custom.css');
							}

							Folder::delete($dest . $template->directory);
						}

						$success = Folder::move($tmpKunena . $template->sourcedir, $dest . $template->directory);

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
					if (is_dir($tmpKunena))
					{
						Folder::delete($tmpKunena);
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
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function uninstall(): void
	{
		$cid      = $this->app->input->get('cid', [], 'array');
		$id       = array_shift($cid);
		$template = $id;

		if (!Session::checkToken())
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
			// Clear all cache, just in case.
			KunenaCacheHelper::clearAll();
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL_SUCCESS', $id));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE') . ' ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Choose less
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function chooseLess(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);
		$this->app->setUserState('kunena.templatename', $templatename);

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $templatename . '/assets/less'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_LESS'), 'warning');

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseLess", false));
	}

	/**
	 * Edit Less
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function editless(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename');

		if (File::getExt($filename) !== 'less')
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_LESS'), 'warning');
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseLess&id=' . $template, false));
		}

		$this->app->setUserState('kunena.templatename', $templatename);
		$this->app->setUserState('kunena.editless.filename', $filename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=editless", false));
	}

	/**
	 * Choose Css
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function chooseCss(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);

		$this->app->setUserState('kunena.templatename', $templatename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseCss", false));
	}

	/**
	 * Apply less
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function applyLess(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename', '', 'cmd');
		$filecontent = $this->app->input->post->get('filecontent', '', 'raw');

		if (!Session::checkToken())
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
		$return = File::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
		}
		else
		{
			$this->app->enqueueMessage(
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file),
				'error'
			);
		}
	}

	/**
	 * Save Less
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function saveLess(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);

		$filename    = $this->app->input->get('filename');
		$filecontent = $this->app->input->get('filecontent', '', 'raw');

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$templatename)
		{
			$this->app->enqueueMessage(
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.')
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$file   = KPATH_SITE . '/template/' . $templatename . '/assets/less/' . $filename;
		$return = File::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseLess&id=' . $templatename, false));
		}
		else
		{
			$this->app->enqueueMessage(
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': '
				. Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file)
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseLess&id=' . $templatename, false));
		}
	}

	/**
	 * Edit Css
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function editCss(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);

		$filename = $this->app->input->get('filename');

		if (File::getExt($filename) !== 'css')
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_CSS'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=chooseCss&id=' . $templatename, false));
		}

		$this->app->setUserState('kunena.editCss.tmpl', $templatename);
		$this->app->setUserState('kunena.editCss.filename', $filename);

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=editCss", false));
	}

	/**
	 * Apply Css
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function applyCss(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);
		$filename     = $this->app->input->get('filename');
		$filecontent  = $this->app->input->get('filecontent', '', 'raw');

		if (!Session::checkToken())
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
		$return = File::write($file, $filecontent);

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
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function saveCss(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);
		$filename     = $this->app->input->get('filename');
		$filecontent  = $this->app->input->get('filecontent', '', 'raw');

		if (!Session::checkToken())
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
		$return = File::write($file, $filecontent);

		if ($return)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseCss", false));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
			$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseCss", false));
		}
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @param   null  $key  key
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 3.0.5
	 */
	public function cancel($key = null)
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
