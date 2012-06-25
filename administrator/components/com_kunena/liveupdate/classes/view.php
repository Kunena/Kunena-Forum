<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * The Live Update MVC view
 */
class LiveUpdateView extends JView
{
	public function display($tpl = null)
	{
		// Load the CSS
		$config = LiveUpdateConfig::getInstance();
		$this->assign('config', $config);
		if(!$config->addMedia()) {
			// No custom CSS overrides were set; include our own
			$document = JFactory::getDocument();
			$url = JURI::base().'/components/'.JRequest::getCmd('option','').'/liveupdate/assets/liveupdate.css';
			$document->addStyleSheet($url, 'text/css');
		}
		
		$requeryURL = 'index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&force=1';
		$this->assign('requeryURL', $requeryURL);
		
		$model = $this->getModel();
		
		$extInfo = (object)$config->getExtensionInformation();
		JToolBarHelper::title($extInfo->title.' &ndash; '.JText::_('LIVEUPDATE_TASK_OVERVIEW'),'liveupdate');
		if(version_compare(JVERSION,'1.6.0','ge')) {
			$msg = 'JTOOLBAR_BACK';
		} else {
			$msg = 'Back';
		}
		JToolBarHelper::back($msg, 'index.php?option='.JRequest::getCmd('option',''));
		
		switch(JRequest::getCmd('task','default'))
		{
			case 'startupdate':
				$this->setLayout('startupdate');
				$this->assign('url','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=download');
				break;
				
			case 'install':
				$this->setLayout('install');

				// Get data from the model
				$state		= $this->get('State');
		
				// Are there messages to display ?
				$showMessage	= false;
				if ( is_object($state) )
				{
					$message1		= $state->get('message');
					$message2		= $state->get('extension.message');
					$showMessage	= ( $message1 || $message2 );
				}
		
				$this->assign('showMessage',	$showMessage);
				$this->assignRef('state',		$state);
				
				break;
				
			case 'nagscreen':
				$this->setLayout('nagscreen');
				$this->assign('updateInfo', LiveUpdate::getUpdateInformation());
				$this->assign('runUpdateURL','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=startupdate&skipnag=1');
				break;
			
			case 'overview':
			default:
				$this->setLayout('overview');
				
				$force = JRequest::getInt('force',0);
				$this->assign('updateInfo', LiveUpdate::getUpdateInformation($force));
				$this->assign('runUpdateURL','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=startupdate');
				
				$needsAuth = !($config->getAuthorization()) && ($config->requiresAuthorization());
				$this->assign('needsAuth', $needsAuth); 
				break;
		}
		
		parent::display($tpl);
	}
}