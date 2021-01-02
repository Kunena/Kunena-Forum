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

namespace Kunena\Forum\Administrator\View\Template;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * Template view for Kunena backend
 *
 * @since  K6.0
 */
class HtmlView extends BaseHtmlView
{

	/**
	 * @param   null  $tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$form         = $this->get('Form');
		$params       = $this->get('editparams');
		$details      = $this->get('templatedetails');
		$templatename = Factory::getApplication()->getUserState('kunena.edit.templatename');
		$template     = Template::getInstance($templatename);
		$template->initializeBackend();

		$templatefile = KPATH_SITE . '/template/' . $templatename . '/config/params.ini';

		if (!file_exists($templatefile))
		{
			$ourFileHandle = @fopen($templatefile, 'w');

			if ($ourFileHandle)
			{
				fclose($ourFileHandle);
			}
		}

		$this->addToolbar();

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
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('template.apply');
		ToolbarHelper::spacer();
		ToolbarHelper::save('template.save');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('template.restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE_TEMPLATE_SETTINGS', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
