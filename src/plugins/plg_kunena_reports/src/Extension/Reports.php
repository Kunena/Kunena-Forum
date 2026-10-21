<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Reports
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Reports\Extension;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The reports of posts become topics in a category for the moderators.
 *
 * @since   Kunena 7.2
 */
class Reports extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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
        return ['onKunenaReportMessage' => 'onKunenaReportMessage'];
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
     * A user reports a post: a topic in the category of the reports (a second report of the post is a reply in that topic).
     *
     * @param   GenericEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 7.2
     */
    public function onKunenaReportMessage(GenericEvent $event): void
    {
        $catid = isset($this->params) ? (int) $this->params->get('category', 0) : 0;

        if (!$catid) {
            return;
        }

        $category = KunenaCategoryHelper::get($catid);

        if (!$category->exists()) {
            return;
        }

        $this->loadLanguage();

        $message  = $event->getArgument('message');
        $topic    = $event->getArgument('topic');
        $reporter = $event->getArgument('reporter');
        $reason   = trim((string) $event->getArgument('reason', ''));
        $text     = trim((string) $event->getArgument('text', ''));
        $url      = (string) $event->getArgument('url', '');

        // A report of a post in the category of the reports is of no use.
        if (!$message || !$message->exists() || !$reporter || !$reporter->exists() || (int) $message->catid === $catid) {
            return;
        }

        try {
            $name    = $reporter->getName();
            $profile = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $reporter->getURL(false);
            $author  = $message->getAuthor();

            $quoted = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', (string) $message->message);
            $body   = Text::_('PLG_KUNENA_REPORTS_REPORTED_BY') . ' [url=' . $profile . ']' . $name . '[/url]';

            if ($reason !== '') {
                $body .= "\n[b]" . Text::_('PLG_KUNENA_REPORTS_REASON') . ':[/b] ' . $reason;
            }

            if ($text !== '') {
                $body .= "\n\n" . $text;
            }

            $body .= "\n\n[hr]\n[b][url=" . $url . ']' . Text::_('PLG_KUNENA_REPORTS_POST') . '[/url][/b] '
                . Text::sprintf('PLG_KUNENA_REPORTS_POST_BY', $author->getName((string) $message->name)) . "\n"
                . '[quote="' . $author->getName((string) $message->name) . ' post=' . (int) $message->id . ' userid=' . (int) $message->userid . '"]' . $quoted . '[/quote]';

            $db       = Factory::getContainer()->get(DatabaseInterface::class);
            $existing = $this->getReportTopicId($db, (int) $message->id);
            $reportTopic = $existing ? KunenaTopicHelper::get($existing) : null;

            // The users who reported the post: a post which enough different users report is held for review.
            $reporters = $this->getReporters($db, (int) $message->id);
            $reporters[(int) $reporter->userid] = (int) $reporter->userid;

            $threshold = (int) $this->params->get('holdThreshold', 0);
            $hold      = $threshold > 0 && \count($reporters) >= $threshold && (int) $message->hold === 0
                && (int) $message->id !== (int) $topic->first_post_id && !KunenaUserHelper::get((int) $message->userid)->isModerator();

            if ($hold) {
                $body .= "\n\n[b]" . Text::sprintf('PLG_KUNENA_REPORTS_HELD', \count($reporters)) . '[/b]';
            }

            if ($reportTopic && $reportTopic->exists()) {
                // A report of the post exists already: this one is a reply in the topic.
                $reply          = $reportTopic->newReply(['message' => '[b]' . Text::sprintf('PLG_KUNENA_REPORTS_SUBJECT_AGAIN', $name) . "[/b]\n" . $body], $reporter);
                $reply->hold    = 0;
                $reply->save();

                $messageId = (int) $message->id;
                $json      = json_encode(array_values($reporters));
                $query     = $db->createQuery()
                    ->update($db->quoteName('#__kunena_reports'))
                    ->set($db->quoteName('reports') . ' = ' . $db->quoteName('reports') . ' + 1')
                    ->set($db->quoteName('reporters') . ' = :reporters')
                    ->where($db->quoteName('message_id') . ' = :message')
                    ->bind(':reporters', $json)
                    ->bind(':message', $messageId, ParameterType::INTEGER);
                $db->setQuery($query)->execute();
            } else {
                $subject = mb_substr(Text::sprintf('PLG_KUNENA_REPORTS_SUBJECT', $name, $reason !== '' ? $reason : (string) $topic->subject), 0, 250);

                [$newTopic, $newMessage] = $category->newTopic(['subject' => $subject, 'message' => $body], $reporter, ['category_id' => $catid]);

                // A report is for the moderators to read: it is not held back for review.
                $newMessage->hold = 0;
                $newMessage->save();

                $row = (object) ['message_id' => (int) $message->id, 'topic_id' => (int) $newTopic->id, 'reports' => 1, 'reporters' => json_encode(array_values($reporters)), 'created_time' => time()];

                // A report of the same post which was deleted with its topic is replaced.
                $db->setQuery($db->createQuery()->delete($db->quoteName('#__kunena_reports'))->where($db->quoteName('message_id') . ' = ' . (int) $message->id))->execute();
                $db->insertObject('#__kunena_reports', $row);
            }

            $event->setArgument('handled', true);

            // Hold the post for review: it is not shown until a moderator approves it.
            if ($hold) {
                $message->publish(KunenaForum::UNAPPROVED);
            }
        } catch (Exception $e) {
            // The report is not lost: the e-mail to the moderators still goes out.
            Log::add('Kunena reports: ' . $e->getMessage(), Log::ERROR, 'kunena');
        }
    }

    /**
     * The users who reported a message.
     *
     * @param   DatabaseInterface  $db         The database.
     * @param   int                $messageId  The reported message.
     *
     * @return  int[]  id => id
     *
     * @since   Kunena 7.2
     */
    protected function getReporters(DatabaseInterface $db, int $messageId): array
    {
        $query = $db->createQuery()
            ->select($db->quoteName('reporters'))
            ->from($db->quoteName('#__kunena_reports'))
            ->where($db->quoteName('message_id') . ' = :message')
            ->bind(':message', $messageId, ParameterType::INTEGER);

        $list = json_decode((string) $db->setQuery($query)->loadResult(), true);

        return \is_array($list) ? array_combine(array_map('intval', $list), array_map('intval', $list)) : [];
    }

    /**
     * The topic of the report of a message.
     *
     * @param   DatabaseInterface  $db         The database.
     * @param   int                $messageId  The reported message.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    protected function getReportTopicId(DatabaseInterface $db, int $messageId): int
    {
        $query = $db->createQuery()
            ->select($db->quoteName('topic_id'))
            ->from($db->quoteName('#__kunena_reports'))
            ->where($db->quoteName('message_id') . ' = :message')
            ->bind(':message', $messageId, ParameterType::INTEGER);

        return (int) $db->setQuery($query)->loadResult();
    }
}
