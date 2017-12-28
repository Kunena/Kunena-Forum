<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Templates view for Kunena backend
 */
class KunenaAdminViewTemplates extends KunenaView
{
	/**
	 *
	 */
	function displayDefault()
	{
		$this->setToolBarDefault();
		$this->templates  = $this->get('templates');
		$this->pagination = $this->get('Pagination');
		$this->display();
	}

	/**
	 *
	 */
	function displayAdd()
	{
		$this->setToolBarAdd();
		$this->display();
	}

	/**
	 *
	 */
	function displayEdit()
	{
		$this->setToolBarEdit();

		// FIXME: enable template parameters
		$this->form         = $this->get('Form');
		$this->params       = $this->get('editparams');
		$this->details      = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState('kunena.edit.template');
		$template           = KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$this->templatefile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

		if (!JFile::exists($this->templatefile))
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
	 *
	 */
	function displayChooseless()
	{
		$this->setToolBarChooseless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		jimport('joomla.filesystem.folder');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/custom.less';

		if (!file_exists($file))
		{
			$fp = fopen($file, "w");
			fwrite($fp, "");
			fclose($fp);
		}

		$this->dir          = KPATH_SITE . '/template/' . $this->templatename . '/assets/less';
		$this->files = JFolder::files($this->dir, '\.less$', false, false);

		$this->display();
	}

	/**
	 *
	 */
	function displayEditless()
	{
		$this->setToolBarEditless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editless.filename');
		$this->content      = $this->get('FileLessParsed');

		$this->less_path     = KPATH_SITE . '/template/' . $this->templatename . '/assets/less/' . $this->filename;
		$this->ftp          = $this->get('FTPcredentials');
		$this->display();
	}

	/**
	 *
	 */
	function displayChoosecss()
	{
		$this->setToolBarChoosecss();
		$this->templatename = $this->app->getUserState('kunena.templatename');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/custom.css';

		if (!file_exists($file))
		{
			$fp = fopen($file, "w");
			fwrite($fp, "");
			fclose($fp);
		}

		$this->dir          = KPATH_SITE . '/template/' . $this->templatename . '/assets/css';
		jimport('joomla.filesystem.folder');
		$this->files = JFolder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	/**
	 *
	 */
	function displayEditcss()
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
	 *
	 */
	protected function setToolBarDefault()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		JToolBarHelper::custom('edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('choosecss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		JToolBarHelper::divider();
		JToolBarHelper::custom('chooseless', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		JToolBarHelper::divider();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/templates/add-template';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 */
	protected function setToolBarAdd()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::back();
		JToolBarHelper::spacer();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 */
	protected function setToolBarEdit()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 */
	protected function setToolBarChoosecss()
	{

		JToolBarHelper::spacer();
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::custom('editcss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	/**
	 *
	 */
	protected function setToolBarEditcss()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('applycss');
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecss');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	/**
	 *
	 */
	protected function setToolBarChooseless()
	{

		JToolBarHelper::spacer();
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::custom('editless', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	/**
	 *
	 */
	protected function setToolBarEditless()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('applyless');
		JToolBarHelper::spacer();
		JToolBarHelper::save('saveless');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}
}

