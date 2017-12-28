<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * About view for Kunena smiley backend
 *
 * @since  K1.X
 */
class KunenaAdminViewSmiley extends KunenaView
{
	/**
	 * @param   null $tpl
	 *
	 * @return mixed|void
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
	 *
	 */
	protected function setToolbar()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/emoticons/edit-emoticon';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}
}
