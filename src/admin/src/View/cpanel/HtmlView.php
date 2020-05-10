<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Cpanel;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\Statistics;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();

		$this->count = Statistics::getInstance()->loadCategoryCount();

		$this->sampledata = Statistics::getTotalEmoticons() == 0 && $this->count['categories'] == 0 && KunenaUserHelper::getTotalRanks() == 0;

		if ($this->sampledata) {
			Factory::getApplication()->getDocument()->getWebAssetManager()
				->registerAndUseScript('mod_sampledata', 'mod_sampledata/sampledata-process.js', [], ['defer' => true], ['core']);

			$lang = Factory::getLanguage();
			$lang->load('mod_sampledata', JPATH_ADMINISTRATOR);

			Text::script('MOD_SAMPLEDATA_CONFIRM_START');
			Text::script('MOD_SAMPLEDATA_ITEM_ALREADY_PROCESSED');
			Text::script('MOD_SAMPLEDATA_INVALID_RESPONSE');

			Factory::getApplication()->getDocument()->addScriptOptions(
				'sample-data',
				[
					'icon' => Uri::root(true) . '/media/system/images/ajax-loader.gif'
				]
			);
		}

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar()
	{
		ToolbarHelper::spacer();
		ToolbarHelper::divider();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_DASHBOARD'), 'dashboard');

		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
