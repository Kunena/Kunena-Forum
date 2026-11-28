<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicListPreload;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class \Kunena\Forum\Libraries\Forum\Message\MessageFinder
 *
 * @since   Kunena 6.0
 */
class KunenaMessageFinder extends KunenaFinder
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $table = '#__kunena_messages';

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $hold = [0];

    /**
     * The user whose own unapproved messages are found besides the holds of $hold, or 0 for nobody.
     *
     * @var     integer
     * @since   Kunena 8.0
     */
    protected int $unapprovedOf = 0;

    /**
     * @var     bool|null
     * @since   Kunena 6.0
     */
    protected mixed $moved = null;

    /**
     * Constructor.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct()
    {
        parent::__construct();

        $this->limit = KunenaFactory::getConfig()->messagesPerPage;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function build(?QueryInterface $query = null): void
    {
        if (!empty($this->hold)) {
            $this->hold = ArrayHelper::toInteger($this->hold, 0);
            $hold       = implode(',', $this->hold);
            $where      = $this->db->quoteName('a.hold') . ' IN (' . $hold . ')';

            if ($this->unapprovedOf > 0) {
                $where = '(' . $where . ' OR (' . $this->db->quoteName('a.hold') . ' = 1 AND '
                    . $this->db->quoteName('a.userid') . ' = ' . $this->unapprovedOf . '))';
            }

            $query->where($where);
        }
    }

    /**
     * Filter by list of categories.
     *
     * It is very important to use this or user access filter. Otherwise messages from unauthorized categories will be
     * included to the search results.
     *
     * $messages->filterByCategories($me->getAllowedCategories())->limit(20)->find();
     *
     * @param   array  $categories  categories
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByCategories(array $categories): KunenaMessageFinder
    {
        $list = [];

        foreach ($categories as $category) {
            if ($category instanceof KunenaCategory) {
                $list[] = (int) $category->id;
            } else {
                $list[] = (int) $category;
            }
        }

        // Handle an empty list as an impossible filter value: no category means no message.
        if (!$list) {
            $list[] = -1;
        }

        $this->query->where($this->db->quoteName('a.catid') . ' IN (' . implode(',', $list) . ')');

        return $this;
    }

    /**
     * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
     *
     * @param   array  $hold  List of hold states to display.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByHold(array $hold = [0]): KunenaMessageFinder
    {
        $this->hold = $hold;

        return $this;
    }

    /**
     * Leave out the replies which wait for their date (scheduled, not held for review): they are for no moderator yet, and an
     * approval of one published it at once, before the date which its author chose (as KunenaModerationQueue leaves them out).
     *
     * @return  $this
     *
     * @since   Kunena 8.0
     */
    public function filterNotScheduled(): KunenaMessageFinder
    {
        $this->query->where('(' . $this->db->quoteName('a.scheduled_time') . ' = 0 OR ' . $this->db->quoteName('a.scheduled_review') . ' <> 0)');

        return $this;
    }

    /**
     * Filter by hold, and find also the unapproved messages (hold 1) of one user: the author of a topic which waits for
     * approval reads his own messages in it, not the unapproved messages of the others.
     *
     * @param   array       $hold  List of hold states to display.
     * @param   KunenaUser  $user  The user whose unapproved messages are found too (a guest has none).
     *
     * @return  $this
     *
     * @since   Kunena 8.0
     */
    public function filterByHoldOrOwnUnapproved(array $hold, KunenaUser $user): KunenaMessageFinder
    {
        $this->hold         = $hold;
        $this->unapprovedOf = $user->exists() ? max(0, (int) $user->userid) : 0;

        return $this;
    }

    /**
     * Filter by time.
     *
     * @param   Date|null  $starting  Starting date or null if older than ending date.
     * @param   Date|null  $ending    Ending date or null if newer than starting date.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByTime(?Date $starting = null, ?Date $ending = null): KunenaMessageFinder
    {
        if ($starting && $ending) {
            $this->query->where($this->db->quoteName('a.time') . ' BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
        } elseif ($starting) {
            $this->query->where($this->db->quoteName('a.time') . ' > ' . $this->db->quote($starting->toUnix()));
        } elseif ($ending) {
            $this->query->where($this->db->quoteName('a.time') . ' <= ' . $this->db->quote($ending->toUnix()));
        }

        return $this;
    }

    /**
     * Filter by users role in the message. Use it once in a query: the thank you filters join their table, a second call would join it again.
     *
     * posted = User has posted the message.
     *
     * @param   KunenaUser|null  $user    user
     * @param   string           $action  Action or negation of the action (!action).
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByUser(?KunenaUser $user = null, $action = 'posted'): KunenaMessageFinder
    {
        if (\is_null($user) || \is_null($user->userid)) {
            return $this;
        }

        switch ($action) {
            case 'author':
                $this->query->where($this->db->quoteName('a.userid') . ' = ' . (int) $user->userid);
                break;
            case '!author':
                $this->query->where($this->db->quoteName('a.userid') . ' != ' . (int) $user->userid);
                break;
            case 'editor':
                $this->query->where($this->db->quoteName('a.modified_by') . ' = ' . (int) $user->userid);
                break;
            case '!editor':
                $this->query->where($this->db->quoteName('a.modified_by') . ' != ' . (int) $user->userid);
                break;
            case 'thanker':
                $this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON ' . $this->db->quoteName('th.postid') . ' = a.id AND ' . $this->db->quoteName('th.userid') . ' = ' . (int) $user->userid);
                break;
            case '!thanker':
                $this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON ' . $this->db->quoteName('th.postid') . ' = a.id AND ' . $this->db->quoteName('th.userid') . ' != ' . (int) $user->userid);
                break;
            case 'thankee':
                // A message once, however many members thanked for it: the join gave a row for each of them, so the list of the
                // thank yous which the user received counted a message that often, and its pages were short or empty.
                $this->query->where('EXISTS (SELECT 1 FROM ' . $this->db->quoteName('#__kunena_thankyou', 'th') . ' WHERE ' . $this->db->quoteName('th.postid') . ' = '
                    . $this->db->quoteName('a.id') . ' AND ' . $this->db->quoteName('th.targetuserid') . ' = ' . (int) $user->userid . ')');
                // Only the messages of which he is still the author: a thank you keeps the author whom it was given to, and a message
                // which he made anonymous afterwards was in the list of his profile, which told every member who wrote it.
                $this->query->where($this->db->quoteName('a.userid') . ' = ' . (int) $user->userid);
                break;
            case '!thankee':
                $this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON ' . $this->db->quoteName('th.postid') . ' = a.id AND ' . $this->db->quoteName('th.targetuserid') . ' != ' . (int) $user->userid);
                break;
            case 'bookmarker':
                // The messages which the user bookmarked.
                $this->query->innerJoin($this->db->quoteName('#__kunena_bookmarks', 'bm') . ' ON ' . $this->db->quoteName('bm.message_id') . ' = a.id AND ' . $this->db->quoteName('bm.user_id') . ' = ' . (int) $user->userid);
                break;
        }

        return $this;
    }

    /**
     * Leave out the messages of the users (the ignore list of a user), as KunenaTopicFinder::filterOutAuthors() does for the topics.
     *
     * @param   int[]  $userids  Ids of the users.
     *
     * @return  $this
     *
     * @since   Kunena 8.0
     */
    public function filterOutAuthors(array $userids): KunenaMessageFinder
    {
        $userids = array_filter(ArrayHelper::toInteger($userids));

        if ($userids) {
            $this->query->where($this->db->quoteName('a.userid') . ' NOT IN (' . implode(',', $userids) . ')');
        }

        return $this;
    }

    /**
     * Filter by user access to the categories.
     *
     * It is very important to use this or category filter. Otherwise messages from unauthorized categories will be
     * included to the search results.
     *
     * @param   KunenaUser  $user  user
     *
     * @return  $this
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function filterByUserAccess(KunenaUser $user): KunenaMessageFinder
    {
        $categories = array_map('intval', (array) $user->getAllowedCategories());

        // A user without any allowed category sees no message: an empty list must not become "IN ()".
        if (empty($categories)) {
            $this->query->where('0');

            return $this;
        }

        $this->query->where($this->db->quoteName('a.catid') . ' IN (' . implode(',', $categories) . ')');

        // Categories where the users only see their own topics.
        $own = KunenaCategoryHelper::getOwnTopicsOnlyWhere($user, $this->db->quoteName('a.catid'), $this->db->quoteName('a.thread'), true);

        if ($own) {
            $this->query->where($own);
        }

        return $this;
    }

    /**
     * Get messages.
     *
     * @param   string  $access  Kunena action access control check.
     *
     * @return array
     *
     * @since   Kunena 6.0
     * @throws Exception
     */
    public function find($access = 'read'): array
    {
        $results = parent::find();

        // A page which surveys its lists (the tabs of a profile) loads the messages of all of them together, later.
        if (KunenaTopicListPreload::isSurveying()) {
            KunenaTopicListPreload::noteFound('messages', $results);

            return [];
        }

        return KunenaMessageHelper::getMessages($results, $access);
    }
}
