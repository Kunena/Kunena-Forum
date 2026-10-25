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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The inbox of the user: a page for a menu item. A plugin which gives the inbox (private messages and notifications) shows
 * it through the event onKunenaInboxView.
 *
 * @since   Kunena 8.0
 */
class InboxDisplay extends Display
{
    /**
     * @var     string
     * @since   Kunena 8.0
     */
    public mixed $header = '';

    /**
     * @var     string
     * @since   Kunena 8.0
     */
    public mixed $content = '';

    /**
     * Return the page with the inbox.
     *
     * @return  KunenaLayout
     *
     * @since   Kunena 8.0
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
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function before()
    {
        parent::before();

        $me = KunenaUserHelper::getMyself();

        // The inbox is for logged in users: a guest goes to the login, and comes back to the inbox.
        if (!$me->exists()) {
            $this->app->enqueueMessage(Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'notice');
            $this->app->redirect(
                Route::_('index.php?option=com_users&view=login&return=' . base64_encode(Uri::getInstance()->toString(['path', 'query'])), false)
            );
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
