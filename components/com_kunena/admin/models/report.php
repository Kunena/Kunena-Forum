<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
jimport ( 'joomla.filesystem.folder' );
jimport ( 'joomla.filesystem.file' );

/**
 * Reportconfiguration Model for Kunena
 *
 * @since 2.0
 **/
class KunenaAdminModelReport extends KunenaModel {

	/**
	 * Method to generate all the reportconfiguration.
	 *
	 * @return	string
	 * @since	1.6
	 */
	public function getSystemReport () {
		$kunena_db = JFactory::getDBO ();

		if(!$this->app->getCfg('smtpuser' )) {
			$jconfig_smtpuser = 'Empty';
		} else {
			$jconfig_smtpuser = $this->app->getCfg('smtpuser' );
		}
		if($this->app->getCfg('ftp_enable' )) {
			$jconfig_ftp = 'Enabled';
		} else {
			$jconfig_ftp = 'Disabled';
		}
		if($this->app->getCfg('sef' )) {
			$jconfig_sef = 'Enabled';
		} else {
			$jconfig_sef = 'Disabled';
		}
		if($this->app->getCfg('sef_rewrite' )) {
			$jconfig_sef_rewrite = 'Enabled';
		} else {
			$jconfig_sef_rewrite = 'Disabled';
		}

		if (file_exists(JPATH_ROOT.'/.htaccess')) {
			$htaccess = 'Exists';
		} else {
			$htaccess = 'Missing';
		}

		if(ini_get('register_globals')) {
			$register_globals = '[u]register_globals:[/u] [color=#FF0000]On[/color]';
		} else {
			$register_globals = '[u]register_globals:[/u] Off';
		}
		if(ini_get('safe_mode')) {
			$safe_mode = '[u]safe_mode:[/u] [color=#FF0000]On[/color]';
		} else {
			$safe_mode = '[u]safe_mode:[/u] Off';
		}
		if(extension_loaded('mbstring')) {
			$mbstring = '[u]mbstring:[/u] Enabled';
		} else {
			$mbstring = '[u]mbstring:[/u] [color=#FF0000]Not installed[/color]';
		}
		if(extension_loaded('gd')) {
			$gd_info = gd_info ();
			$gd_support = '[u]GD:[/u] '.$gd_info['GD Version'] ;
		} else {
			$gd_support = '[u]GD:[/u] [color=#FF0000]Not installed[/color]';
		}
		$maxExecTime = ini_get('max_execution_time');
		$maxExecMem = ini_get('memory_limit');
		$fileuploads = ini_get('upload_max_filesize');
		$kunenaVersionInfo = KunenaVersion::getVersionHTML ();

		// Get Kunena default template
		$ktemplate = KunenaFactory::getTemplate();
		$ktemplatedetails = $ktemplate->getTemplateDetails();

		$jtemplatedetails = $this->_getJoomlaTemplate();

		$joomlamenudetails = $this->_getJoomlaMenuDetails();

		$collation = $this->_getTablesCollation();

		$kconfigsettings = $this->_getKunenaConfiguration();

		// Get Joomla! languages installed
		$joomlalanguages = $this->_getJoomlaLanguagesInstalled();

		// Check if Mootools plugins and others kunena plugins are enabled, and get the version of this modules
		$plg['jfirephp'] = $this->getExtensionVersion('system/jfirephp', 'System - JFirePHP');
		$plg['ksearch'] = $this->getExtensionVersion('search/kunena', 'Search - Kunena Search');
		$plg['kdiscuss'] = $this->getExtensionVersion('content/kunenadiscuss', 'Content - Kunena Discuss');
		$plg['jxfinderkunena'] = $this->getExtensionVersion('finder/plg_jxfinder_kunena', 'Finder - Kunena Posts');
		$plg['kjomsocialmenu'] = $this->getExtensionVersion('community/kunenamenu', 'JomSocial - My Kunena Forum Menu');
		$plg['kjomsocialmykunena'] = $this->getExtensionVersion('community/mykunena', 'JomSocial - My Kunena Forum Posts');
		$plg['kjomsocialgroups'] = $this->getExtensionVersion('community/kunenagroups', 'JomSocial - Kunena Groups');
		foreach ($plg as $id=>$item) {
			if (empty($item)) unset ($plg[$id]);
		}
		if (!empty($plg)) $plgtext = '[quote][b]Plugins:[/b] ' . implode(' | ', $plg) . ' [/quote]';
		else $plgtext = '[quote][b]Plugins:[/b] None [/quote]';

		$mod = array();
		$mod['kunenalatest'] = $this->getExtensionVersion('mod_kunenalatest', 'Kunena Latest');
		$mod['kunenastats'] = $this->getExtensionVersion('mod_kunenastats', 'Kunena Stats');
		$mod['kunenalogin'] = $this->getExtensionVersion('mod_kunenalogin', 'Kunena Login');
		$mod['kunenasearch'] = $this->getExtensionVersion('mod_kunenasearch', 'Kunena Search');
		foreach ($mod as $id=>$item) {
			if (empty($item)) unset ($mod[$id]);
		}
		if (!empty($mod)) $modtext = '[quote][b]Modules:[/b] ' . implode(' | ', $mod) . ' [/quote]';
		else $modtext = '[quote][b]Modules:[/b] None [/quote]';

		$thirdparty = array();
		$thirdparty['aup'] = $this->getExtensionVersion('com_alphauserpoints', 'AlphaUserPoints');
		$thirdparty['cb'] = $this->getExtensionVersion('com_comprofiler', 'CommunityBuilder');
		$thirdparty['jomsocial'] = $this->getExtensionVersion('com_community', 'Jomsocial');
		$thirdparty['uddeim'] = $this->getExtensionVersion('com_uddeim', 'UddeIM');
		foreach ($thirdparty as $id=>$item) {
			if (empty($item)) unset ($thirdparty[$id]);
		}
		if (!empty($thirdparty)) $thirdpartytext = '[quote][b]Third-party components:[/b] ' . implode(' | ', $thirdparty) . ' [/quote]';
		else $thirdpartytext = '[quote][b]Third-party components:[/b] None [/quote]';

		$sef = array();
		$sef['sh404sef'] = $this->getExtensionVersion('com_sh404sef', 'sh404sef');
		$sef['joomsef'] = $this->getExtensionVersion('com_joomsef', 'ARTIO JoomSEF');
		$sef['acesef'] = $this->getExtensionVersion('com_acesef', 'AceSEF');
		foreach ($sef as $id=>$item) {
			if (empty($item)) unset ($sef[$id]);
		}
		if (!empty($sef)) $seftext = '[quote][b]Third-party SEF components:[/b] ' . implode(' | ', $sef) . ' [/quote]';
		else $seftext = '[quote][b]Third-party SEF components:[/b] None [/quote]';

		// Get integration settings
		$integration_settings = $this->getIntegrationSettings();

		$report = '[confidential][b]Joomla! version:[/b] '.JVERSION.' [b]Platform:[/b] '.$_SERVER['SERVER_SOFTWARE'].' ('
	    .$_SERVER['SERVER_NAME'].') [b]PHP version:[/b] '.phpversion().' | '.$safe_mode.' | '.$register_globals.' | '.$mbstring
	    .' | '.$gd_support.' | [b]MySQL version:[/b] '.$kunena_db->getVersion().' | [b]Base URL:[/b]' .JUri::root(). '[/confidential][quote][b]Database collation check:[/b] '.$collation.'
		[/quote][quote][b]Joomla! SEF:[/b] '.$jconfig_sef.' | [b]Joomla! SEF rewrite:[/b] '
	    .$jconfig_sef_rewrite.' | [b]FTP layer:[/b] '.$jconfig_ftp.' |
	    [confidential][b]Mailer:[/b] '.$this->app->getCfg('mailer' ).' | [b]Mail from:[/b] '.$this->app->getCfg('mailfrom' ).' | [b]From name:[/b] '.$this->app->getCfg('fromname' ).' | [b]SMTP Secure:[/b] '.$this->app->getCfg('smtpsecure' ).' | [b]SMTP Port:[/b] '.$this->app->getCfg('smtpport' ).' | [b]SMTP User:[/b] '.$jconfig_smtpuser.' | [b]SMTP Host:[/b] '.$this->app->getCfg('smtphost' ).' [/confidential] [b]htaccess:[/b] '.$htaccess
	    .' | [b]PHP environment:[/b] [u]Max execution time:[/u] '.$maxExecTime.' seconds | [u]Max execution memory:[/u] '
	    .$maxExecMem.' | [u]Max file upload:[/u] '.$fileuploads.' [/quote] [quote][b]Kunena menu details[/b]:[spoiler] '.$joomlamenudetails.'[/spoiler][/quote][quote][b]Joomla default template details :[/b] '.$jtemplatedetails->name.' | [u]author:[/u] '.$jtemplatedetails->author.' | [u]version:[/u] '.$jtemplatedetails->version.' | [u]creationdate:[/u] '.$jtemplatedetails->creationdate.' [/quote][quote][b]Kunena default template details :[/b] '.$ktemplatedetails->name.' | [u]author:[/u] '.$ktemplatedetails->author.' | [u]version:[/u] '.$ktemplatedetails->version.' | [u]creationdate:[/u] '.$ktemplatedetails->creationDate.' [/quote][quote] [b]Kunena version detailed:[/b] '.$kunenaVersionInfo.'
	    | [u]Kunena detailed configuration:[/u] [spoiler] '.$kconfigsettings.'[/spoiler]| [u]Kunena integration settings:[/u][spoiler] '.implode(' ', $integration_settings).'[/spoiler]| [u]Joomla! detailed language files installed:[/u][spoiler] '.$joomlalanguages.'[/spoiler][/quote]'.$thirdpartytext.' '.$seftext.' '.$plgtext.' '.$modtext;
		return $report;
	}

	/**
	 * Method to get all languages installed into Joomla! and the default one
	 *
	 * @return	string
	 * @since	2.0
	 */
	protected function _getJoomlaLanguagesInstalled() {
		$lang = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages();
		$table_lang = '[table]';
		$table_lang .= '[tr][th]Joomla! languages installed:[/th][/tr]';
		foreach ($languages as $language) {
			$table_lang .= '[tr][td]'. $language['tag'] .'[/td][td]'. $language['name'].'[/td][/tr]';
		}

		$table_lang .= '[/table]';

		return $table_lang;
	}

	/**
	 * Method to get all the kunena configuration settings.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getKunenaConfiguration() {
		if ($this->config) {
			$params = $this->config->getProperties();

			$kconfigsettings = '[table]';
			$kconfigsettings .= '[tr][th]Kunena config settings:[/th][/tr]';
			foreach ($params as $key => $value ) {

				if (!is_array($value) && $key != 'id' && $key != 'board_title' && $key != 'email' && $key != 'offline_message'
					&& $key != 'recaptcha_publickey' && $key != 'recaptcha_privatekey' && $key != 'email_visible_address'
					&& $key != 'recaptcha_theme' && $key != 'stopforumspam_key' && $key != 'ebay_affiliate_id') {
					$kconfigsettings .= '[tr][td]'.$key.'[/td][td]'.$value.'[/td][/tr]';
				}
		}
			$kconfigsettings .= '[/table]';
		} else {
			$kconfigsettings = 'Your configuration settings aren\'t yet recorded in the database';
		}

		return $kconfigsettings;
	}

	/**
	 * Method to get the default joomla template.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getJoomlaTemplate() {
		$db = JFactory::getDBO ();

		// Get Joomla! frontend assigned template
		$query = "SELECT template FROM #__template_styles WHERE client_id=0 AND home=1";

		$db->setQuery($query);
		$template = $db->loadResult();
		if (KunenaError::checkDatabaseError()) return false;

		$xml = simplexml_load_file(JPATH_SITE.'/templates/'.$template.'/templateDetails.xml');
		if (!$xml || $xml->getName() != 'extension') {
			return false;
		}

		$data = new stdClass();
		$data->name = (string) $xml->name;
		$data->type = (string) $xml->attributes()->type;
		$data->creationdate = (string) $xml->creationdate;
		$data->author = (string) $xml->author;
		$data->copyright = (string) $xml->copyright;
		$data->authorEmail = (string) $xml->authorEmail;
		$data->authorUrl = (string) $xml->authorUrl;
		$data->version = (string) $xml->version;
		$data->description = (string) $xml->description;
		$data->thumbnail = (string) $xml->thumbnail;

		if (!$data->creationdate) $data->creationdate = JText::_('Unknown');
		if (!$data->author) JText::_('Unknown');

		return $data;
	}

	/**
	 * Method to get all joomla menu details about kunena.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getJoomlaMenuDetails() {
		$items = KunenaMenuFix::getAll();

		$joomlamenudetails = '[table][tr][td][u] ID [/u][/td][td][u] Name [/u][/td][td][u] Menutype [/u][/td][td][u] Link [/u][/td][td][u] Path [/u][/td][/tr] ';
		foreach($items as $item) {
			$link = preg_replace('/^.*\?(option=com_kunena&)?/', '', $item->link);
			$joomlamenudetails .= '[tr][td]'.$item->id.' [/td][td] '.$item->title.' [/td][td] '.$item->menutype.' [/td][td] '.$link.' [/td][td] '.$item->route.'[/td][/tr] ';
		}
		$joomlamenudetails .='[/table]';

		return $joomlamenudetails;

	}

	/**
	 * Method to check the tables collation.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getTablesCollation() {
		$kunena_db = JFactory::getDBO ();

		// Check each table in the database if the collation is on utf8
		$tableslist = $kunena_db->getTableList();
		$collation = '';
		foreach($tableslist as $table) {
			if (preg_match('`_kunena_`',$table)) {
				$kunena_db->setQuery("SHOW FULL FIELDS FROM " .$table. "");
				$fullfields = $kunena_db->loadObjectList ();
				if (KunenaError::checkDatabaseError()) return null;

				$fieldTypes = array('tinytext','text','char','varchar');

				foreach ($fullfields as $row) {
					$tmp = strpos ( $row->Type , '(' );

					if ($tmp) {
						if ( in_array(substr($row->Type,0,$tmp),$fieldTypes) ) {
							if(!empty($row->Collation) && !preg_match('`utf8`',$row->Collation)) {
								$collation .= $table.' [color=#FF0000]have wrong collation of type '.$row->Collation.' [/color] on field '.$row->Field.'  ';
							}
						}
					} else {
						if ( in_array($row->Type,$fieldTypes) ) {
							if(!empty($row->Collation) && !preg_match('`utf8`',$row->Collation)) {
								$collation .= $table.' [color=#FF0000]have wrong collation of type '.$row->Collation.' [/color] on field '.$row->Field.'  ';
							}
						}
					}
				}
			}
		}
		if(empty($collation)) {
			$collation = 'The collation of your table fields are correct';
		}

		return $collation;
	}

	/**
	 * Return extension version string if installed.
	 *
	 * @param  string  $extension
	 * @param  string  $name
	 * @return	string
	 * @since	1.6
	 */
	protected function getExtensionVersion($extension, $name) {
		if (substr($extension, 0, 4) == 'com_') {
			$path = JPATH_ADMINISTRATOR . "/components/{$extension}";
		} elseif (substr($extension, 0, 4) == 'mod_') {
			$path = JPATH_SITE . "/modules/{$extension}";
		} else {
			list($folder, $element) = explode('/', $extension, 2);
			$path = JPATH_PLUGINS . "/{$folder}/{$element}";
		}
		$version = $this->findExtensionVersion($path);
		return $version ? '[u]'.$name.'[/u] '.$version : '';
	}

	/**
	 * Tries to find the extension manifest file and returns version
	 *
	 * @param  $path $path	Path to extension directory
	 * @return  string  Version number
	 */
	public function findExtensionVersion($path) {
		if (is_file($path)) {
			// Make array from the xml file
			$xmlfiles = array($path);
		} elseif (is_dir($path)) {
			// Get an array of all the XML files from the directory
			$xmlfiles = JFolder::files($path, '\.xml$', 1, true);
		}
		$version = null;
		if (!empty($xmlfiles)) {
			$installer = JInstaller::getInstance();

			foreach ($xmlfiles as $file) {
				// Is it a valid Joomla installation manifest file?
				$manifest = $installer->isManifest($file);
				$newversion = $manifest ? (string) $manifest->version[0] : null;

				// We check all files just in case if there are more than one manifest file
				if (version_compare($newversion, $version, '>')) {
					$version = $newversion;
				}
			}
		}
		return $version;
	}

	public function getIntegrationSettings() {
		$plugins_list = array('alphauserpoints' => 'Kunena - AlphaUserPoints','comprofiler' => 'Kunena - Community Builder','gravatar' => 'Kunena - Gravatar','community' => 'Kunena - JomSocial','joomla' => 'Kunena - Joomla', 'kunena' => 'Kunena - Kunena', 'uddeim' => 'Kunena - UddeIM');
		$plugin_final = array();

		foreach($plugins_list as $name=>$desc) {
		$plugin = JPluginHelper::getPlugin('kunena', $name);

		if ($plugin) {
			$pluginParams = new JRegistry($plugin->params);
			$params = $pluginParams->toArray();
			$plugin_final[] = '[b]'.$desc.'[/b] Enabled: ';
			foreach ($params as $param=>$value) {
				$plugin_final[] = "{$param}={$value} ";
			}
			$plugin_final[] = "\n";
		} else {
			$plugin_final[] = "[b]{$desc}[/b] Disabled\n";
		}

		}

		return $plugin_final;
	}
}
