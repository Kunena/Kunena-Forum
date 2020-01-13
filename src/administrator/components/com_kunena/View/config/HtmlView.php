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

namespace Kunena\Forum\Administrator\View\Config;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\View\View;
use function defined;

/**
 * About view for Kunena config backend
 *
 * @since K1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function display($tpl = null)
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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarDefault()
	{
		$bar = Toolbar::getInstance('toolbar');

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
