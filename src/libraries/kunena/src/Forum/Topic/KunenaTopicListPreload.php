<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Closure;
use Exception;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * What the lists of topics and messages of one page need, loaded together: a page with several lists (the tabs of a profile)
 * prepares all of them first, and each list adds the users, topics and messages which it needs here instead of loading them;
 * then one query per kind loads them for all the lists (the users, the data of the reader for the topics, the tags, the last
 * messages, the new messages, the places of the messages). A list which is prepared when nothing collects loads its own, as
 * before.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaTopicListPreload
{
    /**
     * How many collections are open (a collection can be opened inside another one): the loading waits for the last one.
     *
     * @var     integer
     * @since   Kunena 8.0
     */
    protected static int $depth = 0;

    /**
     * The users, by id.
     *
     * @var     integer[]
     * @since   Kunena 8.0
     */
    protected static array $users = [];

    /**
     * The topics, by id.
     *
     * @var     KunenaTopic[]
     * @since   Kunena 8.0
     */
    protected static array $topics = [];

    /**
     * The last messages of the topics, by id.
     *
     * @var     integer[]
     * @since   Kunena 8.0
     */
    protected static array $messages = [];

    /**
     * The messages whose place in their topic is needed (the links to them), by id.
     *
     * @var     integer[]
     * @since   Kunena 8.0
     */
    protected static array $locations = [];

    /**
     * What loads the collection (a test gives its own): null for the loading of the helpers.
     *
     * @var     Closure|null
     * @since   Kunena 8.0
     */
    protected static ?Closure $loader = null;

    /**
     * Is the page surveying its lists (their finders note what they found and show nothing)?
     *
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected static bool $surveying = false;

    /**
     * Did a finder note something since the last question (takeFound)?
     *
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected static bool $found = false;

    /**
     * The topics and the messages which the finders found while surveying, by id.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected static array $surveyed = ['topics' => [], 'messages' => []];

    /**
     * What loads the topics and the messages of a survey (a test gives its own): null for the loading of the helpers.
     *
     * @var     Closure|null
     * @since   Kunena 8.0
     */
    protected static ?Closure $surveyLoader = null;

    /**
     * Survey the lists of a page: their finders note the topics and the messages which they find and give nothing, so the lists
     * prepare nothing; the page loads all of them together (stopSurvey) and then prepares the lists for real.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function startSurvey(): void
    {
        self::$surveying = true;
        self::$found     = false;
        self::$surveyed  = ['topics' => [], 'messages' => []];
    }

    /**
     * Is the page surveying its lists?
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isSurveying(): bool
    {
        return self::$surveying;
    }

    /**
     * A finder found these topics or messages while surveying.
     *
     * @param   string  $kind  topics or messages.
     * @param   array   $ids   The ids.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function noteFound(string $kind, array $ids): void
    {
        self::$found = true;

        if (isset(self::$surveyed[$kind])) {
            self::$surveyed[$kind] = self::addIds(self::$surveyed[$kind], $ids);
        }
    }

    /**
     * Did a finder note something since the last question? The list which was prepared then must be prepared again.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function takeFound(): bool
    {
        $found       = self::$found;
        self::$found = false;

        return $found;
    }

    /**
     * End the survey: load the topics and the messages which the finders found, the messages (with their topics) with one query
     * and the topics with one more.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function stopSurvey(): void
    {
        if (!self::$surveying) {
            return;
        }

        $surveyed        = self::$surveyed;
        self::$surveying = false;
        self::$found     = false;
        self::$surveyed  = ['topics' => [], 'messages' => []];

        if (!$surveyed['topics'] && !$surveyed['messages']) {
            return;
        }

        (self::$surveyLoader ?? Closure::fromCallable([self::class, 'loadSurveyed']))($surveyed);
    }

    /**
     * Load the topics and the messages of a survey, without the check of the access: the finders check it when the lists are
     * prepared for real.
     *
     * @param   array  $surveyed  topics and messages (ids by id).
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected static function loadSurveyed(array $surveyed): void
    {
        // The messages load their topics: the topics of the lists come with them (one query for all the topics).
        if ($surveyed['messages']) {
            KunenaTopicHelper::loadAlso(array_values($surveyed['topics']));
            KunenaMessageHelper::getMessages(array_values($surveyed['messages']), 'none');
        }

        if ($surveyed['topics']) {
            KunenaTopicHelper::getTopics(array_values($surveyed['topics']), 'none');
        }
    }

    /**
     * Open a collection: until it is closed, the lists add what they need instead of loading it.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function start(): void
    {
        self::$depth++;
    }

    /**
     * Do the lists add what they need here (a collection is open)?
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isCollecting(): bool
    {
        return self::$depth > 0;
    }

    /**
     * Add what a list needs.
     *
     * @param   KunenaTopic[]  $topics     The topics of the list.
     * @param   int[]          $users      The users which it shows.
     * @param   int[]          $messages   The last messages of its topics.
     * @param   int[]          $locations  The messages whose place it needs (besides the first new message of each topic).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function add(array $topics, array $users = [], array $messages = [], array $locations = []): void
    {
        foreach ($topics as $topic) {
            if (\is_object($topic) && (int) $topic->id > 0) {
                self::$topics[(int) $topic->id] = $topic;
            }
        }

        self::$users     = self::addIds(self::$users, $users);
        self::$messages  = self::addIds(self::$messages, $messages);
        self::$locations = self::addIds(self::$locations, $locations);
    }

    /**
     * Add ids to a list of ids by id (the ids which are no whole numbers above 0 are left out).
     *
     * @param   int[]  $list  The list.
     * @param   array  $ids   The ids to add.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    protected static function addIds(array $list, array $ids): array
    {
        foreach ($ids as $id) {
            if ((int) $id > 0) {
                $list[(int) $id] = (int) $id;
            }
        }

        return $list;
    }

    /**
     * What the open collection has.
     *
     * @return  array  users, topics, messages and locations.
     *
     * @since   Kunena 8.0
     */
    public static function collected(): array
    {
        return [
            'users'     => self::$users,
            'topics'    => self::$topics,
            'messages'  => self::$messages,
            'locations' => self::$locations,
        ];
    }

    /**
     * Close a collection: when it is the last one, load what the lists added, with one query per kind.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function flush(): void
    {
        if (self::$depth === 0) {
            return;
        }

        self::$depth--;

        if (self::$depth > 0) {
            return;
        }

        $collected = self::collected();

        self::$users     = [];
        self::$topics    = [];
        self::$messages  = [];
        self::$locations = [];

        if (!$collected['topics'] && !$collected['users'] && !$collected['messages'] && !$collected['locations']) {
            return;
        }

        (self::$loader ?? Closure::fromCallable([self::class, 'load']))($collected);
    }

    /**
     * Load a collection: what ListDisplay::prepareTopics() loads for one list, for all the lists.
     *
     * @param   array  $collected  users, topics, messages and locations.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected static function load(array $collected): void
    {
        if ($collected['users']) {
            KunenaUserHelper::loadUsers($collected['users']);
        }

        $topicIds = array_keys($collected['topics']);

        if ($topicIds) {
            KunenaTopicHelper::getUserTopics($topicIds);
            KunenaTopicTags::preload($topicIds);
        }

        if ($collected['messages']) {
            KunenaMessageHelper::getMessages($collected['messages']);
        }

        // The first new message of each topic (it sets the number of new messages of the topics too).
        $locations = $collected['locations'];

        if ($collected['topics']) {
            foreach ((array) KunenaTopicHelper::fetchNewStatus(array_values($collected['topics'])) as $id) {
                if ((int) $id > 0) {
                    $locations[(int) $id] = (int) $id;
                }
            }
        }

        if ($locations) {
            KunenaMessageHelper::loadLocation($locations);
        }
    }
}
