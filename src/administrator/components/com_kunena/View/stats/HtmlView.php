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

namespace Kunena\Forum\Administrator\View\Stats;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use function defined;

/**
 * About view for Kunena stats backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @internal param null $tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function displayDefault($tpl = null)
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'kunena.png');

		$document = Factory::getApplication()->getDocument();
		$document->setTitle(Text::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' . $this->config->board_title);

		$kunena_stats = \KunenaForumStatistics::getInstance();
		$kunena_stats->loadAll(true);
		$this->kunena_stats = $kunena_stats;

		return parent::display($tpl);
	}
}
