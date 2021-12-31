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
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/**
 * About view for Kunena smiley backend
 *
 * @since  K1.X
 */
class KunenaAdminViewSmiley extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @return mixed|void
	 * @since Kunena
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
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		JToolbarHelper::spacer();
		JToolbarHelper::save('save');
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		$help_url = 'https://docs.kunena.org/en/manual/backend/emoticons/edit-emoticon';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
