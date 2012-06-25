<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * The Live Update MVC model
 */
class LiveUpdateModel extends JModel
{
	public function download()
	{
		// Get the path to Joomla!'s temporary directory
		$jreg = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0.0', 'ge')) {
			$tmpdir = $jreg->get('tmp_path');
		} else {
			$tmpdir = $jreg->getValue('config.tmp_path');
		}

		jimport('joomla.filesystem.folder');
		// Make sure the user doesn't use the system-wide tmp directory. You know, the one that's
		// being erased periodically and will cause a real mess while installing extensions (Grrr!)
		if(realpath($tmpdir) == '/tmp') {
			// Someone inform the user that what he's doing is insecure and stupid, please. In the
			// meantime, I will fix what is broken.
			$tmpdir = JPATH_SITE.'/tmp';
		} // Make sure that folder exists (users do stupid things too often; you'd be surprised)
		elseif(!JFolder::exists($tmpdir)) {
			// Darn it, user! WTF where you thinking? OK, let's use a directory I know it's there...
			$tmpdir = JPATH_SITE.'/tmp';
		}

		// Oki. Let's get the URL of the package
		$updateInfo = LiveUpdate::getUpdateInformation();
		$config = LiveUpdateConfig::getInstance();
		$auth = $config->getAuthorization();
		$url = $updateInfo->downloadURL;

		// Sniff the package type. If sniffing is impossible, I'll assume a ZIP package
		$basename = basename($url);
		if(strstr($basename,'?')) {
			$basename = substr($basename, strstr($basename,'?')+1);
		}
		if(substr($basename,-4) == '.zip') {
			$type = 'zip';
		} elseif(substr($basename,-4) == '.tar') {
			$type = 'tar';
		} elseif(substr($basename,-4) == '.tgz') {
			$type = 'tar.gz';
		} elseif(substr($basename,-7) == '.tar.gz') {
			$type = 'tar.gz';
		} else {
			$type = 'zip';
		}

		// Cache the path to the package file and the temp installation directory in the session
		$target = $tmpdir.'/'.$updateInfo->extInfo->name.'.update.'.$type;
		$tempdir = $tmpdir.'/'.$updateInfo->extInfo->name.'_update';

		$session = JFactory::getSession();
		$session->set('target', $target, 'liveupdate');
		$session->set('tempdir', $tempdir, 'liveupdate');

		// Let's download!
		require_once dirname(__FILE__).'/download.php';
		return LiveUpdateDownloadHelper::download($url, $target);
	}

	public function extract()
	{
		$session = JFactory::getSession();
		$target = $session->get('target', '', 'liveupdate');
		$tempdir = $session->get('tempdir', '', 'liveupdate');

		jimport('joomla.filesystem.archive');
		return JArchive::extract( $target, $tempdir);
	}

	public function install()
	{
		$session = JFactory::getSession();
		$tempdir = $session->get('tempdir', '', 'liveupdate');

		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		$installer = JInstaller::getInstance();
		$packageType = JInstallerHelper::detectType($tempdir);

		if(!$packageType) {
			$msg = JText::_('LIVEUPDATE_INVALID_PACKAGE_TYPE');
			$result = false;
		} elseif (!$installer->install($tempdir)) {
			// There was an error installing the package
			$msg = JText::sprintf('LIVEUPDATE_INSTALLEXT', JText::_($packageType), JText::_('LIVEUPDATE_Error'));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('LIVEUPDATE_INSTALLEXT', JText::_($packageType), JText::_('LIVEUPDATE_Success'));
			$result = true;
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage($msg);
		$this->setState('result', $result);
		$this->setState('packageType', $packageType);
		if($packageType) {
			$this->setState('name', $installer->get('name'));
			$this->setState('message', $installer->message);
			if(version_compare(JVERSION,'1.6.0','ge')) {
				$this->setState('extmessage', $installer->get('extension_message'));
			} else {
				$this->setState('extmessage', $installer->get('extension.message'));
			}
		}

		return $result;
	}

	public function cleanup()
	{
		$session = JFactory::getSession();
		$target = $session->get('target', '', 'liveupdate');
		$tempdir = $session->get('tempdir', '', 'liveupdate');

		jimport('joomla.installer.helper');
		JInstallerHelper::cleanupInstall($target, $tempdir);

		$session->clear('target','liveupdate');
		$session->clear('tempdir','liveupdate');
	}

	public function getSRPURL($return = '')
	{
		$session = JFactory::getSession();
		$tempdir = $session->get('tempdir', '', 'liveupdate');

		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file');

		$instModelFile = JPATH_ADMINISTRATOR.'/components/com_akeeba/models/installer.php';
		if(!JFile::exists($instModelFile)) return false;

		require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/models/installer.php';
		$model	= JModel::getInstance('Installer', 'AkeebaModel');
		$packageType = JInstallerHelper::detectType($tempdir);
		$name = $model->getExtensionName($tempdir);

		$url = 'index.php?option=com_akeeba&view=backup&tag=restorepoint&type='.$packageType.'&name='.urlencode($name['name']);
		switch($packageType) {
			case 'module':
			case 'template':
				$url .= '&group='.$name['client'];
				break;
			case 'plugin':
				$url .= '&group='.$name['group'];
				break;
		}

		if(!empty($return)) $url .= '&returnurl='.urlencode($return);

		return $url;
	}
}