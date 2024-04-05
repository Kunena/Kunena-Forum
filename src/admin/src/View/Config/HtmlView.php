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

namespace Kunena\Forum\Administrator\View\Config;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * About view for Kunena config backend
 *
 * @since K1.X
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var object
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public $config;

    /**
     * @param   null  $tpl  tmpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function display($tpl = null)
    {
        $this->lists  = $this->get('Configlists');
        $this->config = KunenaConfig::getInstance();

        // Only set the toolbar if not modal
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }

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
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CONFIGURATION'), 'wrench');
        $toolbar->apply('config.apply');
        $toolbar->save('config.save');

        $toolbar->popupButton('batch', 'COM_KUNENA_RESET_CONFIG')
            ->selector('joomla-dialog-setting')
            ->listCheck(false);
        $toolbar->cancel('config.cancel', 'JTOOLBAR_CANCEL');

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/configuration';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
