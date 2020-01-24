<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Templates;

defined('_JEXEC') or die();

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use function defined;

/**
 * Templates view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{

	public $templates = [] ;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function display($tpl = null)
	{
		$this->templates  = $this->get('templates');
		$this->pagination = $this->get('Pagination');

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
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::addNew('add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		ToolbarHelper::custom('edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('choosecss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		ToolbarHelper::divider();
		ToolbarHelper::custom('chooseless', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		ToolbarHelper::divider();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/add-template';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function displayAdd()
	{
		$this->setToolBarAdd();
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarAdd()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::back();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function displayEdit()
	{
		$this->setToolBarEdit();

		$this->form         = $this->get('Form');
		$this->params       = $this->get('editparams');
		$this->details      = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState('kunena.edit.templatename');
		$template           = \KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$this->templatefile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

		if (!File::exists($this->templatefile))
		{
			$ourFileHandle = @fopen($this->templatefile, 'w');

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
	protected function setToolBarEdit()
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
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function displayChooseless()
	{
		$this->setToolBarChooseless();
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
	protected function setToolBarChooseless()
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
	 * @throws  \Exception
	 */
	public function displayEditless()
	{
		$this->setToolBarEditless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editless.filename');
		$this->content      = $this->get('FileLessParsed');

		$this->less_path = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/' . $this->filename;
		$this->ftp       = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEditless()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('applyless');
		ToolbarHelper::spacer();
		ToolbarHelper::save('saveless');
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
	 * @throws  \Exception
	 */
	public function displayChoosecss()
	{
		$this->setToolBarChoosecss();
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
	protected function setToolBarChoosecss()
	{

		ToolbarHelper::spacer();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::custom('editcss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
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
	 * @throws  \Exception
	 */
	public function displayEditcss()
	{
		$this->setToolBarEditcss();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editcss.filename');
		$this->content      = $this->get('FileContentParsed');
		$this->css_path     = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/' . $this->filename;
		$this->ftp          = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarEditcss()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('applycss');
		ToolbarHelper::spacer();
		ToolbarHelper::save('savecss');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}
}
