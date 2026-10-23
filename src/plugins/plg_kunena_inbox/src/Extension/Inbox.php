<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Inbox\Extension;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Event\Plugin\AjaxEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Event\KunenaUserTabsEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Plugin\Kunena\Inbox\Helper\InboxAttachments;
use Kunena\Forum\Plugin\Kunena\Inbox\Helper\InboxHelper;
use Kunena\Forum\Plugin\Kunena\Inbox\Helper\KunenaPrivateInbox;
use RuntimeException;
use stdClass;

/**
 * An inbox for the users with the private messages and the notifications of the forum.
 *
 * @since   Kunena 7.2
 */
class Inbox extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * @var     boolean
     * @since   Kunena 7.2
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     * @since   Kunena 7.2
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onKunenaGetPrivate' => 'onKunenaGetPrivate',
            'onKunenaUserTabs'   => 'onKunenaUserTabs',
            'onKunenaInboxView'  => 'onKunenaInboxView',
            'onAjaxInbox'        => 'onAjaxInbox',
        ];
    }

    /**
     * @param   array  $config  config
     *
     * @since   Kunena 7.2
     */
    public function __construct(array $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('7.1') && KunenaForum::enabled())) {
            return;
        }

        // Call loadApi method to load Kunena constants
        KunenaForum::loadApi();

        parent::__construct($config);
    }

    /**
     * Get Kunena private message integration object.
     *
     * @param   KunenaGetPrivateEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 7.2
     */
    public function onKunenaGetPrivate(KunenaGetPrivateEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        $event->stopPropagation();
        $event->setPrivate(new KunenaPrivateInbox($this->params));
    }

    /**
     * The inbox is a tab of the own profile.
     *
     * @param   KunenaUserTabsEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 7.2
     */
    public function onKunenaUserTabs(KunenaUserTabsEvent $event): void
    {
        $profile = $event->getArgument('profile');
        $me      = $event->getArgument('me');
        $tabs    = $event->getArgument('tabs', []);

        if (!isset($this->params) || !$profile || !$me || !$me->exists() || !$profile->isMyself()) {
            return;
        }

        $input  = Factory::getApplication()->getInput();
        $toId   = $input->getInt('inboxto', 0);
        $open   = $toId > 0 || $input->getInt('inbox', 0) === 1;
        $unread = InboxHelper::unreadCount((int) $me->userid);

        $this->loadLanguage();

        $tab          = new stdClass();
        $tab->title   = Text::_('PLG_KUNENA_INBOX_TAB') . ($unread ? ' (' . $unread . ')' : '');
        $tab->content = $this->render((int) $me->userid, $toId);
        $tab->active  = $open;

        if ($open) {
            foreach ($tabs as $other) {
                $other->active = false;
            }
        }

        $tabs['inbox'] = $tab;

        $event->setArgument('tabs', $tabs);
    }

    /**
     * The inbox as a page: the view of a menu item of the forum.
     *
     * @param   GenericEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 7.2
     */
    public function onKunenaInboxView(GenericEvent $event): void
    {
        $me = $event->getArgument('me');

        if (!isset($this->params) || !$me || !$me->exists()) {
            return;
        }

        $this->loadLanguage();
        $event->setArgument('html', $this->render((int) $me->userid, (int) $event->getArgument('inboxto', 0)));
    }

    /**
     * The html of the inbox of a user.
     *
     * @param   int  $userid  The user.
     * @param   int  $toId    The user who is the recipient of a new message (the form is opened).
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected function render(int $userid, int $toId): string
    {
        $to     = $toId > 0 && $toId !== $userid ? KunenaUserHelper::get($toId) : null;
        $toName = $to && $to->exists() ? $to->username : '';

        $messages      = (int) $this->params->get('private', 1);
        $notifications = (int) $this->params->get('notifications', 1);
        $token         = Session::getFormToken();
        $ajax          = Uri::root(true) . '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
        $emailOn       = InboxHelper::wantsEmail($userid, $this->params);
        $attachments   = (int) $this->params->get('attachments', 1);

        ob_start();
        include __DIR__ . '/../../tmpl/inbox.php';

        return (string) ob_get_clean();
    }

    /**
     * The requests of the inbox page (com_ajax).
     *
     * @param   AjaxEvent  $event  The event instance
     *
     * @return  void
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function onAjaxInbox(AjaxEvent $event): void
    {
        $app   = Factory::getApplication();
        $me    = KunenaUserHelper::getMyself();
        $input = $app->getInput();

        if (!isset($this->params)) {
            return;
        }

        $this->loadLanguage();

        if (!$me->exists()) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_LOGIN'), 403);
        }

        $userid = (int) $me->userid;
        $task   = $input->getCmd('task', 'list');

        // Everything which changes something needs the token.
        if (!\in_array($task, ['list', 'view', 'download', 'blocked'], true) && !Session::checkToken('request')) {
            throw new RuntimeException(Text::_('JINVALID_TOKEN_NOTICE'), 403);
        }

        $ids = array_filter(array_map('intval', (array) $input->get('ids', [], 'array')));

        switch ($task) {
            case 'list':
                $filter = $input->getCmd('filter', 'all');
                $filter = \in_array($filter, ['all', 'message', 'notification', 'sent'], true) ? $filter : 'all';

                [$total, $items] = InboxHelper::getList($userid, $filter, max(0, $input->getInt('start', 0)), 20);

                $result = ['total' => $total, 'items' => $items, 'unread' => InboxHelper::unreadCount($userid)];
                break;
            case 'view':
                $result = ['items' => InboxHelper::getThread($userid, $input->getInt('id', 0)), 'unread' => InboxHelper::unreadCount($userid)];
                break;
            case 'send':
                if (!$this->params->get('private', 1)) {
                    throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'));
                }

                // A user who is banned from the forum can not write to others, like he can not post.
                if ($me->isBanned()) {
                    throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_BANNED'), 403);
                }

                $replyTo  = $input->getInt('reply', 0);
                $username = trim($input->getString('to', ''));
                $toId     = 0;

                if (!$replyTo) {
                    $db    = Factory::getContainer()->get('DatabaseDriver');
                    $query = $db->createQuery()
                        ->select($db->quoteName('id'))
                        ->from($db->quoteName('#__users'))
                        ->where($db->quoteName('username') . ' = :name')
                        ->bind(':name', $username);
                    $toId = (int) $db->setQuery($query)->loadResult();

                    if (!$toId) {
                        throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_RECIPIENT'));
                    }
                }

                // The files are checked before anything is stored.
                $uploads = InboxAttachments::getUploads();
                InboxAttachments::check($uploads, $this->params);

                $id = InboxHelper::send($userid, $toId, $input->getString('subject', ''), (string) $input->get('body', '', 'raw'), $replyTo);

                InboxAttachments::store($id, $userid, $uploads);

                $result = ['message' => Text::_('PLG_KUNENA_INBOX_SENT_OK')];
                break;
            case 'download':
                InboxAttachments::download($userid, $input->getInt('id', 0));

                return;
            case 'block':
                $target = $input->getInt('id', 0);

                if (!$target) {
                    $db    = Factory::getContainer()->get('DatabaseDriver');
                    $name  = trim($input->getString('to', ''));
                    $query = $db->createQuery()
                        ->select($db->quoteName('id'))
                        ->from($db->quoteName('#__users'))
                        ->where($db->quoteName('username') . ' = :name')
                        ->bind(':name', $name);
                    $target = (int) $db->setQuery($query)->loadResult();
                }

                InboxHelper::block($userid, $target);
                $result = ['blocked' => InboxHelper::getBlocked($userid)];
                break;
            case 'unblock':
                InboxHelper::unblock($userid, $input->getInt('id', 0));
                $result = ['blocked' => InboxHelper::getBlocked($userid)];
                break;
            case 'blocked':
                $result = ['blocked' => InboxHelper::getBlocked($userid)];
                break;
            case 'read':
                InboxHelper::markRead($userid, $ids);
                $result = ['unread' => InboxHelper::unreadCount($userid)];
                break;
            case 'delete':
                InboxHelper::delete($userid, $ids);
                $result = ['unread' => InboxHelper::unreadCount($userid)];
                break;
            case 'setmail':
                $value = $input->getInt('value', 1) ? 1 : 0;
                KunenaUserHelper::setJoomlaParam($userid, 'kunena_inbox_email', $value);
                $result = ['value' => $value];
                break;
            default:
                throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'));
        }

        $event->addResult($result);
    }
}
