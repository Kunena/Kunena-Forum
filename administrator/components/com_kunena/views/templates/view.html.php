<?php
/**
 * @version $Id: view.html.php 4443 2011-02-18 19:51:15Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Templates view for Kunena backend
 */
class KunenaAdminViewTemplates extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->templates = $this->get('templates');
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
		$this->display();
	}

	function displayAdd () {
		$this->setToolBarAdd();
		$this->display ();
	}

	function displayEdit () {
		$this->setToolBarEdit();
		$app = JFactory::getApplication ();
		$this->params = $this->get('editparams');
		$this->details = $this->get('templatedetails');
		$this->templatename = $app->getUserState ( 'kunena.edit.template');

		// Loading language strings for default template and override with current template
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.tpl_default', JPATH_SITE);
		if ($this->templatename != 'default') {
			if (!$lang->load('com_kunena.tpl_'.$this->templatename, JPATH_SITE)) {
				$lang->load('com_kunena.tpl_'.$this->templatename, KPATH_SITE.'/template/'.$this->templatename);
			}
		}
		$this->display();
	}

	function displayChoosecss() {
		$this->setToolBarChoosecss();
		$app = JFactory::getApplication ();
		$this->templatename = $app->getUserState ( 'kunena.edit.template');
		$this->dir = KPATH_SITE.'/template/'.$this->templatename.'/css';
		jimport('joomla.filesystem.folder');
		$this->files = JFolder::files($this->dir, '\.css$', false, false);		;
		$this->display();
	}

	function displayEditcss() {
		$this->setToolBarEditcss();
		$app = JFactory::getApplication ();
		$this->templatename = $app->getUserState ( 'kunena.editcss.tmpl');
		$this->filename = $app->getUserState ( 'kunena.editcss.filename');
		$this->content = $this->get ( 'FileContentParsed');
		$this->css_path = KPATH_SITE.'/template/'.$this->templatename.'/css/'.$this->filename;
		$this->ftp = $this->get('FTPcredentials');
		$this->display();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('publish', 'default.png', 'default_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_A_TEMPLATE_MANAGER_ADD');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('uninstall', 'delete.png','delete_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
		JToolBarHelper::spacer();
	}

	protected function setToolBarAdd() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('choosecss', 'css.png','css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS', false, false );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		JToolBarHelper::spacer();
	}

	protected function setToolBarChoosecss() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('editcss', 'css.png', 'css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		JToolBarHelper::spacer();
	}

	protected function setToolBarEditcss() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('savecss');
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		JToolBarHelper::spacer();
	}
}
