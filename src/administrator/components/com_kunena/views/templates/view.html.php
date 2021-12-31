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
defined('_JEXEC') or die();

use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

/**
 * Templates view for Kunena backend
 *
 * @since Kunena
 */
class KunenaAdminViewTemplates extends KunenaView
{
	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->setToolBarDefault();
		$this->templates    = $this->get('Templates');
		$this->pagination   = $this->get('Pagination');
		$this->templatesxml = $this->get('Templatesxml');
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::spacer();
		JToolbarHelper::addNew('add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		JToolbarHelper::custom('edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		JToolbarHelper::divider();
		JToolbarHelper::custom('publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		JToolbarHelper::divider();
		JToolbarHelper::custom('uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('choosecss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		JToolbarHelper::divider();
		JToolbarHelper::custom('chooseless', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		JToolbarHelper::divider();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/add-template';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayAdd()
	{
		$this->setToolBarAdd();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarAdd()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::spacer();
		JToolbarHelper::back();
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public function displayEdit()
	{
		$this->setToolBarEdit();

		$this->form         = $this->get('Form');
		$this->params       = $this->get('editparams');
		$this->details      = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState('kunena.edit.template');
		$template           = KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$this->templatefile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

		if (!JFile::exists($this->templatefile) && JFolder::exists(KPATH_SITE . '/template/' . $this->templatename . '/config/'))
		{
			$ourFileHandle = fopen($this->templatefile, 'w');

			if ($ourFileHandle)
			{
				fclose($ourFileHandle);
			}
		}

		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarEdit()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::spacer();
		JToolbarHelper::apply('apply');
		JToolbarHelper::spacer();
		JToolbarHelper::save('save');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE_TEMPLATE_SETTINGS', false);
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayChooseless()
	{
		$this->setToolBarChooseless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		jimport('joomla.filesystem.folder');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/custom.less';

		if (!file_exists($file) && JFolder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/less/'))
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
	 * @since Kunena
	 */
	protected function setToolBarChooseless()
	{

		JToolbarHelper::spacer();
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::custom('editless', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		JToolbarHelper::spacer();
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
	}

	/**
	 * @since Kunena
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
	 * @since Kunena
	 */
	protected function setToolBarEditless()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::spacer();
		JToolbarHelper::apply('applyless');
		JToolbarHelper::spacer();
		JToolbarHelper::save('saveless');
		JToolbarHelper::spacer();
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
	}

	/**
	 * @since Kunena
	 */
	public function displayChoosecss()
	{
		$this->setToolBarChoosecss();
		$this->templatename = $this->app->getUserState('kunena.templatename');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/custom.css';

		if (!file_exists($file) && JFolder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/'))
		{
			$fp = fopen($file, "w");
			fwrite($fp, "");
			fclose($fp);
		}

		$this->dir = KPATH_SITE . '/template/' . $this->templatename . '/assets/css';
		jimport('joomla.filesystem.folder');
		$this->files = Folder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarChoosecss()
	{

		JToolbarHelper::spacer();
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::custom('editcss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		JToolbarHelper::spacer();
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
	}

	/**
	 * @since Kunena
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
	 * @since Kunena
	 */
	protected function setToolBarEditcss()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolbarHelper::spacer();
		JToolbarHelper::apply('applycss');
		JToolbarHelper::spacer();
		JToolbarHelper::save('savecss');
		JToolbarHelper::spacer();
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
	}
}
