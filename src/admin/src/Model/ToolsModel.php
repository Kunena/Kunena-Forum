<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use RuntimeException;
use stdClass;

/**
 * Tools Model for Kunena
 *
 * @since   Kunena 2.0
 */
class ToolsModel extends AdminModel
{
	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $jconfigSmtpUser = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $jconfigFtp = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $jconfigSef = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $jconfigSefRewrite = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $htaccess = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $mbstring = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $gd_info = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $gdSupport = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $openssl = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $json = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $fileinfo = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $maxExecTime = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $maxExecMem = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $fileUploads = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $kunenaVersionInfo = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $ktemplate = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $ktemplateDetails = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $jtemplateDetails = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $joomlaMenuDetails = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $collation = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $kconfigSettings = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $joomlaLanguages = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $plgText = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $modText = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $thirdPartyText = [];

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	protected $sefText = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $integrationSettings = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	private $ktemplateParams = [];

	/**
	 * @var CMSApplicationInterface|null
	 * @since version
	 */
	private $app;

	/**
	 * @inheritDoc
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getPruneCategories()
	{
		$catParams                = [];
		$catParams['ordering']    = 'ordering';
		$catParams['toplevel']    = 0;
		$catParams['sections']    = 0;
		$catParams['direction']   = 1;
		$catParams['unpublished'] = 1;
		$catParams['action']      = 'admin';

		return HTMLHelper::_('select.genericlist', $catParams, 'prune_forum', 'class="inputbox form-control" multiple="multiple"', 'value', 'text', 0);
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getPruneListTrashDelete()
	{
		$trashDelete    = [];
		$trashDelete [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_TRASH_USERMESSAGES'));
		$trashDelete [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_DELETE_PERMANENTLY'));

		return HTMLHelper::_('select.genericlist', $trashDelete, 'trashDelete', 'class="inputbox form-control" size="1"', 'value', 'text', 0);
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getPruneControlOptions()
	{
		$controlOptions    = [];
		$controlOptions [] = HTMLHelper::_('select.option', 'all', Text::_('COM_KUNENA_A_PRUNE_ALL'));
		$controlOptions [] = HTMLHelper::_('select.option', 'normal', Text::_('COM_KUNENA_A_PRUNE_NORMAL'));
		$controlOptions [] = HTMLHelper::_('select.option', 'locked', Text::_('COM_KUNENA_A_PRUNE_LOCKED'));
		$controlOptions [] = HTMLHelper::_('select.option', 'unanswered', Text::_('COM_KUNENA_A_PRUNE_UNANSWERED'));
		$controlOptions [] = HTMLHelper::_('select.option', 'answered', Text::_('COM_KUNENA_A_PRUNE_ANSWERED'));
		$controlOptions [] = HTMLHelper::_('select.option', 'unapproved', Text::_('COM_KUNENA_A_PRUNE_UNAPPROVED'));
		$controlOptions [] = HTMLHelper::_('select.option', 'deleted', Text::_('COM_KUNENA_A_PRUNE_DELETED'));
		$controlOptions [] = HTMLHelper::_('select.option', 'shadow', Text::_('COM_KUNENA_A_PRUNE_SHADOW'));

		return HTMLHelper::_('select.genericlist', $controlOptions, 'controlOptions', 'class="inputbox form-control" size="1"', 'value', 'text', 'normal');
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getPruneKeepSticky()
	{
		$optionSticky    = [];
		$optionSticky [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_NO'));
		$optionSticky [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_YES'));

		return HTMLHelper::_('select.genericlist', $optionSticky, 'keepsticky', 'class="inputbox form-control" size="1"', 'value', 'text', 1);
	}

	/**
	 * Method to generate the report configuration with anonymous data
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public function getSystemReportAnonymous(): string
	{
		$this->app = Factory::getApplication();
		$kunenaDB  = Factory::getContainer()->get('DatabaseDriver');

		$this->getPhpExtensions();
		$this->getReportData();

		return '[confidential][b]Joomla! version:[/b] ' . JVERSION . ' [b]Platform:[/b] ' . $_SERVER['SERVER_SOFTWARE'] . '[b]PHP version:[/b] ' . phpversion() . ' | ' . $this->mbstring
			. ' | ' . $this->gdSupport . ' | ' . $this->openssl . ' | ' . $this->json . ' | ' . $this->fileinfo . ' | [b]MySQL version:[/b] ' . $kunenaDB->getVersion() . ' (Server type: ' . $kunenaDB->getServerType() . ') | [b]Base URL:[/b]' . Uri::root() . '[/confidential][quote][b]Database collation check:[/b] ' . $this->collation . '
		[/quote][quote][b]Joomla! SEF:[/b] ' . $this->jconfigSef . ' | [b]Joomla! SEF rewrite:[/b] '
			. $this->jconfigSefRewrite . ' | [b]FTP layer:[/b] ' . $this->jconfigFtp . ' |
	    [confidential][b]Mailer:[/b] ' . $this->app->get('mailer') . ' | [b]SMTP Secure:[/b] ' . $this->app->get('smtpsecure') . ' | [b]SMTP Port:[/b] ' . $this->app->get('smtpport') . ' | [b]SMTP User:[/b] ' . $this->jconfigSmtpUser . ' | [b]SMTP Host:[/b] ' . $this->app->get('smtphost') . ' [/confidential] [b]htaccess:[/b] ' . $this->htaccess
			. ' | [b]PHP environment:[/b] [u]Max execution time:[/u] ' . $this->maxExecTime . ' seconds | [u]Max execution memory:[/u] '
			. $this->maxExecMem . ' | [u]Max file upload:[/u] ' . $this->fileUploads . ' [/quote] [quote][b]Kunena menu details[/b]:[spoiler] ' . $this->joomlaMenuDetails . '[/spoiler][/quote][quote][b]Joomla default template details :[/b] ' . $this->jtemplateDetails->name . ' | [u]author:[/u] ' . $this->jtemplateDetails->author . ' | [u]version:[/u] ' . $this->jtemplateDetails->version . ' | [u]creationdate:[/u] ' . $this->jtemplateDetails->creationdate . ' [/quote][quote][b]Kunena default template details :[/b] ' . $this->ktemplateDetails->name . ' | [u]author:[/u] ' . $this->ktemplateDetails->author . ' | [u]version:[/u] ' . $this->ktemplateDetails->version . ' | [u]creationdate:[/u] ' . $this->ktemplateDetails->creationDate . ' [/quote][quote][b]Kunena template params[/b]:[spoiler] ' . $this->ktemplateParams . '[/spoiler][/quote][quote] [b]Kunena version detailed:[/b] ' . $this->kunenaVersionInfo . '
	    | [u]Kunena detailed configuration:[/u] [spoiler] ' . $this->kconfigSettings . '[/spoiler]| [u]Kunena integration settings:[/u][spoiler] ' . implode(' ', $this->integrationSettings) . '[/spoiler]| [u]Joomla! detailed language files installed:[/u][spoiler] ' . $this->joomlaLanguages . '[/spoiler][/quote]' . $this->thirdPartyText . ' ' . $this->sefText . ' ' . $this->plgText . ' ' . $this->modText;
	}

	/**
	 * Check if php extensions needed by kunena are right loaded
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function getPhpExtensions()
	{
		if (\extension_loaded('mbstring'))
		{
			$this->mbstring = '[u]mbstring:[/u] Enabled';
		}
		else
		{
			$this->mbstring = '[u]mbstring:[/u] [color=#FF0000]Not installed[/color]';
		}

		if (\extension_loaded('gd'))
		{
			$gd_info         = gd_info();
			$this->gdSupport = '[u]GD:[/u] ' . $gd_info['GD Version'];
		}
		else
		{
			$this->gdSupport = '[u]GD:[/u] [color=#FF0000]Not installed[/color]';
		}

		if (\extension_loaded('openssl'))
		{
			$this->openssl = '[u]openssl:[/u] Enabled';
		}
		else
		{
			$this->openssl = '[u]openssl:[/u] [color=#FF0000]Not installed[/color]';
		}

		if (\extension_loaded('fileinfo'))
		{
			$this->fileinfo = '[u]fileinfo:[/u] Enabled';
		}
		else
		{
			$this->fileinfo = '[u]fileinfo:[/u] [color=#FF0000]Not installed[/color]';
		}

		if (\extension_loaded('json'))
		{
			$this->json = '[u]json:[/u] Enabled';
		}
		else
		{
			$this->json = '[u]json:[/u] [color=#FF0000]Not installed[/color]';
		}
	}

	/**
	 * Initialize data to generate configuration report
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function getReportData(): void
	{
		$this->app = Factory::getApplication();

		if (!$this->app->get('smtpuser'))
		{
			$this->jconfigSmtpUser = 'Empty';
		}
		else
		{
			$this->jconfigSmtpUser = $this->app->get('smtpuser');
		}

		if ($this->app->get('ftp_enable'))
		{
			$this->jconfigFtp = 'Enabled';
		}
		else
		{
			$this->jconfigFtp = 'Disabled';
		}

		if ($this->app->get('sef'))
		{
			$this->jconfigSef = 'Enabled';
		}
		else
		{
			$this->jconfigSef = 'Disabled';
		}

		if ($this->app->get('sef_rewrite'))
		{
			$this->jconfigSefRewrite = 'Enabled';
		}
		else
		{
			$this->jconfigSefRewrite = 'Disabled';
		}

		if (is_file(JPATH_ROOT . '/.htaccess'))
		{
			$this->htaccess = 'Exists';
		}
		else
		{
			$this->htaccess = 'Missing';
		}

		$this->maxExecTime       = ini_get('max_execution_time');
		$this->maxExecMem        = ini_get('memory_limit');
		$this->fileUploads       = ini_get('upload_max_fileSize');
		$this->kunenaVersionInfo = KunenaVersion::getVersionHTML();

		// Get Kunena default template
		$ktemplate              = KunenaFactory::getTemplate();
		$this->ktemplateDetails = $ktemplate->getTemplateDetails();
		$this->ktemplateParams  = $this->getKunenaTemplateParams($ktemplate->params);

		$this->jtemplateDetails = $this->internalGetJoomlaTemplate();

		$this->joomlaMenuDetails = $this->internalGetJoomlaMenuDetails();

		$this->collation = $this->internalGetTablesCollation();

		$this->kconfigSettings = $this->internalGetKunenaConfiguration();

		// Get Joomla! languages installed
		$this->joomlaLanguages = $this->internalGetJoomlaLanguagesInstalled();

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
			$this->plgText = '[quote][b]Plugins:[/b] ' . implode(' | ', $plg) . ' [/quote]';
		}
		else
		{
			$this->plgText = '[quote][b]Plugins:[/b] None [/quote]';
		}

		$mod                 = [];
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
			$this->modText = '[quote][b]Modules:[/b] ' . implode(' | ', $mod) . ' [/quote]';
		}
		else
		{
			$this->modText = '[quote][b]Modules:[/b] None [/quote]';
		}

		$thirdParty              = [];
		$thirdParty['alup']      = $this->getExtensionVersion('com_altauserpoints', 'AltaUserPoints');
		$thirdParty['cb']        = $this->getExtensionVersion('com_comprofiler', 'CommunityBuilder');
		$thirdParty['jomsocial'] = $this->getExtensionVersion('com_community', 'Jomsocial');

		foreach ($thirdParty as $id => $item)
		{
			if (empty($item))
			{
				unset($thirdParty[$id]);
			}
		}

		if (!empty($thirdParty))
		{
			$this->thirdPartyText = '[quote][b]Third-party components:[/b] ' . implode(' | ', $thirdParty) . ' [/quote]';
		}
		else
		{
			$this->thirdPartyText = '[quote][b]Third-party components:[/b] None [/quote]';
		}

		$sef             = [];
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
			$this->sefText = '[quote][b]Third-party SEF components:[/b] ' . implode(' | ', $sef) . ' [/quote]';
		}
		else
		{
			$this->sefText = '[quote][b]Third-party SEF components:[/b] None [/quote]';
		}

		// Get integration settings
		$this->integrationSettings = $this->getIntegrationSettings();
	}

	/**
	 * Method to put readable correctly the kunena template params
	 *
	 * @param   object  $params  params
	 *
	 * @return  string
	 *
	 * @since   Kunena 5.1.1
	 */
	protected function getKunenaTemplateParams(object $params): string
	{
		$templateParams  = json_decode($params);
		$ktemplateParams = '[table]';

		foreach ($templateParams as $param => $value)
		{
			$ktemplateParams .= '[tr][td][b]' . $param . '[/b][/td][td]' . $value . '[/td][/tr]';
		}

		$ktemplateParams .= '[table]';

		return $ktemplateParams;
	}

	/**
	 * Method to get the default joomla template.
	 *
	 * @return  boolean|stdClass|void
	 *
	 * @throws  Exception
	 * @since   Kunena 1.6
	 */
	protected function internalGetJoomlaTemplate()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Get Joomla! frontend assigned template
		$query = $db->getQuery(true);
		$query->select('template')
			->from($db->quoteName('#__template_styles'))
			->where('client_id = 0 AND home = 1');
		$db->setQuery($query);

		try
		{
			$template = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

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
				$data->creationdate = Text::_('Unknown');
			}
		}

		if (!$data->author)
		{
			Text::_('Unknown');
		}

		return $data;
	}

	/**
	 * Method to get all joomla menu details about kunena.
	 *
	 * @return  string
	 *
	 * @since   Kunena 1.6
	 */
	protected function internalGetJoomlaMenuDetails(): string
	{
		$items = KunenaMenuFix::getAll();

		if (!empty($items))
		{
			$joomlaMenuDetails = '[table][tr][td][u] ID [/u][/td][td][u] Name [/u][/td][td][u] Menutype [/u][/td][td][u] Link [/u][/td][td][u] Path [/u][/td][td][u] In trash [/u][/td][/tr] ';

			foreach ($items as $item)
			{
				$trashed = 'No';

				if ($item->published == '-2')
				{
					$trashed = 'Yes';
				}

				$link              = preg_replace('/^.*\?(option=com_kunena&)?/', '', $item->link);
				$joomlaMenuDetails .= '[tr][td]' . $item->id . ' [/td][td] ' . $item->title . ' [/td][td] ' . $item->menutype . ' [/td][td] ' . $link . ' [/td][td] ' . $item->route . '[/td][td] ' . $trashed . '[/td][/tr] ';
			}

			$joomlaMenuDetails .= '[/table]';
		}
		else
		{
			$joomlaMenuDetails = "Menu items doesn't exists";
		}

		return $joomlaMenuDetails;

	}

	/**
	 * Method to check the tables collation.
	 *
	 * @return string
	 *
	 * @since   Kunena 1.6
	 * @throws \Exception
	 */
	protected function internalGetTablesCollation(): string
	{
		$kunenaDB = Factory::getContainer()->get('DatabaseDriver');

		// Check each table in the database if the collation is on utf8
		$tablesList = $kunenaDB->getTableList();
		$collation  = '';

		foreach ($tablesList as $table)
		{
			if (preg_match('`_kunena_`', $table))
			{
				$kunenaDB->setQuery("SHOW FULL FIELDS FROM " . $table . "");

				try
				{
					$fullFields = $kunenaDB->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage());

					return false;
				}

				$fieldTypes = ['tinytext', 'text', 'char', 'varchar'];

				foreach ($fullFields as $row)
				{
					$tmp = strpos($row->Type, '(');

					if ($tmp)
					{
						if (\in_array(substr($row->Type, 0, $tmp), $fieldTypes))
						{
							if (!empty($row->Collation) && !preg_match('`utf8`', $row->Collation))
							{
								$collation .= $table . ' [color=#FF0000]have wrong collation of type ' . $row->Collation . ' [/color] on field ' . $row->Field . '  ';
							}
						}
					}
					else
					{
						if (\in_array($row->Type, $fieldTypes))
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
			$collation = '[color=green]The collation of your table fields are correct[/color]';
		}

		return $collation;
	}

	/**
	 * Method to get all the kunena configuration settings.
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 1.6
	 */
	protected function internalGetKunenaConfiguration(): string
	{
		$config = KunenaConfig::getInstance();

		if ($config)
		{
			$params = $config->getProperties();

			$kconfigSettings = '[table]';
			$kconfigSettings .= '[tr][th]Kunena config settings:[/th][/tr]';

			foreach ($params as $key => $value)
			{
				if (!\is_array($value) && $key != 'id' && $key != 'boardTitle' && $key != 'email' && $key != 'offlineMessage'
					&& $key != 'emailVisibleAddress' && $key != 'stopForumSpamKey' && $key != 'ebayAffiliateId'
					&& $key != 'ebayApiKey' && $key != 'twitterConsumerKey' && $key != 'twitterConsumerSecret'
					&& $key != 'googleMapApiKey')
				{
					$kconfigSettings .= '[tr][td]' . $key . '[/td][td]' . $value . '[/td][/tr]';
				}
			}

			$kconfigSettings .= '[/table]';
		}
		else
		{
			$kconfigSettings = 'Your configuration settings aren\'t yet recorded in the database';
		}

		return $kconfigSettings;
	}

	/**
	 * Method to get all languages installed into Joomla! and the default one
	 *
	 * @return  string
	 *
	 * @since   Kunena 2.0
	 */
	protected function internalGetJoomlaLanguagesInstalled(): string
	{
		$languages = LanguageHelper::getKnownLanguages();
		$tableLang = '[table]';
		$tableLang .= '[tr][th]Joomla! languages installed:[/th][/tr]';

		foreach ($languages as $language)
		{
			$tableLang .= '[tr][td]' . $language['tag'] . '[/td][td]' . $language['name'] . '[/td][/tr]';
		}

		$tableLang .= '[/table]';

		return $tableLang;
	}

	/**
	 * Return extension version string if installed.
	 *
	 * @param   string  $extension  extension
	 * @param   string  $name       name
	 *
	 * @return  string
	 *
	 * @since   Kunena  1.6
	 */
	protected function getExtensionVersion(string $extension, string $name): string
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
	 * @param   string  $path  Path to extension directory
	 *
	 * @return string|null Version number
	 *
	 * @since   Kunena 6.0
	 */
	public function findExtensionVersion(string $path): ?string
	{
		if (is_file($path))
		{
			// Make array from the xml file
			$xmlFiles = [$path];
		}
		elseif (is_dir($path))
		{
			// Get an array of all the XML files from the directory
			$xmlFiles = Folder::files($path, '\.xml$', 1, true);
		}

		$version = null;

		if (!empty($xmlFiles))
		{
			$installer = Installer::getInstance();

			foreach ($xmlFiles as $file)
			{
				// Is it a valid Joomla installation manifest file?
				$manifest   = $installer->isManifest($file);
				$newVersion = $manifest ? (string) $manifest->version[0] : null;

				// We check all files just in case if there are more than one manifest file
				if (version_compare($newVersion, $version, '>'))
				{
					$version = $newVersion;
				}
			}
		}

		return $version;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getIntegrationSettings(): array
	{
		$pluginsList = ['finder' => 'Kunena - Finder', 'altauserpoints' => 'Kunena - AltaUserPoints', 'comprofiler' => 'Kunena - Community Builder', 'easyblog' => 'Kunena - Easyblog', 'easyprofile' => 'Kunena - Easyprofile', 'easysocial' => 'Kunena - Easysocial', 'gravatar' => 'Kunena - Gravatar', 'community' => 'Kunena - JomSocial', 'joomla' => 'Kunena - Joomla', 'kunena' => 'Kunena - Kunena'];
		$pluginFinal = [];

		foreach ($pluginsList as $name => $desc)
		{
			$plugin = PluginHelper::getPlugin('kunena', $name);

			if ($plugin)
			{
				$pluginParams = new Registry($plugin->params);
				$params       = $pluginParams->toArray();

				if (!empty($params))
				{
					$pluginFinal[] = '[b]' . $desc . '[/b] Enabled: ';

					foreach ($params as $param => $value)
					{
						$pluginFinal[] = "{$param}={$value} ";
					}
				}
				else
				{
					$pluginFinal[] = '[b]' . $desc . '[/b] Enabled';
				}

				$pluginFinal[] = "\n";
			}
			else
			{
				$pluginFinal[] = "[b]{$desc}[/b] Disabled\n";
			}
		}

		return $pluginFinal;
	}

	/**
	 * Method to generate all the report configuration.
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 1.6
	 */
	public function getSystemReport(): string
	{
		$kunenaDB  = Factory::getContainer()->get('DatabaseDriver');
		$this->app = Factory::getApplication();

		$this->getReportData();
		$this->getPhpExtensions();

		return '[confidential][b]Joomla! version:[/b] ' . JVERSION . ' [b]Platform:[/b] ' . $_SERVER['SERVER_SOFTWARE'] . ' ('
			. $_SERVER['SERVER_NAME'] . ') [b]PHP version:[/b] ' . phpversion() . ' | ' . $this->mbstring
			. ' | ' . $this->gdSupport . ' | ' . $this->openssl . ' | ' . $this->json . ' | ' . $this->fileinfo . ' | [b]MySQL version:[/b] ' . $kunenaDB->getVersion() . ' (Server type: ' . $kunenaDB->getServerType() . ') | [b]Base URL:[/b]' . Uri::root() . '[/confidential][quote][b]Database collation check:[/b] ' . $this->collation . '
		[/quote][quote][b]Joomla! SEF:[/b] ' . $this->jconfigSef . ' | [b]Joomla! SEF rewrite:[/b] '
			. $this->jconfigSefRewrite . ' | [b]FTP layer:[/b] ' . $this->jconfigFtp . ' |
	    [confidential][b]Mailer:[/b] ' . $this->app->get('mailer') . ' | [b]Mail from:[/b] ' . $this->app->get('mailfrom') . ' | [b]From name:[/b] ' . $this->app->get('fromname') . ' | [b]SMTP Secure:[/b] ' . $this->app->get('smtpsecure') . ' | [b]SMTP Port:[/b] ' . $this->app->get('smtpport') . ' | [b]SMTP User:[/b] ' . $this->jconfigSmtpUser . ' | [b]SMTP Host:[/b] ' . $this->app->get('smtphost') . ' [/confidential] [b]htaccess:[/b] ' . $this->htaccess
			. ' | [b]PHP environment:[/b] [u]Max execution time:[/u] ' . $this->maxExecTime . ' seconds | [u]Max execution memory:[/u] '
			. $this->maxExecMem . ' | [u]Max file upload:[/u] ' . $this->fileUploads . ' [/quote] [quote][b]Kunena menu details[/b]:[spoiler] ' . $this->joomlaMenuDetails . '[/spoiler][/quote][quote][b]Joomla default template details :[/b] ' . $this->jtemplateDetails->name . ' | [u]author:[/u] ' . $this->jtemplateDetails->author . ' | [u]version:[/u] ' . $this->jtemplateDetails->version . ' | [u]creationdate:[/u] ' . $this->jtemplateDetails->creationdate . ' [/quote][quote][b]Kunena default template details :[/b] ' . $this->ktemplateDetails->name . ' | [u]author:[/u] ' . $this->ktemplateDetails->author . ' | [u]version:[/u] ' . $this->ktemplateDetails->version . ' | [u]creationdate:[/u] ' . $this->ktemplateDetails->creationDate . ' [/quote] [quote][b]Kunena template params[/b]:[spoiler] ' . $this->ktemplateParams . '[/spoiler][/quote][quote] [b]Kunena version detailed:[/b] ' . $this->kunenaVersionInfo . '
	    | [u]Kunena detailed configuration:[/u] [spoiler] ' . $this->kconfigSettings . '[/spoiler]| [u]Kunena integration settings:[/u][spoiler] ' . implode(' ', $this->integrationSettings) . '[/spoiler]| [u]Joomla! detailed language files installed:[/u][spoiler] ' . $this->joomlaLanguages . '[/spoiler][/quote]' . $this->thirdPartyText . ' ' . $this->sefText . ' ' . $this->plgText . ' ' . $this->modText;
	}
}
