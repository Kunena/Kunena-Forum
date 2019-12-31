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
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * About view for Kunena config backend
 *
 * @since K1.X
 */
class KunenaAdminViewConfig extends KunenaView
{
	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->lists = $this->get('Configlists');

		// Only set the toolbar if not modal
		if ($this->getLayout() !== 'modal')
		{
			$this->setToolBarDefault();
		}

		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		$bar = Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');

		ToolbarHelper::spacer();
		ToolbarHelper::apply();
		ToolbarHelper::save('save');
		ToolbarHelper::divider();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CONFIGURATION'), 'wrench');

		HTMLHelper::_('bootstrap.renderModal', 'settingModal');

		$title = Text::_('COM_KUNENA_RESET_CONFIG');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#settingModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
					$title</button>";
		$bar->appendButton('Custom', $dhtml, 'restore');
		ToolbarHelper::back('JTOOLBAR_CANCEL', 'index.php?option=com_kunena');

		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/configuration';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
