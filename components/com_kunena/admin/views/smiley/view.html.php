<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * About view for Kunena smiley backend
 */
class KunenaAdminViewSmiley extends KunenaView
{
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

	protected function setToolbar()
	{
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'smilies');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		$help_url  = 'https://www.kunena.org/docs/Smiley_management';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}
}
