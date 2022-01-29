<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Cpanel;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;

/**
 * About view for Kunena cpanel
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @param   null  $tpl  tmpl
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();

		$lang = Factory::getApplication()->getLanguage();
		$lang->load('mod_sampleData', JPATH_ADMINISTRATOR);

		if (!KunenaForum::versionSampleData())
		{
			Factory::getApplication()->getDocument()->getWebAssetManager()
				->registerAndUseScript('mod_sampleData', 'mod_sampleData/sampleData-process.js', [], ['defer' => true], ['core']);

			Text::script('MOD_SAMPLEDATA_CONFIRM_START');
			Text::script('MOD_SAMPLEDATA_ITEM_ALREADY_PROCESSED');
			Text::script('MOD_SAMPLEDATA_INVALID_RESPONSE');

			Factory::getApplication()->getDocument()->addScriptOptions(
				'sample-data',
				[
					'icon' => Uri::root(true) . '/media/system/images/ajax-loader.gif',
				]
			);
		}

		$this->KunenaMenusExists = KunenaMenuHelper::KunenaMenusExists();

		$this->upgradeDatabase();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::spacer();
		ToolbarHelper::divider();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_DASHBOARD'), 'dashboard');

		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * Method to upgrade the database at the end of installation.
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function upgradeDatabase()
	{
		$app = Factory::getApplication();

		$xml = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_kunena/install/kunena.install.upgrade.xml');

		if ($xml === false)
		{
			$app->enqueueMessage(Text::_('COM_KUNENA_INSTALL_DB_UPGRADE_FAILED_XML'), 'error');

			return false;
		}

		// The column "state" in kunena_version indicate from which version to update
		$db = Factory::getContainer()->get('DatabaseDriver');
		$db->setQuery("SELECT state FROM #__kunena_version ORDER BY `id` DESC", 0, 1);
		$stateVersion = $db->loadResult();

		if (!empty($stateVersion))
		{
			$curversion = $stateVersion;
		}
		else
		{
			return false;
		}

		$modelInstall = new KunenaModelInstall;

		foreach ($xml->upgrade[0] as $version)
		{
			// If we have already upgraded to this version, continue to the next one
			$vernum = (string) $version['version'];

			if (!empty($status[$vernum]))
			{
				continue;
			}

			// Update state
			$status[$vernum] = 1;

			if ($version['version'] == '@' . 'kunenaversion' . '@')
			{
				$git    = 1;
				$vernum = KunenaForum::version();
			}

			if (isset($git) || version_compare(strtolower($version['version']), strtolower($curversion), '>'))
			{
				foreach ($version as $action)
				{
					$modelInstall->processUpgradeXMLNode($action);

					$app->enqueueMessage(Text::sprintf('COM_KUNENA_INSTALL_VERSION_UPGRADED', $vernum));
				}

				$query = "UPDATE `#__kunena_version` SET state='';";
				$db->setQuery($query);

				$db->execute();

				// Database install continues
				// return false;
			}
		}

		return true;
	}
}
