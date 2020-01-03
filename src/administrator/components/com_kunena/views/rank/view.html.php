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
 * About view for Kunena rank backend
 *
 * @since  K1.X
 */
class KunenaAdminViewRank extends KunenaView
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
		$this->state         = $this->get('state');
		$this->rank_selected = $this->get('rank');
		$this->rankpath      = $this->ktemplate->getRankPath();
		$this->listranks     = $this->get('Rankspaths');

		parent::display($tpl);
	}

	/**
	 * @since   Kunena 6.0
	 */
	protected function setToolbar()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_RANK_MANAGER'), 'ranks');
		ToolbarHelper::spacer();
		ToolbarHelper::save('save');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		$help_url = 'https://docs.kunena.org/en/manual/backend/ranks/edit-rank';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
