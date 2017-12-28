<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
require_once __DIR__ . '/cpanel.php';

/**
 * Tools Model for Kunena
 *
 * @since  2.0
 */
class KunenaAdminModelTools extends KunenaAdminModelCpanel
{
	/**
	 * @return mixed
	 *
	 */
	function getPruneCategories()
	{
		$cat_params                = array();
		$cat_params['ordering']    = 'ordering';
		$cat_params['toplevel']    = 0;
		$cat_params['sections']    = 0;
		$cat_params['direction']   = 1;
		$cat_params['unpublished'] = 1;
		$cat_params['action']      = 'admin';

		$forum = JHtml::_('kunenaforum.categorylist', 'prune_forum[]', 0, null, $cat_params, 'class="inputbox" multiple="multiple"', 'value', 'text');

		return $forum;
	}

	/**
	 * @return mixed
	 *
	 */
	function getPruneListtrashdelete()
	{
		$trashdelete    = array();
		$trashdelete [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_TRASH_USERMESSAGES'));
		$trashdelete [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_DELETE_PERMANENTLY'));

		return JHtml::_('select.genericlist', $trashdelete, 'trashdelete', 'class="inputbox" size="1"', 'value', 'text', 0);
	}

	/**
	 * @return mixed
	 *
	 */
	function getPruneControlOptions()
	{
		$contoloptions    = array();
		$contoloptions [] = JHtml::_('select.option', 'all', JText::_('COM_KUNENA_A_PRUNE_ALL'));
		$contoloptions [] = JHtml::_('select.option', 'normal', JText::_('COM_KUNENA_A_PRUNE_NORMAL'));
		$contoloptions [] = JHtml::_('select.option', 'locked', JText::_('COM_KUNENA_A_PRUNE_LOCKED'));
		$contoloptions [] = JHtml::_('select.option', 'unanswered', JText::_('COM_KUNENA_A_PRUNE_UNANSWERED'));
		$contoloptions [] = JHtml::_('select.option', 'answered', JText::_('COM_KUNENA_A_PRUNE_ANSWERED'));
		$contoloptions [] = JHtml::_('select.option', 'unapproved', JText::_('COM_KUNENA_A_PRUNE_UNAPPROVED'));
		$contoloptions [] = JHtml::_('select.option', 'deleted', JText::_('COM_KUNENA_A_PRUNE_DELETED'));
		$contoloptions [] = JHtml::_('select.option', 'shadow', JText::_('COM_KUNENA_A_PRUNE_SHADOW'));

		return JHtml::_('select.genericlist', $contoloptions, 'controloptions', 'class="inputbox" size="1"', 'value', 'text', 'normal');
	}

	/**
	 * @return mixed
	 *
	 */
	function getPruneKeepSticky()
	{
		$optionsticky    = array();
		$optionsticky [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_NO'));
		$optionsticky [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_YES'));

		return JHtml::_('select.genericlist', $optionsticky, 'keepsticky', 'class="inputbox" size="1"', 'value', 'text', 1);
	}

	protected $jconfig_smtpuser = null;
	protected $jconfig_ftp = null;
	protected $jconfig_sef = null;
	protected $jconfig_sef_rewrite = null;
	protected $htaccess = null;
	protected $register_globals = null;
	protected $safe_mode = null;
	protected $mbstring = null;
	protected $gd_info = null;
	protected $gd_support = null;
	protected $maxExecTime = null;
	protected $maxExecMem = null;
	protected $fileuploads = null;
	protected $kunenaVersionInfo = null;
	protected $ktemplate = null;
	protected $ktemplatedetails = null;
	protected $jtemplatedetails = null;
	protected $joomlamenudetails = null;
	protected $collation = null;
	protected $kconfigsettings = null;
	protected $joomlalanguages = null;
	protected $plgtext = null;
	protected $modtext = null;
	protected $thirdpartytext = null;
	protected $seftext = null;
	protected $integration_settings = null;

	/**
	 * Initialize data to generate configuration report
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	protected function getReportData()
	{
		if (!$this->app->get('smtpuser'))
		{
			$this->jconfig_smtpuser = 'Empty';
		}
		else
		{
			$this->jconfig_smtpuser = $this->app->get('smtpuser');
		}

		if ($this->app->get('ftp_enable'))
		{
			$this->jconfig_ftp = 'Enabled';
		}
		else
		{
			$this->jconfig_ftp = 'Disabled';
		}

		if ($this->app->get('sef'))
		{
			$this->jconfig_sef = 'Enabled';
		}
		else
		{
			$this->jconfig_sef = 'Disabled';
		}

		if ($this->app->get('sef_rewrite'))
		{
			$this->jconfig_sef_rewrite = 'Enabled';
		}
		else
		{
			$this->jconfig_sef_rewrite = 'Disabled';
		}

		if (is_file(JPATH_ROOT . '/.htaccess'))
		{
			$this->htaccess = 'Exists';
		}
		else
		{
			$this->htaccess = 'Missing';
		}

		if (ini_get('register_globals'))
		{
			$this->register_globals = '[u]register_globals:[/u] [color=#FF0000]On[/color]';
		}
		else
		{
			$this->register_globals = '[u]register_globals:[/u] Off';
		}

		if (ini_get('safe_mode'))
		{
			$this->safe_mode = '[u]safe_mode:[/u] [color=#FF0000]On[/color]';
		}
		else
		{
			$this->safe_mode = '[u]safe_mode:[/u] Off';
		}

		if (extension_loaded('mbstring'))
		{
			$this->mbstring = '[u]mbstring:[/u] Enabled';
		}
		else
		{
			$this->mbstring = '[u]mbstring:[/u] [color=#FF0000]Not installed[/color]';
		}

		if (extension_loaded('gd'))
		{
			$gd_info    = gd_info();
			$this->gd_support = '[u]GD:[/u] ' . $gd_info['GD Version'];
		}
		else
		{
			$this->gd_support = '[u]GD:[/u] [color=#FF0000]Not installed[/color]';
		}

		$this->maxExecTime       = ini_get('max_execution_time');
		$this->maxExecMem        = ini_get('memory_limit');
		$this->fileuploads       = ini_get('upload_max_filesize');
		$this->kunenaVersionInfo = KunenaVersion::getVersionHTML();

		// Get Kunena default template
		$ktemplate        = KunenaFactory::getTemplate();
		$this->ktemplatedetails = $ktemplate->getTemplateDetails();
		$this->ktemplateparams = $ktemplate->params;

		$this->jtemplatedetails = $this->_getJoomlaTemplate();

		$this->joomlamenudetails = $this->_getJoomlaMenuDetails();

		$this->collation = $this->_getTablesCollation();

		$this->kconfigsettings = $this->_getKunenaConfiguration();

		// Get Joomla! languages installed
		$this->joomlalanguages = $this->_getJoomlaLanguagesInstalled();

		// Check if Mootools plugins and others kunena plugins are enabled, and get the version of this modules
		$plg['jfirephp']           = $this->getExtensionVersion('system/jfirephp', 'System - JFirePHP');
		$plg['ksearch']            = $this->getExtensionVersion('search/kunena', 'Search - Kunena Search');
		$plg['kdiscuss']           = $this->getExtensionVersion('content/kunenadiscuss', 'Content - Kunena Discuss');
		$plg['jxfinderkunena']     = $this->getExtensionVersion('finder/plg_jxfinder_kunena', 'Finder - Kunena Posts');
		$plg['kjomsocialmenu']     = $this->getExtensionVersion('community/kunenamenu', 'JomSocial - My Kunena Forum Menu');
		$plg['kjomsocialmykunena'] = $this->getExtensionVersion('community/mykunena', 'JomSocial - My Kunena Forum Posts');
		$plg['kjomsocialgroups']   = $this->getExtensionVersion('community/kunenagroups', 'JomSocial - Kunena Groups');

		foreach ($plg as $id => $item)
		{
			if (empty($item))
			{
				unset($plg[$id]);
			}
		}

		if (!empty($plg))
		{
			$this->plgtext = '[quote][b]Plugins:[/b] ' . implode(' | ', $plg) . ' [/quote]';
		}
		else
		{
			$this->plgtext = '[quote][b]Plugins:[/b] None [/quote]';
		}

		$mod                 = array();
		$mod['kunenalatest'] = $this->getExtensionVersion('mod_kunenalatest', 'Kunena Latest');
		$mod['kunenastats']  = $this->getExtensionVersion('mod_kunenastats', 'Kunena Stats');
		$mod['kunenalogin']  = $this->getExtensionVersion('mod_kunenalogin', 'Kunena Login');
		$mod['kunenasearch'] = $this->getExtensionVersion('mod_kunenasearch', 'Kunena Search');

		foreach ($mod as $id => $item)
		{
			if (empty($item))
			{
				unset($mod[$id]);
			}
		}

		if (!empty($mod))
		{
			$this->modtext = '[quote][b]Modules:[/b] ' . implode(' | ', $mod) . ' [/quote]';
		}
		else
		{
			$this->modtext = '[quote][b]Modules:[/b] None [/quote]';
		}

		$thirdparty              = array();
		$thirdparty['aup']       = $this->getExtensionVersion('com_alphauserpoints', 'AlphaUserPoints');
		$thirdparty['alup']       = $this->getExtensionVersion('com_altauserpoints', 'AltaUserPoints');
		$thirdparty['cb']        = $this->getExtensionVersion('com_comprofiler', 'CommunityBuilder');
		$thirdparty['jomsocial'] = $this->getExtensionVersion('com_community', 'Jomsocial');
		$thirdparty['uddeim']    = $this->getExtensionVersion('com_uddeim', 'UddeIM');

		foreach ($thirdparty as $id => $item)
		{
			if (empty($item))
			{
				unset($thirdparty[$id]);
			}
		}

		if (!empty($thirdparty))
		{
			$this->thirdpartytext = '[quote][b]Third-party components:[/b] ' . implode(' | ', $thirdparty) . ' [/quote]';
		}
		else
		{
			$this->thirdpartytext = '[quote][b]Third-party components:[/b] None [/quote]';
		}

		$sef             = array();
		$sef['sh404sef'] = $this->getExtensionVersion('com_sh404sef', 'sh404sef');
		$sef['joomsef']  = $this->getExtensionVersion('com_joomsef', 'ARTIO JoomSEF');
		$sef['acesef']   = $this->getExtensionVersion('com_acesef', 'AceSEF');

		foreach ($sef as $id => $item)
		{
			if (empty($item))
			{
				unset($sef[$id]);
			}
		}

		if (!empty($sef))
		{
			$this->seftext = '[quote][b]Third-party SEF components:[/b] ' . implode(' | ', $sef) . ' [/quote]';
		}
		else
		{
			$this->seftext = '[quote][b]Third-party SEF components:[/b] None [/quote]';
		}

		// Get integration settings
		$this->integration_settings = $this->getIntegrationSettings();
	}

	/**
	 * Method to generate the report configuration with anonymous data
	 *
	 * @since 5.0
	 *
	 * @return string
	 */
	public function getSystemReportAnonymous()
	{
		$kunena_db = JFactory::getDBO();

		$this->getReportData();

		$report = '[confidential][b]Joomla! version:[/b] ' . JVERSION . ' [b]Platform:[/b] ' . $_SERVER['SERVER_SOFTWARE'] . '[b]PHP version:[/b] ' . phpversion() . ' | ' . $this->safe_mode . ' | ' . $this->register_globals . ' | ' . $this->mbstring
			. ' | ' . $this->gd_support . ' | [b]MySQL version:[/b] ' . $kunena_db->getVersion() . ' | [b]Base URL:[/b]' . JUri::root() . '[/confidential][quote][b]Database collation check:[/b] ' . $this->collation . '
		[/quote][quote][b]Joomla! SEF:[/b] ' . $this->jconfig_sef . ' | [b]Joomla! SEF rewrite:[/b] '
			. $this->jconfig_sef_rewrite . ' | [b]FTP layer:[/b] ' . $this->jconfig_ftp . ' |
	    [confidential][b]Mailer:[/b] ' . $this->app->get('mailer') . ' | [b]SMTP Secure:[/b] ' . $this->app->get('smtpsecure') . ' | [b]SMTP Port:[/b] ' . $this->app->get('smtpport') . ' | [b]SMTP User:[/b] ' . $this->jconfig_smtpuser . ' | [b]SMTP Host:[/b] ' . $this->app->get('smtphost') . ' [/confidential] [b]htaccess:[/b] ' . $this->htaccess
			. ' | [b]PHP environment:[/b] [u]Max execution time:[/u] ' . $this->maxExecTime . ' seconds | [u]Max execution memory:[/u] '
			. $this->maxExecMem . ' | [u]Max file upload:[/u] ' . $this->fileuploads . ' [/quote] [quote][b]Kunena menu details[/b]:[spoiler] ' . $this->joomlamenudetails . '[/spoiler][/quote][quote][b]Joomla default template details :[/b] ' . $this->jtemplatedetails->name . ' | [u]author:[/u] ' . $this->jtemplatedetails->author . ' | [u]version:[/u] ' . $this->jtemplatedetails->version . ' | [u]creationdate:[/u] ' . $this->jtemplatedetails->creationdate . ' [/quote][quote][b]Kunena default template details :[/b] ' . $this->ktemplatedetails->name . ' | [u]author:[/u] ' . $this->ktemplatedetails->author . ' | [u]version:[/u] ' . $this->ktemplatedetails->version . ' | [u]creationdate:[/u] ' . $this->ktemplatedetails->creationDate . ' [/quote][quote][b]Kunena template params[/b]:[spoiler] ' . $this->_getKunenaTemplateDetailsReadable() . '[/spoiler][/quote][quote] [b]Kunena version detailed:[/b] ' . $this->kunenaVersionInfo . '
	    | [u]Kunena detailed configuration:[/u] [spoiler] ' . $this->kconfigsettings . '[/spoiler]| [u]Kunena integration settings:[/u][spoiler] ' . implode(' ', $this->integration_settings) . '[/spoiler]| [u]Joomla! detailed language files installed:[/u][spoiler] ' . $this->joomlalanguages . '[/spoiler][/quote]' . $this->thirdpartytext . ' ' . $this->seftext . ' ' . $this->plgtext . ' ' . $this->modtext;

		return $report;
	}

	/**
	 * Method to generate all the report configuration.
	 *
	 * @return    string
	 * @since    1.6
	 */
	public function getSystemReport()
	{
		$kunena_db = JFactory::getDBO();

		$this->getReportData();

		$report = '[confidential][b]Joomla! version:[/b] ' . JVERSION . ' [b]Platform:[/b] ' . $_SERVER['SERVER_SOFTWARE'] . ' ('
			. $_SERVER['SERVER_NAME'] . ') [b]PHP version:[/b] ' . phpversion() . ' | ' . $this->safe_mode . ' | ' . $this->register_globals . ' | ' . $this->mbstring
			. ' | ' . $this->gd_support . ' | [b]MySQL version:[/b] ' . $kunena_db->getVersion() . ' | [b]Base URL:[/b]' . JUri::root() . '[/confidential][quote][b]Database collation check:[/b] ' . $this->collation . '
		[/quote][quote][b]Joomla! SEF:[/b] ' . $this->jconfig_sef . ' | [b]Joomla! SEF rewrite:[/b] '
			. $this->jconfig_sef_rewrite . ' | [b]FTP layer:[/b] ' . $this->jconfig_ftp . ' |
	    [confidential][b]Mailer:[/b] ' . $this->app->get('mailer') . ' | [b]Mail from:[/b] ' . $this->app->get('mailfrom') . ' | [b]From name:[/b] ' . $this->app->get('fromname') . ' | [b]SMTP Secure:[/b] ' . $this->app->get('smtpsecure') . ' | [b]SMTP Port:[/b] ' . $this->app->get('smtpport') . ' | [b]SMTP User:[/b] ' . $this->jconfig_smtpuser . ' | [b]SMTP Host:[/b] ' . $this->app->get('smtphost') . ' [/confidential] [b]htaccess:[/b] ' . $this->htaccess
			. ' | [b]PHP environment:[/b] [u]Max execution time:[/u] ' . $this->maxExecTime . ' seconds | [u]Max execution memory:[/u] '
			. $this->maxExecMem . ' | [u]Max file upload:[/u] ' . $this->fileuploads . ' [/quote] [quote][b]Kunena menu details[/b]:[spoiler] ' . $this->joomlamenudetails . '[/spoiler][/quote][quote][b]Joomla default template details :[/b] ' . $this->jtemplatedetails->name . ' | [u]author:[/u] ' . $this->jtemplatedetails->author . ' | [u]version:[/u] ' . $this->jtemplatedetails->version . ' | [u]creationdate:[/u] ' . $this->jtemplatedetails->creationdate . ' [/quote][quote][b]Kunena default template details :[/b] ' . $this->ktemplatedetails->name . ' | [u]author:[/u] ' . $this->ktemplatedetails->author . ' | [u]version:[/u] ' . $this->ktemplatedetails->version . ' | [u]creationdate:[/u] ' . $this->ktemplatedetails->creationDate . ' [/quote] [quote][b]Kunena template params[/b]:[spoiler] ' . $this->_getKunenaTemplateDetailsReadable() . '[/spoiler][/quote][quote] [b]Kunena version detailed:[/b] ' . $this->kunenaVersionInfo . '
	    | [u]Kunena detailed configuration:[/u] [spoiler] ' . $this->kconfigsettings . '[/spoiler]| [u]Kunena integration settings:[/u][spoiler] ' . implode(' ', $this->integration_settings) . '[/spoiler]| [u]Joomla! detailed language files installed:[/u][spoiler] ' . $this->joomlalanguages . '[/spoiler][/quote]' . $this->thirdpartytext . ' ' . $this->seftext . ' ' . $this->plgtext . ' ' . $this->modtext;

		return $report;
	}

	/**
	 * Method to get all languages installed into Joomla! and the default one
	 *
	 * @return    string
	 *
	 * @since    2.0
	 */
	protected function _getJoomlaLanguagesInstalled()
	{
		$lang       = JFactory::getLanguage();
		$languages  = $lang->getKnownLanguages();
		$table_lang = '[table]';
		$table_lang .= '[tr][th]Joomla! languages installed:[/th][/tr]';

		foreach ($languages as $language)
		{
			$table_lang .= '[tr][td]' . $language['tag'] . '[/td][td]' . $language['name'] . '[/td][/tr]';
		}

		$table_lang .= '[/table]';

		return $table_lang;
	}

	/**
	 * Method to get all the kunena configuration settings.
	 *
	 * @return    string
	 * @since    1.6
	 */
	protected function _getKunenaConfiguration()
	{
		if ($this->config)
		{
			$params = $this->config->getProperties();

			$kconfigsettings = '[table]';
			$kconfigsettings .= '[tr][th]Kunena config settings:[/th][/tr]';

			foreach ($params as $key => $value)
			{
				if (!is_array($value) && $key != 'id' && $key != 'board_title' && $key != 'email' && $key != 'offline_message'
					&& $key != 'email_visible_address' && $key != 'stopforumspam_key' && $key != 'ebay_affiliate_id' 
					&& $key != 'ebay_api_key' && $key != 'twitter_consumer_key' && $key != 'twitter_consumer_secret' 
					&& $key != 'google_map_api_key')
				{
					$kconfigsettings .= '[tr][td]' . $key . '[/td][td]' . $value . '[/td][/tr]';
				}
			}

			$kconfigsettings .= '[/table]';
		}
		else
		{
			$kconfigsettings = 'Your configuration settings aren\'t yet recorded in the database';
		}

		return $kconfigsettings;
	}

	/**
	 * Method to get the default joomla template.
	 *
	 * @return    string
	 *
	 * @since    1.6
	 */
	protected function _getJoomlaTemplate()
	{
		$db = JFactory::getDBO();

		// Get Joomla! frontend assigned template
		$query = "SELECT template FROM #__template_styles WHERE client_id=0 AND home=1";

		$db->setQuery($query);

		try
		{
			$template = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$xml = simplexml_load_file(JPATH_SITE . '/templates/' . $template . '/templateDetails.xml');

		if (!$xml || $xml->getName() != 'extension')
		{
			return false;
		}

		$data               = new stdClass;
		$data->name         = (string) $xml->name;
		$data->type         = (string) $xml->attributes()->type;
		$data->creationdate = (string) $xml->creationDate;
		$data->author       = (string) $xml->author;
		$data->copyright    = (string) $xml->copyright;
		$data->authorEmail  = (string) $xml->authorEmail;
		$data->authorUrl    = (string) $xml->authorUrl;
		$data->version      = (string) $xml->version;
		$data->description  = (string) $xml->description;
		$data->thumbnail    = (string) $xml->thumbnail;

		if (!$data->creationdate)
		{
			$data->creationdate = (string) $xml->creationdate;

			if (!$data->creationdate)
			{
				$data->creationdate = JText::_('Unknown');
			}
		}

		if (!$data->author)
		{
			JText::_('Unknown');
		}

		return $data;
	}

	/**
	 * Method to get all joomla menu details about kunena.
	 *
	 * @return    string
	 *
	 * @since    1.6
	 */
	protected function _getJoomlaMenuDetails()
	{
		$items = KunenaMenuFix::getAll();

		if (!empty($items))
		{
			$joomlamenudetails = '[table][tr][td][u] ID [/u][/td][td][u] Name [/u][/td][td][u] Menutype [/u][/td][td][u] Link [/u][/td][td][u] Path [/u][/td][td][u] In trash [/u][/td][/tr] ';

			foreach ($items as $item)
			{
				$trashed = 'No';

				if ($item->published == '-2')
				{
					$trashed = 'Yes';
				}

				$link = preg_replace('/^.*\?(option=com_kunena&)?/', '', $item->link);
				$joomlamenudetails .= '[tr][td]' . $item->id . ' [/td][td] ' . $item->title . ' [/td][td] ' . $item->menutype . ' [/td][td] ' . $link . ' [/td][td] ' . $item->route . '[/td][td] ' . $trashed . '[/td][/tr] ';
			}

			$joomlamenudetails .= '[/table]';
		}
		else
		{
			$joomlamenudetails = "Menu items doesn't exists";
		}

		return $joomlamenudetails;

	}

	/**
	 * Method to check the tables collation.
	 *
	 * @return    string
	 *
	 * @since    1.6
	 */
	protected function _getTablesCollation()
	{
		$kunena_db = JFactory::getDBO();

		// Check each table in the database if the collation is on utf8
		$tableslist = $kunena_db->getTableList();
		$collation  = '';

		foreach ($tableslist as $table)
		{
			if (preg_match('`_kunena_`', $table))
			{
				$kunena_db->setQuery("SHOW FULL FIELDS FROM " . $table . "");

				try
				{
					$fullfields = $kunena_db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage());

					return;
				}

				$fieldTypes = array('tinytext', 'text', 'char', 'varchar');

				foreach ($fullfields as $row)
				{
					$tmp = strpos($row->Type, '(');

					if ($tmp)
					{
						if (in_array(substr($row->Type, 0, $tmp), $fieldTypes))
						{
							if (!empty($row->Collation) && !preg_match('`utf8`', $row->Collation))
							{
								$collation .= $table . ' [color=#FF0000]have wrong collation of type ' . $row->Collation . ' [/color] on field ' . $row->Field . '  ';
							}
						}
					}
					else
					{
						if (in_array($row->Type, $fieldTypes))
						{
							if (!empty($row->Collation) && !preg_match('`utf8`', $row->Collation))
							{
								$collation .= $table . ' [color=#FF0000]have wrong collation of type ' . $row->Collation . ' [/color] on field ' . $row->Field . '  ';
							}
						}
					}
				}
			}
		}

		if (empty($collation))
		{
			$collation = 'The collation of your table fields are correct';
		}

		return $collation;
	}

	/**
	 * Return extension version string if installed.
	 *
	 * @param   string $extension
	 * @param   string $name
	 *
	 * @return    string
	 *
	 * @since    1.6
	 */
	protected function getExtensionVersion($extension, $name)
	{
		if (substr($extension, 0, 4) == 'com_')
		{
			$path = JPATH_ADMINISTRATOR . "/components/{$extension}";
		}
		elseif (substr($extension, 0, 4) == 'mod_')
		{
			$path = JPATH_SITE . "/modules/{$extension}";
		}
		else
		{
			list($folder, $element) = explode('/', $extension, 2);
			$path = JPATH_PLUGINS . "/{$folder}/{$element}";
		}

		$version = $this->findExtensionVersion($path);

		return $version ? '[u]' . $name . '[/u] ' . $version : '';
	}

	/**
	 * Tries to find the extension manifest file and returns version
	 *
	 * @param   $path  $path    Path to extension directory
	 *
	 * @return  string  Version number
	 */
	public function findExtensionVersion($path)
	{
		if (is_file($path))
		{
			// Make array from the xml file
			$xmlfiles = array($path);
		}
		elseif (is_dir($path))
		{
			// Get an array of all the XML files from the directory
			$xmlfiles = KunenaFolder::files($path, '\.xml$', 1, true);
		}

		$version = null;

		if (!empty($xmlfiles))
		{
			$installer = JInstaller::getInstance();

			foreach ($xmlfiles as $file)
			{
				// Is it a valid Joomla installation manifest file?
				$manifest   = $installer->isManifest($file);
				$newversion = $manifest ? (string) $manifest->version[0] : null;

				// We check all files just in case if there are more than one manifest file
				if (version_compare($newversion, $version, '>'))
				{
					$version = $newversion;
				}
			}
		}

		return $version;
	}

	/**
	 * @return array
	 *
	 */
	public function getIntegrationSettings()
	{
		$plugins_list = array('finder' => 'Kunena - Finder', 'alphauserpoints' => 'Kunena - AlphaUserPoints', 'altauserpoints' => 'Kunena - AltaUserPoints', 'comprofiler' => 'Kunena - Community Builder', 'easyblog' => 'Kunena - Easyblog', 'easyprofile' => 'Kunena - Easyprofile', 'easysocial' => 'Kunena - Easysocial', 'gravatar' => 'Kunena - Gravatar', 'community' => 'Kunena - JomSocial', 'joomla' => 'Kunena - Joomla', 'kunena' => 'Kunena - Kunena', 'uddeim' => 'Kunena - UddeIM');
		$plugin_final = array();

		foreach ($plugins_list as $name => $desc)
		{
			$plugin = JPluginHelper::getPlugin('kunena', $name);

			if ($plugin)
			{
				$pluginParams   = new JRegistry($plugin->params);
				$params         = $pluginParams->toArray();
				
				if (!empty($params))
				{
					$plugin_final[] = '[b]' . $desc . '[/b] Enabled: ';
	
					foreach ($params as $param => $value)
					{
						$plugin_final[] = "{$param}={$value} ";
					}
				}
				else 
				{
					$plugin_final[] = '[b]' . $desc . '[/b] Enabled';
				}

				$plugin_final[] = "\n";
			}
			else
			{
				$plugin_final[] = "[b]{$desc}[/b] Disabled\n";
			}
		}

		return $plugin_final;
	}

	/**
	 * Improve readabilty of Kunena default template settings
	 */
	protected function _getKunenaTemplateDetailsReadable()
	{
		$ktemplatesettings = '[table]';

		foreach ($this->ktemplateparams->toObject() as $key => $value)
		{

			$ktemplatesettings .= '[tr][td]' . $key . '[/td][td]' . $value . '[/td][/tr]';

		}

		$ktemplatesettings .= '[/table]';

		return $ktemplatesettings;
	}
}
