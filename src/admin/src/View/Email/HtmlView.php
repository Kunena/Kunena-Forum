<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Email;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use function defined;

/**
 * Email view for Kunena backend
 *
 * @since 5.0
 */
class HtmlView extends BaseHtmlView
{

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function displayDefault($tpl = null)
	{
		$state      = $this->get('state');
		$group      = $state->get('group');
		$items      = $this->get('items');
		$pagination = $this->get('Pagination');

		$document = Factory::getApplication()->getDocument();
		$document->setTitle(Text::_('COM_KUNENA_A_EMAIL_MANAGER'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Set the toolbar on log manager
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		// Get the toolbar object instance
		$bar = Toolbar::getInstance();

		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_A_EMAIL_MANAGER'));
	}
}
