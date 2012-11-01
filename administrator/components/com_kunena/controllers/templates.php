<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Backend Templates Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerTemplates extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
	}

	function publish() {
		$db		= JFactory::getDBO();
		$cid	= JRequest::getVar('cid', array(), 'method', 'array');
		$id = array_shift($cid);

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if ($id) {
			$this->config->template = $id;
			$this->config->save ();
		}
		$template = KunenaFactory::getTemplate($id);
		$template->clearCache();

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT_SELECTED'));
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function add() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add", false));
	}

	function edit() {
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');
		$db		= JFactory::getDBO();
		$cid	= JRequest::getVar('cid', array(), 'method', 'array');
		$template = array_shift($cid);

		if (!$template) {
			return JError::raiseWarning( 500, JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED') );
		}
		$tBaseDir	= JPath::clean(KPATH_SITE.'/template');
		if (!is_dir( $tBaseDir . '/' . $template )) {
			return JError::raiseWarning( 500, JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_FOUND') );
		}

		$this->app->setUserState ( 'kunena.edit.template', $template );

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=edit", false));
	}

	function install() {
		jimport ( 'joomla.filesystem.folder' );
		jimport ( 'joomla.filesystem.file' );
		jimport ( 'joomla.filesystem.archive' );
		$tmp = JPATH_ROOT . '/tmp/kinstall/';
		$dest = KPATH_SITE . '/template/';
		$file = JRequest::getVar ( 'install_package', NULL, 'FILES', 'array' );

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if (!$file || !is_uploaded_file ( $file ['tmp_name'])) {
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_MISSING', $file ['name']), 'notice' );
		}
		else {
			$success = JFile::upload($file ['tmp_name'], $tmp . $file ['name']);
			$success = JArchive::extract ( $tmp . $file ['name'], $tmp );
			if (! $success) {
				$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_FAILED', $file ['name']), 'notice' );
			}
			// Delete the tmp install directory
			if (JFolder::exists($tmp)) {
				$templates = KunenaTemplateHelper::parseXmlFiles($tmp);
				if (!empty($templates)) {
					foreach ($templates as $template) {
						// Never overwrite default template
						if ($template->directory == 'default') continue;
						if (is_dir($dest.$template->directory)) {
							if (is_file($dest.$template->directory.'/params.ini')) {
								if (is_file($tmp.$template->directory.'/params.ini')) {
									JFile::delete($tmp.$template->directory.'/params.ini');
								}
								JFile::move($dest.$template->directory.'/params.ini', $tmp.$template->directory.'/params.ini');
							}
							JFolder::delete($dest.$template->directory);
						}
						$error = JFolder::move($tmp.$template->directory, $dest.$template->directory);
						if ($error !== true) $this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE').': ' . $error, 'notice' );
					}
					$retval = JFolder::delete($tmp);
					$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_EXTRACT_SUCCESS', $file ['name']) );
				} else {
					JError::raiseWarning(100, JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_MISSING_FILE'));
					$retval = false;
				}
			} else {
				JError::raiseWarning(100, JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE').' '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'));
				$retval = false;
			}
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function uninstall() {
		jimport ( 'joomla.filesystem.folder' );
		$cid	= JRequest::getVar('cid', array(), 'method', 'array');
		$id = array_shift($cid);
		$template	= $id;

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		// templates to prevent to remove
		$templates_locked = array('blue_eagle', 'mirage');
		$otemplate = KunenaTemplateHelper::parseXmlFile($id);

		// Initialize variables
		$retval	= true;
		if ( !$otemplate ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		if ( in_array($id, $templates_locked) ) {
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_SYSTEM_TEMPLATE', $otemplate->name), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		if ( KunenaTemplateHelper::isDefault($template) ) {
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_CTRL_TEMPLATES_ERROR_UNINSTALL_DEFAULT_TEMPLATE', $otemplate->name), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}
		$tpl = KPATH_SITE . '/template/'.$template;
		// Delete the template directory
		if (JFolder::exists($tpl)) {
			$retval = JFolder::delete($tpl);
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL_SUCCESS', $id) );
		} else {
			JError::raiseWarning(100, JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE').' '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DIR_NOT_EXIST'));
			$retval = false;
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function choosecss() {
		$template	= JRequest::getVar('id', '', 'method', 'cmd');
		$this->app->setUserState ( 'kunena.choosecss', $template );

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=choosecss", false));
	}

	function editcss() {
		$template	= JRequest::getVar('id', '', 'method', 'cmd');
		$filename	= JRequest::getVar('filename', '', 'method', 'cmd');

		jimport('joomla.filesystem.file');
		if (JFile::getExt($filename) !== 'css') {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_WRONG_CSS'));
			$this->setRedirect(KunenaRoute::_($this->baseurl.'&layout=choosecss&id='.$template, false));
		}

		$this->app->setUserState ( 'kunena.editcss.tmpl', $template );
		$this->app->setUserState ( 'kunena.editcss.filename', $filename );

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=editcss", false));
	}

	function savecss() {
		$template		= JRequest::getVar('id', '', 'post', 'cmd');
		$filename		= JRequest::getVar('filename', '', 'post', 'cmd');
		$filecontent	= JRequest::getVar('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if (!$template) {
			$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED.'));
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		if (!$filecontent) {
			$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONTENT_EMPTY'));
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');
		$file = KPATH_SITE.'/template/'.$template.'/css/'.$filename;
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_COULD_NOT_CSS_WRITABLE'));
		}
		jimport('joomla.filesystem.file');
		$return = JFile::write($file, $filecontent);
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_COULD_NOT_CSS_UNWRITABLE'));
		}
		if ($return) {
			$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FILE_SAVED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=edit&cid[]='.$template", false));
		} else {
			$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_OPEN_FILE.', $file));
			$this->setRedirect(KunenaRoute::_($this->baseurl.'&layout=choosecss&id='.$template, false));
		}
	}

	function apply() {
		$task = JRequest::getCmd('task');
		$template= JRequest::getVar('templatename', '', 'method', 'cmd');
		$menus= JRequest::getVar('selections', array(), 'post', 'array');
		$params= JRequest::getVar('params', array(), 'post', 'array');
		$default= JRequest::getBool('default');
		JArrayHelper::toInteger($menus);

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if (!$template) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');
		$file = KPATH_SITE.'/template/'.$template.'/params.ini';
		jimport('joomla.filesystem.file');
		if ( count($params) ) {
			$registry = new JRegistry();
			$registry->loadArray($params);
			$txt = $registry->toString();
			$return = JFile::write($file, $txt);
			if (!$return) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE.', $file));
				$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			}
		}

		$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->app->redirect ( KunenaRoute::_($this->baseurl.'&layout=edit&cid[]='.$template, false) );
	}

	function save() {
		$task = JRequest::getCmd('task');
		$template= JRequest::getVar('templatename', '', 'method', 'cmd');
		$menus= JRequest::getVar('selections', array(), 'post', 'array');
		$params= JRequest::getVar('params', array(), 'post', 'array');
		$default= JRequest::getBool('default');
		JArrayHelper::toInteger($menus);

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if (!$template) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');
		$file = KPATH_SITE.'/template/'.$template.'/params.ini';
		jimport('joomla.filesystem.file');
		if ( count($params) ) {
			$registry = new JRegistry();
			$registry->loadArray($params);
			$txt = $registry->toString();
			$return = JFile::write($file, $txt);
			if (!$return) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED').': '.JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE.', $file));
				$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			}
		}

		$this->app->enqueueMessage (JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

}
