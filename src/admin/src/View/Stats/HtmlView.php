<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Stats;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;

/**
 * About view for Kunena stats backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var \stdClass
     * @since version
     */
    public $config;

    /**
     * @var KunenaStatistics
     * @since Kunena 6.3.0-BETA3
     */
    public $kunenaStats;

    /**
     * DisplayDefault
     *
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->kunenaStats = KunenaStatistics::getInstance();
        $this->kunenaStats->loadAll(true);

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
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance();

        // Set the title bar text
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_STAT_FORUMSTATS'), 'stats');
    }
}
