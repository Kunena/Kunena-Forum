<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Templates;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use function defined;

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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->templates    = $this->get('templates');
		$this->pagination   = $this->get('Pagination');
		$this->templatesxml = $this->get('templatesxml');

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
		ToolbarHelper::addNew('template.add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		ToolbarHelper::custom('template.edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('template.chooseCss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		ToolbarHelper::divider();
		ToolbarHelper::custom('template.chooseLess', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		ToolbarHelper::divider();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/templates/add-template';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayEdit(): void
	{
		$this->setToolBarEdit();

		$form               = $this->get('Form');
		$params             = $this->get('editparams');
		$details            = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState('kunena.edit.templatename');
		$template           = KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$templateFile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

		if (!File::exists($templateFile))
		{
			$ourFileHandle = @fopen($templateFile, 'w');

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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayChooseLess(): void
	{
		$this->setToolBarChooseLess();
		$this->templatename = $this->app->getUserState('kunena.templatename');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/custom.less';

		if (!file_exists($file))
		{
			$fp = fopen($file, "w");
			fwrite($fp, "");
			fclose($fp);
		}

		$this->dir   = KPATH_SITE . '/template/' . $this->templatename . '/assets/less';
		$this->files = Folder::files($this->dir, '\.less$', false, false);

		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarChooseLess(): void
	{
		ToolbarHelper::spacer();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::custom('editless', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayEditLess(): void
	{
		$this->setToolBarEditLess();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editless.filename');
		$this->content      = $this->get('FileLessParsed');

		$less_path = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/' . $this->filename;
		$this->ftp = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEditLess(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('applyLess');
		ToolbarHelper::spacer();
		ToolbarHelper::save('saveLess');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayChooseCss(): void
	{
		$this->setToolBarChooseCss();
		$this->templatename = $this->app->getUserState('kunena.templatename');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/custom.css';

		if (!file_exists($file))
		{
			if (!Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/'))
			{
				Folder::create(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/');
			}

			$fp = fopen($file, "w");
			fwrite($fp, "");
			fclose($fp);
		}

		$this->dir   = KPATH_SITE . '/template/' . $this->templatename . '/assets/css';
		$this->files = Folder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarChooseCss(): void
	{
		ToolbarHelper::spacer();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::custom('editCss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayEditCss(): void
	{
		$this->setToolBarEditCss();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editCss.filename');
		$this->content      = $this->get('FileContentParsed');
		$cssPath            = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/' . $this->filename;
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
