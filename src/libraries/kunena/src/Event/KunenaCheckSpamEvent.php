<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Event
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Event;

\defined('_JEXEC') or die;

use BadMethodCallException;
use Joomla\CMS\Event\AbstractImmutableEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class for onKunenaCheckSpam event, dispatched before a new topic or a reply posted from the frontend is saved.
 * A listener rejects the post with reject() or sends it to moderation with hold(); the strictest answer wins.
 * Example:
 *  new KunenaCheckSpamEvent('onKunenaCheckSpam', ['message' => KunenaMessage, 'topic' => KunenaTopic,
 *      'category' => KunenaCategory, 'user' => KunenaUser, 'isNewTopic' => bool, 'ip' => string]);
 *
 * @since  Kunena 8.0
 */
class KunenaCheckSpamEvent extends AbstractImmutableEvent
{
    /**
     * No listener objects to the post.
     *
     * @since  Kunena 8.0
     */
    public const string NONE = '';

    /**
     * The post waits for a moderator.
     *
     * @since  Kunena 8.0
     */
    public const string HOLD = 'hold';

    /**
     * The post is not saved.
     *
     * @since  Kunena 8.0
     */
    public const string REJECT = 'reject';

    /**
     * The required arguments and the class or type each one must have.
     *
     * @since  Kunena 8.0
     */
    private const array ARGUMENTS = [
        'message'    => KunenaMessage::class,
        'topic'      => KunenaTopic::class,
        'category'   => KunenaCategory::class,
        'user'       => KunenaUser::class,
        'isNewTopic' => 'bool',
        'ip'         => 'string',
    ];

    private string $verdict = self::NONE;

    private string $reason = '';

    /**
     * Constructor.
     *
     * @param   string  $name       The event name.
     * @param   array   $arguments  The event arguments.
     *
     * @since   Kunena 8.0
     * @throws  BadMethodCallException
     */
    public function __construct(string $name, array $arguments = [])
    {
        foreach (self::ARGUMENTS as $key => $type) {
            $value = $arguments[$key] ?? null;
            $valid = match ($type) {
                'bool'   => \is_bool($value),
                'string' => \is_string($value),
                default  => $value instanceof $type,
            };

            if (!$valid) {
                $short = substr(strrchr('\\' . $type, '\\'), 1);

                throw new BadMethodCallException("Argument '{$key}' of event {$name} is required and must be a {$short}");
            }
        }

        parent::__construct($name, $arguments);
    }

    /**
     * The post must not be saved. The first non-empty reason is the one shown to the user.
     *
     * @param   string  $reason  An optional text for the user.
     *
     * @return  void
     * @since   Kunena 8.0
     */
    public function reject(string $reason = ''): void
    {
        $this->verdict = self::REJECT;

        if ($this->reason === '') {
            $this->reason = $reason;
        }
    }

    /**
     * The post must wait for a moderator. Never lowers a rejection.
     *
     * @return  void
     * @since   Kunena 8.0
     */
    public function hold(): void
    {
        if ($this->verdict !== self::REJECT) {
            $this->verdict = self::HOLD;
        }
    }

    /**
     * The answer of the listeners: NONE, HOLD or REJECT.
     *
     * @return  string
     * @since   Kunena 8.0
     */
    public function getVerdict(): string
    {
        return $this->verdict;
    }

    /**
     * The reason given with the first reject() which had one, or an empty string.
     *
     * @return  string
     * @since   Kunena 8.0
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Getter for the message argument: the message about to be saved.
     *
     * @return  KunenaMessage
     * @since   Kunena 8.0
     */
    public function getMessage(): KunenaMessage
    {
        return $this->arguments['message'];
    }

    /**
     * Getter for the topic argument: the new, unsaved topic for a new topic.
     *
     * @return  KunenaTopic
     * @since   Kunena 8.0
     */
    public function getTopic(): KunenaTopic
    {
        return $this->arguments['topic'];
    }

    /**
     * Getter for the category argument.
     *
     * @return  KunenaCategory
     * @since   Kunena 8.0
     */
    public function getCategory(): KunenaCategory
    {
        return $this->arguments['category'];
    }

    /**
     * Getter for the user argument: the poster, a guest user object for a guest.
     *
     * @return  KunenaUser
     * @since   Kunena 8.0
     */
    public function getUser(): KunenaUser
    {
        return $this->arguments['user'];
    }

    /**
     * Getter for the isNewTopic argument: true for a new topic, false for a reply.
     *
     * @return  boolean
     * @since   Kunena 8.0
     */
    public function isNewTopic(): bool
    {
        return $this->arguments['isNewTopic'];
    }

    /**
     * Getter for the ip argument: the address of the visitor, even when IP tracking is off.
     *
     * @return  string
     * @since   Kunena 8.0
     */
    public function getIp(): string
    {
        return $this->arguments['ip'];
    }
}
