<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport( 'joomla.application.component.modeladmin' );
jimport( 'joomla.html.pagination' );

/**
 * Templates Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelTemplates extends JModelAdmin {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->app = JFactory::getApplication();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState() {
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		// Edit state information
		$value = $this->getUserStateFromRequest ( $this->context.'.edit', 'name', '', 'cmd' );
		$this->setState ( 'template', $value );

		// List state information
		$value = $this->getUserStateFromRequest ( $this->context.'.list.limit', 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.list.start', 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );
	}

	/**
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$jinput = JFactory::getApplication()->input;

		// Load the configuration definition file.
		$template = $this->getState ('template');
		$xml = KunenaTemplate::getInstance($template)->getConfigXml();

		// Get the form.
		$form = $this->loadForm('com_kunena_template', $xml, array('control' => 'jform', 'load_data' => $loadData, 'file'=>false), true, '//config');
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_kunena.edit.template.data', array());

		if (empty($data)) {
			$data = KunenaTemplate::getInstance()->params->toArray();
		}

		return $data;
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
		// FIXME: Joomla 3.0: need to use JForm
		$params = new JRegistry();
		$params->loadString($content,'INI');
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

	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$input     = $app->input;
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $input->get($request, null, $type);

		if (($cur_state != $new_state) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null)
		{
			$app->setUserState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}
}
