<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Templates view for Kunena backend
 */
class KunenaAdminViewTemplates extends KunenaView
{
	function displayDefault()
	{
		$this->setToolBarDefault();
		$this->templates  = $this->get('templates');
		$this->pagination = $this->get('Pagination');
		$this->display();
	}

	function displayAdd()
	{
		$this->setToolBarAdd();
		$this->display();
	}

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

		$this->templatefile = KPATH_SITE . '/template/' . $this->templatename . '/params.ini';

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

	function displayChooseless()
	{
		$this->setToolBarChooseless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		jimport('joomla.filesystem.folder');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/less/custom.less';

		if (!file_exists($file))
		{
			$fp = fopen($file,"w");
			fwrite($fp,"");
			fclose($fp);
		}

		$this->dir          = KPATH_SITE . '/template/' . $this->templatename . '/less';
		$this->files = JFolder::files($this->dir, '\.less$', false, false);

		$this->display();
	}

	function displayEditless()
	{
		$this->setToolBarEditless();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editless.filename');
		$this->content      = $this->get('FileLessParsed');

		$this->less_path     = KPATH_SITE . '/template/' . $this->templatename . '/less/' . $this->filename;
		$this->ftp          = $this->get('FTPcredentials');
		$this->display();
	}

	function displayChoosecss()
	{
		$this->setToolBarChoosecss();
		$this->templatename = $this->app->getUserState('kunena.templatename');

		$file = KPATH_SITE . '/template/' . $this->templatename . '/css/custom.css';

		if(!file_exists($file))
		{
			$fp = fopen($file,"w");
			fwrite($fp,"");
			fclose($fp);
		}

		$this->dir          = KPATH_SITE . '/template/' . $this->templatename . '/css';
		jimport('joomla.filesystem.folder');
		$this->files = JFolder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	function displayEditcss()
	{
		$this->setToolBarEditcss();
		$this->templatename = $this->app->getUserState('kunena.templatename');
		$this->filename     = $this->app->getUserState('kunena.editcss.filename');
		$this->content      = $this->get('FileContentParsed');
		$this->css_path     = KPATH_SITE . '/template/' . $this->templatename . '/css/' . $this->filename;
		$this->ftp          = $this->get('FTPcredentials');
		$this->display();
	}

	protected function setToolBarDefault()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		JToolBarHelper::custom('edit', 'edit', 'edit', 'COM_KUNENA_EDIT');
		JToolBarHelper::divider();
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::custom('publish', 'star', 'star', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		}
		else
		{
			JToolBarHelper::custom('publish', 'default', 'default', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('uninstall', 'remove', 'remove', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		JToolBarHelper::spacer();
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::custom('choosecss', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
			JToolBarHelper::divider();
			JToolBarHelper::custom('chooseless', 'edit', 'edit', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
			JToolBarHelper::divider();
		}
		$help_url  = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}

	protected function setToolBarAdd()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::back();
		JToolBarHelper::spacer();
		$help_url  = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	protected function setToolBarEdit()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url  = 'https://www.kunena.org/docs/Changing_Templates_-_the_Basics';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}

	protected function setToolBarChoosecss()
	{

		JToolBarHelper::spacer();

		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
			JToolBarHelper::custom('editcss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
			JToolBarHelper::custom('editcss', 'css.png', 'css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarEditcss()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
		}
		JToolBarHelper::spacer();
		JToolBarHelper::apply('applycss');
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecss');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarChooseless()
	{

		JToolBarHelper::spacer();

		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
			JToolBarHelper::custom('editless', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
			JToolBarHelper::custom('editless', 'css.png', 'css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITLESS');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarEditless()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates');
		}
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

