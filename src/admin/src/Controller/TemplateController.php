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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;

/**
 * Kunena Template Controller
 *
 * @since   Kunena 6.0
 */
class TemplateController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * @var     KunenaConfig
	 * @since   Kunena 6.0
	 */
	public $config = null;

	/**
	 * Constructor.
	 *
	 * @param   MVCFactoryInterface|null  $factory  The factory.
	 * @param   null                      $app      The CMSApplication for the dispatcher
	 * @param   null                      $input    Input
	 *
	 * @param   array                     $config   An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 * @since   Kunena 2.0
	 *
	 * @see     BaseController
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
		$this->config  = KunenaFactory::getConfig();
	}

	/**
	 * Save template settings
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function save($key = null, $urlVar = null)
	{
		$template = $this->app->input->get('templatename', '');
		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' .
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED')
			);

			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->internalSaveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save param.ini file on filesystem.
	 *
	 * @param   string  $template  The name of the template.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @throws null
	 * @since   Kunena 3.0
	 */
	protected function internalSaveParamFile(string $template): void
	{
		$params = $this->app->input->get('jform', [], 'post', 'array');

		if ($params['editorType']=='ckeditor')
		{
			$editorButtons = $this->SaveCKeditorParamFile($params);
		}
		else
		{
			$editorButtons = $this->SaveSCeditorParamFile($params);
		}

		if (!empty($params['nameskinckeditor']))
		{
			if (!Folder::exists(KPATH_MEDIA . '/core/js/skins/' . $params['nameskinckeditor']))
			{
				$params['nameskinckeditor'] = '';
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CANNOT_FIND_CKEDITOR_SKIN'), 'error');
			}
		}

		if (!empty($params['ckeditorcustomprefixconfigfile']))
		{
			if (!File::exists(KPATH_MEDIA . '/core/js/' . $params['ckeditorcustomprefixconfigfile'] . 'ckeditor_config.js'))
			{
				$params['ckeditorcustomprefixconfigfile'] = '';
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CANNOT_FIND_CKEDITOR_CUSTOM_CONFIG_FILE'), 'error');
			}
		}

		$file = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (\count($params) > 0)
		{
			if (\count($editorButtons) > 0)
			{
				$editorButtons           = implode(',', $editorButtons);
				$params['editorButtons'] = $editorButtons;
			}
			else
			{
				$params['editorButtons'] = '';
			}

			$registry = new Registry;
			$registry->loadArray($params);
			$txt    = $registry->toString('INI');
			$return = File::write($file, $txt);

			if (!$return)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE', $file));
				$this->app->redirect(KunenaRoute::_($this->baseurl, false));
			}
		}
	}

	/**
	 * Save param into ini file for CKeditor
	 *
	 * @return  array
	 *
	 * @since   Kunena 2.0
	 *@throws  Exception
	 * @throws  null
	 */
	protected function SaveCKeditorParamFile(array $params)
	{
		$editorButtons = [];

		if (!$params['Bold'])
		{
			$editorButtons[] = 'Bold';
		}

		if (!$params['Italic'])
		{
			$editorButtons[] = 'Italic';
		}

		if (!$params['Underline'])
		{
			$editorButtons[] = 'Underline';
		}

		if (!$params['Strike'])
		{
			$editorButtons[] = 'Strike';
		}

		if (!$params['Superscript'])
		{
			$editorButtons[] = 'Superscript';
		}

		if (!$params['Subscript'])
		{
			$editorButtons[] = 'Subscript';
		}

		if (!$params['JustifyRight'])
		{
			$editorButtons[] = 'JustifyRight';
		}

		if (!$params['JustifyLeft'])
		{
			$editorButtons[] = 'JustifyLeft';
		}

		if (!$params['JustifyBlock'])
		{
			$editorButtons[] = 'JustifyBlock';
		}

		if (!$params['JustifyCenter'])
		{
			$editorButtons[] = 'JustifyCenter';
		}

		if (!$params['RemoveFormat'])
		{
			$editorButtons[] = 'RemoveFormat';
		}

		if (!$params['Confidential'])
		{
			$editorButtons[] = 'Confidential';
		}

		if (!$params['Hidetext'])
		{
			$editorButtons[] = 'Hidetext';
		}

		if (!$params['Spoiler'])
		{
			$editorButtons[] = 'Spoiler';
		}

		if (!$params['Smiley'])
		{
			$editorButtons[] = 'Smiley';
		}

		if (!$params['Ebay'])
		{
			$editorButtons[] = 'Ebay';
		}

		if (!$params['Twitter'])
		{
			$editorButtons[] = 'Twitter';
		}

		if (!$params['Instagram'])
		{
			$editorButtons[] = 'Instagram';
		}

		if (!$params['Soundcloud'])
		{
			$editorButtons[] = 'Soundcloud';
		}

		if (!$params['Map'])
		{
			$editorButtons[] = 'Map';
		}

		if (!$params['FontSize'])
		{
			$editorButtons[] = 'FontSize';
		}

		if (!$params['TextColor'])
		{
			$editorButtons[] = 'TextColor';
		}

		if (!$params['Maximize'])
		{
			$editorButtons[] = 'Maximize';
		}

		if (!$params['Image'])
		{
			$editorButtons[] = 'Image';
		}

		if (!$params['Video'])
		{
			$editorButtons[] = 'Video';
		}

		if (!$params['Link_Unlink'])
		{
			$editorButtons[] = 'Link,Unlink';
		}

		if (!$params['BulletedList'])
		{
			$editorButtons[] = 'BulletedList';
		}

		if (!$params['NumberedList'])
		{
			$editorButtons[] = 'NumberedList';
		}

		if (!$params['Blockquote'])
		{
			$editorButtons[] = 'Blockquote';
		}

		if (!$params['Code'])
		{
			$editorButtons[] = 'Code';
		}

		return $editorButtons;
	}

	/**
	 * Save param into ini file for SCeditor
	 *
	 * @return  array
	 *
	 * @since   Kunena 2.0
	 *@throws  Exception
	 * @throws  null
	 */
	protected function SaveSCeditorParamFile(array $params)
	{
		$editorButtons = [];

		if ($params['Bold'])
		{
			$editorButtons[] = 'bold';
		}

		if ($params['Italic'])
		{
			$editorButtons[] = 'italic';
		}

		if ($params['Underline'])
		{
			$editorButtons[] = 'underline';
		}

		if ($params['Strike'])
		{
			$editorButtons[] = 'strike';
		}

		if ($params['Superscript'])
		{
			$editorButtons[] = 'superscript';
		}

		if ($params['Subscript'])
		{
			$editorButtons[] = 'subscript';
		}

		if ($params['Right'])
		{
			$editorButtons[] = 'right';
		}

		if ($params['Left'])
		{
			$editorButtons[] = 'left';
		}

		if (!$params['Justify'])
		{
			$editorButtons[] = 'justify';
		}

		if (!$params['Center'])
		{
			$editorButtons[] = 'center';
		}

		if ($params['RemoveFormat'])
		{
			$editorButtons[] = 'removeFormat';
		}

		if (!$params['Confidential'])
		{
			$editorButtons[] = 'Confidential';
		}

		if (!$params['Hidetext'])
		{
			$editorButtons[] = 'Hidetext';
		}

		if (!$params['Spoiler'])
		{
			$editorButtons[] = 'Spoiler';
		}

		if (!$params['Smiley'])
		{
			$editorButtons[] = 'Smiley';
		}

		if (!$params['Ebay'])
		{
			$editorButtons[] = 'Ebay';
		}

		if (!$params['Twitter'])
		{
			$editorButtons[] = 'Twitter';
		}

		if (!$params['Instagram'])
		{
			$editorButtons[] = 'Instagram';
		}

		if (!$params['Soundcloud'])
		{
			$editorButtons[] = 'Soundcloud';
		}

		if (!$params['Map'])
		{
			$editorButtons[] = 'Map';
		}

		if ($params['Font'])
		{
			$editorButtons[] = 'font';
		}

		if ($params['Size'])
		{
			$editorButtons[] = 'Size';
		}

		if ($params['TextColor'])
		{
			$editorButtons[] = 'color';
		}

		if (!$params['Maximize'])
		{
			$editorButtons[] = 'Maximize';
		}

		if (!$params['Image'])
		{
			$editorButtons[] = 'Image';
		}

		if (!$params['Video'])
		{
			$editorButtons[] = 'Video';
		}

		if ($params['Link'])
		{
			$editorButtons[] = 'link';
		}

		if ($params['Unlink'])
		{
			$editorButtons[] = 'unlink';
		}

		if (!$params['BulletedList'])
		{
			$editorButtons[] = 'bulletedList';
		}

		if (!$params['NumberedList'])
		{
			$editorButtons[] = 'orderedlist';
		}

		if (!$params['Blockquote'])
		{
			$editorButtons[] = 'Blockquote';
		}

		if (!$params['Code'])
		{
			$editorButtons[] = 'Code';
		}

		return $editorButtons;
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function applychanges(): void
	{
		$template = $this->app->input->get('templatename', '');
		$menus    = $this->app->input->get('selections', [], 'array');
		$menus    = ArrayHelper::toInteger($menus);

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' .
				Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED')
			);

			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->internalSaveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl . '&view=template&layout=edit&name=' . $template, false));
	}

	/**
	 * Method to restore the default settings of the template selected
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 5.1
	 */
	public function restore(): void
	{
		$template = $this->app->input->get('templatename');
		$file     = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (file_exists($file))
		{
			$result = File::delete($file);

			if ($result)
			{
				File::write($file, '');
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_TEMPLATES_SETTINGS_RESTORED_SUCCESSFULLY'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Edit the template selected
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

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&view=template&layout=edit&name={$template}", false));
	}

	/**
	 * Choose scss
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function chooseScss(): void
	{
		$template     = $this->app->input->getArray(['cid' => '']);
		$templatename = array_shift($template['cid']);
		$this->app->setUserState('kunena.templatename', $templatename);

		$tBaseDir = KunenaPath::clean(KPATH_SITE . '/template');

		if (!is_dir($tBaseDir . '/' . $templatename . '/assets/scss'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_SCSS'), 'warning');

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=chooseScss", false));
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
	 * Add a new template
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0
	 */
	public function addnew()
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . "&layout=addnew", false));
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
	 * Uninstall Kunena template
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

		if (\in_array($id, $this->locked))
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
}
