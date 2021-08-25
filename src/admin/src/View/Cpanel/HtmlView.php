<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
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

		if (!KunenaForum::versionSampleData())
		{
			Factory::getApplication()->getDocument()->getWebAssetManager()
				->registerAndUseScript('mod_sampleData', 'mod_sampleData/sampleData-process.js', [], ['defer' => true], ['core']);

			$lang = Factory::getApplication()->getLanguage();
			$lang->load('mod_sampleData', JPATH_ADMINISTRATOR);

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
}
