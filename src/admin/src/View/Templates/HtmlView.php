<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Templates;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * Templates view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var mixed
	 * @since version
	 */
	public $templates = [];

	/**
	 * @var mixed
	 * @since version
	 */
	private $ftp;

	/**
	 * @var mixed
	 * @since version
	 */
	private $content;

	/**
	 * @var mixed
	 * @since version
	 */
	private $app;

	/**
	 * @var mixed
	 * @since version
	 */
	private $filename;

	/**
	 * @var mixed
	 * @since version
	 */
	private $templatename;

	/**
	 * @var array|false
	 * @since version
	 */
	private $files;

	/**
	 * @var string
	 * @since version
	 */
	private $dir;

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function displayAdd(): void
	{
		$this->setToolBarAdd();
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarAdd(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::back();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function display($tpl = null)
	{
		$this->templates    = $this->get('Templates');
		$this->pagination   = $this->get('Pagination');
		$this->templatesxml = $this->get('Templatesxml');

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::addNew('template.addnew', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		ToolbarHelper::custom('template.edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('template.chooseCss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.chooseScss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITSCSS');
		ToolbarHelper::divider();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/templates/add-template';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function displayEdit(): void
	{
		$this->setToolBarEdit();

		$this->form         = $this->get('Form');
		$this->params       = $this->get('editparams');
		$this->details      = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState('kunena.edit.templatename');
		$template           = KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$templateFile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

		if (!File::exists($templateFile) && Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/config/'))
		{
			$ourFileHandle = fopen($templateFile, 'w');

			if ($ourFileHandle)
			{
				fclose($ourFileHandle);
			}
		}

		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEdit(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('apply');
		ToolbarHelper::spacer();
		ToolbarHelper::save('save');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE_TEMPLATE_SETTINGS', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function displayEditScss(): void
	{
		$this->setToolBarEditScss();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editscss.filename');
		$this->content      = $this->get('FileScssParsed');

		$this->scss_path = KPATH_SITE . '/template/' . $this->templatename . '/assets/scss/' . $this->filename;
		$this->ftp       = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEditScss(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('applyScss');
		ToolbarHelper::spacer();
		ToolbarHelper::save('saveScss');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function displayEditCss(): void
	{
		$this->setToolBarEditCss();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editCss.filename');
		$this->content      = $this->get('FileContentParsed');
		$this->cssPath      = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/' . $this->filename;
		$this->ftp          = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEditCss(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('applyCss');
		ToolbarHelper::spacer();
		ToolbarHelper::save('saveCss');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}
}
