<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * The Live Update MVC controller
 */
class LiveUpdateController extends JController
{
	private $jversion = '15';

	/**
	 * Object contructor
	 * @param array $config
	 *
	 * @return LiveUpdateController
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		// Do we have Joomla! 1.6?
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$this->jversion = '16';
		}

		$basePath = dirname(__FILE__);
		if($this->jversion == '15') {
			$this->_basePath = $basePath;
		} else {
			$this->basePath = $basePath;
		}

		$this->registerDefaultTask('overview');
	}

	/**
	 * Runs the overview page task
	 */
	public function overview()
	{
		$this->display();
	}

	/**
	 * Starts the update procedure. If the FTP credentials are required, it asks for them.
	 */
	public function startupdate()
	{
		$updateInfo = LiveUpdate::getUpdateInformation();
		if($updateInfo->stability != 'stable') {
			$skipNag = JRequest::getBool('skipnag', false);
			if(!$skipNag) {
				$this->setRedirect('index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=nagscreen');
				$this->redirect();
			}
		}

		$ftp = $this->setCredentialsFromRequest('ftp');
		if($ftp === true) {
			// The user needs to supply the FTP credentials
			$this->display();
		} else {
			// No FTP credentials required; proceed with the download
			$this->setRedirect('index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=download');
			$this->redirect();
		}
	}

	/**
	 * Download the update package
	 */
	public function download()
	{
		$ftp = $this->setCredentialsFromRequest('ftp');
		$model = $this->getThisModel();
		$result = $model->download();
		if(!$result) {
			// Download failed
			$msg = JText::_('LIVEUPDATE_DOWNLOAD_FAILED');
			$this->setRedirect('index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=overview', $msg, 'error');
		} else {
			// Download successful. Let's extract the package.
			$url = 'index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=extract';
			$user = JRequest::getString('username', null, 'GET', JREQUEST_ALLOWRAW);
			$pass = JRequest::getString('password', null, 'GET', JREQUEST_ALLOWRAW);
			if($user) {
				$url .= '&username='.urlencode($user).'&password='.urlencode($pass);
			}
			$this->setRedirect($url);
		}
		$this->redirect();
	}

	public function extract()
	{
		$ftp = $this->setCredentialsFromRequest('ftp');
		$model = $this->getThisModel();
		$result = $model->extract();
		if(!$result) {
			// Download failed
			$msg = JText::_('LIVEUPDATE_EXTRACT_FAILED');
			$this->setRedirect('index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=overview', $msg, 'error');
		} else {
			// Extract successful. Let's install the package.
			$url = 'index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=install';
			$user = JRequest::getString('username', null, 'GET', JREQUEST_ALLOWRAW);
			$pass = JRequest::getString('password', null, 'GET', JREQUEST_ALLOWRAW);
			if($user) {
				$url .= '&username='.urlencode($user).'&password='.urlencode($pass);
			}

			// Do we have SRP installed yet?
			$app = JFactory::getApplication();
			$jResponse = $app->triggerEvent('onSRPEnabled');
			$status = false;
			if(!empty($jResponse)) {
				$status = false;
				foreach($jResponse as $response)
				{
					$status = $status || $response;
				}
			}

			// SRP enabled, use it
			if($status) {
				$return = $url;
				$url = $model->getSRPURL($return);
				if(!$url) {
					$url = $return;
				}
			}

			$this->setRedirect($url);
		}
		$this->redirect();
	}

	public function install()
	{
		$ftp = $this->setCredentialsFromRequest('ftp');
		$model = $this->getThisModel();
		$result = $model->install();
		if(!$result) {
			// Installation failed
			$model->cleanup();
			$this->setRedirect('index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=overview');
			$this->redirect();
		} else {
			// Installation successful. Show the installation message.
			if(version_compare(JVERSION,'1.6.0','ge')) {
				$cache = JFactory::getCache('mod_menu');
				$cache->clean();
			}

			$model->cleanup();

			// Force reload update information
			$dummy = LiveUpdate::getUpdateInformation(true);

			$this->setRedirect('index.php?option=com_kunena&view=install');
			$this->redirect();
		}
	}

	public function cleanup()
	{
		// Perform the cleanup
		$ftp = $this->setCredentialsFromRequest('ftp');
		$model = $this->getThisModel();
		$model->cleanup();

		// Force reload update information
		$dummy = LiveUpdate::getUpdateInformation(true);

		die('OK');
	}

	/**
	 * Displays the current view
	 * @param bool $cachable Ignored!
	 */
	public final function display($cachable = false, $urlparams = false)
	{
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view = $this->getThisView();

		// Get/Create the model
		$model = $this->getThisModel();
		$view->setModel($model, true);

		// Assign the FTP credentials from the request, or return TRUE if they are required
		jimport('joomla.client.helper');
		$ftp	= $this->setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	public final function getThisView()
	{
		static $view = null;

		if(is_null($view))
		{
			$basePath = ($this->jversion == '15') ? $this->_basePath : $this->basePath;
			$tPath = dirname(__FILE__).'/tmpl';

			require_once('view.php');
			$view = new LiveUpdateView(array('base_path'=>$basePath, 'template_path'=>$tPath));
		}

		return $view;
	}

	public final function getThisModel()
	{
		static $model = null;

		if(is_null($model))
		{
			require_once('model.php');
			$model = new LiveUpdateModel();
			$task = ($this->jversion == '15') ? $this->_task : $this->task;

			$model->setState( 'task', $task );

			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			if (is_object( $menu ))
			{
				$item = $menu->getActive();
				if ($item)
				{
					$params	= $menu->getParams($item->id);
					// Set Default State Data
					$model->setState( 'parameters.menu', $params );
				}
			}

		}

		return $model;
	}

	private function setCredentialsFromRequest($client)
	{
		// Determine wether FTP credentials have been passed along with the current request
		jimport('joomla.client.helper');
		$user = JRequest::getString('username', null, 'GET', JREQUEST_ALLOWRAW);
		$pass = JRequest::getString('password', null, 'GET', JREQUEST_ALLOWRAW);
		if ($user != '' && $pass != '')
		{
			// Add credentials to the session
			if (JClientHelper::setCredentials($client, $user, $pass)) {
				$return = false;
			} else {
				$return = JError::raiseWarning('SOME_ERROR_CODE', 'JClientHelper::setCredentialsFromRequest failed');
			}
		}
		else
		{
			// Just determine if the FTP input fields need to be shown
			$return = !JClientHelper::hasCredentials('ftp');
		}

		return $return;
	}
}