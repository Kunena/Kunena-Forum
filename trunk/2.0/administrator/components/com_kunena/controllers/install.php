<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.archive' );

jimport('joomla.application.component.controller');

/**
 * The Kunena Installer Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerInstall extends KunenaController
{
	protected $step = null;
	protected $steps = null;
	protected $model = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('Install');
		$this->step = $this->model->getStep();
		$this->steps = $this->model->getSteps();
	}

	public function install()
	{
		// Check requirements
		$reqs = $this->model->getRequirements();
		if (!empty($reqs->fail))
		{
			// If requirements are not met, do not install
			$this->model->setStep(0);
			$this->setRedirect('index.php?option=com_kunena&view=install');
			return;
		}
		if (!$this->step) $this->model->setStep(++$this->step);
		if ($this->step >= count($this->steps)-1)
		{
			$this->model->setStep(0);
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		
		do
		{
			$error = $this->model->getError();
			if (isset($this->steps[$this->step]))
			{
				$this->next();
				$this->setRedirect('index.php?option=com_kunena&view=install');
			}
			$stop = ($this->checkTimeout() || $this->step<=2 || ($this->step >= count($this->steps)-1));
		}
		while (!$stop && !$error);
	}

	function next() {
		if (empty($this->steps[$this->step]['step'])) return $this->model->setStep(++$this->step);
		return call_user_func(array($this, "step".$this->steps[$this->step]['step']));
	}
	
	function stepPrepare() {
		$this->model->beginInstall();
		$error = $this->model->getError();
		if (!$error) $this->model->setStep(++$this->step);
	}
	
	function stepBackend() {
		$path = JPATH_ADMINISTRATOR.DS."components".DS."com_kunena".DS."archive";
		if (file_exists($path.DS.$file)) $this->model->extract($path, "admin.zip", JPATH_ROOT);
		$error = $this->model->getError();
		if (!$error) $this->model->setStep(++$this->step);
	}
	
	function stepFrontend() {
		$path = JPATH_ADMINISTRATOR.DS."components".DS."com_kunena".DS."archive";
		if (file_exists($path.DS.$file)) $this->model->extract($path, "site.zip", JPATH_ROOT);
		$error = $this->model->getError();
		if (!$error) $this->model->setStep(++$this->step);
	}
		
	function stepDatabase() {
		$prefix = $this->model->getVersionPrefix();
		if ($prefix == 'kunena_')
		{
			kimport('models.version', 'admin');
			$versionModel = new KunenaModelVersion();
			$version = $versionModel->getDBVersion();
		} else {
			$version->state = '';
		}

		switch ($version->state)
		{
			case 'migrateDatabase':
				$results = $this->model->migrateDatabase();
				$this->model->addStatus('Update database:migrate', true, $html);
				break;
			case 'upgradeDatabase':
				$results = $this->model->upgradeDatabase();
				$this->model->addStatus('Update database:upgrade', true, $html);
				break;
			case 'installSampleData':
				$results = $this->model->installSampleData();
				$this->model->addStatus('Update database:sample', true, $html);
				break;
			case '':
				$error = $this->model->getError();
				if (!$error) $this->model->setStep(++$this->step);
		}
	}
	
	function stepFinish() {
		$this->model->addStatus('Installation success!', true, '');
		$error = $this->model->getError();
		if (!$error) $this->model->setStep(++$this->step);
	}
	
	function checkTimeout() {
		static $start = null;

        list( $usec, $sec ) = explode( ' ', microtime() );
        $time = ((float)$usec + (float)$sec);

		if (empty($start)) $start = $time;

		if ($time-$start < 2) return false;
		return true;
	}
}