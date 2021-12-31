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

namespace Kunena\Forum\Administrator\View\Rank;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * About view for Kunena rank backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->setLayout('edit');

		$this->state        = $this->get('state');
		$this->rankSelected = $this->get('rank');
		$this->rankPath     = KunenaFactory::getTemplate()->getRankPath();
		$this->listRanks    = $this->get('RanksPaths');

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
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_RANK_MANAGER'), 'ranks');
		ToolbarHelper::spacer();
		ToolbarHelper::save('rank.save');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/ranks/edit-rank';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}
}
