<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Templates view for Kunena backend
 * @since Kunena
 */
class KunenaAdminViewTemplates extends KunenaView
{
	/**
	 *
	 * @since Kunena
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
	 * @since Kunena
	 */
	function displayAdd()
	{
		$this->setToolBarAdd();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayEdit()
	{
		$this->setToolBarEdit();

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
	 * @since Kunena
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

		$this->dir   = KPATH_SITE . '/template/' . $this->templatename . '/assets/less';
		$this->files = JFolder::files($this->dir, '\.less$', false, false);

		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayEditless()
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
	 *
	 * @since Kunena
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

		$this->dir = KPATH_SITE . '/template/' . $this->templatename . '/assets/css';
		jimport('joomla.filesystem.folder');
		$this->files = JFolder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	/**
	 *
	 * @since Kunena
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
	 * @since Kunena
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
		$help_url = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarAdd()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		JToolBarHelper::spacer();
		JToolBarHelper::back();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
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
		$help_url = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
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
	 * @since Kunena
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
	 * @since Kunena
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
	 * @since Kunena
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

