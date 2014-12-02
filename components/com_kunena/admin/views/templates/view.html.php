<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Templates view for Kunena backend
 */
class KunenaAdminViewTemplates extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->templates = $this->get('templates');
		$this->pagination = $this->get ( 'Pagination' );
		$this->display();
	}

	function displayAdd () {
		$this->setToolBarAdd();
		$this->display ();
	}

	function displayEdit () {
		$this->setToolBarEdit();
		// FIXME: enable template parameters
		$this->form = $this->get('Form');
		$this->params = $this->get('editparams');
		$this->details = $this->get('templatedetails');
		$this->templatename = $this->app->getUserState ( 'kunena.edit.template');
		$template = KunenaTemplate::getInstance($this->templatename);
		$template->initializeBackend();

		$this->templatefile = KPATH_SITE.'/template/'.$this->templatename.'/params.ini';

		if ( !JFile::exists($this->templatefile))  {
			$ourFileHandle = @fopen($this->templatefile, 'w');
			if ($ourFileHandle) fclose($ourFileHandle);
		}

		$this->display();
	}

	function displayChoosecss() {
		$this->setToolBarChoosecss();
		$this->templatename = $this->app->getUserState ( 'kunena.edit.template');
		$this->dir = KPATH_SITE.'/template/'.$this->templatename.'/css';
		jimport('joomla.filesystem.folder');
		$this->files = JFolder::files($this->dir, '\.css$', false, false);
		$this->display();
	}

	function displayEditcss() {
		$this->setToolBarEditcss();
		$this->templatename = $this->app->getUserState ( 'kunena.editcss.tmpl');
		$this->filename = $this->app->getUserState ( 'kunena.editcss.filename');
		$this->content = $this->get ( 'FileContentParsed');
		$this->css_path = KPATH_SITE.'/template/'.$this->templatename.'/css/'.$this->filename;
		$this->ftp = $this->get('FTPcredentials');
		$this->display();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_TEMPLATES_NEW_TEMPLATE');
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::divider();
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('publish', 'star.png', 'star_f2.png', 'COM_KUNENA_TEMPLATES_FIELD_LABEL_MAKE_DEFAULT');
		} else {
			JToolBarHelper::custom('publish', 'default.png', 'default_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('uninstall', 'delete.png','delete_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		JToolBarHelper::spacer();
	}

	protected function setToolBarAdd() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates' );
		JToolBarHelper::spacer();
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates' );
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
// TODO: figure out how to do css/less editing so that the distribution files don't get overridden
/*
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('choosecss', 'edit.png','edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS', false, false );
		} else {
			JToolBarHelper::custom('choosecss', 'css.png','css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS', false, false );
		}
		JToolBarHelper::spacer();
*/
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarChoosecss() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates' );
		JToolBarHelper::spacer();
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('editcss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		} else {
			JToolBarHelper::custom('editcss', 'css.png', 'css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		}
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarEditcss() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TEMPLATE_MANAGER'), 'templates' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecss');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}
}
