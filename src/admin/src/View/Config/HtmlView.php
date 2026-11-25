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

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * The configuration of Kunena: every option of config.xml, by section.
 *
 * @since   Kunena 8.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var  Form
     * @since   Kunena 8.0
     */
    public Form $form;

    /**
     * Who may change the permissions (core.admin); the others see the rest of the options.
     *
     * @var  bool
     * @since   Kunena 8.0
     */
    public bool $canAdmin = false;

    /**
     * @param   string|null  $tpl  The name of the template file.
     *
     * @return  void
     *
     * @throws  NotAllowed
     * @since   Kunena 8.0
     */
    public function display($tpl = null): void
    {
        $user = $this->getCurrentUser();

        if (!$user->authorise('core.admin', 'com_kunena') && !$user->authorise('core.options', 'com_kunena')) {
            throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->form     = $this->getModel()->getForm();
        $this->canAdmin = $user->authorise('core.admin', 'com_kunena');

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CONFIGURATION'), 'options');
        ToolbarHelper::apply('config.apply');
        ToolbarHelper::save('config.save');
        ToolbarHelper::cancel('config.cancel', 'JTOOLBAR_CLOSE');
        ToolbarHelper::help('COM_KUNENA', false, 'https://docs.kunena.org/en/');
    }
}
