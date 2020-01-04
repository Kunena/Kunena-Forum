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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * About view for Kunena smiley backend
 *
 * @since  K1.X
 */
class KunenaAdminViewSmiley extends KunenaView
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->setLayout('edit');
		$this->setToolbar();
		$this->state           = $this->get('state');
		$this->smiley_selected = $this->get('smiley');
		$this->smileypath      = $this->ktemplate->getSmileyPath();
		$this->listsmileys     = $this->get('Smileyspaths');

		parent::display($tpl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolbar()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		ToolbarHelper::spacer();
		ToolbarHelper::save('save');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		$help_url = 'https://docs.kunena.org/en/manual/backend/emoticons/edit-emoticon';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
