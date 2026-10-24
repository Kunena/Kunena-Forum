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

namespace Kunena\Forum\Administrator\View\Socials;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Socials view for Kunena backend
 *
 * @since   Kunena 7.2
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var     Form
     * @since   Kunena 7.2
     */
    protected mixed $form = null;

    /**
     * @param   null  $tpl  tpl
     *
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function display($tpl = null)
    {
        $this->form = $this->getModel()->getForm();

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    protected function addToolbar(): void
    {
        $toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_A_SOCIALS'), 'star-2');

        $toolbar->apply('socials.apply');
        $toolbar->save('socials.save');
        $toolbar->cancel('socials.cancel', 'JTOOLBAR_CLOSE');
    }
}
