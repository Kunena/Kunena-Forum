<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\History;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHistory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The edit history of a message: each edit, the newest first, with who edited, when, the reason and the change of the subject and
 * of the text; then the original version.
 *
 * @since   Kunena 8.0
 */
class TopicHistoryDisplay extends KunenaControllerDisplay
{
    /**
     * @var    KunenaMessage|null
     * @since  Kunena 8.0
     */
    public mixed $message = null;

    /**
     * @var    KunenaTopic|null
     * @since  Kunena 8.0
     */
    public mixed $topic = null;

    /**
     * @var    KunenaCategory|null
     * @since  Kunena 8.0
     */
    public mixed $category = null;

    /**
     * The edits, the newest first: objects with editor (KunenaUser), time, reason, subject and text (the changes, HTML).
     *
     * @var    array
     * @since  Kunena 8.0
     */
    public array $edits = [];

    /**
     * The original version: object with author (KunenaUser), time, subject and text (escaped), or null without history.
     *
     * @var    object|null
     * @since  Kunena 8.0
     */
    public ?object $original = null;

    /**
     * @var    string
     * @since  Kunena 8.0
     */
    public string $uri = '';

    /**
     * @var    string
     * @since  Kunena 8.0
     */
    protected mixed $name = 'Topic/History';

    /**
     * Load the message and its history, for a user who may see it.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function before()
    {
        parent::before();

        $setting = (int) ($this->config->editHistory ?? KunenaMessageHistory::MODERATORS);

        if ($setting === KunenaMessageHistory::OFF) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
        }

        $this->message = KunenaMessageHelper::get($this->input->getInt('mesid'));
        $this->message->tryAuthorise('read');
        $this->topic    = $this->message->getTopic();
        $this->category = $this->topic->getCategory();

        $me        = KunenaUserHelper::getMyself();
        $moderator = $me->isModerator($this->category);

        if (!KunenaMessageHistory::canSee($setting, $moderator, true)) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), $me->exists() ? 403 : 401);
        }

        // The versions are the text as it was typed: the blocks which the message hides from this reader are left out, as on the
        // message. The text in [confidential] is for the author and the moderators, the one in [private] for nobody and the one in
        // [hide] for the members who are logged in; the history showed them to every reader who may see it.
        $hidden = [];

        if (!$moderator && !($me->exists() && (int) $me->userid === (int) $this->message->userid)) {
            $hidden = ['confidential', 'private'];
        }

        if (!$me->exists()) {
            $hidden[] = 'hide';
        }

        $text = static fn (string $message): string => $hidden ? KunenaParser::removeBlocks($message, $hidden) : $message;

        $versions = KunenaMessageHistory::getVersions((int) $this->message->id);
        $count    = \count($versions);

        foreach ($versions as $version) {
            $version->message = $text((string) $version->message);
        }

        // Each edit changed its version into the next one (the last edit into the message as it is now).
        for ($i = $count - 1; $i >= 0; $i--) {
            $version = $versions[$i];
            $next    = $versions[$i + 1] ?? (object) ['subject' => (string) $this->message->subject, 'message' => $text((string) $this->message->message)];

            $this->edits[] = (object) [
                'editor'  => KunenaUserHelper::get((int) $version->edited_by),
                'time'    => (int) $version->edited_time,
                'reason'  => (string) $version->reason,
                'subject' => (string) $version->subject !== (string) $next->subject
                    ? KunenaMessageHistory::diff((string) $version->subject, (string) $next->subject) : '',
                'text' => KunenaMessageHistory::diff((string) $version->message, (string) $next->message),
            ];
        }

        if ($count) {
            $this->original = (object) [
                'author'  => $this->message->getAuthor(),
                'time'    => (int) $this->message->time,
                'subject' => htmlspecialchars((string) $versions[0]->subject, ENT_QUOTES, 'UTF-8'),
                'text'    => htmlspecialchars((string) $versions[0]->message, ENT_QUOTES, 'UTF-8'),
            ];
        }

        $this->uri = "index.php?option=com_kunena&view=topic&layout=history&catid={$this->category->id}&id={$this->topic->id}"
            . "&mesid={$this->message->id}";
    }

    /**
     * The title of the page.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function prepareDocument()
    {
        $this->setTitle(Text::sprintf('COM_KUNENA_HISTORY_TITLE', $this->message->displayField('subject')));
        $this->setMetaData('robots', 'noindex, follow');
    }
}
