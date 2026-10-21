<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Inbox\Initial;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The inbox of the user: a page for a menu item. A plugin which gives the inbox (private messages and notifications) shows
 * it through the event onKunenaInboxView.
 *
 * @since   Kunena 7.2
 */
class InboxDisplay extends Display
{
    /**
     * @var     string
     * @since   Kunena 7.2
     */
    public $header = '';

    /**
     * @var     string
     * @since   Kunena 7.2
     */
    public $content = '';

    /**
     * Return the page with the inbox.
     *
     * @return  KunenaLayout
     *
     * @since   Kunena 7.2
     *
     * @throws  Exception
     */
    protected function display()
    {
        $menu_item = $this->app->getMenu()->getActive();
        $params    = $menu_item ? $menu_item->getParams() : null;
        $title     = $params && $params->get('page_title') ? $params->get('page_title') : Text::_('COM_KUNENA_VIEW_INBOX_DEFAULT');

        $this->setTitle($title);
        $this->setMetaData('robots', 'noindex, nofollow');

        $this->header = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

        return KunenaPage::factory('Inbox/Default')
            ->set('header', $this->header)
            ->set('content', $this->content);
    }

    /**
     * Ask the plugin for the inbox of the user.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     *
     * @throws  Exception
     */
    protected function before()
    {
        parent::before();

        $me = KunenaUserHelper::getMyself();

        // The inbox is for logged in users.
        if (!$me->exists()) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
        }

        PluginHelper::importPlugin('kunena');

        $event = new GenericEvent('onKunenaInboxView', ['me' => $me, 'inboxto' => $this->input->getInt('inboxto', 0), 'html' => '']);
        $this->app->getDispatcher()->dispatch('onKunenaInboxView', $event);

        $this->content = (string) $event->getArgument('html', '');

        if ($this->content === '') {
            $this->content = '<div class="alert alert-info">' . Text::_('COM_KUNENA_INBOX_NOT_AVAILABLE') . '</div>';
        }
    }
}
