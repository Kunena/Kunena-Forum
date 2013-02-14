<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

/**
 * The Kunena Installer Controller
 *
 * @since		2.0
 */
class KunenaControllerInstall extends JControllerLegacy {
	public function remind() {
		// User wants to continue using the old version
		$app = JFactory::getApplication();
		$app->setUserState('kunena-old', 1);
		$this->setRedirect(JRoute::_('index.php?option=com_kunena', false), sprintf('Installation screen will show up the next time you log in and visit Kunena administration.'));
	}

	public function update() {
		JRequest::setVar('hidemainmenu', 1);
		$new = file_exists(JPATH_COMPONENT.'/new') ? '/new' : '';
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install', JPATH_COMPONENT.$new, 'en-GB');
		$lang->load('com_kunena.install', JPATH_COMPONENT.$new);

		if ($new && file_exists(JPATH_COMPONENT.'/new/install')) {
			$md5 = md5_file(JPATH_COMPONENT.'/new/install/entrypoints/tmpl.update.php');
			$this->restore(JPATH_COMPONENT.'/new/install', JPATH_COMPONENT.'/install');
			$this->waitFile(JPATH_COMPONENT.'/install/entrypoints/tmpl.update.php', $md5);
			JFolder::delete(JPATH_COMPONENT.'/new/install');
		}
		$this->app = JFactory::getApplication();
		$this->document = JFactory::getDocument();
		include JPATH_COMPONENT.'/install/entrypoints/tmpl.update.php';
	}

	public function prepare() {
		// Start our main installer
		if (file_exists(JPATH_COMPONENT.'/bak')) JFolder::delete(JPATH_COMPONENT.'/bak');
		if (file_exists(JPATH_COMPONENT.'/new')) {
			$md5 = md5_file(JPATH_COMPONENT.'/new/install/entrypoints/api.php');
			$this->restore(JPATH_COMPONENT.'/new', JPATH_COMPONENT);
			JFolder::delete(JPATH_COMPONENT.'/new');
		}
		clearstatcache();
		JFile::copy(JPATH_COMPONENT.'/install/entrypoints/api.php', JPATH_COMPONENT.'/api.php');
		JFile::copy(JPATH_COMPONENT.'/install/entrypoints/kunena.php', JPATH_ROOT.'/components/com_kunena/kunena.php');
		JFile::copy(JPATH_COMPONENT.'/install/entrypoints/router.php', JPATH_ROOT.'/components/com_kunena/router.php');
		if (!empty($md5)) $this->waitFile(JPATH_COMPONENT.'/api.php', $md5);
		JFolder::delete(JPATH_COMPONENT.'/install/entrypoints');
		$success = JArchive::extract ( JPATH_COMPONENT.'/archive/com_kunena-install.zip', JPATH_COMPONENT.'/install' );
		clearstatcache();
		if (function_exists('apc_clear_cache')) apc_clear_cache('system');

		$this->setRedirect(JRoute::_($this->getUrl(), false));
	}

	public function display($cachable = false, $urlparams = Array()) {
		$view = JRequest::getCmd ( 'view' );
		$task = JRequest::getCmd ( 'task' );

		$app = JFactory::getApplication();
		if ($app->getUserState('kunena-old') && file_exists(JPATH_COMPONENT.'/bak/admin.kunena.php')) {
			// Load old version
			require_once JPATH_COMPONENT.'/bak/admin.kunena.php';
			return;
		}

		$version = $this->detectKunena();
		if ($view == 'update') {
			$this->update();
		} elseif ($version && $view != 'install' && version_compare($version, '2.0', '<')) {
			// Display pre-installation screen
			$this->preInstallKunena($version);
		} else {
			$this->prepare();
		}
	}

	protected function preInstallKunena($oldversion) {
		$version = '@kunenaversion@';
		$shortversion = substr($version, 0, 3);
		JRequest::setVar('hidemainmenu', 1);
		$new = file_exists(JPATH_COMPONENT.'/new') ? '/new' : '';

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install', JPATH_COMPONENT.$new, 'en-GB');
		$lang->load('com_kunena.install', JPATH_COMPONENT.$new);

		JFactory::getDocument()->addStyleDeclaration("
	div.icon-48-kunena {
		background-image:url('components/com_kunena$new/install/media/kunena-logo-48-white.png');
	}
	.kunena {
	font-size: 15px;
	}
	.kunena a.btn {
	display: inline-block;
	*display: inline;
	padding: 4px 10px 4px;
	margin-bottom: 0;
	*margin-left: .3em;
	font-size: 13px;
	line-height: 18px;
	*line-height: 20px;
	color: #333333;
	text-align: center;
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
	vertical-align: middle;
	cursor: pointer;
	background-color: #f5f5f5;
	*background-color: #e6e6e6;
	background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
	background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
	background-repeat: repeat-x;
	border: 1px solid #cccccc;
	*border: 0;
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	border-color: #e6e6e6 #e6e6e6 #bfbfbf;
	border-bottom-color: #b3b3b3;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	filter: progid:dximagetransform.microsoft.gradient(startColorstr='#ffffff', endColorstr='#e6e6e6', GradientType=0);
	filter: progid:dximagetransform.microsoft.gradient(enabled=false);
	*zoom: 1;
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	}

	.kunena a.btn:hover,
	.kunena a.btn:active {
	background-color: #e6e6e6;
	*background-color: #d9d9d9;
	}

	.kunena a.btn:active {
	background-color: #cccccc \\9;
	}

	.kunena a.btn:hover {
	color: #333333;
	text-decoration: none;
	background-color: #e6e6e6;
	*background-color: #d9d9d9;
	/* Buttons in IE7 don't get borders, so darken on hover */

	background-position: 0 -15px;
	-webkit-transition: background-position 0.1s linear;
	-moz-transition: background-position 0.1s linear;
	-ms-transition: background-position 0.1s linear;
	-o-transition: background-position 0.1s linear;
	transition: background-position 0.1s linear;
	}

	.kunena a.btn:focus {
	outline: thin dotted #333;
	outline: 5px auto -webkit-focus-ring-color;
	outline-offset: -2px;
	}

	.kunena a.btn:active {
	background-color: #e6e6e6;
	background-color: #d9d9d9 \\9;
	background-image: none;
	outline: 0;
	-webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
	-moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
	box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
	}

	.kunena a.btn-large {
	padding: 9px 14px;
	font-size: 15px;
	line-height: normal;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	margin-top: 1px;
	}

	.kunena a.btn-primary,
	.kunena a.btn-primary:hover {
	color: #ffffff;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	}

	.kunena a.btn {
	border-color: #ccc;
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	}

	.kunena a.btn-primary {
	background-color: #0074cc;
	*background-color: #0055cc;
	background-image: -ms-linear-gradient(top, #0088cc, #0055cc);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0055cc));
	background-image: -webkit-linear-gradient(top, #0088cc, #0055cc);
	background-image: -o-linear-gradient(top, #0088cc, #0055cc);
	background-image: -moz-linear-gradient(top, #0088cc, #0055cc);
	background-image: linear-gradient(top, #0088cc, #0055cc);
	background-repeat: repeat-x;
	border-color: #0055cc #0055cc #003580;
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	filter: progid:dximagetransform.microsoft.gradient(startColorstr='#0088cc', endColorstr='#0055cc', GradientType=0);
	filter: progid:dximagetransform.microsoft.gradient(enabled=false);
	}

	.kunena a.btn-primary:hover,
	.kunena a.btn-primary:active {
	background-color: #0055cc;
	*background-color: #004ab3;
	}

	.kunena a.btn-primary:active {
	background-color: #004099 \\9;
	}
	");
		JToolBarHelper::title(sprintf("Kunena %s is ready to be installed!", $version), 'kunena.png' );
		?>
	<div class="kunena">
	<p><strong><?php echo sprintf("A new version is available to upgrade your existing Kunena %s installation.", $oldversion) ?></strong></p>
	<p>
		<?php echo sprintf("Internally the new version of Kunena is very different to your currently installed version. In most situations, the upgrade is automatic and will be succesful; however it is highly recommended that you make a backup before going forward and installing the new version. To minimise downtime and extra work if something goes wrong, you should use your backup to create a test site to find out if there are any problems.") ?>
	</p>
	<p>
		<?php echo sprintf("Upgrading to Kunena %s involves changes that may affect Kunena's interoperability with other extensions installed on your site. For your own benefit, please ensure that all extensions that currently integrate with Kunena are up-to-date and compatible with Kunena %s. Some examples of affected extensions are SEF components, social networking, private messaging and Tapatalk. If you have questions about compatibility with these things, please contact the developer(s) of the extension(s) that you are currently using.", $version, $shortversion) ?>
	</p>
	<p>
		<?php echo sprintf("Kunena %s has a new templating system, which offers improved compatibility to both Joomla! 1.5 and 2.5. To minimise the changes that are needed to get your forum back to online, an updated version of Blue Eagle template, which was the default template in Kunena 1.6 and 1.7, is shipped with the installation package. Additionally Kunena %s offers limited backwards compatibility, which allows you to keep using many of the existing Kunena templates. For more information, please read %s Updating existing templates to Kunena 2.0 %s.", $shortversion, $shortversion, '<a href="http://docs.kunena.org/index.php/Updating_existing_templates_to_Kunena_2.0">', '</a>' ) ?>
	</p>
	<p>
		<?php echo sprintf("Please read %s Kunena %s Release notes %s to get more detailed information from the new release.", '<a href="http://docs.kunena.org/index.php/Kunena_@kunenaversion@_Read_Me">', $version, '</a>') ?>
	</p>
	<p>
		<?php echo sprintf("<strong style='color:red'>Important notice:</strong> Upgrading from your current installation to Kunena %s is irreversible. Downgrade is only possible by restoring backups.", $shortversion) ?>
	</p>
		<a class="btn btn-large btn-primary" href="index.php?option=com_kunena&view=update"><?php echo sprintf("Upgrade now") ?></a>
		<?php $kunenaBackupPhp = JPATH_ADMINISTRATOR . '/components/com_kunena/bak/admin.kunena.php';
			if (file_exists($kunenaBackupPhp)) {
				$link = 'index.php?option=com_kunena&task=remind';
				$text = sprintf("Remind me later and continue to Kunena administration");
			} else {
				$link = 'index.php';
				$text = sprintf("Remind me later");
			}
		?>
		<a class="btn btn-large" href="<?php echo $link ?>"><?php echo $text ?></a>
	</div>
	<?php
		return;
	}

	// Helper functions to enable pre-installation page

	protected function detectKunena() {
		$db = JFactory::getDBO();

		// Detect Kunena version table
		$table = str_replace('_', '\_', "{$db->getPrefix()}%_version");
		$query = "SHOW TABLES LIKE {$db->quote($table)}";
		$db->setQuery ( $query );
		$tables = (array) $db->loadColumn();
		if (in_array("{$db->getPrefix()}kunena_version", $tables)) {
			$table = '#__kunena_version';
		} elseif (in_array("{$db->getPrefix()}fb_version", $tables)) {
			$table = '#__fb_version';
		} else {
			return;
		}

		// Load Kunena version
		$query = "SELECT version FROM {$db->quoteName($table)} ORDER BY `id` DESC";
		$db->setQuery($query, 0, 1);
		$version = $db->loadResult();
		// Ignore FireBoard
		return version_compare ( $version, '1.0.5', ">" ) ? $version : null;
	}


	protected function restore($from, $to) {
		clearstatcache();
		$files = JFolder::files($from);
		$folders = JFolder::folders($from);

		foreach ($files as $filename) {
			if (file_exists("{$to}/{$filename}")) JFile::delete("{$to}/{$filename}");
			JFile::move("{$from}/{$filename}", "{$to}/{$filename}");
		}
		foreach ($folders as $foldername) {
			if (file_exists("{$to}/{$foldername}")) JFolder::delete("{$to}/{$foldername}");
			JFolder::move("{$from}/{$foldername}", "{$to}/{$foldername}");
		}
		clearstatcache();
	}

	protected function waitFile($file, $md5) {
		// Test if file has been fully copied and wait if not
		for ($i=0; $i<10; $i++) {
			if (is_file($file) && md5_file($file) == $md5) return true;
			sleep(1);
			clearstatcache();
		}
		return false;
	}

	protected function getUrl() {
		return 'index.php?option=com_kunena&view=install&task=prepare&start=1&'.JSession::getFormToken().'=1';
	}
}
