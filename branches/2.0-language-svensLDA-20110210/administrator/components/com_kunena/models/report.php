<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
jimport ( 'joomla.filesystem.file' );

/**
 * Reportconfiguration Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelReport extends JModel {

	/**
	 * Method to generate all the reportconfiguration.
	 *
	 * @return	string
	 * @since	1.6
	 */
	public function getSystemReport () {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = JFactory::getDBO ();

		$jversion = new JVersion();
		$jversiontxt = $jversion->PRODUCT .' '. $jversion->RELEASE .'.'. $jversion->DEV_LEVEL .' '. $jversion->DEV_STATUS.' [ '.$jversion->CODENAME .' ] '. $jversion->RELDATE;

		if($kunena_app->getCfg('legacy' )) {
			$jconfig_legacy = '[color=#FF0000]Enabled[/color]';
		} else {
			$jconfig_legacy = 'Disabled';
		}
		if(!$kunena_app->getCfg('smtpuser' )) {
			$jconfig_smtpuser = 'Empty';
		}
		if($kunena_app->getCfg('ftp_enable' )) {
			$jconfig_ftp = 'Enabled';
		} else {
			$jconfig_ftp = 'Disabled';
		}
		if($kunena_app->getCfg('sef' )) {
			$jconfig_sef = 'Enabled';
		} else {
			$jconfig_sef = 'Disabled';
		}
		if($kunena_app->getCfg('sef_rewrite' )) {
			$jconfig_sef_rewrite = 'Enabled';
		} else {
			$jconfig_sef_rewrite = 'Disabled';
		}

		if (file_exists(JPATH_ROOT. DS. '.htaccess')) {
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

		// Get Kunena default template
		$ktemplate = KunenaFactory::getTemplate();
		$ktempaltedetails = $ktemplate->getTemplateDetails();

		//get all the config settings for Kunena
		$kconfig = $this->_getKunenaConfiguration();

		$jtemplate = $this->_getJoomlaTemplate($jversion);

		$menudisplaytable = $this->_getJoomlaMenuDetails($jversion);

		$collation = $this->_getTablesCollation();

		// Check if Mootools plugins and others kunena plugins are enabled, and get the version of this modules
		jimport( 'joomla.plugin.helper' );

		if ( JPluginHelper::isEnabled('system', 'mtupgrade') ) 	$mtupgrade = '[u]System - Mootools Upgrade:[/u] Enabled';
		else $mtupgrade = '[u]System - Mootools Upgrade:[/u] Disabled';

		if ( JPluginHelper::isEnabled('system', 'mootools12') ) $plg_mt = '[u]System - Mootools12:[/u] Enabled';
		else $plg_mt = '[u]System - Mootools12:[/u] Disabled';

		$plg_jfirephp = $this->_checkThirdPartyVersion('jfirephp', 'jfirephp', 'JFirePHP', 'plugins/system', 'system', 0, 0, 1);
		$plg_ksearch = $this->_checkThirdPartyVersion('kunenasearch', 'kunenasearch', 'Kunena Search', 'plugins/search', 'search', 0, 0, 1);
		$plg_kdiscuss = $this->_checkThirdPartyVersion('kunenadiscuss', 'kunenadiscuss', 'Kunena Discuss', 'plugins/content', 'content', 0, 0, 1);
		$plg_kjomsocialmenu = $this->_checkThirdPartyVersion('kunenamenu', 'kunenamenu', 'My Kunena Forum Menu', 'plugins/community', 'community', 0, 0, 1);
		$plg_kjomsocialmykunena = $this->_checkThirdPartyVersion('mykunena', 'mykunena', 'My Kunena Forum Posts', 'plugins/community', 'community', 0, 0, 1);
		$mod_kunenalatest = $this->_checkThirdPartyVersion('mod_kunenalatest', 'mod_kunenalatest', 'Kunena Latest', 'modules/mod_kunenalatest', null, 0, 1, 0);
		$mod_kunenastats = $this->_checkThirdPartyVersion('mod_kunenastats', 'mod_kunenastats', 'Kunena Stats', 'modules/mod_kunenastats', null, 0, 1, 0);
		$mod_kunenalogin = $this->_checkThirdPartyVersion('mod_kunenalogin', 'mod_kunenalogin', 'Kunena Login', 'modules/mod_kunenalogin', null, 0, 1, 0);
		$aup = $this->_checkThirdPartyVersion('alphauserpoints', 'alphauserpoints', 'AlphaUserPoints', 'components/com_alphauserpoints', null, 1, 0, 0);
		$cb = $this->_checkThirdPartyVersion('comprofiler', 'comprofilej' , 'CommunityBuilder', 'components/com_comprofiler', null, 1, 0, 0);
		$jomsocial = $this->_checkThirdPartyVersion('community', 'community', 'Jomsocial', 'components/com_community', null, 1, 0, 0);
		$uddeim = $this->_checkThirdPartyVersion('uddeim', 'uddeim.j15', 'UddeIm', 'components/com_uddeim', null, 1, 0, 0);

		$sh404sef = $this->_checkThirdPartyVersion('sh404sef', 'sh404sef', 'sh404sef', 'components/com_sh404sef', null, 1, 0, 0);
		$joomsef = $this->_checkThirdPartyVersion('joomsef', 'sef', 'ARTIO JoomSEF', 'components/com_sef', null, 1, 0, 0);
		$acesef = $this->_checkThirdPartyVersion('acesef', 'acesef', 'AceSEF', 'components/com_acesef', null, 1, 0, 0);

		// FIXME: $kconfigsettings ?!?
		// Also model should not build html, it should just give data
		$report = '[confidential][b]Joomla! version:[/b] '.$jversiontxt.' [b]Platform:[/b] '.$_SERVER['SERVER_SOFTWARE'].' ('
	    .$_SERVER['SERVER_NAME'].') [b]PHP version:[/b] '.phpversion().' | '.$safe_mode.' | '.$register_globals.' | '.$mbstring
	    .' | '.$gd_support.' | [b]MySQL version:[/b] '.$kunena_db->getVersion().'[/confidential][quote][b]Database collation check:[/b] '.$collation.'
		[/quote][quote][b]Legacy mode:[/b] '.$jconfig_legacy.' | [b]Joomla! SEF:[/b] '.$jconfig_sef.' | [b]Joomla! SEF rewrite:[/b] '
	    .$jconfig_sef_rewrite.' | [b]FTP layer:[/b] '.$jconfig_ftp.' |[confidential][b]Mailer:[/b] '.$kunena_app->getCfg('mailer' ).' | [b]Mail from:[/b] '.$kunena_app->getCfg('mailfrom' ).' | [b]From name:[/b] '.$kunena_app->getCfg('fromname' ).' | [b]SMTP Secure:[/b] '.$kunena_app->getCfg('smtpsecure' ).' | [b]SMTP Port:[/b] '.$kunena_app->getCfg('smtpport' ).' | [b]SMTP User:[/b] '.$jconfig_smtpuser.' | [b]SMTP Host:[/b] '.$kunena_app->getCfg('smtphost' ).' [/confidential] [b]htaccess:[/b] '.$htaccess
	    .' | [b]PHP environment:[/b] [u]Max execution time:[/u] '.$maxExecTime.' seconds | [u]Max execution memory:[/u] '
	    .$maxExecMem.' | [u]Max file upload:[/u] '.$fileuploads.' [/quote][confidential][b]Kunena menu details[/b]:[spoiler] '.$menudisplaytable.'[/spoiler][/confidential][quote][b]Joomla default template details :[/b] '.$jtemplate->name.' | [u]author:[/u] '.$jtemplate->author.' | [u]version:[/u] '.$jtemplate->version.' | [u]creationdate:[/u] '.$jtemplate->creationdate.' [/quote][quote][b]Kunena default template details :[/b] '.$ktempaltedetails->name.' | [u]author:[/u] '.$ktempaltedetails->author.' | [u]version:[/u] '.$ktempaltedetails->version.' | [u]creationdate:[/u] '.$ktempaltedetails->creationDate.' [/quote][quote] [b]Kunena version detailled:[/b] [u]Installed version:[/u] '.KunenaForum::version().' | [u]Build:[/u] '
	    .KunenaForum::versionBuild().' | [u]Version name:[/u] '.KunenaForum::versionName().' | [u]Kunena detailled configuration:[/u] [spoiler] '.$kconfig.'[/spoiler][/quote][quote][b]Third-party components:[/b] '.$aup.' | '.$cb.' | '.$jomsocial.' | '.$uddeim.' [/quote][quote][b]Third-party SEF components:[/b] '.$sh404sef.' | '.$joomsef.' | '.$acesef.' [/quote][quote][b]Plugins:[/b] '.$plg_mt.' | '.$mtupgrade.' | '.$plg_jfirephp.' | '.$plg_kdiscuss.' | '.$plg_ksearch.' | '.$plg_kjomsocialmenu.' | '.$plg_kjomsocialmykunena.' [/quote][quote][b]Modules:[/b] '.$mod_kunenalatest.' | '.$mod_kunenastats.' | '.$mod_kunenalogin.'[/quote]';

		return $report;
	}

	/**
	 * Method to get all the kunena configuration settings.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getKunenaConfiguration() {
		kimport('kunena.error');
		$kunena_db = JFactory::getDBO ();
		$kunena_db->setQuery ( "SHOW TABLES LIKE '" . $kunena_db->getPrefix () ."kunena_config'" );
		$table_config = $kunena_db->loadResult ();
		if (KunenaError::checkDatabaseError()) return;

		if ($table_config) {
			$kunena_db->setQuery("SELECT * FROM #__kunena_config");
			$kconfig = (object)$kunena_db->loadObject ();
			if (KunenaError::checkDatabaseError()) return;

			$kconfigsettings = '[table]';
			$kconfigsettings .= '[th]Kunena config settings:[/th]';
			foreach ($kconfig as $key => $value ) {
				if ($key != 'id' && $key != 'email') {
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
	protected function _getJoomlaTemplate($jversion) {
		kimport('kunena.error');
  		$kunena_db = JFactory::getDBO ();

		if ($jversion->RELEASE == '1.5') {
			$templatedetails = new stdClass();
			// Get Joomla! frontend assigned template for Joomla! 1.5

			$query = ' SELECT template '
				.' FROM #__templates_menu '
				.' WHERE client_id = 0 AND menuid = 0 ';
			$kunena_db->setQuery($query);
			$jdefaultemplate = $kunena_db->loadResult();
			if (KunenaError::checkDatabaseError()) return;

			$templatedetails->name = $jdefaultemplate;

			$xml_tmpl = JFactory::getXMLparser('Simple');
			$xml_tmpl->loadFile(JPATH_SITE.'/templates/'.$jdefaultemplate.'/templateDetails.xml');
			$templatecreationdate= $xml_tmpl->document->creationDate[0];
			$templatedetails->creationdate = $templatecreationdate->data();
			$templateauthor= $xml_tmpl->document->author[0];
			$templatedetails->author = $templateauthor->data();
			$templateversion = $xml_tmpl->document->version[0];
			$templatedetails->version = $templateversion->data();
		} elseif ($jversion->RELEASE == '1.6') {
			$templatedetails = new stdClass();
			// Get Joomla! frontend assigned template for Joomla! 1.6
			$query = " SELECT template,title "
				." FROM #__template_styles "
				." WHERE client_id = '0' AND home = '1'";
			$kunena_db->setQuery($query);
			$jdefaultemplate = $kunena_db->loadObject();
			//if (KunenaError::checkDatabaseError()) return;

			$templatedetails->name = $jdefaultemplate->template;

			$xml_tmpl = JFactory::getXMLparser('Simple');
			$xml_tmpl->loadFile(JPATH_SITE.'/templates/'.$jdefaultemplate->template.'/templateDetails.xml');
			$templatecreationdate= $xml_tmpl->document->creationDate[0];
			$templatedetails->creationdate = $templatecreationdate->data();
			$templateauthor= $xml_tmpl->document->author[0];
			$templatedetails->author = $templateauthor->data();
			$templateversion = $xml_tmpl->document->version[0];
			$templatedetails->version = $templateversion->data();
		}

		return $templatedetails;
	}

	/**
	 * Method to get all joomla menu details about kunena.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getJoomlaMenuDetails($jversion) {
		kimport('kunena.error');
		$kunena_db = JFactory::getDBO ();
		if ($jversion->RELEASE == '1.5') {
			// Get Kunena menu items
			$query = "SELECT id, menutype, name, alias, link, parent "
				." FROM #__menu "
				." WHERE menutype = {$kunena_db->Quote('kunenamenu')} OR name='forum' ORDER BY id ASC";
			$kunena_db->setQuery($query);
			$kmenustype = $kunena_db->loadObjectlist();
			if (KunenaError::checkDatabaseError()) return;

			$joomlamenudetails = '[table][tr][td][u] ID [/u][/td][td][u] Name [/u][/td][td][u] Alias [/u][/td][td][u] Menutype [/u][/td][td][u] Link [/u][/td][td][u] ParentID [/u][/td][/tr] ';
			foreach($kmenustype as $item) {
				$joomlamenudetails .= '[tr][td]'.$item->id.' [/td][td] '.$item->name.' [/td][td] '.$item->alias.' [/td][td] '.$item->menutype.' [/td][td] '.$item->link.' [/td][td] '.$item->parent.'[/td][/tr] ';
			}
		} elseif ($jversion->RELEASE == '1.6') {
			// Get Kunena menu items
			$query = "SELECT id "
				." FROM #__menu "
				." WHERE type='component' AND title ='Kunena Forum' ORDER BY id ASC";
			$kunena_db->setQuery($query);
			$kmenuparentid = $kunena_db->loadResult();
			if (KunenaError::checkDatabaseError()) return;

			$query = "SELECT id, menutype, title, alias, link, path "
				." FROM #__menu "
				." WHERE parent_id={$kunena_db->Quote($kmenuparentid)} AND type='component' OR title='Kunena Forum' OR title='Kunena' ORDER BY id ASC";
			$kunena_db->setQuery($query);
			$kmenustype = $kunena_db->loadObjectlist();
			if (KunenaError::checkDatabaseError()) return;

			$joomlamenudetails = '[table][tr][td][u] ID [/u][/td][td][u] Name [/u][/td][td][u] Alias [/u][/td][td][u] Menutype [/u][/td][td][u] Link [/u][/td][td][u] Path [/u][/td][/tr] ';
			foreach($kmenustype as $item) {
				$joomlamenudetails .= '[tr][td]'.$item->id.' [/td][td] '.$item->title.' [/td][td] '.$item->alias.' [/td][td] '.$item->menutype.' [/td][td] '.$item->link.' [/td][td] '.$item->path.'[/td][/tr] ';
			}
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
		kimport('kunena.error');
		$kunena_db = JFactory::getDBO ();

		// Check each table in the database if the collation is on utf8
		$tableslist = $kunena_db->getTableList();
		$collation = '';
		foreach($tableslist as $table) {
			if (preg_match('`_kunena_`',$table)) {
				$kunena_db->setQuery("SHOW FULL FIELDS FROM " .$table. "");
				$fullfields = $kunena_db->loadObjectList ();
				if (KunenaError::checkDatabaseError()) return;

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
	 * Method to check if third party plugin/module/component is here and in which version.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _checkThirdPartyVersion($namephp, $namexml, $namedetailled, $path, $plggroup=null, $components=0, $module=0, $plugin=0) {
	// need update
		if ($components) {
		if ( JComponentHelper::isEnabled($namephp) && JFile::exists(JPATH_SITE.'/'.$path.'/'.$namephp.'.php') ) {
			if ( JFile::exists(JPATH_ADMINISTRATOR.'/'.$path.'/'.$namexml.'.xml') ) {
				$xml_com = JFactory::getXMLparser('Simple');
				$xml_com->loadFile(JPATH_ADMINISTRATOR.'/'.$path.'/'.$namexml.'.xml');
				$com_version = $xml_com->document->version[0];
				$com_version = '[u]'.$namedetailled.':[/u] Installed (Version : '.$com_version->data().')';
			} else {
				$com_version = '[u]'.$namedetailled.':[/u] The file doesn\'t exist '.$namexml.'.xml !';
			}
		} else {
			$com_version = '[u]'.$namedetailled.':[/u] Disabled or not installed';
		}
		return $com_version;
	} elseif ($module) {
		if ( JModuleHelper::isEnabled($namephp) && JFile::exists(JPATH_SITE.'/'.$path.'/'.$namephp.'.php') ) {
			if ( JFile::exists(JPATH_SITE.'/'.$path.'/'.$namexml.'.xml') ) {
				$xml_mod = JFactory::getXMLparser('Simple');
				$xml_mod->loadFile(JPATH_SITE.'/'.$path.'/'.$namexml.'.xml');
				$mod_version = $xml_mod->document->version[0];
				$mod_version = '[u]'.$namedetailled.':[/u] Enabled (Version : '.$mod_version->data().')';
			} else {
				$mod_version = '[u]'.$namedetailled.':[/u] The file doesn\'t exist '.$namexml.'.xml !';
			}
		} else {
			$mod_version = '[u]'.$namedetailled.':[/u] Disabled or not installed';
		}
		return $mod_version;
	} elseif ($plugin) {
		if ( JPluginHelper::isEnabled($plggroup, $namephp) && JFile::exists(JPATH_SITE.'/'.$path.'/'.$namephp.'.php') ) {
			if ( JFile::exists(JPATH_SITE.'/'.$path.'/'.$namexml.'.xml') ) {
				$xml_plg = JFactory::getXMLparser('Simple');
				$xml_plg->loadFile(JPATH_SITE.'/'.$path.'/'.$namexml.'.xml');
				$plg_version = $xml_plg->document->version[0];
				$plg_version = '[u]'.$namedetailled.':[/u] Enabled (Version : '.$plg_version->data().')';
			}	else {
				$plg_version = '[u]'.$namedetailled.':[/u] The file doesn\'t exist '.$namexml.'.xml !';
			}
		} else {
			$plg_version = '[u]'.$namedetailled.':[/u] Disabled or not installed';
		}
		return $plg_version;
	}
}
}