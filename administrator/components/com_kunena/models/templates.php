<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );

/**
 * Templates Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelTemplates extends KunenaModel {
	protected $__state_set = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.templates.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.templates.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.templates.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );
	}

	function getTemplates() {
		//get template xml file info
		$rows = array();
		$rows = KunenaTemplateHelper::parseXmlFiles();
		// set dynamic template information
		foreach( $rows as $row ) {
			$row->published = KunenaTemplateHelper::isDefault($row->directory);
		}
		$this->setState ( 'list.total', count($rows) );
		if ($this->getState ( 'list.limit')) $rows = array_slice($rows, $this->getState ( 'list.start'), $this->getState ( 'list.limit'));
		return $rows;
	}

	function getEditparams() {
		jimport('joomla.filesystem.file');

		$tBaseDir	= JPath::clean(KPATH_SITE.'/template');
		$template = $this->app->getUserState ( 'kunena.edit.template');
		$ini	= KPATH_SITE.'/template/'.$template.'/params.ini';
		$xml	= KPATH_SITE.'/template/'.$template.'/template.xml';

		// Read the ini file
		if (JFile::exists($ini)) {
			$content = JFile::read($ini);
		} else {
			$content = null;
		}
		// FIXME: Joomla 1.6+: Deprecated JParameter
		$params = new JParameter($content, $xml, 'template');
		return $params;
	}

	function getTemplatedetails() {
		$template = $this->app->getUserState ( 'kunena.edit.template');
		$details	= KunenaTemplateHelper::parseXmlFile($template);

		return $details;
	}

	function getFileContentParsed() {
		jimport('joomla.filesystem.file');
		$template = $this->app->getUserState ( 'kunena.edit.template');
		$filename = $this->app->getUserState ( 'kunena.editcss.filename');
		$content = JFile::read(KPATH_SITE.'/template/'.$template.'/css/'.$filename);
		if ($content === false) {
			 return;
		}
		$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

		return $content;
	}

	function getFTPcredentials() {
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		$ftp = JClientHelper::setCredentialsFromRequest('ftp');

		return $ftp;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
