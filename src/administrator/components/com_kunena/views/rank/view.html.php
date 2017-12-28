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
 * About view for Kunena rank backend
 *
 * @since  K1.X
 */
class KunenaAdminViewRank extends KunenaView
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
		$this->state         = $this->get('state');
		$this->rank_selected = $this->get('rank');
		$this->rankpath      = $this->ktemplate->getRankPath();
		$this->listranks     = $this->get('Rankspaths');

		parent::display($tpl);
	}

	/**
	 *
	 */
	protected function setToolbar()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_RANK_MANAGER'), 'ranks');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/ranks/edit-rank';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}
}
